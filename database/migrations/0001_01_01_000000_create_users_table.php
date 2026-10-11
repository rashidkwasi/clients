<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Your custom fields
            $table->string('userid', 50);
            $table->string('firstname', 50);
            $table->string('surname', 50);
            $table->string('othername', 50);
            $table->tinyInteger('status')->default(1);
            $table->string('usergroup', 50)->default('z');
            $table->string('email', 100)->unique();
            $table->string('telephone', 30);
            $table->string('storeid', 100);
            $table->string('businessid', 20);
            $table->string('createdby', 20);
            $table->string('modifiedby', 20)->nullable();


            // Laravel default fields
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // You may remove if using your own 'pwd'
            $table->rememberToken();
            $table->timestamps();
        });






        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
