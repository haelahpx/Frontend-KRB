<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

#[Layout('layouts.auth')]
#[Title('Login')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
    }

    protected function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function login()
    {
        $this->validate();

        $key = 'login:' . Str::lower($this->email) . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $sec = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many attempts. Try again in {$sec}s.",
            ]);
        }

        $ok = Auth::attempt(
            ['email' => Str::lower($this->email), 'password' => $this->password],
            $this->remember
        );

        if (! $ok) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        RateLimiter::clear($key);

        request()->session()->regenerate();
        session()->forget('url.intended');

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.pages.auth.login');
    }
}
