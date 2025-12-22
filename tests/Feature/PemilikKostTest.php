<?php

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\get;

// Pemilik kos melakukan login
// Pemilik kos melihat daftar kos miliknya di dashboard
// Pemilik kos melihat dashboard kosong jika belum memiliki kos
// Pemilik kos dapat membuat kos baru
// Pemilik kos gagal membuat kos jika form tidak valid
// Pemilik kos melihat data pemesanan penghuni
// Pemilik kos melihat halaman laporan dengan data miliknya
// Pemilik kos dapat melihat halaman tambah kamar jika sudah memiliki kos
// Pemilik kos dialihkan kembali jika belum memiliki kos saat akses tambah kamar
// Pemilik kos dapat menyimpan data kamar baru dengan valid
// Pemilik kos gagal menyimpan kamar jika validasi error
// Pemilik kos dapat memperbarui status pesanan dan kamar otomatis menjadi booked


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

test('Pemilik kos melihat daftar kos miliknya di dashboard', function () {
    // Create & Pastiin Role ID 2 ada
    $rolePemilik = Role::firstOrCreate(
        ['id' => 2],
        ['nama' => 'pemilik']
    );

    $user = Pengguna::factory()
        ->recycle($rolePemilik)
        ->create();

    $kosMilikUser = Kos::factory()->count(2)->create([
        'id_pengguna' => $user->id
    ]);

    // Buat User lain
    $otherUser = Pengguna::factory()
        ->recycle($rolePemilik)
        ->create();

    $kosOrangLain = Kos::factory()->create([
        'id_pengguna' => $otherUser->id
    ]);

    actingAs($user)
        ->get(route('pemilik.index'))
        ->assertStatus(200)
        ->assertViewIs('pemilik_kos.index')
        ->assertViewHas('kos', function ($data) use ($kosMilikUser, $kosOrangLain) {
            // Validasi jumlah
            if ($data->count() !== 2) {
                return false;
            }

            // Validasi punya sendiri HARUS ada
            if (!$data->contains('id', $kosMilikUser->first()->id)) {
                return false;
            }

            // Validasi punya orang lain JANGAN sampai ada
            if ($data->contains('id', $kosOrangLain->id)) {
                return false;
            }

            return true;
        })
        ->assertViewHas('jumlahKos', 2);
});

test('Pemilik kos melihat dashboard kosong jika belum memiliki kos', function () {
    // Buat User Pemilik baru (tanpa data kos)
    $user = Pengguna::factory()->pemilik()->create();

    // Jalankan Request
    actingAs($user)
        ->get(route('pemilik.index'))
        ->assertStatus(200)
        ->assertViewIs('pemilik_kos.index')

        // Validasi 'kos' harus kosong
        ->assertViewHas('kos', function ($data) {
            return $data->isEmpty();
        })

        // Validasi 'jumlahKos' harus 0
        ->assertViewHas('jumlahKos', 0);
});

test('Pemilik kos dapat membuat kos baru', function () {
    // Buat user pemilik
    $user = Pengguna::factory()->pemilik()->create();

    // Data yang akan dikirim
    $kosData = [
        'name' => 'Kos Sejahtera',
        'alamat' => 'Jl. Mawar No. 123, Jakarta',
    ];

    // Lakukan request POST sebagai user tersebut
    actingAs($user)
        ->post(route('kos.add-kos'), $kosData)
        ->assertRedirect(route('pemilik.index')) // Pastikan redirect benar
        ->assertSessionHas('success', 'Kos berhasil dibuat!'); // Pastikan pesan sukses muncul

    // Verifikasi data masuk ke database
    $this->assertDatabaseHas('kos', [
        'name' => 'Kos Sejahtera',
        'alamat' => 'Jl. Mawar No. 123, Jakarta',
        'id_pengguna' => $user->id, // Pastikan ID pemilik tersimpan benar
    ]);
});

