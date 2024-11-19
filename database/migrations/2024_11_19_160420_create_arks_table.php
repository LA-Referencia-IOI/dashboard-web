<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('who')->nullable();
            $table->string('what')->nullable();
            $table->string('when')->nullable();
            $table->string('where')->nullable();
            $table->string('how')->nullable();
            $table->string('why')->nullable();
            $table->string('address')->nullable();
            $table->string('contact')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arks');
    }
}
