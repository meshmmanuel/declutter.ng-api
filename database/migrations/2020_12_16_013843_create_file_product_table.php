<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileProductTable extends Migration
{
    /**
     * Run the migration to create a pivot table for product's file.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_product', function (Blueprint $table) {
            $table->integer('file_id')->unsigned()->index();
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade')->onDelete('cascade');
            $table->integer('product_id')->unsigned()->index();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onDelete('cascade');
            $table->primary(['file_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_product');
    }
}
