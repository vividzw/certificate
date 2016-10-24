<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProDepartment extends TermModel
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', //专业部 50
		'subjects', //科目
		'schoolterm',
	];

	public $array = ['subjects'];
	public $related = ['subjects' => 'Subject'];

	public function subject_list() {
		return $this->array_field_list('subjects');
	}
}
