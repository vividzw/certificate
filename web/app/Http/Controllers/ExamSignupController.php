<?php

namespace App\Http\Controllers;

use App\ClassTeacher;
use App\ProDepartment;
use App\Student;
use App\Subject;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\ClassRoom;
use App\ExamSignup;
use App\Exam;

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
		$path = "examadd/";
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
		if (!$this->check_classteacher($classroom->id)) {
			die("你没有权限");
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
		$form->hidden('select_students', '')->insertValue(1);
		$form->hidden('classroomId', '')->insertValue($classroom->id);
		$form->hidden('subjectId', '')->insertValue($subject->id);
		//$form->submit(trans('comm.examsignup') . "(2)");
		$form->build();
		$path = "examadd/";
		return view('exam_signup/select_students/grid', compact('grid', 'form', 'path'));
	}

	public function signups(Request $request) {
		$classroom = ClassRoom::cacheObject($request->get('classroom'));
		$subject = Subject::cacheObject($request->get('subject'));
		if (!$classroom) {
			die("没有这个班级");
		}
		if (!$this->check_classteacher($classroom->id)) {
			die("你没有权限");
		}
		if (!$subject) {
			die("没有这个科目");
		}
		$sIds = $request->get('cellid');
		if (!$sIds) {
			die('请选择学生');
		}
		$exam_signups = [];
		$students = Student::find($sIds) ?: [];
		foreach ($students as $student) {
			if (!$student::checkObject($classroom, $student->classroom)) continue;
			$exam_signup = ExamSignup::activeWhere('student', $student->name)->first();
			if (!$exam_signup) {
				$exam_signup = new ExamSignup();
				$exam_signup->student = $student->name;
				$exam_signup->pay_fee = $subject->fee;
				$exam_signup->paid_fee = 0;
				$exam_signup->score = -1;
				$exam_signup->pass = "待考";
				$exam_signup->schoolterm = ExamSignup::school_term()->id;
				$exam_signup->appendValue('subjects', $subject->name);
				$exam_signup->locked = 0;
				$exam_signup->save();
				$this->afterSaved($request, $exam_signup);
			} else {
				if ($exam_signup->locked) {
					die("报名已经锁定,你不能再修改,请联系考试中心");
				}
				if ($exam_signup->appendValue('subjects', $subject->name)) {
					$exam_signup->pay_fee += $subject->fee;
				}
				$exam_signup->update();
				$this->afterSaved($request, $exam_signup);
			}
			$exam_signups[$exam_signup->id] = $exam_signup;
			$exam = Exam::activeWhere('student', $student->name)
				->where('subject', $subject->name);
			if (!$exam) {
				$exam = new Exam();
				$exam->student = $student->name;
				$exam->subject = $subject->name;
				$exam->fee = $subject->fee;
				$exam->score = -1;
				$exam->pass = "待考";
				$exam->schoolterm = Exam::school_term()->id;
				$exam->save();
				$this->afterSaved($request, $exam);
			}
		}
		$path = "examadd/";
		$grid = \DataGrid::source($exam_signups);
		self::build_grid($request, new \App\ExamSignup(), $grid, ['score', 'pass', 'bak']);
		/*$grid->add('score', '成绩')->cell(function($value) {
			return $value == -1 ? "待考" : $value;
		});*/
		return view('exam_signup/students/grid', compact('grid', 'path'));
	}

	public function pay(Request $request) {
		$classrooms = ClassTeacher::current_classrooms();
		if (count($classrooms) == 1) {
			$request->merge(["classroom" => current($classrooms)]);
			return $this->pay_students($request);
		}

		$data = new \stdClass();
		$data->classroom = join("|", $classrooms);
		$form = \DataForm::source($data);
		$form->add('classroom', trans("comm.classroom"), 'radiogroup')
			->options($classrooms);
		$form->hidden('select_classroom', '1')->insertValue(1);
		$form->submit(trans('comm.pay') . "(1)", "BL", ['id' => 'examsignup']);
		$path = "examadd/";
		return view('exam_signup/pay/form', compact('form', 'path'));
	}

	protected function pay_students(Request $request) {
		if ($request->get('select_students') == "1") {
			return $this->paid($request);
		}
		$classroom = ClassRoom::cacheUniqueObject($request->get('classroom'));
		if (!$classroom) die("班级错误");
		if (!$this->check_classteacher($classroom->id)) {
			die("你没有权限");
		}
		$students = [];
		foreach ($classroom->students()->get() as $student) {
			$students[] = $student->name;
		}
		$grid = \DataGrid::source(ExamSignup::activeWhere()->whereIn('student', $students));
		self::build_grid($request, new \App\ExamSignup(), $grid, ['score', 'pass', 'bak']);

		$grid->row(function ($row) use ($classroom) {
			$id_cell = $row->cell("id");
			$id = $id_cell->value;
			$paid_cell = $row->cell('paid_fee');
			if ($paid_cell->value < $row->cell('pay_fee')->value) {
				$classroomname = urlencode($classroom->name);
				$paid_cell->value .= " 元已缴 <a href='pay_one?id={$id}&classroom={$classroomname}' class='btn btn-small'>缴费</a>";
				$id_cell->value = "<input type='checkbox' value='{$id}' class='cellid' name='cellid[]'/> {$id}";
			} else {
				$paid_cell->value .= " 元已缴";
				$id_cell->value = " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; " . $id_cell->value;
			}
		});

		/*$grid->add('score', '成绩')->cell(function($value) {
			return $value == -1 ? "待考" : $value;
		});
		$grid->row(function ($row) {
			$row->cell('id')->attributes(['class' => 'cellid']);
		});*/

		$form = \DataForm::source(new \stdClass());
		$form->hidden('select_students', '')->insertValue(1);
		$form->hidden('classroom', '')->insertValue($classroom->name);

		//$form->submit(trans('comm.examsignup') . "(2)");
		$form->build();
		$path = "examadd/";
		return view('exam_signup/pay/grid', compact('grid', 'form', 'path'));
	}
	public function paid(Request $request) {
		if ($request->get('select_classroom') == '1' && $request->get('classroom')) {
			return $this->pay_students($request);
		}
		$classroom = ClassRoom::cacheUniqueObject($request->get('classroom'));
		if (!$classroom) die("班级错误");
		if (!$this->check_classteacher($classroom->id)) {
			die("你没有权限");
		}
		$sIds = $request->get('cellid');
		if (!$sIds) {
			die('请选择学生');
		}
		$exam_signups = ExamSignup::find($sIds) ?: [];
		foreach ($exam_signups as $exam_signup) {
			$student = Student::cacheUniqueObject($exam_signup->student);
			if (!$student::checkObject($classroom, $student->classroom)) continue;
			if ($exam_signup->paid_fee < $exam_signup->pay_fee) {
				$exam_signup->paid_fee = $exam_signup->pay_fee;
				$exam_signup->update();
				$this->afterSaved($request, $exam_signup);
			}
		}
		return redirect('/examadd/pay_students?classroom=' . urlencode($classroom->name));
	}

	public function pay_one(Request $request) {
		$exam_signup = ExamSignup::object($request->get('id'));
		if (!$exam_signup) die('非法访问');
		$classroom = $request->get('classroom');
		if (!$this->check_classteacher($classroom)) {
			die("你没有权限");
		}
		$form = \DataForm::source($exam_signup);
		$exam_signup->readonly = array_merge($exam_signup->readonly, ['pay_fee']);
		$this->build_form($request, $form, $exam_signup, ['score', 'pass', 'bak']);
		$form->submit(trans('comm.save'));
		$form->saved(function() use ($form, $request, $exam_signup, $classroom)
		{
			if ($this->afterSaved($request, $exam_signup)) {
				$form->message(trans('comm.saveok'));
				$form->link("/examadd/pay_students?classroom=" . urlencode($classroom), trans('comm.back'));
			} else {
				$form->message(trans('comm.saveerr'));
			}
		});
		$path = "examadd/";
		return view('exam_signup/pay/pay_one_form', compact('grid', 'form', 'path'));
	}
}
