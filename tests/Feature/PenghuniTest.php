<?php

use App\Http\Controllers\PenghuniController;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Models\Review;
use App\Models\Role; // Import Model Role
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

// --- 1. SETUP GLOBAL: SEEDING ROLE ---
// Ini solusi untuk error "Foreign Key constraint failed"
// Kita pastikan Role 1, 2, dan 3 selalu ada sebelum setiap test jalan.
beforeEach(function () {
    Role::factory()->create(['id' => 1, 'nama' => 'admin']);
    Role::factory()->create(['id' => 2, 'nama' => 'pemilik']);
    Role::factory()->create(['id' => 3, 'nama' => 'penghuni']);
});

// --- HELPER UNTUK USER ---
function createSpecialPenghuni()
{
    // Sekarang aman membuat user dengan id_role 3 karena Role ID 3 sudah dibuat di beforeEach
    $user = Pengguna::factory()->create(['id_role' => 3]);
    $user->username = (string) $user->id;
    $user->save();
    return $user;
}

afterEach(function () {
    Mockery::close();
});

test('penghuni bisa melihat daftar semua kos', function () {
    $user = createSpecialPenghuni();
    // Kita buat Kos lengkap dengan pemiliknya (role 2) agar tidak error foreign key juga
    $pemilik = Pengguna::factory()->create(['id_role' => 2]);
    Kos::factory()->count(3)->create(['id_pengguna' => $pemilik->id]);

    $response = $this->actingAs($user)
        ->get(route('penghuni.index'));

    $response->assertStatus(200)
        ->assertViewIs('penghuni.index')
        ->assertViewHas('listKos');
});

test('penghuni bisa melihat detail kamar dalam kos tertentu', function () {
    $user = createSpecialPenghuni();
    $pemilik = Pengguna::factory()->create(['id_role' => 2]);

    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);
    Kamar::factory()->count(2)->create(['id_kos' => $kos->id]);
    Review::factory()->create(['id_kos' => $kos->id, 'id_pengguna' => $user->id]);

    $response = $this->actingAs($user)
        ->get(action([PenghuniController::class, 'showAllKamar'], $kos->id));

    $response->assertStatus(200)
        ->assertViewIs('penghuni.kos.index')
        ->assertViewHas(['listKamar', 'kos', 'listReview']);
});

test('penghuni melihat 404 jika melihat kos yang tidak ada', function () {
    $user = createSpecialPenghuni();

    $response = $this->actingAs($user)
        ->get(action([PenghuniController::class, 'showAllKamar'], 99999));

    $response->assertNotFound();
});

// --- PERBAIKAN ERROR "NOT NULL constraint failed: kamar.id_kos" ---
test('pesan kamar redirect ke login jika user belum auth', function () {
    // Kita harus buat parent (Kos & Pemilik) dulu sebelum buat Kamar
    $pemilik = Pengguna::factory()->create(['id_role' => 2]);
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);

    // Explicitly set id_kos agar tidak error NOT NULL
    $kamar = Kamar::factory()->create(['id_kos' => $kos->id]);

    $response = $this->post(action([PenghuniController::class, 'pesanKamar'], $kamar->id));

    $response->assertRedirect('/')
        ->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
});

test('pesan kamar redirect ke homepage jika user belum login (guest)', function () {
    // 1. Buat data dummy untuk parameter route
    // Kita tetap butuh ID kamar untuk generate URL, meskipun user belum login
    // Pastikan Role & Parent dibuat agar tidak error Foreign Key
    $pemilik = Pengguna::factory()->create(['id_role' => 2]);
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);
    $kamar = Kamar::factory()->create(['id_kos' => $kos->id]);

    // 2. Lakukan POST request TANPA actingAs()
    // Ini mensimulasikan user tamu (Guest) yang mengakses endpoint
    $response = post(action([PenghuniController::class, 'pesanKamar'], $kamar->id));

    // 3. Assert Redirect ke '/' (Sesuai kodingan Anda: redirect('/'))
    $response->assertRedirect('/');
});

test('pesan kamar redirect back jika kamar tidak ditemukan', function () {
    $user = createSpecialPenghuni();

    $response = $this->actingAs($user)
        ->post(action([PenghuniController::class, 'pesanKamar'], 99999));

    $response->assertRedirect()
        ->assertSessionHas('error', 'Kamar tidak ditemukan!');
});

