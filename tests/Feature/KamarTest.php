<?php

use App\Models\Kamar;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Model memiliki relasi hasMany ke Pesanan', function () {
    $model = new Kamar();

    $relation = $model->pesanan();

    expect($relation)->toBeInstanceOf(HasMany::class);

    expect($relation->getRelated())->toBeInstanceOf(Pesanan::class);
});