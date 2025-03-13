<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PasswordUpdatedNotification extends Notification
{
    use Queueable;

    protected $channel;

    /**
     * Create a new notification instance.
     *
     * @param string $channel The notification channel (e.g., 'database', 'mail')
     */
    public function __construct($channel)
    {
        $this->channel = $channel;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [$this->channel]; // Send via the specified channel
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Password Updated Successfully')
                    ->line('Your password has been updated successfully.')
                    ->line('If you did not make this change, please contact support immediately.')
                    ->action('Visit Dashboard', url('/dashboard'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'message' => 'Your password was updated successfully.',
            'action' => url('/dashboard'),
            'details' => 'Password change detected at ' . now()->toDateTimeString(),
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'Your password was updated successfully.',
            'action' => url('/dashboard'),
            'details' => 'Password change detected at ' . now()->toDateTimeString(),
        ];
    }
}
