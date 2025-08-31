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
        Schema::create('conversation_members', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('joined_at')->useCurrent(); // Now the system only allows a 1:1 conversation
            $table->timestamp('last_seen_at')->nullable();

            $table->foreign('conversation_id')
                  ->references('id')->on('conversations')
                  ->onDelete('cascade');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->unique(['conversation_id', 'user_id']);

            $table->index('last_seen_at');
            $table->integer('unread_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversation_members');
    }
};
