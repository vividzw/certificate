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

	public function classroom_list() {
		return $this->array_field_list('classrooms');
	}

	public function students($classroom) {
		$classroom = ClassRoom::cacheUniqueObject($classroom);
		if (!$classroom) die("班级错误");
		if (!in_array($classroom->name, self::current_classrooms())) {
			die("你没有权限");
		}
		return $classroom->students();
	}

	public static function current_classteacher() {
		return ClassTeacher::cacheUniqueObject(\Auth::user()->name);
	}
	public static function check_classteacher($classname = null) {
		$teacher = self::current_classteacher();
		$classrooms = explode("|", $teacher->classrooms);
		if ($classname) {
			if (!is_numeric($classname)) {
				if (in_array($classname, $classrooms)) {
					return $classname;
				}
				$classroom = ClassRoom::objectByIdOrName($classname);
			} else {
				$classroom = ClassRoom::cacheObject($classname);
			}
			foreach ($classrooms as $clr) {
				if (ClassRoom::checkObject($classroom, $clr)) return $clr;
			}
		} else {
			return $classrooms;
		}
		return false;
	}

	public static function current_classrooms() {
		$teacher = self::current_classteacher();
		$classrooms = [];
		foreach($teacher->classroom_list() as $name => $class_room) {
			if (isset($classrooms[$class_room->id])) continue;
			$classrooms[$class_room->name] = $class_room->name;
		}
		return $classrooms;
	}
}
