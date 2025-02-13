<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Loans;

class LoanCreationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $loan;

    public function __construct(Loans $loan)
    {
        $this->loan = $loan;
    }

    // Delivery channels
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    // Email Content
    public function toMail($notifiable)
    {
        $name = $this->loan->user->first_name . ' ' . $this->loan->user->last_name;
        return (new MailMessage)
            ->subject('New Loan Application Received')
            ->greeting("Hello {$name}!")
            ->line('Your loan application has been successfully submitted.')
            ->line("**Amount:** {$this->loan->amount}")
            ->line("**Term:** {$this->loan->term} months")
            ->action('View Application', route('loans.show', $this->loan->id))
            ->line('We will notify you once it\'s reviewed.');
    }

    // Database Notification Content
    public function toDatabase($notifiable)
    {
        return [
            'type' => 'loan-created',
            'loan_id' => $this->loan->id,
            'message' => "New loan application for {$this->loan->amount} submitted",
            'link' => route('loans.show', $this->loan->id)
        ];
    }

}