<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrCreate(['name' => 'admin',  'guard_name' => 'api']);
        Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'api']);

    }
}
