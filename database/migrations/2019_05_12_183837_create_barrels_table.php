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
            $table->bigIncrements('id');
            $table->foreignId('supplier_id')->nullable()->constrained()
                ->onDelete('set null')->onUpdate('cascade');
            $table->string('name');
            $table->unsignedInteger('quantity');
            $table->unsignedDecimal('unit_price', 9, 3);
            $table->unsignedDecimal('volume', 5, 2);
            $table->string('coupler')->nullable();
            $table->unsignedDecimal('abv', 3, 1)->nullable();
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
