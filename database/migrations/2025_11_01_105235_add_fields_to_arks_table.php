<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToArksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('arks', function (Blueprint $table) {

            $table->string('target_url')->nullable();
            $table->integer('target_http_code')->nullable();


            $table->string('who_name_native')->nullable();
            $table->string('who_acronym')->nullable();
            $table->json('who_location')->nullable();


            $table->string('na_orgtype')->nullable();
            $table->string('na_policy')->nullable();
            $table->string('na_tenure')->nullable();
            $table->string('na_policy_url')->nullable();


            $table->string('test_identifier')->nullable();
            $table->string('service_provider')->nullable();
            $table->string('purpose')->nullable();
            $table->string('rtype')->nullable();


            $table->string('contact_unit')->nullable();
            $table->string('contact_tenure')->nullable();
            $table->string('contact_phone')->nullable();

            $table->string('alternate_contact')->nullable();
            $table->text('comments')->nullable();
            $table->string('provider')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('arks', function (Blueprint $table) {
            $table->dropColumn([
                'target_url',
                'target_http_code',
                'who_name_native',
                'who_acronym',
                'who_location',
                'na_orgtype',
                'na_policy',
                'na_tenure',
                'na_policy_url',
                'test_identifier',
                'service_provider',
                'purpose',
                'rtype',
                'contact_unit',
                'contact_tenure',
                'contact_phone',
                'alternate_contact',
                'comments',
                'provider'
            ]);
        });
    }
}
