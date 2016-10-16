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

}
