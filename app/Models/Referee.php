<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Referee extends Model 
{
    use Notifiable;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function loan(){
        return $this->belongsTo(Loans::class, 'loan_id');
    }
}
