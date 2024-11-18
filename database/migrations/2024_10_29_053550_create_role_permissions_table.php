<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //角色表
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug', 64)->comment('角色标识');
            $table->string('remark', 64)->default('')->comment('角色备注');
            $table->timestamps();
            $table->softDeletes();
        });

        //权限表
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug', 64)->comment('权限标识');
            $table->bigInteger('parent_id')->nullable()->default(0)->comment('父权限ID');
            $table->text('uri')->nullable()->comment('权限动作');
            $table->string('remark', 64)->default('')->comment('权限备注');
            $table->timestamps();
            $table->softDeletes();
        });

        //角色用户关联表
        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
            $table->primary(['role_id', 'user_id']);
            $table->timestamp('created_at')->useCurrent();
        });

        //角色权限关联表
        Schema::create('role_permission', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->primary(['role_id', 'permission_id']);
            $table->timestamp('created_at')->useCurrent();
        });

        //用户权限关联表
        Schema::create('user_permission', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->primary(['user_id', 'permission_id']);
            $table->timestamp('created_at')->useCurrent();
        });


        // Create default admin/user role
        $adminRoleId = DB::table('roles')->insertGetId([
            'remark' => '超级管理员',
            'slug' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $userRoleId = DB::table('roles')->insertGetId([
            'remark' => '普通用户',
            'slug' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create default admin user
        $adminUserId = DB::table('users')->insertGetId([
            'name' => env('ADMIN_NAME'),
            'email' => env('ADMIN_EMAIL'),
            'is_admin' => 1,
            'email_verified_at' => now(),
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign admin role to admin user
        DB::table('role_user')->insert([
            'role_id' => $adminRoleId,
            'user_id' => $adminUserId,
            'created_at' => now(),
        ]);


        /* Create default permissions for user management */
        // 插入“查看权限”权限
        $viewPermissionId = DB::table('permissions')->insertGetId([
            'remark' => '查看权限',
            'slug' => 'view.permissions',
            'uri' => 'user/view-permission',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('role_permission')->insert([
            'role_id' => $userRoleId,
            'permission_id' => $viewPermissionId,
            'created_at' => now(),
        ]);

        // 插入“用户管理”权限，并将其设置为“查看权限”的子集
        $viewUserPermissionId = DB::table('permissions')->insertGetId([
            'remark' => '查看用户',
            'slug' => 'view.user.details',
            'uri' => 'user/detail',
            'parent_id' => $viewPermissionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('role_permission')->insert([
            'role_id' => $userRoleId,
            'permission_id' => $viewUserPermissionId,
            'created_at' => now(),
        ]);

        // 插入“用户管理”下的子权限
        $permissions = [
            ['remark' => '修改用户名', 'slug' => 'modify.username', 'uri' => 'user/modify-name', 'parent_id' => $viewUserPermissionId],
            ['remark' => '修改用户密码', 'slug' => 'modify.user.password', 'uri' => 'user/modify-password', 'parent_id' => $viewUserPermissionId],
        ];

        foreach ($permissions as $permission) {
            $permission['created_at'] = now();
            $permission['updated_at'] = now();
            $permissionId = DB::table('permissions')->insertGetId($permission);

            // Assign permissions to user role
            DB::table('role_permission')->insert([
                'role_id' => $userRoleId,
                'permission_id' => $permissionId,
                'created_at' => now(),
            ]);
        }


        /**
         * // 创建roles表唯一索引
         * DB::statement('CREATE UNIQUE INDEX unique_roles_slug_deleted_at_null ON roles (slug) WHERE deleted_at IS NULL;');
         * DB::statement('CREATE UNIQUE INDEX unique_roles_slug_deleted_at_not_null ON roles (slug, deleted_at) WHERE deleted_at IS NOT NULL;');
         * // 创建permissions表唯一索引
         * DB::statement('CREATE UNIQUE INDEX unique_permissions_slug_deleted_at_null ON permissions (slug) WHERE deleted_at IS NULL;');
         * DB::statement('CREATE UNIQUE INDEX unique_permissions_slug_deleted_at_not_null ON permissions (slug, deleted_at) WHERE deleted_at IS NOT NULL;');
         **/

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('user_permission');
    }
};
