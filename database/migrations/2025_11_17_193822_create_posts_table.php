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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('id_code', 20);  // renamed because "id" is reserved by Laravel
            $table->string('name', 50);

            $table->double('cpx')->default(0);       // cost price
            $table->double('spx')->default(0);       // selling price

            $table->string('category', 50);
            $table->string('brand', 45)->default('');
            $table->string('barcode', 100);

            $table->string('image', 256)->default('default.png');

            $table->double('max_order')->default(0);
            $table->double('min_order')->default(0);
            $table->double('reorder')->default(0);

            $table->string('createdby', 50);
            $table->string('modifiedby', 50);


            $table->string('storeid', 50);
            $table->string('businessid', 50);
            $table->string('unit', 50);
            $table->string('partno', 50);

            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();

            $table->string('journalno', 15)->default('');
            $table->string('journalsource', 20);
            $table->char('account', 1)->default('');
            $table->string('accountcode', 20);
            $table->double('amount');

            $table->string('ref', 11)->default(' ');
            $table->string('description', 50)->default(' ');

            $table->date('tdate');
            $table->string('period', 7);

            $table->string('createdby', 20)->default('');
            $table->string('postedby', 20)->default('');

            $table->boolean('posted')->default(0);

            $table->string('storeid', 20)->default(' ');
            $table->string('businessid', 20)->default(' ');

            // Laravel timestamps (instead of createddate, posteddate)
            $table->timestamps();
        });

        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();  // replaces `no`

            $table->string('brandid', 20);          // product brand id
            $table->string('name', 30);
            $table->string('storeid', 30);
            $table->string('businessid', 30);

            $table->tinyInteger('status')->default(1);

            $table->timestamps(); // created_at + updated_at
        });
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id(); // replaces `no` as primary key

            $table->string('name', 30)->default('');
            $table->tinyInteger('status')->default(1);
            $table->string('storeid', 20);
            $table->string('businessid', 20);

            $table->timestamps(); // created_at + updated_at
        });
        Schema::create('stores', function (Blueprint $table) {
            $table->id(); // replaces `no` primary key

            $table->string('store_id', 20);
            $table->string('store_name', 100);
            $table->string('location', 100);
            $table->string('phone', 100);
            $table->string('businessid', 20);
            $table->string('ledgercode', 20)->default(' ');
            $table->integer('base')->default(0);
            $table->tinyInteger('retail')->default(1);
            $table->tinyInteger('headoffice')->default(0);
            $table->string('storecode', 4);
            $table->tinyInteger('allowoutofstocksale')->default(1);

            $table->timestamps(); // created_at + updated_at
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // replaces `no` primary key

            $table->string('customercode', 30);
            $table->string('accountcode', 20);
            $table->string('bankname', 30)->nullable();
            $table->string('reference', 30);
            $table->dateTime('receiptdate')->nullable();
            $table->dateTime('maturitydate')->nullable();
            $table->double('amount')->default(0);
            $table->string('narration', 50);
            $table->integer('status')->default(0);
            $table->string('manualno', 20);
            $table->string('receiptno', 20);
            $table->integer('isposted')->default(0);
            $table->string('createdby', 30);
            $table->dateTime('createddate')->nullable();
            $table->string('modifiedby', 30);
            $table->dateTime('modifieddate')->nullable();
            $table->string('storeid', 30);
            $table->string('businessid', 30);
            $table->double('oldbalance')->default(0);

            $table->timestamps(); // Laravel created_at + updated_at
        });
        Schema::create('paymentoptions', function (Blueprint $table) {
            $table->id(); // replaces `no` primary key

            $table->string('paymentoption', 20);
            $table->string('accountcode', 20);
            $table->string('storeid', 20);
            $table->string('businessid', 20);
            $table->tinyInteger('status')->default(1);

            $table->timestamps(); // created_at + updated_at
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id(); // replaces `no` primary key

            $table->string('customercode', 30);
            $table->string('accountcode', 20);
            $table->string('bankname', 30)->nullable();
            $table->string('reference', 30);
            $table->dateTime('receiptdate')->nullable();
            $table->dateTime('maturitydate')->nullable();
            $table->double('amount')->default(0);
            $table->string('narration', 50);
            $table->integer('status')->default(0);
            $table->string('manualno', 20);
            $table->string('receiptno', 20);
            $table->integer('isposted')->default(0);
            $table->string('createdby', 30);
            $table->string('modifiedby', 30);
            $table->string('storeid', 30);
            $table->string('businessid', 30);
            $table->double('oldbalance')->default(0);

            $table->timestamps(); // created_at + updated_at
        });

        Schema::create('product_units', function (Blueprint $table) {
            $table->id(); // replaces `no` primary key

            $table->string('name', 20);
            $table->string('unit_id', 20); // renamed from `id` because `id` is reserved
            $table->double('qty')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->string('storeid', 20);
            $table->string('businessid', 20);

            $table->timestamps(); // created_at + updated_at
        });

        Schema::create('usergroups', function (Blueprint $table) {
            $table->id(); // replaces `no` primary key

            $table->string('group_id', 30)->unique(); // renamed from `id` to avoid Laravel primary key conflict
            $table->string('description', 45);
            $table->tinyInteger('status')->default(1);
            $table->string('roles', 1200)->default('');
            $table->string('createdby', 45)->default('');
            $table->string('modifiedby', 45);

            $table->timestamps(); // created_at + updated_at
        });
        Schema::create('trnxdetails', function (Blueprint $table) {
            $table->id(); // replaces `no` primary key

            $table->string('productid', 30)->nullable();
            $table->string('invoiceno', 30)->nullable();
            $table->char('account', 1);
            $table->double('qty')->default(0);
            $table->double('px')->default(0);
            $table->double('amount')->default(0);
            $table->string('tax', 20)->nullable();
            $table->string('discount', 20)->nullable();
            $table->string('storeid', 20);
            $table->string('businessid', 20);

        });

        Schema::create('trnxhdr', function (Blueprint $table) {
            $table->id(); // replaces `no` primary key

            $table->string('customerid', 30)->nullable();
            $table->string('invoiceno', 30)->nullable()->unique();
            $table->string('transactiontype', 30)->nullable();
            $table->double('totalamount')->default(0);
            $table->string('storeid', 30)->nullable();
            $table->string('businessid', 30)->nullable();
            $table->date('tdate')->nullable();
            $table->string('createdby', 50);
            $table->dateTime('createddate')->default(now());
            $table->string('name', 50);
            $table->string('internal_inv_no', 11)->default('0');
            $table->string('customerphone', 30)->default('z');
            $table->string('ledgeraccountcode', 20)->default(' ');
            $table->string('trfsreceiver', 30)->default('');
            $table->tinyInteger('status')->default(1);
            $table->double('balance')->default(0);
            $table->string('comment', 255);

            $table->timestamps(); // created_at + updated_at
        });

        Schema::create('pricebook', function (Blueprint $table) {
            $table->id(); // replaces `no` primary key

            $table->string('productid', 20);
            $table->double('cpx')->default(0);
            $table->double('spx')->default(0);
            $table->double('min_order')->default(0);
            $table->double('max_order')->default(0);
            $table->double('reorder')->default(0);
            $table->string('createdby', 20);
            $table->string('modifiedby', 20);
            $table->string('storeid', 20);
            $table->string('businessid', 20);

            $table->unique(['productid', 'storeid'], 'onlyone');

            $table->timestamps(); // created_at + updated_at
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('ledgers');
        Schema::dropIfExists('product_brands');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('paymentoptions');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('product_units');
        Schema::dropIfExists('usergroups');
        Schema::dropIfExists('trnxdetails');
        Schema::dropIfExists('trnxhdr');
        Schema::dropIfExists('pricebook');
    }
};
