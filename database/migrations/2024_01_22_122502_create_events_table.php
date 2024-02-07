<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name', 255);
            $table->text('description');
            $table->boolean('ongoing')->default(false);
            $table->string('location', 255);
            $table->integer('price')->nullable();
            $table->string('file_link', 255)->nullable();
            $table->string('img_link', 255);
            $table->dateTime('start_time');
            $table->dateTime('time_ends');
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
