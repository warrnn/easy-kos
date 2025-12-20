<?php

namespace Tests\Unit;

use App\Models\Pesanan;
use PHPUnit\Framework\TestCase;

class PesananTest extends TestCase
{
    /** @test */
    public function is_pending_mengembalikan_true_jika_status_pending()
    {
        $pesanan = new Pesanan();
        $pesanan->status_pemesanan = 'pending';

        $this->assertTrue($pesanan->isPending());
        $this->assertFalse($pesanan->isDiterima());
    }

    /** @test */
    public function is_diterima_mengembalikan_true_jika_status_diterima()
    {
        $pesanan = new Pesanan();
        $pesanan->status_pemesanan = 'diterima';

        $this->assertTrue($pesanan->isDiterima());
        $this->assertFalse($pesanan->isPending());
    }

    /** @test */
    public function logic_status_case_insensitive()
    {
        // Memastikan kalo 'PENDING' (huruf besar) tetap valid
        $pesanan = new Pesanan();
        $pesanan->status_pemesanan = 'PENDING';

        $this->assertTrue($pesanan->isPending());
    }

    /** @test */
    public function get_status_badge_color_memberikan_warna_yang_sesuai()
    {
        $pesanan = new Pesanan();

        // Cek Pending -> Warning
        $pesanan->status_pemesanan = 'pending';
        $this->assertEquals('warning', $pesanan->getStatusBadgeColor());

        // Cek Diterima -> Success
        $pesanan->status_pemesanan = 'diterima';
        $this->assertEquals('success', $pesanan->getStatusBadgeColor());

        // Cek Ditolak -> Danger
        $pesanan->status_pemesanan = 'ditolak';
        $this->assertEquals('danger', $pesanan->getStatusBadgeColor());

        // Cek Ngaco -> Secondary
        $pesanan->status_pemesanan = 'unknown_status';
        $this->assertEquals('secondary', $pesanan->getStatusBadgeColor());
    }

    /** @test */
    public function bisa_dibatalkan_hanya_jika_status_pending()
    {
        $pesanan = new Pesanan();

        // Jika pending, harusnya BISA dibatalkan
        $pesanan->status_pemesanan = 'pending';
        $this->assertTrue($pesanan->bisaDibatalkan());

        // Jika diterima, GABISA dibatalkan
        $pesanan->status_pemesanan = 'diterima';
        $this->assertFalse($pesanan->bisaDibatalkan());

        // Jika ditolak, GABISA dibatalkan
        $pesanan->status_pemesanan = 'ditolak';
        $this->assertFalse($pesanan->bisaDibatalkan());
    }
}