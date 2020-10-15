<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_id');
            $table->integer('retries')->default(0);
            $table->decimal('amount', 6, 2);
            $table->text('transaction_id')->nullable();
            $table->boolean('settled')->default(false);
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
        Schema::dropIfExists('refund_transactions');
    }
}
