<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RebuildBlockchainsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('blockchains');

        Schema::create('blockchains', function (Blueprint $table) {
            $table->id();
            $table->uuid('authority_id');
            $table->foreign('authority_id')->references('id')->on('authorities')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->string('number_nodes')->nullable();
            $table->string('local')->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->text('enodes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blockchains');
    }
}
