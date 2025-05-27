<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Counter extends Component
{
    public User $user;
    public $position = 0;
    public $users;

    public function mount()
    {
        $this->users = User::all();
        $this->updateCurrentUser();
    }

    public function increment()
    {
        if ($this->position < $this->users->count() - 1) {
            $this->position++;
        }
        $this->updateCurrentUser();
    }

    public function decrement()
    {
        if ($this->position > 0) {
            $this->position--;
        }
        $this->updateCurrentUser();
    }

    public function render()
    {
        return view('livewire.counter')
            ->extends('layouts.app');
    }

    public function updateCurrentUser(): void
    {
        $this->user = $this->users[$this->position];
    }
}
