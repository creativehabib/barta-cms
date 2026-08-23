<?php

namespace App\Livewire\Admin\Media;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Media Library')]
class MediaLibrary extends Component
{
    use WithFileUploads;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public $uploads = [];

    public string $disk = 'public';
    public string $folder = 'media';

    public function updatedUploads(): void
    {
        $this->validate([
            'uploads.*' => ['file', 'max:20480'],
        ]);

        foreach ($this->uploads as $file) {
            $name = Str::random(8).'-'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                .'.'.$file->getClientOriginalExtension();
            $file->storeAs($this->folder, $name, $this->disk);
        }

        $this->uploads = [];
        session()->flash('status', __('Files uploaded.'));
    }

    public function delete(string $path): void
    {
        if (Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
            session()->flash('status', __('File deleted.'));
        }
    }

    public function render()
    {
        $files = collect(Storage::disk($this->disk)->files($this->folder))
            ->sortDesc()
            ->map(fn ($path) => [
                'path' => $path,
                'name' => basename($path),
                'url' => Storage::disk($this->disk)->url($path),
                'size' => Storage::disk($this->disk)->size($path),
                'is_image' => in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif']),
            ])
            ->values();

        return view('livewire.admin.media.index', ['files' => $files]);
    }
}
