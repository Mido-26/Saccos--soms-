<?php
// app/Notifications/LoanDisbursedNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanDisbursedNotification extends Notification
{
    use Queueable;

    protected $loan;

    public function __construct($loan)
    {
        $this->loan = $loan;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Add other channels like SMS if needed
    }

    public function toMail($notifiable)
    {
        $borrowerName = $this->loan->user->first_name . ' ' . $this->loan->user->last_name;
        return (new MailMessage)
            ->subject('Loan Disbursed Successfully')
            ->line("Dear {$borrowerName},")
            ->line("Your loan of {$this->loan->principal_amount} has been disbursed on {$this->loan->disbursed_at}.")
            ->line('Thank you for using our services!')
            ->action('View Loan Details', url('/loans/' . $this->loan->id));
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Your loan of {$this->loan->principal_amount} has been disbursed.",
            'loan_id' => $this->loan->id,
        ];
    }
}