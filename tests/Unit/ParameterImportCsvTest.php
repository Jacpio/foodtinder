<?php

use App\Models\Parameter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $adminRole = Role::firstOrCreate([
        'name' => 'admin',
        'guard_name' => 'api',
    ]);

    $this->user->assignRole($adminRole);

    Sanctum::actingAs($this->user, ['*']);
});

test('imports parameters from CSV (comma delimiter)', function () {
    $csv = <<<CSV
                Id,Name,Type,Value,IsActive
                1,Zupy,other,1,1
                2,Włoska,cuisine,1,1
                3,Słony,flavour,1,1
                4,Sezonowe,other,0.5,0
            CSV;

    $file = UploadedFile::fake()->createWithContent('params_comma.csv', $csv);

    $response = $this->post('/api/parameter/import-csv', [
        'file' => $file,
        'delimiter' => 'comma',
    ]);

    $response->assertStatus(200)->assertJson(['message' => 'Success']);
    expect(
        Parameter::where('name', 'Zupy')->where('type', 'other')
            ->exists()
    )->toBeTrue()
        ->and(
            Parameter::where('name', 'Włoska')
                ->whereHas('type', fn($q) => $q->where('name', 'cuisine'))
                ->exists()
        )->toBeTrue()
        ->and(
            Parameter::where('name', 'Słony')
                ->whereHas('type', fn($q) => $q->where('name', 'flavour'))
                ->exists()
        )->toBeTrue();

    $other = Parameter::where('name', 'Sezonowe')
        ->whereHas('type', fn($q) => $q->where('name', 'other'))
        ->first();
    dump($other);
    expect($other)->not->toBeNull()
        ->and((float) $other->value)->toBe(0.5)
        ->and((int) $other->is_active)->toBe(0)
    ->and(Parameter::where('name', 'Włoska')->where('type', 'cuisine')->exists())->toBeTrue()
        ->and(Parameter::where('name', 'Słony')->where('type', 'flavour')->exists())->toBeTrue();
    $other = Parameter::where('name', 'Sezonowe')->where('type', 'other')->first();
    expect($other)->not->toBeNull()
        ->and((float) $other->value)->toBe(0.5)
        ->and((int) $other->is_active)->toBe(0);
});

test('imports parameters from CSV (semicolon delimiter)', function () {
    $csv = <<<CSV
            Id;Name;Type;Value;IsActive
            10;Zupy;category;1;1
            11;Polska;cuisine;1;1
        CSV;

    $file = UploadedFile::fake()->createWithContent('params_semicolon.csv', $csv);

    $response = $this->post('/api/parameter/import-csv', [
        'file' => $file,
        'delimiter' => 'semicolon',
    ]);

    $response->assertStatus(200)->assertJson(['message' => 'Success']);

    expect(Parameter::where('name', 'Zupy')->where('type', 'category')->exists())->toBeTrue()
        ->and(Parameter::where('name', 'Polska')->where('type', 'cuisine')->exists())->toBeTrue();
});

test('returns 302 when file is missing', function () {
    $response = $this->post('/api/parameter/import-csv', [
        'delimiter' => 'comma',
    ]);

    $response->assertStatus(302);
});
