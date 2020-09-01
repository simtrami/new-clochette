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
            $table->foreignId('id')->primary()->constrained('articles')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedDecimal('volume', 5, 2);
            $table->string('coupler')->nullable();
            $table->unsignedDecimal('abv', 4, 2)->nullable();
            $table->unsignedDecimal('ibu', 4, 1)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('barrels');
    }
}
