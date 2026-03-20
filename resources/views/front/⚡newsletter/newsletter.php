<?php

use App\Models\NewsletterSubscriber;
use Livewire\Attributes\Validate;

new class extends \Livewire\Component {

    #[Validate('required|email|max:255')]
    public string $email = '';

    public bool $subscribed = false;

    public function subscribe()
    {
        $this->validate();

        if (!NewsletterSubscriber::where('email', $this->email)->exists()) {
            NewsletterSubscriber::create(['email' => $this->email]);
        }

        $this->subscribed = true;
        $this->email = '';
    }

};
