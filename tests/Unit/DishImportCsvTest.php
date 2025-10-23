<?php

use App\Models\User;
use App\Services\ImportCSVDish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {

    config(['auth.defaults.guard' => 'api']);

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    Sanctum::actingAs($this->admin, ['*']);
});

it('POST /api/dish/import-csv', function () {
    $mock = Mockery::mock(ImportCSVDish::class);
    $mock->shouldReceive('createDishByFile')->once()->andReturnTrue();
    app()->instance(ImportCSVDish::class, $mock);

    $file = UploadedFile::fake()->create('dishes.csv', 2, 'text/csv');

    $res = $this->post('/api/dish/import-csv', [
        'file' => $file,
        'delimiter' => ',',
    ], ['Accept' => 'application/json']);

    $res->assertOk()->assertJson(['message' => 'Success']);
});

it('POST /api/dish/import-csv – błąd z serwisu (422)', function () {
    $mock = Mockery::mock(ImportCSVDish::class);
    $mock->shouldReceive('createDishByFile')->once()->andReturnFalse();
    app()->instance(ImportCSVDish::class, $mock);

    $file = UploadedFile::fake()->create('dishes.csv', 2, 'text/csv');

    $res = $this->post('/api/dish/import-csv', [
        'file' => $file,
        'delimiter' => ';',
    ], ['Accept' => 'application/json']);

    $res->assertStatus(422)->assertJson(['message' => 'Bad data']);
});

it('POST /api/dish/import-csv – walidacja: brak pliku -> 422', function () {
    $res = $this->post('/api/dish/import-csv', [
        'delimiter' => ',',
    ], ['Accept' => 'application/json']);

    $res->assertStatus(422);
});
