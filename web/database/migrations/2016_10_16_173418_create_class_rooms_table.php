<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassRoomsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('class_rooms', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('depart');
			$table->string('classroom')->nullable();
			$table->integer('schoolterm');
			$table->tinyInteger('status');
			$table->timestamps();

			$table->index('name', 'index_name');
			$table->index('depart', 'index_depart');
			$table->index('schoolterm', 'index_schoolterm');
			$table->index('status', 'index_status');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('class_rooms');
	}
}
