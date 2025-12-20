<?php

use App\Models\Kos;
use App\Models\Review;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Model memiliki relasi hasMany ke Pesanan', function () {
    $model = new Review();

    $relation = $model->kos();

    expect($relation)->toBeInstanceOf(BelongsTo::class);

    expect($relation->getRelated())->toBeInstanceOf(Kos::class);
});