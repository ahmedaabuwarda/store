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
            $table->string('phone')->unique()->nullable();
            $table->string('family_number')->nullable();
            $table->unsignedBigInteger('mosque_id')->nullable();
            $table->foreign('mosque_id')->references('id')->on('mosques')->onDelete('cascade');
            $table->double('balance', 15, 4);
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('customers');
    }
}
