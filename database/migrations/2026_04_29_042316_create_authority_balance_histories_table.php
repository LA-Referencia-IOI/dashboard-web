<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorityBalanceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authority_balance_histories', function (Blueprint $table) {
            $table->id();
            $table->string('authority_id')->index();
            $table->decimal('balance', 18, 8)->default(0);
            $table->timestamps();

            $table->foreign('authority_id')->references('id')->on('authorities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authority_balance_histories');
    }
}
