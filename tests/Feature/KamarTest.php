<?php

use App\Models\Kamar;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Model memiliki relasi hasMany ke Pesanan', function () {
    // 1. Setup: Instance Model (Tanpa save ke DB)
    $model = new Kamar(); // Ganti 'Kamar' jika fungsi ini ada di 'Pengguna'

    // 2. Act: Panggil fungsi relasinya
    $relation = $model->pesanan();

    // 3. Assert: Pastikan tipe return-nya adalah HasMany
// Ini memastikan Anda tidak salah ketik menjadi 'hasOne' atau 'belongsTo'
    expect($relation)->toBeInstanceOf(HasMany::class);

    // Opsional: Cek apakah model targetnya benar (Pesanan::class)
    expect($relation->getRelated())->toBeInstanceOf(Pesanan::class);
});