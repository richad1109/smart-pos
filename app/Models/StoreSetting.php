<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'phone',
        'email',
        'address',
        'tax_percentage',
        'receipt_footer',
    ];

    protected function casts(): array
    {
        return [
            'tax_percentage' => 'decimal:2',
        ];
    }
}
