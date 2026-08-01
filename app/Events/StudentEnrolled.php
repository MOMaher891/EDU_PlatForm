<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentEnrolled
{
    use Dispatchable, SerializesModels;

    public User $user;
    public mixed $payable;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param mixed $payable
     */
    public function __construct(User $user, mixed $payable)
    {
        $this->user = $user;
        $this->payable = $payable;
    }
}
