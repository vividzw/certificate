<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends TermModel
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', //姓名 30
		'classroom', //班级 50
		'sex', //性别 2
		'idcard', //身份证 18
		'education', //学历
		'subjects',
		'schoolterm',
	];

	public $array = ['subjects'];
	public $related = [
		'classroom' => 'ClassRoom',
		'subjects' => 'Subject'
	];
	public $readonly = ['name'];
	public $initData = [
		'sex' => [
			'男' => '男',
			'女' => '女',
		],
		'education' => [
			'本科' => '本科',
			'专科' => '专科',
			'中专' => '中专',
			'高中' => '高中',
		],
	];
}
