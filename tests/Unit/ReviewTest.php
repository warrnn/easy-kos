<?php

namespace Tests\Unit;

use App\Models\Review;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ReviewTest extends TestCase
{
    /**
     * Reset waktu setelah setiap test selesai (Penting!)
     */
    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Kembalikan waktu ke normal
        parent::tearDown();
    }

    /** @test */
    public function get_cuplikan_memotong_teks_jika_terlalu_panjang()
    {
        // 1. Arrange
        $review = new Review();
        $review->isi = 'Ini adalah review yang sangat panjang sekali dan melebihi batas limit karakter yang ditentukan.'; // > 50 chars

        // 2. Act
        $cuplikan = $review->getCuplikan(20); // Limit cuma 20 huruf

        // 3. Assert
        // Harapannya: 20 huruf pertama + "..."
        // "Ini adalah review ya..."
        $this->assertEquals('Ini adalah review ya...', $cuplikan);
    }

    /** @test */
    public function get_cuplikan_tidak_memotong_teks_pendek()
    {
        $review = new Review();
        $review->isi = 'Review singkat.';

        // Limit 50, tapi teks cuma 15. Harusnya tampil semua tanpa "..."
        $this->assertEquals('Review singkat.', $review->getCuplikan(50));
    }
}