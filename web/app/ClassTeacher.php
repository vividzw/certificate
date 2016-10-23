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
		'email',
		'mobile', //手机号 11
		'back',
		'schoolterm',
	];

	public function editable() {
		return array_diff(parent::editable(), ['back']);
	}

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token', 'user'
	];

	public $array = ['classrooms'];
	public $related = [
		'classrooms' => 'ClassRoom',
		'user' => 'User',
	];
	public $readonly = ['name'];

	public static function objectByMobile($mobile) {
		return static::activeWhere('mobile', $mobile)->first();
	}
}
