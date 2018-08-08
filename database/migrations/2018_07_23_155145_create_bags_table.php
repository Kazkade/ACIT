<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('part_id');
            $table->integer('location_id');
            $table->integer('to_total');
            $table->integer('from_total');

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
        //
    }
}
