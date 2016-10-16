<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProDepartmentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pro_departments', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('subjects');
			$table->integer('schoolterm');
			$table->tinyInteger('status');
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
		Schema::drop('pro_departments');
	}
}
