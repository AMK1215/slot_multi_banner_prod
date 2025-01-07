<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerDepositNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
     use Queueable, InteractsWithSockets;

    /**
     * Create a new notification instance.
     */
     protected $deposit;

    /**
     * Create a new notification instance.
     */
    public function __construct($deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * Determine how the notification will be delivered.
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Store notification in the database.
     */
    public function toDatabase($notifiable)
    {
        return [
            'player_name' => $this->deposit->user->user_name,
            'amount' => $this->deposit->amount,
            'refrence_no' => $this->deposit->refrence_no,
            'message' => "Player {$this->deposit->user->user_name} has deposited {$this->deposit->amount}."
        ];
    }

    /**
     * Broadcast the notification (for real-time updates).
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'player_name' => $this->deposit->user->user_name,
            'amount' => $this->deposit->amount,
            'refrence_no' => $this->deposit->refrence_no,
            'message' => "Player {$this->deposit->user->user_name} has deposited {$this->deposit->amount}."
        ]);
    }

     /**
     * Get the broadcast channel.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('agent.' . $this->deposit->agent_id);
    }
}