<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
        'sku',
        'category',
        'sub_category',
        'price',
        'quantity_per_carton',
        'stock_unit',
        'stock_quantity',
        'pieces_per_carton',
        'description',
        'is_active',
    ];

    protected $appends = ['stock_status', 'formatted_price'];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity_per_carton' => 'integer',
        'stock_quantity' => 'decimal:2',
        'pieces_per_carton' => 'integer',
        'is_active' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function salesTransactions()
    {
        return $this->hasMany(SalesTransaction::class);
    }

    // Scope untuk produk aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Method untuk menghitung total penjualan
    public function getTotalSalesAttribute()
    {
        return $this->salesTransactions()->sum('total_amount');
    }

    // Method untuk menghitung total quantity terjual
    public function getTotalQuantitySoldAttribute()
    {
        return $this->salesTransactions()->sum('total_quantity_piece');
    }

    // Method untuk format harga
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // Method untuk mendapatkan nama yang aman untuk form
    public function getSafeNameAttribute()
    {
        return $this->name;
    }

    // Method untuk status stok
    public function getStockStatusAttribute()
    {
        $stock = $this->quantity_per_carton ?? 0;
        
        if ($stock <= 0) {
            return [
                'label' => 'Habis',
                'class' => 'bg-red-100 text-red-700'
            ];
        } elseif ($stock <= 10) {
            return [
                'label' => 'Sedikit',
                'class' => 'bg-yellow-100 text-yellow-700'
            ];
        } else {
            return [
                'label' => 'Tersedia',
                'class' => 'bg-green-100 text-green-700'
            ];
        }
    }

    // Method untuk mendapatkan stok dalam pcs (alias untuk quantity_per_carton)
    public function getStokAttribute()
    {
        return $this->quantity_per_carton ?? 0;
    }

    // Method untuk nama barang (alias untuk name)
    public function getNamaAttribute()
    {
        return $this->name;
    }

    // Method untuk kategori (alias untuk category)
    public function getKategoriAttribute()
    {
        return $this->category;
    }

    // Method untuk harga (alias untuk price)
    public function getHargaAttribute()
    {
        return $this->price;
    }

    // Method untuk deskripsi (alias untuk description)
    public function getDeskripsiAttribute()
    {
        return $this->description;
    }
}
