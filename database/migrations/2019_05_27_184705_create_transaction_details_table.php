<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('item_id')->nullable()
                ->comment('Will not remove the entry when the item is deleted.');
            $table->unsignedInteger('quantity')->default(1);

            $table->foreign('transaction_id')->references('id')->on('transactions')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('item_id')->references('id')->on('items')
                ->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_details');
    }
}
