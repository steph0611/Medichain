<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $table = 'customers'; // your table name

    protected $primaryKey = 'customer_id'; // if your PK is customer_id

    protected $fillable = [
        'username', 'email', 'password', // update with your real fields
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
