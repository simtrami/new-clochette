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
            $table->foreignId('id')->primary()->constrained('articles')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedDecimal('volume', 5, 3);
            $table->boolean('is_returnable')->default(false);
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
        Schema::dropIfExists('bottles');
    }
}
