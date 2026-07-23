<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;

    /**
     * Create a new event instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->payment->user_id),
        ];
    }

    /**
     * Get the event data for broadcasting.
     */
    public function broadcastWith(): array
    {
        return [
            'payment_id' => $this->payment->id,
            'course_title' => $this->payment->course->title,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'completed_at' => $this->payment->completed_at,
        ];
    }
}
