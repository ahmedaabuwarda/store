<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('identity')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('family_number')->nullable();
            $table->unsignedBigInteger('mosque_id')->nullable();
            $table->foreign('mosque_id')->references('id')->on('mosques')->onDelete('cascade');
            $table->double('balance', 15, 4)->nullable();
            $table->boolean('status')->default(true)->comment('0: مرشح, 1: مستفيد');
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('customers');
    }
}
