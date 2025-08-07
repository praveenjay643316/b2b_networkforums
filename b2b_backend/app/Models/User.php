<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // ✅ Correct Base Class
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable // ✅ Change this!
{
    use HasApiTokens, Notifiable;

    // ✅ Set custom table and primary key
    protected $table = 't_user';
    protected $primaryKey = 'profile_id';

    // ✅ Fillable columns
    protected $fillable = [
        'first_name',
        'last_name',
        'personal_mobile',
        'personal_email',
        'job_title',
        'business_mobile_number',
        'user_name',
        'password',
        'active',
        'user_type'
    ];

    // ✅ If the table has no created_at / updated_at fields
    public $timestamps = false;

    // ✅ Relationship example
    public function company()
    {
        return $this->hasOne(Company::class, 'profile_id', 'profile_id');
    }

    // ✅ Password mutator (optional for safety)
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }
}