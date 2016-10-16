<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolTerm extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', //名称 50
		'date', //学期年月
	];

	public static function current_term() {
		return static::where('id', '>', 0)->orderBy('id', 'desc')->first();
	}

	public function cache_put() {
		\Cache::store('memcached')->put('schoolterm', serialize($this), 3600);
	}

	public function cache_get() {
		return \Cache::store('memcached')->get('schoolterm');
	}
}
