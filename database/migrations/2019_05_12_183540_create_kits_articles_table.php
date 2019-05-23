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
            $table->unsignedBigInteger('kit_id');
            $table->unsignedBigInteger('article_id');
            $table->unsignedInteger('article_quantity');
            $table->timestamps();

            $table->foreign('kit_id')->references('item_id')->on('kits')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('article_id')->references('item_id')->on('articles')
                ->onDelete('cascade')->onUpdate('cascade');
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
