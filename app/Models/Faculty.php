<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'id_number',
        'first_name',
        'middle_initial',
        'last_name',
        'name',
        'position',
        'gender',
        'blood_type',
        'address',
        'emergency_contact_address',
        'emergency_contact_name',
        'emergency_contact_number',
        'civil_status',
        'gsis_number',
        'sss_number',
        'tin_number',
        'birthday',
        'photo_path',
        'id_path',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];
}
