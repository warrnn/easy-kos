<?php

namespace Tests\Unit;

use App\Models\Kamar;
use PHPUnit\Framework\TestCase; // Gunakan TestCase murni PHPUnit (Tanpa DB Laravel)

class KamarTest extends TestCase
{
    /** @test */
    public function hitung_total_harga_mengkalkulasi_dengan_benar()
    {
        $kamar = new Kamar();
        $kamar->harga = 500000;

        $total = $kamar->hitungTotalHarga(3); // 3 bulan

        $this->assertEquals(1500000, $total);
    }

    /** @test */
    public function format_harga_menghasilkan_string_rupiah_yang_valid()
    {
        $kamar = new Kamar();
        $kamar->harga = 1000000;

        $formatted = $kamar->formatHarga();

        $this->assertEquals('Rp 1.000.000', $formatted);
    }

    /** @test */
    public function is_tersedia_mengembalikan_true_jika_status_tersedia()
    {
        $kamar = new Kamar();
        $kamar->status = 'tersedia';

        $this->assertTrue($kamar->isTersedia());
    }

    /** @test */
    public function is_tersedia_mengembalikan_false_jika_status_booked()
    {
        $kamar = new Kamar();
        $kamar->status = 'booked';

        $this->assertFalse($kamar->isTersedia());
    }

    /** @test */
    public function hitung_harga_setelah_diskon_mengkalkulasi_dengan_benar()
    {
        $kamar = new Kamar();
        $kamar->harga = 200000;

        // Diskon 10% dari 200.000 = 20.000. Sisa 180.000
        $hargaDiskon = $kamar->hitungHargaSetelahDiskon(10);

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