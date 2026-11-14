<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'email',
        'service',
        'project_details',
        'ip_address',
        'user_agent',
        'recaptcha_score',
        'mail_sent',
    ];

    protected $casts = [
        'mail_sent' => 'boolean',
        'recaptcha_score' => 'decimal:2',
    ];
}
