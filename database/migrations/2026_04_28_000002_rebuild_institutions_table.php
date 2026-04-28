<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RebuildInstitutionsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('institutions');

        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->uuid('authority_id');
            $table->foreign('authority_id')->references('id')->on('authorities')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email');
            $table->string('responsible');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('numberNodes')->nullable();
            $table->string('typeNodes')->nullable();
            $table->string('status')->default('0');
            $table->text('detailsNodes')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('institutions');
    }
}
