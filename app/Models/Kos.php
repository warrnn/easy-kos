<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Kos extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alamat',
        'id_pengguna',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function kamar(): HasMany
    {
        return $this->hasMany(Kamar::class, 'id_kos');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_kos');
    }

    /**
     * 1. Membuat Slug URL dari nama Kos.
     * Contoh: "Kos Mawar Indah" -> "kos-mawar-indah"
     * Berguna untuk SEO friendly URL.
     */
    public function getSlug()
    {
        // Mengubah spasi jadi dash dan huruf kecil
        return strtolower(str_replace(' ', '-', $this->name));
    }

    /**
     * 2. Mengecek apakah user tertentu adalah pemilik kos ini.
     * Mengembalikan true/false.
     * @param int $userId
     */
    public function isOwnedBy($userId)
    {
        return $this->id_pengguna == $userId;
    }

    /**
     * 3. Membuat label lokasi singkat.
     * Contoh: "Kos Melati (Jl. Sudirman)"
     */
    public function getLocationLabel()
    {
        return "{$this->name} ({$this->alamat})";
    }

    /**
     * 4. Menghitung rata-rata rating secara manual (jika data review dikirim sebagai array/collection).
     * Ini cara test logic tanpa query DB relationship.
     */
    public function hitungRataRataRating($kumpulanReview)
    {
        if ($kumpulanReview->isEmpty()) {
            return 0;
        }

        // Asumsi $kumpulanReview adalah Collection yang punya properti 'rating'
        return round($kumpulanReview->avg('rating'), 1);
    }
}