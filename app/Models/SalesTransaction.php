<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SalesTransaction extends Model
{
    use HasFactory;

    protected $table = 'sales_transactions';

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'delivery_date',
        'product_id',
        'sales_id',
        'supplier_id',
        'quantity_carton',
        'quantity_piece',
        'total_quantity_piece',
        'unit_price',
        'total_amount',
        'approval_status',
        'notes',
        'general_notes',
        'order_acc_by',
        'po_number',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'delivery_date' => 'date',
        'quantity_carton' => 'integer',
        'quantity_piece' => 'integer',
        'total_quantity_piece' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Method untuk menghitung total quantity piece
    public function calculateTotalQuantityPiece()
    {
        return ($this->quantity_carton * $this->product->quantity_per_carton) + $this->quantity_piece;
    }

    // Method untuk menghitung total amount
    public function calculateTotalAmount()
    {
        return $this->total_quantity_piece * $this->unit_price;
    }

    // Method untuk approval
    public function approve($userId, $notes = null)
    {
        $this->update([
            'approval_status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    // Method untuk reject
    public function reject($userId, $notes = null)
    {
        $this->update([
            'approval_status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    // Method untuk check status
    public function isPending()
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected()
    {
        return $this->approval_status === 'rejected';
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];

        $statusLabels = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        return [
            'class' => $statuses[$this->approval_status] ?? 'bg-gray-100 text-gray-800',
            'label' => $statusLabels[$this->approval_status] ?? ucfirst($this->approval_status),
        ];
    }

    // Boot method untuk auto calculate dan generate transaction number
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($salesTransaction) {
            // Generate transaction number jika belum ada
            if (empty($salesTransaction->transaction_number)) {
                $salesTransaction->transaction_number = 'ST-' . date('Ymd') . '-' . Str::upper(Str::random(6));
            }

            // Generate PO number jika belum ada
            if (empty($salesTransaction->po_number)) {
                $salesTransaction->po_number = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);
            }

            // Auto calculate total quantity piece
            if ($salesTransaction->product && $salesTransaction->quantity_carton !== null && $salesTransaction->quantity_piece !== null) {
                $salesTransaction->total_quantity_piece = $salesTransaction->calculateTotalQuantityPiece();
            }

            // Auto calculate total amount
            if ($salesTransaction->total_quantity_piece !== null && $salesTransaction->unit_price !== null) {
                $salesTransaction->total_amount = $salesTransaction->calculateTotalAmount();
            }
        });
    }
}
