<?php

namespace App\Models;

use App\Models\Pivots\CategoryComplementPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryComplement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'category_complements';

    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'id'          => 'integer',
        'name'        => 'string',
        'description' => 'string',
    ];

    protected $dates = ['deleted_at'];

    public function complements(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Complement::class, 'category_complement_complement')
            ->using(CategoryComplementPivot::class)
            ->withTimestamps();
    }
}
