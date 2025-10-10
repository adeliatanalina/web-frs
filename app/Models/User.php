<?php

namespace App\Models;
// app/Models/User.php
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name','email','password','nrp'];

    // Jika email sesuai 10digit@student.its.ac.id → set nrp otomatis
    public function setEmailAttribute($value)
    {
        $email = strtolower($value);
        $this->attributes['email'] = $email;

        if (preg_match('/^(\d{10})@student\.its\.ac\.id$/i', $email, $m)) {
            $this->attributes['nrp'] = $m[1];
        }
    }
}
