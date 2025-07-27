<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Login extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $password = '';

    #[Rule('boolean')]
    public bool $remember = false;

    public function login()
    {
        $credentials = $this->validate();

        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        session()->regenerate();

        $this->redirect(intended: route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.login')->layout('layouts.auth');
    }
}
