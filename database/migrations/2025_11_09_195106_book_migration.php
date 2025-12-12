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
        // Schema::create("books", function(Blueprint $table){
        //     $table->id();
        //     $table->string("name");
        //     $table->text("description");
        //     $table->string("author");
        //     $table->string("publisher");
        //     $table->date("published_date");
        //     $table->integer('pages');
        //     $table->string("cover_url");
        //     $table->string("pdf_url");
        //     $table->boolean('is_visible')->default(true);
        //     $table->unsignedBigInteger('readers')->default(0);
        //     $table->unsignedBigInteger('likes')->default(0);
        //     $table->unsignedBigInteger('dislikes')->default(0);
        //     $table->unsignedBigInteger('favorites')->default(0);
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
