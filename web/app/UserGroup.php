<?php

namespace App;

class UserGroup
{

	protected static $super = [
		'Admin' => '/man',
	];
	protected static $admin = [
		'User' => '/user',
		'ClassRoom' => '/classroom',
		'ClassTeacher' => '/classteacher',
		'Student' => '/student',
		'Subject' => '/subject',
		'Department' => '/depart',
	];
	protected static $class_teacher = [
		'Student2' => '/std',
	];
	protected static function routes() {
		return [
			'Super' => array_merge(self::$super, self::$admin, self::$class_teacher),
			'Admin' => array_merge(self::$admin, self::$class_teacher),
			'ClassTeacher' => self::$class_teacher,
		];
	}

	public function access($user) {

	}
}
