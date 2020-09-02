<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundlesArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundles_articles', function (Blueprint $table) {
            $table->foreignId('bundle_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('article_id');
            $table->string('article_type');
            $table->unsignedInteger('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bundles_articles');
    }
}
