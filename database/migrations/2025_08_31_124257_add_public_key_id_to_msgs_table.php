<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msgs', function (Blueprint $table) {
            $table->unsignedBigInteger('public_key_id')->nullable();
            
            $table->foreign('public_key_id')
                  ->references('id')->on('public_keys')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('msgs', function (Blueprint $table) {
            $table->dropForeign(['public_key_id']);
            $table->dropColumn('public_key_id');
        });
    }
};
