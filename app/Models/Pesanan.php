<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'id_pengguna',
        'id_kamar',
        'status_pemesanan'
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'id_kamar');
    }

    /**
     * 1. Cek apakah pesanan masih menunggu konfirmasi.
     */
    public function isPending()
    {
        return strtolower($this->status_pemesanan) === 'pending';
    }

    /**
     * 2. Cek apakah pesanan sudah disetujui pemilik.
     */
    public function isDiterima()
    {
        return strtolower($this->status_pemesanan) === 'diterima';
    }

    /**
     * 3. Menentukan warna badge CSS berdasarkan status.
     * Logic ini sering dipakai di View (Blade).
     */
    public function getStatusBadgeColor()
    {
        return match (strtolower($this->status_pemesanan)) {
            'pending' => 'warning', // Kuning
            'diterima' => 'success', // Hijau
            'ditolak' => 'danger',  // Merah
            default => 'secondary', // Abu-abu
        };
    }

    /**
     * 4. Cek apakah user boleh membatalkan pesanan.
     * Aturan bisnis: Hanya boleh batal jika status masih 'pending'.
     */
    public function bisaDibatalkan()
    {
        return $this->isPending();
    }
}