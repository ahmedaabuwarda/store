<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportAiniatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_ainiats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('number')->unique();
            $table->date('date_created');
            $table->integer('provider_id')->default(0);
            $table->integer('customer_id')->default(0);
            $table->integer('worker_id')->default(0);
            $table->double('total_balance', 15, 4);
            $table->double('paid_balance', 15, 4);
            $table->double('remaining_balance', 15, 4);
            $table->double('total_profit', 15, 4);
            $table->double('expense', 15, 4);
            $table->string('byan');
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
        Schema::dropIfExists('export_ainiats');
    }
}
