<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Reset Password')]
#[Layout('components.layouts.reset-password-layout')]
class ResetPasswordForm extends Component
{
    #[Validate('required|string')]
    public string $token = '';

    #[Url]
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|confirmed')]
    public string $password = '';

    #[Validate('required')]
    public string $password_confirmation = '';

    public bool $done = false;

    public bool $success = false;

    public string $reset_message = '';

    public function mount(string $token): void
    {
        $this->token = $token;
    }

    public function resetPassword()
    {
        $this->validate();

        $status = Password::broker('customers')->reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        $this->done = true;
        $this->success = $status === Password::PASSWORD_RESET;
        $this->reset_message = __($status);

        session()->flash('done', $this->done);
        session()->flash('success', $this->success);
        session()->flash('reset_message', $this->reset_message);
    }

    public function render()
    {
        return view('livewire.reset-password-form');
    }
}
