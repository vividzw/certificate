<?php

namespace App;

trait CacheModel
{
	private static function cacheKey($field, $key) {
		return static::class . "::" . $field . "::" . $key;
	}
	private static $caches = [];
	private static function __cache($field, $key, $update) {
		$cacheKey = static::cacheKey($field, $key);
		if (!$update) {
			if (isset(self::$caches[$cacheKey])) {
				return self::$caches[$cacheKey];
			}
			$srObj = \Cache::store('redis')->get($cacheKey);
			if ($srObj) {
				$obj = unserialize($srObj);
				$obj->cached = true;
				self::$caches[$cacheKey] = $obj;
				return $obj;
			}
		}
		$obj = ($field == "id") ? static::object($key) : static::uniqueObject($key);
		self::$caches[$cacheKey] = $obj;
		if ($obj) {
			\Cache::store('redis')->put($cacheKey, serialize($obj), 86400 * 30);
			if ($update) {
				if ($field != "id") {
					if (static::$unique) {
						\Cache::store('redis')->put(
							static::cacheKey(static::$unique, $obj->{static::$unique}),
							serialize($obj), 86400 * 30);
					}
				} else {
					\Cache::store('redis')->put(
						static::cacheKey("id", $obj->id),
						serialize($obj), 86400 * 30);
				}
			}
		}
		return $obj;
	}

	public static function cacheObject($id, $update = false) {
		return $id ? static::__cache('id', $id, $update) : null;
	}

	public static function cacheUniqueObject($name, $update = null) {
		return $name && static::$unique ? static::__cache(static::$unique, $name, $update) : null;
	}
}
