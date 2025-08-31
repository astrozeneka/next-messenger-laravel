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
        Schema::create('msgs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id');
            $table->text('content')->nullable();
            $table->enum('type', ['text', 'image', 'file'])->default('text');
            $table->enum('status', ['sent', 'delivered', 'read'])->default('sent');

            $table->foreign('conversation_id')
                  ->references('id')->on('conversations')
                  ->onDelete('cascade');
            $table->foreign('sender_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            
            $table->index(['conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('msgs');
    }
};
