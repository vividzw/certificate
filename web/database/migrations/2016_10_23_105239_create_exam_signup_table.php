<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamSignupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('exam_signups', function (Blueprint $table) {
			$table->increments('id');
			$table->string('student');
			$table->string('subjects');
			$table->float('pay_fee');
			$table->float('paid_fee');
			$table->integer('score');
			$table->string('pass');
			$table->string('bak');
			$table->integer('schoolterm');
			$table->tinyInteger('status');
			$table->timestamps();

			$table->index('student', 'index_student');
			$table->index('schoolterm', 'index_schoolterm');
			$table->index('status', 'index_status');
		});
    }

    /*public function change() {
		Schema::table('exam_signups', function (Blueprint $table) {
			$table->renameColumn('student_name', 'student');
			$table->renameColumn('subject_name', 'subjects');
			$table->string('subject_cores');
		});
	}*/

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('exam_signups');
    }
}
