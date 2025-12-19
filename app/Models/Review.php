<?php

namespace App\Models;

use Carbon\Carbon; // Import Library Tanggal
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str; // Import Helper String

class Review extends Model
{
    use HasFactory;

    protected $table = 'review';

    protected $fillable = ['isi', 'tanggal_review', 'id_pengguna', 'id_kos'];

    protected $casts = [
        'tanggal_review' => 'datetime',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class, 'id_kos');
    }

    /**
     * 1. Mengambil cuplikan isi review (Excerpt).
     * Jika review panjang > 50 karakter, potong dan tambah "..."
     * Berguna untuk preview di halaman dashboard.
     */
    public function getCuplikan($limit = 50)
    {
        return Str::limit($this->isi, $limit, '...');
    }
}