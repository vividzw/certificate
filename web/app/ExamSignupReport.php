<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamSignupReport extends TermModel
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'student', //名称 50
		'idcard',
		'education',
		'subject_scores', //学科成绩
		'pass', //综合评定:合格?
		'bak',  //备注
		'classroom',
		'schoolterm',
	];

	public $related = [
		'student' => 'Student',
		'classroom' => 'ClassRoom',
	];

	public function extend_fields() {
		if ($this->extend_fields) return $this->extend_fields;
		/**
		 * 比如:
		 * [
		 * 		'C语言' => [
		 * 			'score' => 80,
		 * 			'pass' => '合格',
		 * 		],
		 * 		'操作系统' => [
		 * 			'score' => 90,
		 * 			'pass' => '合格',
		 * 		],
		 * ]
		 */
		$sub_extend_fields = [];
		$exams = \App\ExamSignup::activeWhere('student', $this->student)->get();
		foreach ($exams as $exam) {
			$sub_extend_fields[$exam->subject] = [
				'score' => $exam->score,
				'pass' => $exam->pass,
			];
		}
		return $this->extend_fields = [
			'subject_scores' => $sub_extend_fields,
		];
	}
	public function extend_related() {
		$extend_function = function ($ext_name) {
			list($class, $field) = explode(".", $ext_name);
			return $this->extend_object($class)->{$field};
		};
		return [
			'idcard' => $extend_function('Student.idcard'),
			'education' => $extend_function('Student.education'),
			'pass' => function() {
				$fields = $this->extend_fields();
				if (!isset($fields['subject_scores'])) return "未知";
				foreach ($fields['subject_scores'] as $subject => $exam) {
					if ($exam['score'] < 60) {
						return $exam['pass'];
					}
				}
				return "合格";
			},
			'bak' => function() {
				$fields = $this->extend_fields();
				if (!isset($fields['subject_scores'])) return "未知";
				$baks = [];
				foreach ($fields['subject_scores'] as $subject => $score) {
					if ($score < 60) {
						$baks[] = $subject . "不合格";
					}
				}
				return join(",", $baks);
			},
		];
	}

}
