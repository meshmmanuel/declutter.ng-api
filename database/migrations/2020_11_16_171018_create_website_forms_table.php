<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsiteFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('state');
            $table->string('email');
            $table->string('phone');
            $table->string('items_interested');
            $table->string('items_to_sell');
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
        Schema::dropIfExists('website_forms');
    }
}
