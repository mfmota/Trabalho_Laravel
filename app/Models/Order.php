<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, HasUuids;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'table',
        'status',
        'draft',
        'name',
    ];

    /**
     * Converte os campos booleanos para o tipo bool do PHP.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'table' => 'integer',
        'status' => 'boolean',
        'draft' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}