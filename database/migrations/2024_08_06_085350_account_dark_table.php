<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AccountDarkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('checkin_date')->nullable();
            $table->string('auth_id')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('naan')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('payload_schema')->nullable();
            $table->string('address')->nullable();
            $table->string('balance')->nullable();
            $table->string('private_key')->nullable();
            $table->string('shoulder')->nullable();
            $table->string('dnam_auth_id')->nullable();
            $table->string('noid_len')->nullable();
            $table->string('noidprovider_addr')->nullable();
            $table->string('status')->nullable();
            $table->string('institution_id')->nullable();
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
