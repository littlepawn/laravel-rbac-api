<?php

namespace Database\Seeders;

use App\Models\User\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentPermission = Permission::create(['slug' => 'view.terminal.list', 'parent_id' => 1, 'uri' => 'terminal/list']);
        Permission::create(['slug' => 'import.terminal', 'parent_id' => $parentPermission->id, 'uri' => 'terminal/import']);
        Permission::create(['slug' => 'export.terminal', 'parent_id' => $parentPermission->id, 'uri' => 'terminal/export']);
        Permission::create(['slug' => 'create.terminal', 'parent_id' => $parentPermission->id, 'uri' => 'terminal/create']);
        Permission::create(['slug' => 'update.terminal', 'parent_id' => $parentPermission->id, 'uri' => 'terminal/update']);
        Permission::create(['slug' => 'delete.terminal', 'parent_id' => $parentPermission->id, 'uri' => 'terminal/delete']);

        Permission::create(['slug' => 'view.login.log', 'parent_id' => 1, 'uri' => 'log/login-log']);
        Permission::create(['slug' => 'view.operation.log', 'parent_id' => 1, 'uri' => 'log/operation-log']);
    }
}
