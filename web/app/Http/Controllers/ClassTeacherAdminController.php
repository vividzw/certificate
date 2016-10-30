<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\ClassRoom;
use App\ClassTeacher;
use App\Student;

class ClassTeacherAdminController extends TermController {
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware(['auth', 'termauth']);
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
		$classroom = $request->get('classroom');
		$grid = \DataGrid::source($classteacher->students($classroom));  //same source types of DataSet
		self::build_grid($request, new Student(), $grid);
		$path = "classroomstudents/";
		$classroom = urlencode($classroom);
		$grid->edit('/' . $path . 'edit/' . $classroom . '/', trans('comm.edit'), 'modify|delete'); //shortcut to link DataEdit actions
		$grid->link('/' . $path . 'edit/' . $classroom . '/', trans('comm.add'), "TR");  //add button
		$grid->link('/' . $path . 'export/' . $classroom . '/tpl', trans('app.excel_template'), "TR");  //add button
		$grid->link('/' . $path . 'export/' . $classroom . '/', trans('app.export'), "TR");  //add button
		$grid->link('/' . $path . 'import/' . $classroom . '/', trans('app.import'), "TR");  //add button
		return view('classteacher/students/grid', compact('grid', 'path'));
	}

	public function students_form(Request $request, $classroom) {
		if (!$this->check_classteacher($classroom)) {
			die("你没有权限");
		}
		$this->object = new Student();
		$key = is_numeric($classroom) ? "id:{$classroom}" : $classroom;
		$classroom = ClassRoom::objectByIdOrName($key);
		$this->object->classroom = "id:{$classroom->id}";
		$path = "classroomstudents/";
		$form = $this->edit_form($request, $this->object, $path, ['classroom']);
		return view('classteacher/students/form', compact('form', 'path'));
	}

	public function student_export(Request $request, $classroom, $template = null) {
		if (!$this->check_classteacher($classroom)) {
			die("你没有权限");
		}
		$key = is_numeric($classroom) ? "id:{$classroom}" : $classroom;
		$classroom = ClassRoom::objectByIdOrName($key);
		$this->object = new Student();
		$source = !$template ? Student::activeWhere('classroom', "id:{$classroom->id}") : null;
		return parent::export($request, $source, $template);
	}

	public function student_import(Request $request, $classroom) {
		if (!$this->check_classteacher($classroom)) {
			die("你没有权限");
		}
		$path = "classroomstudents/";
		$key = is_numeric($classroom) ? "id:{$classroom}" : $classroom;
		$classroom = ClassRoom::objectByIdOrName($key);
		$form = $this->import_form($request, new Student(), $path, $classroom);
		return view('classteacher/students/form', compact('form', 'path'));
	}
}
