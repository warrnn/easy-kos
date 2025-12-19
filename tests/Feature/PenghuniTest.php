<?php

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Models\Review;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

function createPenghuni() {
    return Pengguna::factory()->create([
        'username' => 'yesto',
        'password' => bcrypt('password123'),
    ]);
};

//
// Test function showAllKos()
//
test('Penghuni dapat melihat daftar kos', function() {
    $user = createPenghuni();

    Role::factory()->pemilik()->create();
    Kos::factory()->count(3)->create();

    actingAs($user)
        ->get(route('penghuni.index'))
        ->assertStatus(200)
        ->assertViewIs('penghuni.index')
        ->assertViewHas('listKos', function ($listKos) {
            return $listKos->count() === 3;
        });
});


//
// Test function showAllKamar ()
//
test('Penghuni dapat melihat list kamar dan review penghuni terhadap kos yang dipilih', function () {
    $user = createPenghuni();

    $pemilik = Pengguna::factory()->pemilik()->create([
        'username' => 'juragan_kos',
    ]);
    
    // data kos yang dicek
    $kos = Kos::factory()->create([
        'name' => 'Kos Target Testing',
        'id_pengguna' => $pemilik->id
    ]);
    
    Kamar::factory()->count(2)->create([
        'id_kos' => $kos->id
    ]);

    Review::factory()->create([
        'isi' => 'Kos ini mantap', 
        'id_kos' => $kos->id,
        'id_pengguna' => $user->id
    ]);

    actingAs($user)
        ->get(route('penghuni.kos.index', $kos->id))
        ->assertStatus(200)
        ->assertViewIs('penghuni.kos.index')
        ->assertViewHas('listKos')
        ->assertViewHas('listKamar', function($list) {
            return $list->count() === 2;
        })
        ->assertViewHas('listReview', function($list) {
            return $list->first()->isi === 'Kos ini mantap';
        });
});

//
// Test function show
//
test('Penghuni dapat melihat detail kos yang dipilih', function () {
    $user = createPenghuni();

    $pemilik = Pengguna::factory()->pemilik()->create([
        'username' => 'juragan_kos',
    ]);
    
    // data kos yang dicek
    $kos = Kos::factory()->create([
        'name' => 'Kos Target Testing',
        'alamat' => 'Jl. Siwalankerto',
        'id_pengguna' => $pemilik->id
    ]);

    actingAs($user)
        ->get(route('kos.show', $kos->id))
        ->assertStatus(200)
        ->assertJson([
            'name' => 'Kos Target Testing',
            'alamat' => 'Jl. Siwalankerto',
        ]);
});


//
// Test function pesanKamar
//
// test('Penghuni dapat memesan kamar pada kos yang dipilih', function () {
//     $user = createPenghuni();


// });


//
// Test function showPemesanan
//
test('Penghuni dapat melihat riwayat pemesanan kamar yang masih pending', function () {
    $user = createPenghuni();

    $pemilik = Pengguna::factory()->pemilik()->create();
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);
    $kamar = Kamar::factory()->create(['id_kos' => $kos->id]);

    Pesanan::factory()->create([
        'id_pengguna' => $user->id,
        'id_kamar' => $kamar->id,
        'status_pemesanan' => 'pending'
    ]);

    actingAs($user)
        ->get(route('penghuni.pemesanan.index'))
        ->assertStatus(200)
        ->assertViewIs('penghuni.pemesanan.index')
        ->assertViewHas('listPesanan', function($list) {
            if ($list->count() !== 1) return false;
            return $list->first()->status_pemesanan === 'pending';
        });
});

test('Penghuni dapat melihat riwayat pemesanan kamar yang sudah diterima', function () {
    $user = createPenghuni();

    $pemilik = Pengguna::factory()->pemilik()->create();
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);
    $kamar = Kamar::factory()->create(['id_kos' => $kos->id]);

    Pesanan::factory()->create([
        'id_pengguna' => $user->id,
        'id_kamar' => $kamar->id,
        'status_pemesanan' => 'booked'
    ]);

    actingAs($user)
        ->get(route('penghuni.pemesanan.index'))
        ->assertStatus(200)
        ->assertViewIs('penghuni.pemesanan.index')
        ->assertViewHas('listPesanan', function($list) {
            if ($list->count() !== 1) return false;
            return $list->first()->status_pemesanan === 'booked';
        });
});


//
// Test function formReview
//
test('Penghuni dapat mengakses form review kos', function () {
    $user = createPenghuni();
    
    $pemilik = Pengguna::factory()->pemilik()->create();
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);

    actingAs($user)
        ->get(route('penghuni.review', $kos->id))
        ->assertStatus(200)
        ->assertViewIs('penghuni.review.index')
        ->assertViewHas('kos', function($viewKos) use ($kos) {
            return $viewKos->id === $kos->id;
        });
});


//
// Test function addReview
//
test('Penghuni dapat menambahkan review pada kamar kos yang dipesan', function () {
    $user = createPenghuni();

    $pemilik = Pengguna::factory()->pemilik()->create();
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);

    $reviewData = [
        'isi' => 'Tempatnya bersih dan nyaman.',
    ];

    actingAs($user)
        ->post(route('penghuni.review', $kos->id), $reviewData)
        ->assertRedirect(route('penghuni.index'))
        ->assertSessionHas('success', 'Review berhasil ditambahkan!');

    $this->assertDatabaseHas('review', [
        'id_pengguna' => $user->id,
        'id_kos' => $kos->id,
        'isi' => 'Tempatnya bersih dan nyaman.',
    ]);
});