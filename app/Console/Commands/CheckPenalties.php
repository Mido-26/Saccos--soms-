<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PenaltsController;

class CheckPenalties extends Command
{
    protected $signature = 'penalties:check'; // The command name
    protected $description = 'Check and apply penalties for overdue savings and loan installments'; // Command description

    protected $penaltyController;

    public function __construct(PenaltsController $penaltyController)
    {
        parent::__construct();
        $this->penaltyController = $penaltyController;
    }

    public function handle()
    {
        $this->info('Checking penalties for overdue savings and loan installments...');

        // Check for savings penalties
        $this->penaltyController->checkSavingsPenalty();

        // Check for loan penalties
        $this->penaltyController->checkLoanPenalty();

        $this->info('Penalty check completed!');
    }
}
