<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayments extends Model
{
    /** @use HasFactory<\Database\Factories\LoanRepaymentsFactory> */
    use HasFactory;

    // Define the fillable columns
    protected $guarded = [];

    // Define the relationship  
    public function loan()
    {
        return $this->belongsTo(Loans::class);
    }

    // Define the relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
