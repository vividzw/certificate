<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
	use CacheModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'role',
    ];

	public function admin() {
		return $this->role == "SuperMan";
	}

	public function exam_admin() {
		return $this->admin() || $this->role == "ExamAdmin";
	}

	public function class_teacher() {
		return $this->exam_admin() || $this->role == "ClassTeacher";
	}
	public function student() {
		return $this->class_teacher() || $this->role == "Student";
	}

	public static function object($id) {
		return static::where('id', $id)->first();
	}

	public static function uniqueObject($uniqueValue) {
		return static::where('name', $uniqueValue)->first();
	}

	public static function validRole(Request $request, $user) {
		$user = static::cacheObject($user->id);
		if (!$user) return false;
		$get_path = function ($path) {
			if (preg_match('/^([a-z]*)\/?.*$/', $path, $matches)) {
				return $matches[1];
			}
			return false;
		};
		$path = $get_path($request->path());
		if ($user->admin()) return true;
		if ($user->exam_admin()) {
			if ($path == 'user') return false;
		}
		if ($user->class_teacher()) {
			if (in_array($path, ['examreport', 'examadd', 'classroomstudents'])){
				return true;
			}
			return false;
		}
		return true;
	}

	private static $guard_user = null;
	public static function checkRole($name, $path = null) {
		if (!self::$guard_user) {
			self::$guard_user = Auth::guard()->user();
		}
		$user = self::$guard_user;
		if (in_array($path, ["classroomstudents/"])) return false;
		if ($user->admin()) {
			return in_array($name, ['admin', 'exam_admin', 'classteacher']);
		}
		if ($user->exam_admin()) {
			return in_array($name, ['exam_admin', 'classteacher']);
		}
		if ($user->class_teacher()) {
			return $name == 'classteacher';
		}
	}
}
