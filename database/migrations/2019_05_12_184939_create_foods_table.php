<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->unsignedBigInteger('article_id')->primary();
            $table->boolean('is_bulk')->default(false);
            $table->unsignedInteger('units_left')->nullable();
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
        Schema::dropIfExists('foods');
    }
}
