<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportAiniatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_ainiats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('number')->unique();
            $table->date('date_created');
            $table->integer('provider_id')->default(0);
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
        Schema::dropIfExists('import_ainiats');
    }
}
