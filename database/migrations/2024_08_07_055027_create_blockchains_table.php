<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockchainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blockchains', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('institution_id');
            $table->string('type')->nullable();
            $table->string('number_nodes')->nullable();
            $table->string('local')->nullable();



            $table
                ->foreign('institution_id')
                ->references('id')
                ->on('institutions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blockchains');
    }
}
