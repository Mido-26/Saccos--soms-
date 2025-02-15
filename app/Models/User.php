<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'Date_OF_Birth',
        'Address',
        'password',
        'status',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
{
    static::created(function ($user) {
        // Create a new savings account with default values
        $user->savings()->create([
            'account_balance' => 1000000,      // Initial balance
            'interest_earned' => 0,      // Initial interest
            'last_deposit_date' => null, // Set this as needed
        ]);
    });
}
    public function savings()
    {
        return $this->hasOne(Savings::class);
    }

    public function transactions(){
        return $this->hasmany(Transactions::class);
    }

    public function loan(){

        return $this->hasMany(Loans::class);
    }

    public function referee(){
        return $this->hasOne(Referee::class);
    }
}
