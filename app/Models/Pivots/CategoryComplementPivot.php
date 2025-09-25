<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryComplementPivot extends Pivot
{
    protected $table = 'category_complement_complement';

    public $incrementing = false; // clave compuesta, no autoincremental
    public $timestamps = true;

    protected $fillable = [
        'complement_id',
        'category_complement_id',
    ];
}
