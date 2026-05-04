<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServerResourceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_resource_histories', function (Blueprint $table) {
            $table->id();
            $table->double('disk_total_gb', 10, 2);
            $table->double('disk_used_gb', 10, 2);
            $table->double('disk_available_gb', 10, 2);
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
        Schema::dropIfExists('server_resource_histories');
    }
}
