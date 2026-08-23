<?php

namespace App\Livewire\Admin\Newsletters;

use App\Models\Newsletter;
use App\Models\Subscriber;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Newsletters')]
class NewsletterManager extends Component
{
    use WithPagination;

    public ?int $editingId = null;
    public bool $showModal = false;

    public string $subject = '';
    public string $content = '';

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $newsletter = Newsletter::findOrFail($id);
        $this->editingId = $newsletter->id;
        $this->subject = $newsletter->subject;
        $this->content = (string) $newsletter->content;
        $this->showModal = true;
    }

    public function save(): Newsletter
    {
        $this->validate([
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $newsletter = $this->editingId ? Newsletter::findOrFail($this->editingId) : new Newsletter(['status' => 'draft']);
        $newsletter->subject = $this->subject;
        $newsletter->content = $this->content;
        $newsletter->save();

        session()->flash('status', __('Newsletter saved.'));
        $this->showModal = false;
        $this->resetForm();

        return $newsletter;
    }

    /**
     * Queue the newsletter to every subscribed reader. The actual mail
     * dispatch is handled by the SendNewsletter job/command; here we mark
     * the campaign as sent and record the audience size.
     */
    public function send(int $id): void
    {
        $newsletter = Newsletter::findOrFail($id);
        $recipients = Subscriber::subscribed()->count();

        if (class_exists(\App\Jobs\SendNewsletter::class)) {
            \App\Jobs\SendNewsletter::dispatch($newsletter);
            $newsletter->update(['status' => 'sending', 'recipients' => $recipients]);
            session()->flash('status', __('Newsletter queued to :count subscribers.', ['count' => $recipients]));

            return;
        }

        $newsletter->update([
            'status' => 'sent',
            'recipients' => $recipients,
            'sent_at' => now(),
        ]);
        session()->flash('status', __('Newsletter marked as sent to :count subscribers.', ['count' => $recipients]));
    }

    public function delete(int $id): void
    {
        Newsletter::whereKey($id)->delete();
        session()->flash('status', __('Newsletter deleted.'));
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'subject', 'content']);
    }

    public function render()
    {
        return view('livewire.admin.newsletters.index', [
            'newsletters' => Newsletter::latest()->paginate(15),
            'subscriberCount' => Subscriber::subscribed()->count(),
        ]);
    }
}
