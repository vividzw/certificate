<?php

namespace App\Http\Controllers;

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

	public function grid(Request $request) {
		return $this->__grid($request, $this->object);
	}

	public function form(Request $request) {
		return $this->__form($request, $this->object);
	}

	public function export(Request $request) {
		$this->__export($request, $this->object);
	}

	private static function class_to_path($object) {
		return str_replace("App\\", "", get_class($object));
	}

	private static function class_to_view_path($object) {
		return preg_replace('/([A-Z]+)/', "_$1", lcfirst(self::class_to_path($object)));
	}

	protected function __grid(Request $request, $object, $path = null, $view = null) {
		$grid = \DataGrid::source($object);  //same source types of DataSet

		$grid->add('id','ID', true)->style("width:100px");
		foreach ($object->editable() as $f) {
			$grid->add($f, trans('comm.' . $f));
		}
		if (!$path) $path = $this->path ?: strtolower(self::class_to_path($object));
		$grid->edit('/' . $path . '/edit', trans('comm.edit'), 'modify|delete'); //shortcut to link DataEdit actions


		$grid->link('/' . $path . '/edit', trans('comm.add'), "TR");  //add button
		$grid->orderBy('id','desc'); //default orderby
		$grid->paginate(10); //pagination

		if (!$view) $view = strtolower(self::class_to_view_path($object));
		return view('admin/' . $view . '/grid', compact('grid'));
	}

	protected function __form(Request $request, $object, $path = null, $view = null) {
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
		if (!$path) $path = $this->path ?: strtolower(self::class_to_path($object));

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

		if (!$view) $view = strtolower(self::class_to_view_path($object));
		return view('admin/' . $view . '/form', compact('form'));
	}

	protected function __export(Request $request, $object) {
		$cellData = [
//			['学号','姓名','成绩'],
//			['10001','AAAAA','99'],
//			['10002','BBBBB','92'],
//			['10003','CCCCC','95'],
//			['10004','DDDDD','89'],
//			['10005','EEEEE','96'],
		];

		$data = [];
		$data[] = "ID";
		foreach ($object->editable() as $k) {
			$data[] = trans("comm.{$k}");
		}
		$cellData[] = $data;
		foreach ($object->activeWhere()->get() as $k => $o) {
			$data = [];
			$data[] = $o->id;
			foreach ($object->editable() as $k) {
				$data[] = $o->{$k};
			}
			$cellData[] = $data;
		}

		\Excel::create(trans('comm.' . strtolower(self::class_to_path($object))), function($excel) use ($cellData) {
			$excel->sheet(trans('comm.list'), function($sheet) use ($cellData) {
				$sheet->rows($cellData);
			});
		})->download('xls');
	}

	protected function __import(Request $request, $object) {
		$filePath = 'storage/exports/'.iconv('UTF-8', 'GBK', '学生成绩').'.xls';
		\Excel::load($filePath, function($reader) {
			$data = $reader->all();
			dd($data);
		});
	}
}
