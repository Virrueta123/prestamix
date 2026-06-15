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
        Schema::table('cronograma', function (Blueprint $table) {
            $table->char("yes_reprogramacion_duplicate", 1)->default("N")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cronograma', function (Blueprint $table) {
            $table->dropColumn('yes_reprogramacion_duplicate');
        });
    }
};
