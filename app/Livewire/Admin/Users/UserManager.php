<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
#[Title('Users')]
class UserManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $role = '';

    public ?int $editingId = null;
    public bool $showModal = false;

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $phone = '';
    public string $bio = '';
    public string $locale = 'bn';
    public bool $is_active = true;
    public array $roles = [];

    public function updating($name): void
    {
        if (in_array($name, ['search', 'role'])) {
            $this->resetPage();
        }
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->username = (string) $user->username;
        $this->email = $user->email;
        $this->password = '';
        $this->phone = (string) $user->phone;
        $this->bio = (string) $user->bio;
        $this->locale = $user->locale ?: 'bn';
        $this->is_active = (bool) $user->is_active;
        $this->roles = $user->getRoleNames()->all();
        $this->showModal = true;
    }

    public function save(): void
    {
        $id = $this->editingId;

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.($id ?? 'NULL').',id'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.($id ?? 'NULL').',id'],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:40'],
            'locale' => ['required', 'string', 'max:10'],
            'roles' => ['array'],
        ]);

        $user = $id ? User::findOrFail($id) : new User();
        $user->name = $this->name;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->phone = $this->phone ?: null;
        $user->bio = $this->bio ?: null;
        $user->locale = $this->locale;
        $user->is_active = $this->is_active;
        if (filled($this->password)) {
            $user->password = Hash::make($this->password);
        }
        $user->save();

        $user->syncRoles($this->roles);

        session()->flash('status', __('User saved.'));
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('status', __('You cannot delete your own account.'));

            return;
        }

        User::whereKey($id)->delete();
        session()->flash('status', __('User deleted.'));
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'username', 'email', 'password', 'phone', 'bio', 'locale', 'is_active', 'roles']);
        $this->locale = 'bn';
        $this->is_active = true;
        $this->roles = [];
    }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('username', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->role, fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', $this->role)))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.users.index', [
            'users' => $users,
            'allRoles' => Role::orderBy('name')->pluck('name'),
        ]);
    }
}
