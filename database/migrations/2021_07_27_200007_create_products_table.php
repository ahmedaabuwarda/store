<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->nullable()->unique();
            $table->string('name')->unique();
            $table->integer('quantity')->default(0);
            $table->integer('original_quantity')->default(0);
            $table->double('original_price', 15, 4)->default(0);
            $table->double('taqseet_price', 15, 4)->default(0);
            $table->unsignedBigInteger('export_ainiat_id');
            $table->unsignedBigInteger('buy_bill_id');
            $table->boolean('status')->default(true);
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
