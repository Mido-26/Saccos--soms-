<?php

namespace App\Notifications;

use App\Models\Loans;
use App\Models\Referee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanApprovedNotification extends Notification implements ShouldQueue
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
                ? "Your loan has been **approved by an administrator**." 
                : "Referee **$refereeName** has approved your loan application.",
            
            'referee' => "You have approved to refer **$borrowerName** for a loan application.",

            'admin' => $this->action === 'admin' 
                ? "Admin has **approved** the loan request for borrower **$borrowerName**." 
                : "Referee **$refereeName** has approved borrower **$borrowerName's** loan.",

            default => "A loan update has been made."
        };

        return (new MailMessage)
                ->line($message)
                ->action('View Loan', url('/loans/' . $this->loan->id))
                ->line('Thank you for using our service!');
    }

    public function toDatabase($notifiable)
    {
        $borrowerName = $this->loan->user->first_name . ' ' . $this->loan->user->last_name;
        $refereeName = $this->referee ? $this->referee->user->first_name . ' ' . $this->referee->user->last_name : null;

        $message = match ($this->recipientType) {
            'borrower' => $this->action === 'admin' 
                ? "Your loan has been **approved by an administrator**." 
                : "Referee **$refereeName** has approved your loan application.",
            
            'referee' => "You have approved to refer **$borrowerName** for a loan application.",

            'admin' => $this->action === 'admin' 
                ? "Admin has **approved** the loan request for borrower **$borrowerName**." 
                : "Referee **$refereeName** has approved borrower **$borrowerName's** loan.",

            default => "A loan update has been made."
        };

        return [
            'loan_id' => $this->loan->id,
            'status' => 'approved',
            'message' => $message,
        ];
    }
}
