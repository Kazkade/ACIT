<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('part_name', 255);
            $table->string('part_serial', 255);
            $table->integer('print_time');
            $table->integer('recommended_bagging');
            $table->string('part_version');
            $table->float('part_weight');
            $table->integer('part_quantity');
            $table->integer('part_cleaned');

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
        Schema::drop('parts');
    }
}
