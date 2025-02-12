<?php

namespace App\Notifications;

use App\Models\LoanRepayment;
use App\Models\LoanRepayments;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OverdueInstallmentNotification extends Notification
{
    protected $installment;

    public function __construct(LoanRepayments $installment)
    {
        $this->installment = $installment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];  // Adjust as needed (mail, database, etc.)
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Overdue Loan Installment')
            ->line('Your loan installment is overdue!')
            ->action('View Loan', url('/loan/' . $this->installment->loan_id));
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your loan installment is overdue.',
            'loan_id' => $this->installment->loan_id,
            'installment_id' => $this->installment->id,
        ];
    }
}
