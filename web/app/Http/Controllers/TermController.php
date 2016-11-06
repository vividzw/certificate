<?php

namespace App\Http\Controllers;

use App\ClassRoom;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\ClassTeacher;

class TermController extends Controller
{
	protected $object = null;
	protected $path = null;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware(['auth', 'termauth']);
		$object_class = "App\\" . $this->classname();
		$this->object = new $object_class;
	}

	private function classname() {
		return str_replace("Controller", "",
			str_replace("App\\Http\\Controllers\\", "", get_class($this))
		);
	}

	private function url_path() {
		return $this->path ? $this->path . "/" : strtolower(self::class_to_path($this->object)) . "/";
	}

	public function check_classteacher($classname = null) {
		return ClassTeacher::check_classteacher($classname);
	}

	public function afterSaved(Request $request, $object) {
		$object::cacheObject($object->id, true);
		if ($object->name) {
			$object::cacheUniqueObject($object->name, true);
		}
		return true;
	}

	public function grid(Request $request) {
		$object = $this->object;
		$grid = \DataGrid::source($object);  //same source types of DataSet
		static::build_grid($request, $object, $grid);
		$path = $this->url_path();
		$grid->edit('/' . $path . 'edit', trans('comm.edit'), 'modify|delete'); //shortcut to link DataEdit actions
		$grid->link('/' . $path . 'edit', trans('comm.add'), "TR");  //add button

		$view = strtolower(self::class_to_view_path($object));
		return view('admin/' . $view . '/grid', compact('grid', 'path'));
	}

	public function build_grid(Request $request, $object, $grid, $exclude_fields = []) {

		$grid->add('id','ID', true)->style("width:100px");
		foreach ($object->editable() as $f) {
			if (in_array($f, $exclude_fields)) continue;
			if (isset($object->related[$f])) {
				$class_name = "App\\" . $object->related[$f];
				if (in_array($f, $object->array)) {
					$grid->add($f, trans('comm.' . $f))->cell(function ($value) use ($class_name) {
						$array = explode("|", $value);
						if ($class_name::$unique) {
							foreach ($array as $i => $v) {
								if ($_v = $class_name::convertValue($v)) {
									$array[$i] = $_v;
								}
							}
						}
						return join(",", $array);
					});
				} else {
					$grid->add($f, trans('comm.' . $f))->cell(function($value) use ($class_name) {
						return $class_name::convertValue($value) ?: $value;
					});
				}
			} else {
				$grid->add($f, trans('comm.' . $f), $f == $object::$unique);
			}
		}

		$grid->orderBy('id','desc'); //default orderby
		$grid->paginate(10); //pagination
	}

	public function form(Request $request) {
		$path = $this->url_path();
		$form = $this->edit_form($request, $this->object, $path);
		$view = strtolower(self::class_to_view_path($this->object));
		return view('admin/' . $view . '/form', compact('form', 'path'));
	}

	public function edit_form(Request $request, $object, $path, $exclude_fields = []) {
		$id = $request->get('modify', $request->get('delete'));
		if ($id && is_numeric($id)) {
			$form = \DataForm::source($object = $object::activeWhere("id", $id)->first());
		} else {
			$form = \DataForm::source($object);
			$form->set('schoolterm', $object::school_term()->id);
		}
		$this->build_form($request, $form, $object, $exclude_fields);

		$readonly_reset = function () use ($request, $form, $object) {
			if (!$request->get('save')) return;
			foreach($object->readonly as $f) {
				$form->set($f, $object->{$f});
			}
		};
		if (!$id || $request->get('modify')) {
			$form->submit(trans('comm.save'));
			$readonly_reset();
		} else {
			$readonly_reset();
			$form->set('status', '0');
			$form->submit(trans('comm.delete'));
		}
		$form->saved(function() use ($form, $request, $object, $path)
		{
			if ($this->afterSaved($request, $object)) {
				$form->message(trans('comm.saveok'));
				$form->link("/$path", trans('comm.back'));
			} else {
				$form->message(trans('comm.saveerr'));
			}
		});
		return $form;
	}

	public function build_form(Request $request, $form, $object, $exclude_fields = []) {
		//add fields to the form
		foreach ($object->editable() as $f) {
			if (in_array($f, $exclude_fields)) continue;
			$cell = null;
			if (isset($object->related[$f]) && !in_array($f, $object->related_text)) {
				$class_name = "App\\" . $object->related[$f];
				if (in_array($f, $object->array)) {
					$cell = $form->add($f, trans('comm.' . $f), 'checkboxgroup')
						->options($class_name::selectObjects());
					$arr = [];
					foreach (explode("|", $form->model->$f) as $_id) {
						if (strpos($_id, "id:") === 0) {
							$arr[] = $_id;
							continue;
						}
						$arr[] = $class_name::convertIdOrName($_id);
					}
					$form->model->$f = join("|", $arr);
				} else {
					$cell = $form->add($f, trans('comm.' . $f),'select')
						->options($class_name::selectObjects());
					if (strpos($form->model->$f, "id:") !== 0) {
						$form->model->$f = $class_name::convertIdOrName($form->model->$f);
					}
				}
				if (in_array($f, $object->readonly) && $form->model->$f) {
					$cell->attributes(['disabled' => 'disabled']);
				}
			} else {
				if (isset($object->initData[$f])) {
					if (is_array($object->initData[$f])) {
						$cell = $form->add($f, trans('comm.' . $f),'select')
							->options($object->initData[$f]);
						if (in_array($f, $object->readonly) && $form->model->$f) {
							$cell->attributes(['disabled' => 'disabled']);
						}
					} else {
						$form->model->$f = $object->initData[$f];
						$cell = $form->add($f, trans('comm.' . $f), 'text')->rule('required');
					}
				} else {
					$cell = $form->add($f, trans('comm.' . $f), 'text')->rule('required');
				}
			}
			if ($cell && in_array($f, $object->readonly) && $form->model->$f) {
				$cell->attributes(['readonly' => 'readonly']);
			}
		}
	}

	public function export(Request $request, $source = null, $template = null) {
		$object = $this->object;
		$cellData = [
//			['学号','姓名','成绩'],
//			['10001','AAAAA','99'],
//			['10002','BBBBB','92'],
//			['10003','CCCCC','95'],
//			['10004','DDDDD','89'],
//			['10005','EEEEE','96'],
		];

		$data0 = [];
		$data1 = [];
		if (!$template) {
			$data0[] = "ID";
			$data1[] = "索引号";
		}
		foreach ($object->editable() as $k) {
			$data0[] = $k;
			$data1[] = trans("comm.{$k}");
		}
		$cellData[] = $data0;
		$cellData[] = $data1;
		if (!$template) {
			if (!$source) $source = $object->activeWhere();
			foreach ($source->get() as $k => $o) {
				$data = [];
				$data[] = $o->id;
				foreach ($object->editable() as $k) {
					if (isset($object->related[$k])) {
						$sub_class_name = "App\\" . $object->related[$k];
						if (in_array($k, $object->array)) {
							$arr = [];
							foreach (explode("|", $o->{$k}) as $_id) {
								$arr[] = $sub_class_name::convertIdOrName($_id, false);
							}
							$data[] = join(",", $arr);
						} else {
							$data[] = $sub_class_name::convertIdOrName($o->{$k}, false);
						}
					} else {
						$data[] = $o->{$k};
					}
				}
				$cellData[] = $data;
			}
		}

		$excel_name = self::class_to_path($object);
		if ($template) {
			$excel_name .= "_Template";
		}
		\Excel::create($excel_name, function($excel) use ($cellData, $object) {
			$sheet_name = trans('comm.' . strtolower(self::class_to_path($object))) . trans('comm.list');
			$excel->sheet($sheet_name, function($sheet) use ($cellData) {
				$sheet->rows($cellData);
			});
		})->download('xls');
	}

	public function import(Request $request) {
		$path = $this->url_path();
		$form = $this->import_form($request, $this->object, $path);
		$view = strtolower(self::class_to_view_path($this->object));
		return view('admin/' . $view . '/form', compact('form', 'path'));
	}

	public function import_form(Request $request, $object, $path, $relateObj = null) {
		$form = \DataForm::source($object);

		$filename = (date('YmdHis')) . '.xls';
		$form->add('excel', trans('comm.import_file'), 'file')
			//->rule('mimes:xls')
			->move('storage/xls/' . $path, $filename);

		$form->submit(trans('app.import'));

		$form->saved(function() use ($form, $path, $filename, $object, $relateObj)
		{
			$filepath = 'storage/xls/' . $path . $filename;
			if (file_exists($filepath)) {
				$message = "";
				\Excel::load($filepath, function ($reader) use ($filepath, $object, $relateObj, &$message) {
					$relatedClassName = $relateObj ? get_class($relateObj) : null;
					$results = $reader->all();
					$sheet_name = trans('comm.' . strtolower(self::class_to_path($object))) . trans('comm.list');
					if ($results->getTitle() == $sheet_name) {
						$class_name = get_class($object);
						foreach($results as $i => $item) {
							if ($i == 0) continue;
							$data = [];
							foreach ($item as $k => $v) {
								$data[$k] = $v;
							}
							$obj = null;
							if (isset($data['id']) && $data['id']) {
								$obj = $class_name::object($data['id']);
							}
							if ($obj) {
								//如果有ID,并且有唯一标识,这个标识不允许修改,避免混乱
								if ($class_name::$unique) {
									unset($data[$class_name::$unique]);
								}
							} elseif ($class_name::$unique && isset($data[$class_name::$unique]) && $data[$class_name::$unique]) {
								$obj = $class_name::uniqueObject($data[$class_name::$unique]);
							}
							if (!$obj) $obj = new $class_name;
							unset($data['id']);
							foreach($data as $k => $v) {
								if (isset($obj->related[$k])) {
									$sub_class_name = "App\\" . $obj->related[$k];
									if (in_array($k, $obj->array)) {
										$arr = [];
										foreach (preg_split("/(,|，)/", $v) as $_id) {
											$arr[] = $sub_class_name::convertIdOrName($_id, true);
										}
										$obj->$k = join("|", $arr);
									} else {
										$obj->$k = $sub_class_name::convertIdOrName($v, true);
									}
									if ($relatedClassName && $relatedClassName == $sub_class_name) {
										if ($obj->$k != "id:{$relateObj->id}") {
											$objName = ($class_name::$unique ? $obj->{$class_name::$unique} : "") . "({$obj->id})";
											$message .= "<br />项: " . $objName . " 没有导入, 可能输入错误或没有权限";
											continue 2;
										}
									}
								} else {
									$obj->$k = $v;
								}
							}
							if ($obj->id) {
								$obj->update();
							} else {
								$hasData = false;
								foreach ($data as $dk => $dv) {
									$dv = trim($dv);
									if ($dv && !$hasData) {
										$hasData = true;
									}
									$data[$dk] = $data;
								}
								if ($hasData) {
									$data['schoolterm'] = $obj::school_term()->id;
									$obj = $class_name::create($data);
								}
							}
							if ($obj->id) $obj::cacheObject($obj->id, true);
						}
					}
					unlink($filepath);
				});

				$form->message(trans('comm.importok') . "<span class='red'>" . $message . "</span>");
				$form->link("/$path", trans('comm.back'));
			} else {
				$form->message(trans('comm.importerr'));
				$form->link("/{$path}import", trans('comm.back'));
			}
		});
		return $form;
	}

	private static function class_to_path($object) {
		return str_replace("App\\", "", get_class($object));
	}

	private static function class_to_view_path($object) {
		return preg_replace('/([A-Z]+)/', "_$1", lcfirst(self::class_to_path($object)));
	}
}
