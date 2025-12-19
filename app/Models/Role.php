<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;
    protected $table = 'role';

    protected $fillable = [
        'id',
        'nama',
    ];

    public function pengguna(): HasMany {
        return $this->hasMany(Pengguna::class, 'id_role');
    }
}
