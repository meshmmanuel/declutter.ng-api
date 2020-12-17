<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefectFileTable extends Migration
{
    /**
     * Run the migration to create a pivot table for defected products file.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_file', function (Blueprint $table) {
            $table->integer('file_id')->unsigned()->index();
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade')->onDelete('cascade');
            $table->integer('defect_id')->unsigned()->index();
            $table->foreign('defect_id')->references('id')->on('defects')->onDelete('cascade')->onDelete('cascade');
            $table->primary(['file_id', 'defect_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('defect_file');
    }
}
