<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreationRequestKeyToAuthorities extends Migration
{
    public function up()
    {
        Schema::table('authorities', function (Blueprint $table) {
            $table->string('creation_request_key', 64)->nullable()->unique();
        });
    }

    public function down()
    {
        Schema::table('authorities', function (Blueprint $table) {
            $table->dropUnique(['creation_request_key']);
            $table->dropColumn('creation_request_key');
        });
    }
}
