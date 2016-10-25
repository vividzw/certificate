<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends TermModel
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', //名称 50
		'depart', //所属专业部 50
		'classteacher',
		'schoolterm',
	];

	public $related = [
		'depart' => 'ProDepartment',
		'classteacher' => 'ClassTeacher'
	];

	public function students() {
		return \App\Student::activeWhere('classroom', "id:{$this->id}");
	}
}
