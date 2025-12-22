<?php

use App\Models\Kos;
use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\delete;

// admin menambahkan akun pemilik kos baru
// admin menambahkan data kos baru
// admin mengubah data kos yang sudah ada
// admin menghapus data kos

// admin menambahkan akun dengan password tidak cocok
// admin menambahkan kos tanpa data lengkap
// admin mengubah kos yang tidak ada
// admin menghapus kos yang tidak ada

uses(RefreshDatabase::class);

// === test case 1: admin menambahkan akun pemilik kos baru ===
test('Admin menambahkan akun pemilik kos baru', function () {
    $adminRole = Role::factory()->admin()->create();
    $pemilikRole = Role::factory()->pemilik()->create();
    $penghuniRole = Role::factory()->penghuni()->create();

    $admin = Pengguna::factory()->state([
        'username' => 'admin',
        'password' => bcrypt('admin'),
        'id_role' => $adminRole->id,
    ])->create();

    actingAs($admin);

    $response = get(route('admin.index'));
    $response->assertStatus(200);
    
    $response = get(route('admin.form-users'));
    $response->assertStatus(200)
        ->assertViewIs('admin.form-users.index')
        ->assertViewHas('roles');

    $response = post(route('admin.form-users.add-user'), [
        'role' => $pemilikRole->id,
        'username' => 'pemilik_baru',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('pengguna', [
        'username' => 'pemilik_baru',
        'id_role' => $pemilikRole->id,
    ]);

    $response = get(route('admin.manage-users'));
    $response->assertStatus(200)
        ->assertViewIs('admin.manage-user.index')
        ->assertSee('pemilik_baru');
});


// === test case 2: admin menambahkan data kos baru ===
test('Admin menambahkan data kos baru', function () {
    $adminRole = Role::factory()->admin()->create();
    $pemilikRole = Role::factory()->pemilik()->create();

    $admin = Pengguna::factory()->state([
        'username' => 'admin',
        'password' => bcrypt('admin'),
        'id_role' => $adminRole->id,
    ])->create();

    $pemilik = Pengguna::factory()->state([
        'username' => 'pemilik_kosong',
        'password' => bcrypt('password'),
        'id_role' => $pemilikRole->id,
    ])->create();

    actingAs($admin);

    $response = get(route('admin.index'));
    $response->assertStatus(200);

    $response = get(route('admin.form-pemilik'));
    $response->assertStatus(200)
        ->assertViewIs('admin.form-pemilik.index')
        ->assertViewHas('pemilikBaru');

    $response = post(route('admin.form-pemilik.add-kos'), [
        'role' => $pemilik->id,
        'name' => 'Kos Baru Sejahtera',
        'alamat' => 'Jl. Raya Sejahtera No. 123',
    ]);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('kos', [
        'name' => 'Kos Baru Sejahtera',
        'alamat' => 'Jl. Raya Sejahtera No. 123',
        'id_pengguna' => $pemilik->id,
    ]);

    $response = get(route('admin.manage-users'));
    $response->assertStatus(200)
        ->assertSee('Kos Baru Sejahtera')
        ->assertSee('Jl. Raya Sejahtera No. 123');
});


// === test case 3: admin mengubah data kos yang sudah ada ===
test('Admin mengubah data kos yang sudah ada', function () {
    $adminRole = Role::factory()->admin()->create();
    $pemilikRole = Role::factory()->pemilik()->create();

    $admin = Pengguna::factory()->state([
        'username' => 'admin',
        'password' => bcrypt('admin'),
        'id_role' => $adminRole->id,
    ])->create();

    $pemilik = Pengguna::factory()->state([
        'username' => 'pemilik_kos',
        'password' => bcrypt('password'),
        'id_role' => $pemilikRole->id,
    ])->create();

    $kos = Kos::create([
        'name' => 'Kos Lama',
        'alamat' => 'Jl. Lama No. 1',
        'id_pengguna' => $pemilik->id,
    ]);

    actingAs($admin);

    $response = get(route('admin.index'));
    $response->assertStatus(200);

    $response = get(route('admin.manage-users'));
    $response->assertStatus(200);

    $response = get(route('admin.form-pemilik.edit', ['kos' => $kos->id]));
    $response->assertStatus(200)
        ->assertViewIs('admin.form-pemilik.index')
        ->assertViewHas('kos');

    $response = post(route('admin.form-pemilik.edit', ['kos' => $kos->id]), [
        'role' => $pemilik->id,
        'name' => 'Kos Baru Diperbarui',
        'alamat' => 'Jl. Baru No. 999',
    ]);

    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('kos', [
        'id' => $kos->id,
        'name' => 'Kos Baru Diperbarui',
        'alamat' => 'Jl. Baru No. 999',
        'id_pengguna' => $pemilik->id,
    ]);

    $this->assertDatabaseMissing('kos', [
        'id' => $kos->id,
        'name' => 'Kos Lama',
    ]);

    $response = get(route('admin.manage-users'));
    $response->assertStatus(200)
        ->assertSee('Kos Baru Diperbarui')
        ->assertSee('Jl. Baru No. 999')
        ->assertDontSee('Kos Lama');
});


// === test case 4: admin menghapus data kos ===
test('Admin menghapus data kos', function () {
    $adminRole = Role::factory()->admin()->create();
    $pemilikRole = Role::factory()->pemilik()->create();

    $admin = Pengguna::factory()->state([
        'username' => 'admin',
        'password' => bcrypt('admin'),
        'id_role' => $adminRole->id,
    ])->create();

    $pemilik = Pengguna::factory()->state([
        'username' => 'pemilik_kos',
        'password' => bcrypt('password'),
        'id_role' => $pemilikRole->id,
    ])->create();

    $kos = Kos::create([
        'name' => 'Kos Akan Dihapus',
        'alamat' => 'Jl. Hapus No. 123',
        'id_pengguna' => $pemilik->id,
    ]);

    actingAs($admin);

    $response = get(route('admin.index'));
    $response->assertStatus(200);

    $response = get(route('admin.manage-users'));
    $response->assertStatus(200)
        ->assertSee('Kos Akan Dihapus');

    $response = delete(route('admin.manage-users.kos.delete', ['kos' => $kos->id]));

    $response->assertRedirect(route('admin.manage-users'))
        ->assertSessionHas('success', 'Kos berhasil dihapus.');

    $this->assertDatabaseMissing('kos', [
        'id' => $kos->id,
        'name' => 'Kos Akan Dihapus',
    ]);

    $response = get(route('admin.manage-users'));
    $response->assertStatus(200)
        ->assertDontSee('Kos Akan Dihapus')
        ->assertDontSee('Jl. Hapus No. 123');
});

// ==== SKENARIO GAGALLLLLLLLLLL ====

// === test case 1 fail: admin menambahkan akun dengan password tidak cocok ===
test('Admin gagal menambahkan akun pemilik kos karena password tidak cocok', function () {
    $adminRole = Role::factory()->admin()->create();
    $pemilikRole = Role::factory()->pemilik()->create();
    $penghuniRole = Role::factory()->penghuni()->create();

    $admin = Pengguna::factory()->state([
        'username' => 'admin',
        'password' => bcrypt('admin'),
        'id_role' => $adminRole->id,
    ])->create();

    actingAs($admin);

    $response = post(route('admin.form-users.add-user'), [
        'role' => $pemilikRole->id,
        'username' => 'pemilik_gagal',
        'password' => 'password123',
        'password_confirmation' => 'password_berbeda',
    ]);

    $response->assertSessionHasErrors(['password']);

    $this->assertDatabaseMissing('pengguna', [
        'username' => 'pemilik_gagal',
    ]);
});


// === test case 2 fail: admin menambahkan kos tanpa data lengkap ===
test('Admin gagal menambahkan kos karena data tidak lengkap', function () {
    $adminRole = Role::factory()->admin()->create();
    $pemilikRole = Role::factory()->pemilik()->create();

    $admin = Pengguna::factory()->state([
        'username' => 'admin',
        'password' => bcrypt('admin'),
        'id_role' => $adminRole->id,
    ])->create();

    $pemilik = Pengguna::factory()->state([
        'username' => 'pemilik_kosong',
        'password' => bcrypt('password'),
        'id_role' => $pemilikRole->id,
    ])->create();

    actingAs($admin);

    $response = post(route('admin.form-pemilik.add-kos'), [
        'role' => $pemilik->id,
        'name' => '',
        'alamat' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'alamat']);

    $this->assertDatabaseMissing('kos', [
        'id_pengguna' => $pemilik->id,
    ]);
});


// === test case 3 fail: admin mengubah kos yang tidak ada ===
test('Admin gagal mengubah kos yang tidak ada', function () {
    $adminRole = Role::factory()->admin()->create();
    $pemilikRole = Role::factory()->pemilik()->create();

    $admin = Pengguna::factory()->state([
        'username' => 'admin',
        'password' => bcrypt('admin'),
        'id_role' => $adminRole->id,
    ])->create();

    $pemilik = Pengguna::factory()->state([
        'username' => 'pemilik_kos',
        'password' => bcrypt('password'),
        'id_role' => $pemilikRole->id,
    ])->create();

    actingAs($admin);

    $kosID= 9999;

    $response = post(route('admin.form-pemilik.edit', ['kos' => $kosID]), [
        'role' => $pemilik->id,
        'name' => 'Kos Tidak Ada',
        'alamat' => 'Jl. Tidak Ada',
    ]);

    $response->assertStatus(404);
});


// === test case 4 fail: admin menghapus kos yang tidak ada ===
test('Admin gagal menghapus kos yang tidak ada', function () {
    $adminRole = Role::factory()->admin()->create();
    $pemilikRole = Role::factory()->pemilik()->create();

    $admin = Pengguna::factory()->state([
        'username' => 'admin',
        'password' => bcrypt('admin'),
        'id_role' => $adminRole->id,
    ])->create();

    actingAs($admin);

    $kosID = 9999;

    $response = delete(route('admin.manage-users.kos.delete', ['kos' => $kosID]));

    $response->assertRedirect(route('admin.manage-users'))
        ->assertSessionHas('error', 'Kos tidak ditemukan.');
});
