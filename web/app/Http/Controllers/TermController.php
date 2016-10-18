<?php

namespace App\Http\Controllers;

use App\ClassRoom;
use Illuminate\Http\Request;

use App\Http\Requests;

class TermController extends Controller
{
	private $object = null;
	protected $path = null;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
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

	public function grid(Request $request) {
		$object = $this->object;
		$grid = \DataGrid::source($object);  //same source types of DataSet

		$grid->add('id','ID', true)->style("width:100px");
		foreach ($object->editable() as $f) {
			if (isset($object->related[$f])) {
				//TODO 检查数据
				$class_name = $object->related[$f];
				$grid->add($f, trans('comm.' . $f));
			} else {
				$grid->add($f, trans('comm.' . $f));
			}
		}
		$path = $this->url_path();

		$grid->edit('/' . $path . 'edit', trans('comm.edit'), 'modify|delete'); //shortcut to link DataEdit actions


		$grid->link('/' . $path . 'edit', trans('comm.add'), "TR");  //add button
		$grid->orderBy('id','desc'); //default orderby
		$grid->paginate(10); //pagination
		$view = strtolower(self::class_to_view_path($object));
		return view('admin/' . $view . '/grid', compact('grid', 'path'));
	}

	public function form(Request $request) {
		$object = $this->object;
		$id = $request->get('modify', $request->get('delete'));
		if ($id && is_numeric($id)) {
			$form = \DataForm::source($object::activeWhere("id", $id)->first());
		} else {
			$form = \DataForm::source(new $object());
			$form->set('schoolterm', $object::school_term()->id);
		}

		//add fields to the form
		foreach ($object->editable() as $f) {
			$form->add($f, trans('comm.' . $f), 'text')->rule('required');
		}

		$path = $this->url_path();

		if (!$id || $request->get('modify')) {
			$form->submit(trans('comm.save'));
		} else {
			$form->set('status', '0');
			$form->submit(trans('comm.delete'));
		}
		$form->saved(function() use ($form, $path)
		{
			$form->message(trans('comm.saveok'));
			$form->link("/$path", trans('comm.back'));
		});

		$view = strtolower(self::class_to_view_path($object));
		return view('admin/' . $view . '/form', compact('form', 'path'));
	}

	public function export(Request $request, $template = null) {
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
			foreach ($object->activeWhere()->get() as $k => $o) {
				$data = [];
				$data[] = $o->id;
				foreach ($object->editable() as $k) {
					$data[] = $o->{$k};
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
		$object = new static();
		$form = \DataForm::source($object);

		$path = $this->url_path();

		$filename = (date('YmdHis')) . '.xls';
		$form->add('excel', trans('comm.import_file'), 'file')
			//->rule('mimes:xls')
			->move('storage/xls/' . $path, $filename);

		$form->submit(trans('app.import'));

		$form->saved(function() use ($form, $path, $filename)
		{
			$filepath = 'storage/xls/' . $path . $filename;
			if (file_exists($filepath)) {
				\Excel::load($filepath, function ($reader) use ($filepath) {
					$results = $reader->all();
					$sheet_name = trans('comm.' . strtolower(self::class_to_path($this->object))) . trans('comm.list');
					if ($results->getTitle() == $sheet_name) {
						$class_name = get_class($this->object);
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
								$obj->$k = $v;
							}
							if ($obj->id) {
								$obj->update();
							} else {
								$data['schoolterm'] = $obj::school_term()->id;
								$class_name::create($data);
							}
						}
					}
					unlink($filepath);
				});

				$form->message(trans('comm.importok'));
				$form->link("/$path", trans('comm.back'));
			} else {
				$form->message(trans('comm.importerr'));
				$form->link("/{$path}import", trans('comm.back'));
			}
		});

		$view = strtolower(self::class_to_view_path($this->object));
		return view('admin/' . $view . '/form', compact('form', 'path'));
	}

	private static function class_to_path($object) {
		return str_replace("App\\", "", get_class($object));
	}

	private static function class_to_view_path($object) {
		return preg_replace('/([A-Z]+)/', "_$1", lcfirst(self::class_to_path($object)));
	}
}
