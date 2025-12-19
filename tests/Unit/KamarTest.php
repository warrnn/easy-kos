<?php

namespace Tests\Unit;

use App\Models\Kamar;
use PHPUnit\Framework\TestCase; // Gunakan TestCase murni PHPUnit (Tanpa DB Laravel)

class KamarTest extends TestCase
{
    /** @test */
    public function hitung_total_harga_mengkalkulasi_dengan_benar()
    {
        // 1. Arrange
        $kamar = new Kamar();
        $kamar->harga = 500000;

        // 2. Act
        $total = $kamar->hitungTotalHarga(3); // 3 bulan

        // 3. Assert
        $this->assertEquals(1500000, $total);
    }

    /** @test */
    public function format_harga_menghasilkan_string_rupiah_yang_valid()
    {
        // 1. Arrange
        $kamar = new Kamar();
        $kamar->harga = 1000000;

        // 2. Act
        $formatted = $kamar->formatHarga();

        // 3. Assert
        $this->assertEquals('Rp 1.000.000', $formatted);
    }

    /** @test */
    public function is_tersedia_mengembalikan_true_jika_status_tersedia()
    {
        // 1. Arrange
        $kamar = new Kamar();
        $kamar->status = 'tersedia';

        // 2. Act & Assert
        $this->assertTrue($kamar->isTersedia());
    }

    /** @test */
    public function is_tersedia_mengembalikan_false_jika_status_booked()
    {
        // 1. Arrange
        $kamar = new Kamar();
        $kamar->status = 'booked';

        // 2. Act & Assert
        $this->assertFalse($kamar->isTersedia());
    }

    /** @test */
    public function hitung_harga_setelah_diskon_mengkalkulasi_dengan_benar()
    {
        // 1. Arrange
        $kamar = new Kamar();
        $kamar->harga = 200000;

        // 2. Act
        // Diskon 10% dari 200.000 = 20.000. Sisa 180.000
        $hargaDiskon = $kamar->hitungHargaSetelahDiskon(10);

        // 3. Assert
        $this->assertEquals(180000, $hargaDiskon);
    }

    /** @test */
    public function hitung_harga_setelah_diskon_mengabaikan_persen_invalid()
    {
        $kamar = new Kamar();
        $kamar->harga = 100000;

        // Diskon 150% itu tidak masuk akal, fungsi harus return harga asli
        $this->assertEquals(100000, $kamar->hitungHargaSetelahDiskon(150));
    }
}