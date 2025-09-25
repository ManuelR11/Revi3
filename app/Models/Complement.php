<?php

namespace App\Models;

use App\Models\Pivots\ComplementItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'complements';

    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
    ];

    protected $casts = [
        'id'          => 'integer',
        'name'        => 'string',
        'description' => 'string',
        'price'       => 'decimal:6',
        'status'      => 'integer',
    ];

    protected $dates = ['deleted_at'];

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'complement_item')
            ->using(ComplementItem::class)
            ->withTimestamps();
    }
}