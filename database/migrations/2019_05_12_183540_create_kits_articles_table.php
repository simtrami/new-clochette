<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKitsArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kits_articles', function (Blueprint $table) {
            $table->foreignId('kit_id')->constrained()
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('article_id')->constrained()
                ->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('article_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kits_articles');
    }
}
