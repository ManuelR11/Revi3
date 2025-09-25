<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ComplementItem extends Pivot
{
    protected $table = 'complement_item';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
        'item_id',
        'complement_id',
    ];
}