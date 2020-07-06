<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->foreignId('id')->primary()->constrained('items')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained()
                ->onDelete('set null')->onUpdate('cascade');
            $table->unsignedDecimal('unit_price', 9, 3);
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
        Schema::dropIfExists('articles');
    }
}
