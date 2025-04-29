<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSanadatSarfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sanadat_sarfs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('number')->unique();
            $table->date('date_created');
            $table->unsignedBigInteger('provider_id')->default(0);
            $table->unsignedBigInteger('customer_id')->default(0);
            $table->unsignedBigInteger('worker_id')->default(0);
            $table->unsignedBigInteger('box_id');
            $table->foreign('box_id')->references('id')->on('box')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('balance', 15, 4);
            $table->string('notes');
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
        Schema::dropIfExists('sanadat_sarfs');
    }
}
