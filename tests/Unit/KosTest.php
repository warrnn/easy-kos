<?php

namespace Tests\Unit;

use App\Models\Kos;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class KosTest extends TestCase
{
    /** @test */
    public function get_slug_mengubah_nama_menjadi_url_friendly()
    {
        $kos = new Kos();
        $kos->name = 'Kos Mawar Indah 2024';

        $slug = $kos->getSlug();

        $this->assertEquals('kos-mawar-indah-2024', $slug);
    }

    /** @test */
    public function is_owned_by_mengembalikan_true_jika_user_id_cocok()
    {
        $kos = new Kos();
        $kos->id_pengguna = 5;

        $this->assertTrue($kos->isOwnedBy(5)); // Harusnya True
        $this->assertFalse($kos->isOwnedBy(99)); // Harusnya False
    }

    /** @test */
    public function get_location_label_menggabungkan_nama_dan_alamat()
    {
        $kos = new Kos();
        $kos->name = 'Kos Putra';
        $kos->alamat = 'Jl. Kenanga';

        $label = $kos->getLocationLabel();

        $this->assertEquals('Kos Putra (Jl. Kenanga)', $label);
    }

    /** @test */
    public function hitung_rata_rata_rating_mengkalkulasi_collection_dengan_benar()
    {
        $kos = new Kos();

        // Bikin data dummy collection (pura pura data dari DB)
        // Objek anonim rating
        $reviews = new Collection([
            (object) ['rating' => 5],
            (object) ['rating' => 3],
            (object) ['rating' => 4],
        ]);

        // (5 + 3 + 4) / 3 = 12 / 3 = 4.0
        $avg = $kos->hitungRataRataRating($reviews);

        $this->assertEquals(4.0, $avg);
    }

    /** @test */
    public function hitung_rata_rata_rating_return_0_jika_tidak_ada_review()
    {
        $kos = new Kos();
        $reviews = new Collection([]);

        $this->assertEquals(0, $kos->hitungRataRataRating($reviews));
    }
}