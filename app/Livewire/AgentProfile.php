<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AgentProfile extends Component
{
    public string $name        = '';
    public string $currentPassword = '';
    public string $newPassword     = '';
    public string $confirmPassword = '';
    public ?string $successMsg  = null;
    public ?string $errorMsg    = null;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
    }

    public function updateName(): void
    {
        $this->validate(['name' => 'required|string|max:255']);
        Auth::user()->update(['name' => $this->name]);
        $this->successMsg = 'Name updated successfully.';
        $this->errorMsg   = null;
    }

    public function updatePassword(): void
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword'     => 'required|min:8',
            'confirmPassword' => 'required|same:newPassword',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->errorMsg   = 'Current password is incorrect.';
            $this->successMsg = null;
            return;
        }

        $user->update(['password' => Hash::make($this->newPassword)]);
        $this->currentPassword = '';
        $this->newPassword     = '';
        $this->confirmPassword = '';
        $this->successMsg = 'Password updated successfully.';
        $this->errorMsg   = null;
    }

    public function render()
    {
        return view('livewire.agent-profile');
    }
}