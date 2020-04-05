<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('slug')->unique();
            $table->string('locale')->default('pl');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->json('available_locales')->default('["pl"]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('locale');
            $table->dropColumn('email');
            $table->dropColumn('phone_number');
            $table->dropColumn('available_locales');
        });
    }
}
