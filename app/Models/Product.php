<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = 'tb_product';

    protected $primaryKey = 'product_id';

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'product_name',
        'product_price',
        'product_description',
        'product_image',
        'product_status',
    ];

    protected function casts(): array
    {
        return [
            'product_price' => 'decimal:2',
            'product_status' => 'integer',
            'date_created' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}
