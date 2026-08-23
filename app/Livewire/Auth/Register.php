<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $this->uniqueUsername($data['name']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'locale' => app()->getLocale(),
            'is_active' => true,
        ]);

        $user->assignRole('subscriber');

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('account');
    }

    protected function uniqueUsername(string $name): string
    {
        $base = Str::slug($name) ?: 'user';
        if (Str::slug($name) === '') {
            $base = 'user-'.Str::lower(Str::random(5));
        }

        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base.'-'.$i++;
        }

        return $username;
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
