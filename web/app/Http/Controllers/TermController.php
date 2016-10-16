<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class TermController extends Controller
{
	private static function class_to_path($object) {
		return str_replace("App\\", "", get_class($object));
	}
	private static function class_to_view_path($object) {
		return preg_replace('/([A-Z]+)/', "_$1", lcfirst(self::class_to_path($object)));
	}
	public static function grid(Request $request, $object, $path = null, $view = null) {
		$grid = \DataGrid::source($object);  //same source types of DataSet

		$grid->add('id','ID', true)->style("width:100px");
		foreach ($object->editable() as $f) {
			$grid->add($f, trans('comm.' . $f));
		}
		if (!$path) $path = strtolower(self::class_to_path($object));
		$grid->edit('/' . $path . '/edit', trans('comm.edit'), 'modify|delete'); //shortcut to link DataEdit actions


		$grid->link('/' . $path . '/edit', trans('comm.add'), "TR");  //add button
		$grid->orderBy('id','desc'); //default orderby
		$grid->paginate(10); //pagination

		if (!$view) $view = strtolower(self::class_to_view_path($object));
		return view('admin/' . $view . '/grid', compact('grid'));
	}

	public static function form(Request $request, $object, $path = null, $view = null) {
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
		if (!$path) $path = strtolower(self::class_to_path($object));

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
}
