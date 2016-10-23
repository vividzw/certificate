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
			$table->string('email');
			$table->string('mobile');
			$table->string('back')->nullable();
			$table->string('password');
			$table->integer('schoolterm');
			$table->tinyInteger('status');
			$table->rememberToken();
			$table->timestamps();

			$table->index('name', 'index_name');
			$table->index('email', 'index_email');
			$table->index('mobile', 'index_mobile');
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
		Schema::drop('class_teachers');
	}
}
