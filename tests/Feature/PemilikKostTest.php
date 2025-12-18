<?php

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('Pemilik kos melakukan login', function () {
    $user = Pengguna::factory()->pemilik()->create([
        'username' => 'pemilik_kos',
        'password' => bcrypt('pemilik123'),
    ]);

    $response = post('/authentication/login', [
        'username' => 'pemilik_kos',
        'password' => 'pemilik123',
    ]);

    $response->assertRedirect(route('authentication.login'))
        ->assertSessionHas('success', 'Login berhasil!')
        ->assertSessionHas('redirectUrl', route('pemilik.index'));

    $this->assertAuthenticatedAs($user);
});

test('Pemilik kos dapat membuat kos baru', function () {
    // 1. Buat user pemilik
    $user = Pengguna::factory()->pemilik()->create();

    // 2. Data yang akan dikirim
    $kosData = [
        'name' => 'Kos Sejahtera',
        'alamat' => 'Jl. Mawar No. 123, Jakarta',
    ];

    // 3. Lakukan request POST sebagai user tersebut
    actingAs($user)
        ->post(route('kos.add-kos'), $kosData)
        ->assertRedirect(route('pemilik.index')) // Pastikan redirect benar
        ->assertSessionHas('success', 'Kos berhasil dibuat!'); // Pastikan pesan sukses muncul

    // 4. Verifikasi data masuk ke database
    $this->assertDatabaseHas('kos', [
        'name' => 'Kos Sejahtera',
        'alamat' => 'Jl. Mawar No. 123, Jakarta',
        'id_pengguna' => $user->id, // Pastikan ID pemilik tersimpan benar
    ]);
});

test('Pemilik kos gagal membuat kos jika form tidak valid', function () {
    // 1. Buat user pemilik
    $user = Pengguna::factory()->pemilik()->create();

    // 2. Kirim data kosong
    actingAs($user)
        ->post(route('kos.add-kos'), [
            'name' => '',
            'alamat' => '',
        ])
        ->assertSessionHasErrors(['name', 'alamat']); // Pastikan error validasi muncul di session

    // 3. Pastikan tidak ada data kosong yang masuk database
    $this->assertDatabaseMissing('kos', [
        'id_pengguna' => $user->id,
    ]);
});

test('Pemilik kos melihat data pemesanan penghuni', function () {
    $user = Pengguna::factory()->pemilik()->create([
        'username' => 'pemilik_kos',
        'password' => bcrypt('pemilik123'),
    ]);

    // Buat relasi data
    $kos = Kos::factory()->create([
        'id_pengguna' => $user->id,
    ]);

    $kamar = Kamar::factory()->create([
        'id_kos' => $kos->id,
    ]);

    $pesanan = Pesanan::factory()->create([
        'id_kamar' => $kamar->id,
        'id_pengguna' => $user->id,
    ]);

    actingAs($user)
        ->get(route('pemilik.request.index'))
        ->assertStatus(200)
        ->assertViewIs('pemilik_kos.request.index')
        ->assertViewHas('pesanan', function ($data) use ($pesanan) {
            return $data->contains($pesanan);
        });
});

test('Pemilik kos melihat halaman laporan dengan data miliknya', function () {
    // 1. Buat User Pemilik
    $user = Pengguna::factory()->pemilik()->create([
        'username' => 'pemilik_kos',
        'password' => bcrypt('pemilik123'),
    ]);

    // 2. Buat Data Milik User ini (Kos & Kamar)
    $kosMilikUser = Kos::factory()->create([
        'id_pengguna' => $user->id
    ]);

    $kamarMilikUser = Kamar::factory()->create([
        'id_kos' => $kosMilikUser->id
    ]);

    // 3. Jalankan Request & Validasi
    actingAs($user)
        ->get(route('pemilik.laporan.index'))
        ->assertStatus(200)
        ->assertViewIs('pemilik_kos.laporan.index')

        // Validasi Variable 'kos'
        ->assertViewHas('kos', function ($data) use ($user) {
            // Cek 1: Pastikan datanya tidak kosong
            if ($data->isEmpty())
                return false;

            // Cek 2: Pastikan SEMUA kos yang tampil punya id_pengguna == user yang login
            // Fungsi 'every' akan bernilai true jika semua item lolos kondisi
            return $data->every(fn($item) => $item->id_pengguna === $user->id);
        })

        // Validasi Variable 'listKamar'
        ->assertViewHas('listKamar', function ($data) use ($kosMilikUser) {
            if ($data->isEmpty())
                return false;

            // Cek: Pastikan SEMUA kamar yang tampil terhubung ke kos milik user
            return $data->every(fn($item) => $item->id_kos === $kosMilikUser->id);
        });
});

