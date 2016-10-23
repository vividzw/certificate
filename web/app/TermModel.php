<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermModel extends Model
{
	use CacheModel;

	public static $unique = "name";
	public $array = [];
	public $related = [];
	public $related_text = [];
	public $readonly = [];
	public $initData = [];

	protected $extend_fields = [];
	public function extend_fields() { return []; }
	public function extend_related() { return []; }
	public function extend_object($name) {
		$class = "App\\" . $name;
		$field = strtolower($name);
		return $class::cacheUniqueObject($this->{$field});
	}

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

	public static function selectObjects() {
		$values = [];
		foreach(static::activeWhere()
			->lists('id', static::$unique)->all() as $k => $id) {
			$values["id:{$id}"] = $k;
		}
		return $values;
	}

	public static function convertValue($v) {
		if (strpos($v, "id:") === 0) {
			$id = intval(substr($v, 3));
			$o = static::cacheObject($id);
			return $o ? $o->{static::$unique} : "<" . $v . ">";
		} else {
			$o = static::cacheUniqueObject($v);
		}
		return !$o ? "<" . $v . ">" : null;
	}

	public static function convertIdOrName($v, $toId = true) {
		if (!static::$unique) return null;
		if (strpos($v, "id:") === 0) {
			$id = intval(substr($v, 3));
			$o = static::cacheObject($id);
		} else {
			$o = static::cacheUniqueObject($v);
		}
		return $o ? ($toId ? "id:" . $o->id : $o->{static::$unique}) : $v;
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

	public static function canRegister() {
		return false;
	}
}
