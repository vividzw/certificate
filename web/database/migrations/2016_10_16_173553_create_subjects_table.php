<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::create('subjects', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('alias');
			$table->float('fee');
			$table->integer('schoolterm');
			$table->tinyInteger('status');
			$table->timestamps();

			$table->index('name', 'index_name');
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
		Schema::drop('subjects');
	}
}
