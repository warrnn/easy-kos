<?php

namespace Tests\Unit;

use App\Models\Review;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ReviewTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @test */
    public function get_cuplikan_memotong_teks_jika_terlalu_panjang()
    {
        $review = new Review();
        $review->isi = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Sint a ex repellendus et dolore. Eum expedita distinctio reiciendis illum iusto tempora modi doloremque voluptatem vitae dolore eveniet, a quia et?';

        $cuplikan = $review->getCuplikan(20);

        $this->assertEquals('Lorem ipsum dolor si...', $cuplikan);
    }

    /** @test */
    public function get_cuplikan_tidak_memotong_teks_pendek()
    {
        $review = new Review();
        $review->isi = 'Review singkat.';

        // Limit 50, tapi cuma 15 char. Harusnya tampil semua tanpa "..."
        $this->assertEquals('Review singkat.', $review->getCuplikan(50));
    }
}