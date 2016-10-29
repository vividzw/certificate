<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends TermModel
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'student', //名称 50
		'subject', //对应科目信息表之科目字段名
		'fee',
		'score', //成绩
		'pass', //合格?
		'schoolterm',
	];

	public $related = [
		'student' => 'Student',
		'subject' => 'Subject',
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
}
