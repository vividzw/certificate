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
			$table->string('classroom');
			$table->string('sex');
			$table->string('idcard');
			$table->string('education');
			$table->string('subjects')->nullable();
			$table->integer('schoolterm');
			$table->tinyInteger('status')->default(1);
			$table->timestamps();

			$table->index('name', 'index_name');
			$table->index('classroom', 'index_classroom');
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
		Schema::drop('students');
	}
}
