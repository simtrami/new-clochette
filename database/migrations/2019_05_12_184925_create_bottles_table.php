<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBottlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bottles', function (Blueprint $table) {
            $table->unsignedBigInteger('article_id')->primary();
            $table->unsignedDecimal('volume', 5, 3);
            $table->boolean('is_returnable')->default(false);
            $table->unsignedDecimal('abv', 4, 2)->nullable();
            $table->unsignedDecimal('ibu', 4, 1)->nullable();
            $table->string('variety')->nullable();
            $table->softDeletes();
            $table->timestamps();

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
        Schema::dropIfExists('bottles');
    }
}
