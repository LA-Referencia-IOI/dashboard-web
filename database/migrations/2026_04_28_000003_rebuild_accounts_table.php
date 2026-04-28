<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\AccountType;

class RebuildAccountsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('accounts');

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid('authority_id')->unique(); // 1 account per authority
            $table->foreign('authority_id')->references('id')->on('authorities')->onDelete('cascade');
            $table->smallInteger('profile')->default(AccountType::Partner);
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
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
