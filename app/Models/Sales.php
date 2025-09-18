<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function salesTransactions()
    {
        return $this->hasMany(SalesTransaction::class);
    }

    // Scope untuk sales aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Method untuk menghitung total transaksi
    public function getTotalTransactionsAttribute()
    {
        return $this->salesTransactions()->count();
    }

    // Method untuk menghitung total amount
    public function getTotalAmountAttribute()
    {
        return $this->salesTransactions()->sum('total_amount');
    }
}
