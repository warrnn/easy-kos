<?php

use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

//
// === Test function login_form ===
//
test('Mengakses login page', function () {
    get(route('authentication.login'))
        ->assertStatus(200)
        ->assertViewIs('authentication.login.index');
});

test('User dialihkan ke halaman login saat akses halaman terproteksi', function (string $routeName) {
    get(route($routeName))
        ->assertRedirect('/'); // Pastikan arahnya ke route awal
})->with(['pemilik.index', 'penghuni.index', 'admin.index']);


//
// === Test function authenticate ===
//
// login admin
test('Admin melakukan login', function () {
    $user = Pengguna::factory()->admin()->create([
        'username' => 'admin_test',
        'password' => bcrypt('password123'),
    ]);

    $response = post('/authentication/login', [
        'username' => 'admin_test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('authentication.login'))
        ->assertSessionHas('success', 'Login berhasil!')
        ->assertSessionHas('redirectUrl', route('admin.index'));

    $this->assertAuthenticatedAs($user);
});

// login pemilik kos
test('Pemilik kos melakukan login', function () {
    $user = Pengguna::factory()->pemilik()->create([
        'username' => 'andre',
        'password' => bcrypt('password123'),
    ]);

    $response = post('/authentication/login', [
        'username' => 'andre',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('authentication.login'))
        ->assertSessionHas('success', 'Login berhasil!')
        ->assertSessionHas('redirectUrl', route('pemilik.index'));

    $this->assertAuthenticatedAs($user);
});

// login penghuni kos
test('Penghuni kos melakukan login', function () {
    $user = Pengguna::factory()->create([
        'username' => 'yesto',
        'password' => bcrypt('password123'),
    ]);

    $response = post('/authentication/login', [
        'username' => 'yesto',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('authentication.login'))
        ->assertSessionHas('success', 'Login berhasil!')
        ->assertSessionHas('redirectUrl', route('penghuni.index'));

    $this->assertAuthenticatedAs($user);
});

// login credentials salah
test('User gagal login karena credentials salah', function () {
    Pengguna::factory()->create([
        'username' => 'niko',
        'password' => bcrypt('password123'),
    ]);

    $response = post('/authentication/login', [
        'username' => 'niko',
        'password' => 'password456',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('error', 'Username atau password salah.');
    // ngecek session tidak memiliki user
    $this->assertGuest();
});


//
// === Test function register_form ===
//
test('Mengakses register page', function () {
    // buat role karena keperluan di form register
    Role::factory()->pemilik()->create(); // id = 2
    Role::factory()->penghuni()->create(); // id = 3

    get(route('authentication.register'))
        ->assertStatus(200)
        ->assertViewIs('authentication.register.index')
        ->assertViewHas('roles');
});


//
// === Test function add_user ===
//
// Pemilik kos membuat akun baru
test('Pemilik kos melakukan registrasi akun', function () {
    // buat role karena keperluan di form register
    Role::factory()->pemilik()->create(); // id = 2
    Role::factory()->penghuni()->create(); // id = 3

    $response = post('/authentication/register', [
        'role' => 2,
        'username' => 'andre',
        'password' => 'andre123',
        'password_confirmation' => 'andre123',
    ]);

    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('pengguna', [
        'username' => 'andre',
        'id_role' => 2,
    ]);
});

// Penghuni membuat akun baru
test('Penghuni melakukan registrasi akun', function () {
    // buat role karena keperluan di form register
    Role::factory()->pemilik()->create(); // id = 2
    Role::factory()->penghuni()->create(); // id = 3

    $response = post('/authentication/register', [
        'role' => 3,
        'username' => 'warren',
        'password' => 'warren123',
        'password_confirmation' => 'warren123',
    ]);

    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('pengguna', [
        'username' => 'warren',
        'id_role' => 3,
    ]);
});

// Register akun gagal
test('Registrasi akun gagal jika password tidak cocok', function () {
    // buat role karena keperluan di form register
    Role::factory()->pemilik()->create(); // id = 2
    Role::factory()->penghuni()->create(); // id = 3

    $response = post('/authentication/register', [
        'username' => 'user_gagal',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia_beda',
        'role' => 3,
    ]);

    $response->assertSessionHasErrors(['password']);

    $this->assertDatabaseMissing('pengguna', [
        'username' => 'user_gagal',
    ]);
});

//
// === Test function logout ===
//
test('User bisa melakukan logout', function () {
    $user = Pengguna::factory()->create();

    // melakukan logout
    $response = actingAs($user)->post(route('authentication.logout'));

    $response->assertRedirect(route('authentication.login'))
        ->assertSessionHas('success', 'Logout berhasil!');

    $this->assertGuest();
});