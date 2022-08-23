<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('orders', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('user_id')->nullable();
        //     $table->integer('category_id')->nullable();
        //     $table->string('product_name')->nullable();
        //     $table->integer('product_quantity')->nullable();
        //     $table->string('status')->default('انتظار');
        //     $table->float('total_price')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
