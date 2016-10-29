<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamSignup extends TermModel
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'student', //名称 50
		'subjects', //对应科目信息表之科目字段名
		'pay_fee', //应交科目收费
		'paid_fee', //已交科目收费
		'score', //成绩
		'pass', //合格?
		'bak',  //备注
		'locked', //锁定,只能查看,不能修改
		'schoolterm',
	];

	public $array = ['subjects'];
	public $related = [
		'student' => 'Student',
		'subjects' => 'Subject',
	];

	public $related_text = [
		'student', 'subjects',
	];
	public $readonly = [
		'student', 'subjects',
	];

	public $initData = [
		'pass' => [
			'待考' => '待考',
			'合格' => '合格',
			'良好' => '良好',
			'优秀' => '优秀',
			'缺考' => '缺考',
		],
	];

	public function editable() {
		return array_diff($this->fillable, ['schoolterm', 'locked']);
	}
}
