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
        //create fks
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('collection_id')->references('id')->on('collections');
            $table->foreign('course_id')->references('id')->on('courses');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // remove fks
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign('documents_collection_id_foreign');
            $table->dropForeign('documents_course_id_foreign');
        });
    }
};
