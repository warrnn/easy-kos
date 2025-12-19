<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamar';

    protected $fillable = [
        'name',
        'status', // Contoh value: 'tersedia', 'booked', 'penuh'
        'harga',
        'deskripsi',
        'id_kos'
    ];

    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class, 'id_kos');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    /**
     * 1. Menghitung total harga berdasarkan durasi bulan.
     */
    public function hitungTotalHarga($durasiBulan)
    {
        return $this->harga * $durasiBulan;
    }

    /**
     * 2. Menformat harga ke format Rupiah string.
     * Contoh: 500000 -> "Rp 500.000"
     */
    public function formatHarga()
    {
        return "Rp " . number_format($this->harga, 0, ',', '.');
    }

    /**
     * 3. Cek apakah kamar tersedia.
     * Mengembalikan true/false (Boolean).
     */
    public function isTersedia()
    {
        // Asumsi string di database kecil semua, kita buat case-insensitive biar aman
        return strtolower($this->status) === 'tersedia';
    }

    /**
     * 4. Menghitung harga setelah diskon (misal promo).
     * @param int $persenDiskon (Contoh: 10 untuk 10%)
     */
    public function hitungHargaSetelahDiskon($persenDiskon)
    {
        if ($persenDiskon < 0 || $persenDiskon > 100) {
            return $this->harga; // Validasi sederhana: jika persen ngaco, return harga asli
        }

        $potongan = $this->harga * ($persenDiskon / 100);
        return $this->harga - $potongan;
    }
}