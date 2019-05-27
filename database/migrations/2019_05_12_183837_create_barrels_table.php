<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarrelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barrels', function (Blueprint $table) {
            $table->unsignedBigInteger('article_id')->primary();
            $table->unsignedDecimal('volume', 5, 2);
            $table->string('withdrawal_type')->nullable();
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
        Schema::dropIfExists('barrels');
    }
}
