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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoiceno')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('month', 2);
            $table->string('year', 4);
            $table->decimal('totalamount', 15, 2);
            $table->decimal('paidamount', 15, 2);
            $table->date('duedate');
            $table->integer('status');
            $table->string('businessid', 45);
            $table->string('createdby', 45);
            $table->timestamp('created_at')->nullable();
            // Remove the timestamps() call since we already have created_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
