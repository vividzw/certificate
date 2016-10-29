<?php

namespace App\Http\Controllers;

use App\ClassTeacher;
use App\Student;
use Illuminate\Http\Request;

use App\Http\Requests;
use Validator;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Hash;

class ClassTeacherController extends TermController {

	use AuthenticatesAndRegistersUsers, ThrottlesLogins;

	public function afterSaved(Request $request, $object) {

		$user = \App\User::where('name', $request->get('name'))->first();
		if (!$user) {
			if (!$request->get('password')) {
				$request->merge([
					'password' => str_random(8),
				]);
			}
			$this->create($request->all());
		} else if ($user->email != $request->get('email')) {
			$user->email = $request->get('email');
			$user->update();
		}
		return parent::afterSaved($request, $object);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	protected function create(array $data)
	{
		return User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
		]);
	}

	public function students(Request $request) {
		if ($request->get('select_classroom') && $request->get('classroom')) {
			return $this->classroom_students($request);
		}
		$classrooms = ClassTeacher::current_classrooms();
		if (count($classrooms) == 1) {
			$request->merge(["classroom" => current($classrooms)]);
			return $this->classroom_students($request);
		}
		$data = new \stdClass();
		$data->classroom = join("|", $classrooms);
		$form = \DataForm::source($data);
		$form->add('classroom', trans("comm.classroom"), 'radiogroup')
			->options($classrooms);
		$form->hidden('select_classroom', '1')->insertValue(1);
		$form->submit(trans('comm.save') . "(1)", "BL", ['id' => 'classroom_student']);
		$path = "classroomstudents/";
		return view('classteacher/students/form', compact('form', 'path'));
	}

	public function classroom_students(Request $request) {
		$classteacher = ClassTeacher::current_classteacher();
		$grid = \DataGrid::source($classteacher->students($request->get('classroom')));  //same source types of DataSet
		self::build_grid($request, new Student(), $grid);
		$path = "classroomstudents/";
		$grid->edit('/' . $path . 'edit/', trans('comm.edit'), 'modify|delete'); //shortcut to link DataEdit actions
		$grid->link('/' . $path . 'edit/', trans('comm.add'), "TR");  //add button
		return view('classteacher/students/grid', compact('grid', 'path'));
	}

	public function students_form(Request $request) {
		die("coming soon");
	}
}
