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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('clientid', 45);
            $table->string('item', 30);
            $table->string('description', 45)->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('createdby', 45);
            $table->timestamp('created_at')->nullable();
            $table->integer('status');
            $table->string('businessid', 45);
            $table->string('paymentno', 20)->unique();
            // Remove the timestamps() call since we already have created_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