test('pesan kamar berhasil dan menghasilkan snap token midtrans', function () {
    $user = createSpecialPenghuni();
    $pemilik = Pengguna::factory()->create(['id_role' => 2]);
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);
    $kamar = Kamar::factory()->create(['id_kos' => $kos->id, 'harga' => 100000]);

    Config::set('midtrans.server_key', 'dummy-server-key');
    Config::set('midtrans.is_production', false);

    $mockSnap = Mockery::mock('alias:Midtrans\Snap');
    $mockSnap->shouldReceive('getSnapToken')
        ->once()
        ->andReturn('dummy-snap-token-123');

    $response = $this->actingAs($user)
        ->post(action([PenghuniController::class, 'pesanKamar'], $kamar->id));

    $response->assertStatus(200)
        ->assertViewIs('penghuni.kos.payment.index')
        ->assertViewHas('snapToken', 'dummy-snap-token-123');

    $this->assertDatabaseHas('pesanan', [
        'id_pengguna' => $user->id,
        'id_kamar' => $kamar->id,
        'status_pemesanan' => 'pending'
    ]);
});

test('pesan kamar menangani error exception dari midtrans', function () {
    $user = createSpecialPenghuni();
    $pemilik = Pengguna::factory()->create(['id_role' => 2]);
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);
    $kamar = Kamar::factory()->create(['id_kos' => $kos->id]);

    Config::set('midtrans.server_key', 'dummy-server-key');

    $mockSnap = Mockery::mock('alias:Midtrans\Snap');
    $mockSnap->shouldReceive('getSnapToken')
        ->andThrow(new \Exception('Koneksi Midtrans Gagal'));

    $response = $this->actingAs($user)
        ->post(action([PenghuniController::class, 'pesanKamar'], $kamar->id));

    $response->assertRedirect()
        ->assertSessionHas('error');
});

test('penghuni bisa melihat daftar pemesanan miliknya', function () {
    $user = createSpecialPenghuni();
    $pemilik = Pengguna::factory()->create(['id_role' => 2]);
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);
    $kamar = Kamar::factory()->create(['id_kos' => $kos->id]);

    $pesananMilikUser = Pesanan::factory()->create([
        'id_pengguna' => $user->id,
        'id_kamar' => $kamar->id
    ]);

    $otherUser = Pengguna::factory()->create(['id_role' => 3]); // Penghuni lain
    $pesananLain = Pesanan::factory()->create([
        'id_pengguna' => $otherUser->id,
        'id_kamar' => $kamar->id
    ]);

    $response = $this->actingAs($user)
        ->get(action([PenghuniController::class, 'showPemesanan']));

    $response->assertStatus(200)
        ->assertViewIs('penghuni.pemesanan.index')
        ->assertViewHas('listPesanan', function ($list) use ($pesananMilikUser, $pesananLain) {
            return $list->contains($pesananMilikUser) && !$list->contains($pesananLain);
        });
});

test('penghuni bisa membuka form review', function () {
    $user = createSpecialPenghuni();
    $pemilik = Pengguna::factory()->create(['id_role' => 2]);
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);

    $response = $this->actingAs($user)
        ->get(action([PenghuniController::class, 'formReview'], $kos->id));

    $response->assertStatus(200)
        ->assertViewIs('penghuni.review.index');
});

test('penghuni bisa menambahkan review', function () {
    $user = createSpecialPenghuni();
    $pemilik = Pengguna::factory()->create(['id_role' => 2]);
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);

    $reviewData = [
        'isi' => 'Tempatnya nyaman dan bersih.',
    ];

    $response = $this->actingAs($user)
        ->post(action([PenghuniController::class, 'addReview'], $kos->id), $reviewData);

    $response->assertRedirect(route('penghuni.index'))
        ->assertSessionHas('success', 'Review berhasil ditambahkan!');

    $this->assertDatabaseHas('review', [
        'isi' => 'Tempatnya nyaman dan bersih.',
        'id_pengguna' => $user->id,
        'id_kos' => $kos->id
    ]);
});

test('tambah review gagal jika validasi error', function () {
    $user = createSpecialPenghuni();
    $pemilik = Pengguna::factory()->create(['id_role' => 2]);
    $kos = Kos::factory()->create(['id_pengguna' => $pemilik->id]);

    $response = $this->actingAs($user)
        ->post(action([PenghuniController::class, 'addReview'], $kos->id), [
            'isi' => ''
        ]);

    $response->assertSessionHasErrors(['isi']);
    $this->assertDatabaseCount('review', 0);
});