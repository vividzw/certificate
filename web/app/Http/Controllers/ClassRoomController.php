<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\ClassRoom;
use App\ExamSignup;

class ClassRoomController extends TermController {
	public function build_grid(Request $request, $object, $grid, $exclude_fields = []) {
		parent::build_grid($request, $object, $grid, $exclude_fields);
		$grid->add('examsign', '报考');
		$grid->row(function ($row) {
			$val = urlencode($row->cell("name")->value);
			$row->cell('examsign')->value = "<a href='examsignup/{$val}'>报考</a>";
		});
	}

	public function exam_students(Request $request, $classroom) {
		$classroom = ClassRoom::cacheUniqueObject($classroom);
		$exam_signups = [];
		foreach ($classroom->students()->get() as $student) {
			if (!$student::checkObject($classroom, $student->classroom)) continue;
			$exam_signup = ExamSignup::activeWhere('student', $student->name)->first();
			if (!$exam_signup) continue;
			$exam_signups[] = $exam_signup;
		}
		$grid = \DataGrid::source($exam_signups);
		parent::build_grid($request, new \App\ExamSignup(), $grid, ['score', 'pass', 'bak']);
		$grid->add('id', '选择')->cell(function($value) {
			return "<input type='checkbox' value='{$value}' class='cellid' name='cellid[]'/>";
		});
		$val = urlencode($classroom->name);
		$exam_signup = current($exam_signups);
		if ($exam_signup && $exam_signup->locked) {
			$grid->link('/classroom/examsignup/' . $val . '/lock', "锁定", "TR", ['disabled' => 'disabled']);  //add button
		} else {
			$grid->link('/classroom/examsignup/' . $val . '/lock', "锁定", "TR");  //add button
		}

		$form = \DataForm::source(new \stdClass());
		$form->hidden('select_students', '')->insertValue(1);
		//$form->submit(trans('comm.examsignup') . "(2)");
		$form->build();
		$path = "classroom/examsignup/{$val}";
		return view('admin/class_room/select_students/grid', compact('grid', 'form', 'path'));
	}

	public function exam_lock(Request $request, $classroom) {
		$classroom = ClassRoom::cacheUniqueObject($classroom);
		$source = $classroom->students();
		foreach ($source->get() as $student) {
			if (!$student::checkObject($classroom, $student->classroom)) continue;
			$exam_signup = ExamSignup::activeWhere('student', $student->name)->first();
			if (!$exam_signup) continue;
			$exam_signup->locked = 1;
			$exam_signup->update();
		}
		$val = urlencode($classroom->name);
		return redirect("/classroom/examsignup/{$val}");
	}
}
