<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassTearchersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('class_teachers', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('classrooms');
			$table->string('mobile');
			$table->string('back')->nullable();
			$table->string('password');
			$table->integer('schoolterm');
			$table->tinyInteger('status');
			$table->rememberToken();
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
		Schema::drop('class_teachers');
	}
}
