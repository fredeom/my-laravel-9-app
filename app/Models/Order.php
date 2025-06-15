<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const NEW = 'Новый';
    const DONE = 'Выполнен';

    public $timestamps = false;

    protected $fillable = [
        "fio",
        "status",
        "amount",
        "comment",
        "product_id",
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