test('Pemilik kos gagal membuat kos jika form tidak valid', function () {
    // Buat user pemilik
    $user = Pengguna::factory()->pemilik()->create();

    // Kirim data kosong
    actingAs($user)
        ->post(route('kos.add-kos'), [
            'name' => '',
            'alamat' => '',
        ])
        ->assertSessionHasErrors(['name', 'alamat']); // Pastikan error validasi muncul di session

    // Pastikan tidak ada data kosong yang masuk database
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
    // Buat User Pemilik
    $user = Pengguna::factory()->pemilik()->create([
        'username' => 'pemilik_kos',
        'password' => bcrypt('pemilik123'),
    ]);

    // Buat Data Milik User ini (Kos & Kamar)
    $kosMilikUser = Kos::factory()->create([
        'id_pengguna' => $user->id
    ]);

    $kamarMilikUser = Kamar::factory()->create([
        'id_kos' => $kosMilikUser->id
    ]);

    // Jalankan Request & Validasi
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
    // Buat user pemilik
    $user = Pengguna::factory()->pemilik()->create();

    // Buat kos milik user tersebut
    $kos = Kos::factory()->create([
        'id_pengguna' => $user->id
    ]);

    // Jalankan request
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
    // Buat user pemilik TANPA kos
    $user = Pengguna::factory()->pemilik()->create();

    // Simulasikan user datang dari halaman dashboard (untuk tes redirect back)
    $previousUrl = route('pemilik.index'); // Atau URL lain yang valid

    actingAs($user)
        ->from($previousUrl) // Set URL "sebelumnya"
        ->get(route('pemilik.laporan.kamar.index'))
        ->assertRedirect($previousUrl) // Pastikan dia dipulangkan ke halaman sebelumnya
        ->assertSessionHas('error', 'Kos tidak ditemukan'); // Cek pesan error
});

test('Pemilik kos dapat menyimpan data kamar baru dengan valid', function () {
    // Buat User & Kos
    $user = Pengguna::factory()->pemilik()->create();
    $kos = Kos::factory()->create([
        'id_pengguna' => $user->id
    ]);

    // Data Kamar yang akan dikirim
    $inputData = [
        'name' => 'Kamar Anggrek 01',
        'status' => 'Tersedia',
        'harga' => 1500000,
        'deskripsi' => 'Kamar mandi dalam dan AC',
    ];

    // Eksekusi Request
    actingAs($user)
        ->post(route('pemilik.laporan.kamar.store', $kos->id), $inputData)
        ->assertRedirect() // Pastikan redirect back (302)
        ->assertSessionHas('success', 'Kamar berhasil disimpan!');

    // Verifikasi Database
    $this->assertDatabaseHas('kamar', [
        'name' => 'Kamar Anggrek 01',
        'id_kos' => $kos->id, // Pastikan terhubung ke kos yang benar
        'harga' => 1500000,
    ]);
});

test('Pemilik kos gagal menyimpan kamar jika validasi error', function () {
    // Buat User & Kos
    $user = Pengguna::factory()->pemilik()->create();
    $kos = Kos::factory()->create(['id_pengguna' => $user->id]);

    // Kirim data kosong/tidak valid
    actingAs($user)
        ->post(route('pemilik.laporan.kamar.store', $kos->id), [
            'name' => '', // Kosong (Error)
            'harga' => 'bukan angka', // Bukan numeric (Error)
        ])
        ->assertSessionHasErrors(['name', 'harga', 'status', 'deskripsi']);

    // Pastikan tidak ada data masuk ke database
    $this->assertDatabaseMissing('kamar', [
        'id_kos' => $kos->id,
    ]);
});

test('Pemilik kos dapat memperbarui status pesanan dan kamar otomatis menjadi booked', function () {
    // Buat User Pemilik
    $user = Pengguna::factory()->pemilik()->create([
        'username' => 'pemilik_kos',
        'password' => bcrypt('pemilik123'),
    ]);

    // Buat Kos & Kamar milik user tersebut
    $kos = Kos::factory()->create(['id_pengguna' => $user->id]);

    // Kamar awalnya 'tersedia'
    $kamar = Kamar::factory()->create([
        'id_kos' => $kos->id,
        'status' => 'ready'
    ]);

    // Buat Pesanan (Status awal 'pending')
    $pesanan = Pesanan::factory()->create([
        'id_kamar' => $kamar->id,
        'id_pengguna' => $user->id
    ]);

    // Jalankan Request Update
    actingAs($user)
        ->put(route('pemilik_kos.request.updateStatus', $pesanan->id), [
            'status' => 'Terima' // Input status baru
        ])
        ->assertRedirect(); // Pastikan redirect back

    // Cek status pesanan berubah jadi 'Terima'
    $this->assertDatabaseHas('pesanan', [
        'id' => $pesanan->id,
        'status_pemesanan' => 'Terima'
    ]);

    // Cek status kamar berubah jadi 'booked' (Sesuai logika controller)
    $this->assertDatabaseHas('kamar', [
        'id' => $kamar->id,
        'status' => 'booked'
    ]);
});