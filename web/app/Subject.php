<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends TermModel
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', //名称 50
		'alias', //对应科目信息表之科目字段名
		'fee', //科目收费
		'schoolterm',
	];
}
