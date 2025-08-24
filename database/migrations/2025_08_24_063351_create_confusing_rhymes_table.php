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
        Schema::create('confusing_rhymes', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->integer('frequency')->default(0);
            $table->string('subject');
            $table->json('same_rhymes');
            $table->json('similar_rhymes');
            $table->timestamps();
            
            $table->index('word');
            $table->index('frequency');
            $table->unique(['word', 'subject']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('confusing_rhymes');
    }
};
