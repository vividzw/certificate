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
		foreach($teacher->classroom_list() as $id => $class_room) {
			if (isset($departs[$class_room->depart])) continue;
			$departs[$class_room->depart] = $class_room->depart;
		}
		foreach ($departs as $depart) {
			$depart = ProDepartment::objectByIdOrName($depart);
			if ($depart) {
				$_subjects = [];
				foreach ($depart->subject_list() as $subject) {
					$_subjects["id:" . $subject->id] = $subject->name;
				}
				$subjects[$depart->name] = $_subjects;
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
		$path = "examadd";
		$subject_names = array_keys($subjects);
		return view('exam_signup/select_subject/form', compact('form', 'path', 'subject_names'));
	}

	public function select_students() {

	}
}
