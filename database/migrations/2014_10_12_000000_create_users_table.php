<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 64);
            $table->string('email', 64)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamp('password_modified_at')->nullable()->comment('密码修改时间');
            $table->rememberToken();
            $table->tinyInteger('is_admin')->default(0)->comment('是否是管理员 1=是 0=否');
            $table->tinyInteger('is_locked')->default(0)->comment('是否被锁定 1=是 0=否');
            $table->tinyInteger('is_banned')->default(0)->comment('是否被禁用 1=是 0=否');
            $table->tinyInteger('login_error_count')->default(0)->comment('登录失败重试次数');
            $table->timestamp('login_at')->nullable()->comment('登录时间');
            $table->string('login_ip', 16)->nullable()->comment('登录IP');
            $table->timestamps();
        });

        //操作日志比表
        Schema::create('user_operation_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->string('role_name');
            $table->string('path');
            $table->string('method', 10);
            $table->string('ip');
            $table->text('input');
            $table->text('output');
            $table->index('user_id');
            $table->timestamp('created_at');
        });

        // 登录日志表
        Schema::create('user_login_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->string('role_name');
            $table->string('ip', 32);
            $table->string('device', 128)->nullable();
            $table->index('user_id');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_operation_logs');
        Schema::dropIfExists('user_login_logs');
    }
};
