<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', //姓名 30
		'classteacher', //班级 50
		'sex', //性别 2
		'idcard', //身份证 18
		'subjects',
	];
}
