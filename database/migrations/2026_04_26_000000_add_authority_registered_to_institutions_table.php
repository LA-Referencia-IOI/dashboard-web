<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuthorityRegisteredToInstitutionsTable extends Migration
{
    public function up()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->boolean('authority_registered')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn('authority_registered');
        });
    }
}
