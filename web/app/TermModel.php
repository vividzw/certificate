<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermModel extends Model
{
	public static $unique = "name";
	public $related = [];

	public function newQuery() {
		$query = parent::newQuery();
		$query->where('status', 1)
			->where('schoolterm', static::school_term()->id);
		return $query;
	}

	public static function activeWhere($key = null, $opt = null, $val = null) {
		if (is_null($val)) {
			$val = $opt;
			$opt = "=";
		}
		if (is_null($key) || is_null($val)) {
			return static::where('status', 1)
				->where('schoolterm', static::school_term()->id);
		} else {
			return static::where('status', 1)
				->where('schoolterm', static::school_term()->id)
				->where($key, $opt, $val);
		}
	}

	public static function object($id) {
		return static::activeWhere('id', $id)->first();
	}

	public static function uniqueObject($uniqueValue) {
		if (!static::$unique) return null;
		return static::activeWhere(static::$unique, $uniqueValue)->first();
	}

	private static $schoolterm;
	protected static function school_term() {
		if (self::$schoolterm) return self::$schoolterm;
		$schoolterm = (new SchoolTerm())->cache_get();
		if (!$schoolterm) {
			$schoolterm = SchoolTerm::current_term();
			if (!$schoolterm) {
				$schoolterm = new SchoolTerm();
				$schoolterm->id = 1;
			}
			$schoolterm->cache_put();
			return self::$schoolterm = $schoolterm;
		} else {
			return self::$schoolterm = unserialize($schoolterm);
		}
	}

	public function editable() {
		return array_diff($this->fillable, ['schoolterm']);
	}

	public static function cache($classname, $name) {

	}
}