test('Pemilik kos dapat melihat halaman tambah kamar jika sudah memiliki kos', function () {
    // 1. Buat user pemilik
    $user = Pengguna::factory()->pemilik()->create();

    // 2. Buat kos milik user tersebut
    $kos = Kos::factory()->create([
        'id_pengguna' => $user->id
    ]);

    // 3. Jalankan request
    actingAs($user)
        ->get(route('pemilik.laporan.kamar.index'))
        ->assertStatus(200) // Pastikan sukses (OK)
        ->assertViewIs('pemilik_kos.laporan.addKamar.index') // Pastikan view benar
        ->assertViewHas('kos', function ($data) use ($kos) {
            // Pastikan data 'kos' yang dikirim ke view adalah milik user ini
            return $data->id === $kos->id;
        });
});

test('Pemilik kos dialihkan kembali jika belum memiliki kos saat akses tambah kamar', function () {
    // 1. Buat user pemilik TANPA kos
    $user = Pengguna::factory()->pemilik()->create();

    // 2. Simulasikan user datang dari halaman dashboard (untuk tes redirect back)
    $previousUrl = route('pemilik.index'); // Atau URL lain yang valid

    actingAs($user)
        ->from($previousUrl) // Set URL "sebelumnya"
        ->get(route('pemilik.laporan.kamar.index'))
        ->assertRedirect($previousUrl) // Pastikan dia dipulangkan ke halaman sebelumnya
        ->assertSessionHas('error', 'Kos tidak ditemukan'); // Cek pesan error
});

test('Pemilik kos dapat menyimpan data kamar baru dengan valid', function () {
    // 1. Buat User & Kos
    $user = Pengguna::factory()->pemilik()->create();
    $kos = Kos::factory()->create([
        'id_pengguna' => $user->id
    ]);

    // 2. Data Kamar yang akan dikirim
    $inputData = [
        'name' => 'Kamar Anggrek 01',
        'status' => 'Tersedia',
        'harga' => 1500000,
        'deskripsi' => 'Kamar mandi dalam dan AC',
    ];

    // 3. Eksekusi Request
    actingAs($user)
        ->post(route('pemilik.laporan.kamar.store', $kos->id), $inputData)
        ->assertRedirect() // Pastikan redirect back (302)
        ->assertSessionHas('success', 'Kamar berhasil disimpan!');

    // 4. Verifikasi Database
    $this->assertDatabaseHas('kamar', [
        'name' => 'Kamar Anggrek 01',
        'id_kos' => $kos->id, // Pastikan terhubung ke kos yang benar
        'harga' => 1500000,
    ]);
});

test('Pemilik kos gagal menyimpan kamar jika validasi error', function () {
    // 1. Buat User & Kos
    $user = Pengguna::factory()->pemilik()->create();
    $kos = Kos::factory()->create(['id_pengguna' => $user->id]);

    // 2. Kirim data kosong/tidak valid
    actingAs($user)
        ->post(route('pemilik.laporan.kamar.store', $kos->id), [
            'name' => '', // Kosong (Error)
            'harga' => 'bukan angka', // Bukan numeric (Error)
        ])
        ->assertSessionHasErrors(['name', 'harga', 'status', 'deskripsi']);

    // 3. Pastikan tidak ada data masuk ke database
    $this->assertDatabaseMissing('kamar', [
        'id_kos' => $kos->id,
    ]);
});

test('Pemilik kos dapat memperbarui status pesanan dan kamar otomatis menjadi booked', function () {
    // 1. Buat User Pemilik
    $user = Pengguna::factory()->pemilik()->create([
        'username' => 'pemilik_kos',
        'password' => bcrypt('pemilik123'),
    ]);
    
    // 2. Buat Kos & Kamar milik user tersebut
    $kos = Kos::factory()->create(['id_pengguna' => $user->id]);
    
    // Kamar awalnya 'tersedia'
    $kamar = Kamar::factory()->create([
        'id_kos' => $kos->id, 
        'status' => 'ready' 
    ]);

    // 3. Buat Pesanan (Status awal 'pending')
    $pesanan = Pesanan::factory()->create([
        'id_kamar' => $kamar->id,
        'id_pengguna' => $user->id
    ]);

    // 4. Jalankan Request Update
    actingAs($user)
        ->put(route('pemilik_kos.request.updateStatus', $pesanan->id), [
            'status' => 'Terima' // Input status baru
        ])
        ->assertRedirect(); // Pastikan redirect back

    // 5. Verifikasi Database
    
    // a. Cek status pesanan berubah jadi 'disetujui'
    $this->assertDatabaseHas('pesanan', [
        'id' => $pesanan->id,
        'status_pemesanan' => 'Terima'
    ]);

    // b. Cek status kamar berubah jadi 'booked' (Sesuai logika controller)
    $this->assertDatabaseHas('kamar', [
        'id' => $kamar->id,
        'status' => 'booked'
    ]);
});