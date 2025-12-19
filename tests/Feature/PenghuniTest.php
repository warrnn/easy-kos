<?php

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pengguna;
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
test('Penghuni dapat melihat daftar kamar pada kos yang dipilih', function () {
    $user = createPenghuni();

    $pemilik = Pengguna::factory()->pemilik()->create([
        'username' => 'juragan_kos',
    ]);
    
    // Kos pertama
    $kos = Kos::factory()->create([
        'name' => 'Kos Target Testing',
        'id_pengguna' => $pemilik->id
    ]);
    
    Kamar::factory()->count(2)->create([
        'id_kos' => $kos->id
    ]);

    Review::factory()->create([
        'isi' => 'Kos mantap',
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
            return $list->first()->isi === 'Kos mantap';
        });
});