<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{
    /** @use HasFactory<\Database\Factories\LoansFactory> */
    use HasFactory;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function referee(){
        return $this->hasMany(Referee::class, 'loan_id');
    }

    public function repayments(){
        return $this->hasMany(LoanRepayments::class, 'loan_id');
    }
    
}
