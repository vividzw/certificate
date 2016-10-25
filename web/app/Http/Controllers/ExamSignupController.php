<?php

namespace App\Http\Controllers;

use App\ClassTeacher;
use App\ProDepartment;
use App\Subject;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\ClassRoom;

class ExamSignupController extends TermController {
	public function select_subject(Request $request) {
		$teacher = ClassTeacher::cacheUniqueObject(\Auth::user()->name);
		$subjects = [];
		$departs = [];
		foreach($teacher->classroom_list() as $name => $class_room) {
			if (isset($departs[$class_room->name])) continue;
			$departs[$class_room->name] = $class_room->depart;
		}
		foreach ($departs as $classroom => $depart) {
			$depart = ProDepartment::objectByIdOrName($depart);
			if ($depart) {
				$_subjects = [];
				foreach ($depart->subject_list() as $subject) {
					$_subjects["id:" . $subject->id] = $subject->name;
				}
				$subjects[$classroom] = $_subjects;
			}
		}

		$data = new \stdClass();
		foreach ($subjects as $name => $ss) {
			$data->{$name} = $ss;
		}
		$form = \DataForm::source($data);
		foreach ($subjects as $name => $ss) {
			$form->add($name, $name, 'radiogroup')
				->options($ss);
		}
		$form->submit(trans('comm.examsignup') . "(1)", "BL", ['id' => 'examsignup']);
		$path = "examadd";
		$subject_names = array_keys($subjects);
		return view('exam_signup/select_subject/form', compact('form', 'path', 'subject_names'));
	}

	public function select_students(Request $request) {
		if ($request->get('select_students') == "1") {
			return $this->signups($request);
		}
		$classroom = new ClassRoom();
		$subject = null;
		foreach ($request->input() as $k => $v) {
			if (in_array($k, ['_token', 'save', 'process'])) continue;
			$classroom = \App\ClassRoom::cacheUniqueObject($k);
			if ($classroom) {
				$depart = \App\ProDepartment::objectByIdOrName($classroom->depart);
				if ($depart) {
					$subject = \App\Subject::cacheObject(\App\Subject::id_parse($v));
					$subjects = $depart->subject_list();
					if (!isset($subjects[$subject->name])) {
						$subject = null;
					}
				}
			}
		}
		if (!$classroom->id) {
			die("没有这个班级");
		}
		if (!$subject) {
			die("没有这个科目或此班级所在专业部没有符合条件的专业");
		}

		$grid = \DataGrid::source($classroom->students());
		self::build_grid($request, new \App\Student(), $grid);
		$grid->add('id', '选择')->cell(function($value) {
			return "<input type='checkbox' value='{$value}' class='cellid' name='cellid[]'/>";
		});
		/*$grid->row(function ($row) {
			$row->cell('id')->attributes(['class' => 'cellid']);
		});*/

		$form = \DataForm::source(new \stdClass());
		//$form->set('select_students', '1');
		//$form->submit(trans('comm.examsignup') . "(2)");
		$form->build();
		$path = "examadd";
		return view('exam_signup/select_students/grid', compact('grid', 'form', 'path'));
	}

	public function signups(Request $request) {
		var_dump($request->get('cellid'));
	}
}
