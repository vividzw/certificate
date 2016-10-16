<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('students', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('classteacher');
			$table->string('sex');
			$table->string('idcard');
			$table->string('subjects')->nullable();
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
		Schema::drop('students');
	}
}
