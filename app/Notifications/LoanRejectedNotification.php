<?php

// // app/Notifications/LoanRejectedNotification.php
namespace App\Notifications;

use App\Models\Loans;
use App\Models\Referee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $loan;
    protected $recipientType;
    protected $referee;
    protected $action;

    public function __construct(Loans $loan, $recipientType, Referee $referee = null, $action = null)
    {
        $this->loan = $loan;
        $this->recipientType = $recipientType;
        $this->referee = $referee;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $borrowerName = $this->loan->user->first_name . ' ' . $this->loan->user->last_name;
        $refereeName = $this->referee ? $this->referee->user->first_name . ' ' . $this->referee->user->last_name : null;

        $message = match ($this->recipientType) {
            'borrower' => $this->action === 'admin'
                ? "Your loan application has been **rejected by an administrator**."
                : "Referee **$refereeName** has rejected your loan application.",
            
            'referee' => "You have rejected to refer **$borrowerName** for a loan application.",
            
            'admin' => $this->action === 'admin'
                ? "Admin has **rejected** the loan request for borrower **$borrowerName**."
                : "Referee **$refereeName** has rejected borrower **$borrowerName's** loan.",
            
            default => "A loan update has been made."
        };

        return (new MailMessage)
                ->line($message)
                ->action('View Loan', url('/loans/' . $this->loan->id))
                ->line('Please review the loan details');
    }

    public function toDatabase($notifiable)
    {
        $borrowerName = $this->loan->user->first_name . ' ' . $this->loan->user->last_name;
        $refereeName = $this->referee ? $this->referee->user->first_name . ' ' . $this->referee->user->last_name : null;

        $message = match ($this->recipientType) {
            'borrower' => $this->action === 'admin'
                ? "Your loan application has been **rejected by an administrator**."
                : "Referee **$refereeName** has rejected your loan application.",
            
            'referee' => "You have rejected to refer **$borrowerName** for a loan application.",
            
            'admin' => $this->action === 'admin'
                ? "Admin has **rejected** the loan request for borrower **$borrowerName**."
                : "Referee **$refereeName** has rejected borrower **$borrowerName's** loan.",
            
            default => "A loan update has been made."
        };

        return [
            'loan_id' => $this->loan->id,
            'status' => 'rejected',
            'message' => $message,
        ];
    }
}


