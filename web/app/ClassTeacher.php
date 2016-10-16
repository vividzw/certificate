<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassTeacher extends TermModel
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', //姓名 30
		'classrooms', //班级 50
		'mobile', //手机号 11
		'back',
		'password',
		'schoolterm',
	];

	public function editable() {
		return array_diff(parent::editable(), ['back', 'password']);
	}

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];
}
