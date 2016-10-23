<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('exams', function (Blueprint $table) {
			$table->increments('id');
			$table->string('student');
			$table->string('subject');
			$table->integer('score');
			$table->string('pass');
			$table->integer('schoolterm');
			$table->tinyInteger('status');
			$table->timestamps();

			$table->index('student', 'index_student');
			$table->index('subject', 'index_subject');
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
		Schema::drop('exams');
	}
}
