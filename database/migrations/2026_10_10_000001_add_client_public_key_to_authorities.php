<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientPublicKeyToAuthorities extends Migration
{
    public function up()
    {
        Schema::table('authorities', function (Blueprint $table) {
            $table->string('client_public_key', 44)->nullable();
        });
    }

    public function down()
    {
        Schema::table('authorities', function (Blueprint $table) {
            $table->dropColumn('client_public_key');
        });
    }
}
