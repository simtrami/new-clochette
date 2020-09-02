<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('value', 8, 2);
            $table->foreignId('user_id')->nullable()->constrained()
                ->onDelete('set null')->cascadeOnUpdate();
            $table->foreignId('payment_method_id')->nullable()->constrained()
                ->onDelete('set null')->cascadeOnUpdate();
            $table->foreignId('customer_id')->nullable()->constrained()
                ->onDelete('set null')->cascadeOnUpdate();
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
