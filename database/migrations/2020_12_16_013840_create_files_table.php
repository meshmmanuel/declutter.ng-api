<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migration to create a table for files.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->text('information')->nullable()->default(null);
            $table->text('source')->nullable();
            $table->text('thumbnail_source')->nullable();
            $table->text('path')->nullable()->default(null);
            $table->text('thumbnail_path')->nullable()->default(null);
            $table->text('file_type')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
