<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selectives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('export_ainiat_number')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->boolean('status')->default(false)->comment('0: مرشح, 1: زبون');
            // unique if customer_id, product_id and status=0
            // $table->unique(['customer_id', 'product_id', 'status']);
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
        Schema::dropIfExists('selectives');
    }
}
