<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthoritiesTable extends Migration
{
    public function up()
    {
        Schema::create('authorities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('responsible');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('wallet_address')->nullable();
            $table->decimal('balance', 18, 8)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('authorities');
    }
}
