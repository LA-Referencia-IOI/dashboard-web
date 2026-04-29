<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorityNaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authority_naan', function (Blueprint $table) {
            $table->id();
            $table->uuid('authority_id');
            $table->unsignedBigInteger('naan_id');
            $table->timestamps();

            $table->foreign('authority_id')->references('id')->on('authorities')->onDelete('cascade');
            $table->foreign('naan_id')->references('id')->on('naans')->onDelete('cascade');
            
            $table->unique(['authority_id', 'naan_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authority_naan');
    }
}
