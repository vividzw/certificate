<?php

namespace App\Http\Controllers;

use App\SchoolTerm;
use App\TermModel;
use Illuminate\Http\Request;

use App\Http\Requests;

class SchoolTermController extends TermController
{

	public function form(Request $request) {
		if ($request->get('add')) {
			$id = null;
		} else {
			$id = TermModel::school_term()->id;
		}
		if ($id && is_numeric($id)) {
			$form = \DataForm::source(SchoolTerm::where("id", $id)->first());
		} else {
			$form = \DataForm::source(new SchoolTerm());
		}

		//add fields to the form
		$form->add('name', trans('comm.name'), 'text')->rule('required'); //validation //field name, label, type
		$form->add('date', trans('comm.term_date'), 'text')->rule('required');; //field name, label, type

		if ($id != null) {
			$form->submit(trans('comm.save'));
			if (!$request->get('save')) {
				$form->link("/term?add=1", trans('comm.add'));
			}
		} else {
			if ($request->get('add') == "1") {
				$form->message(trans('comm.schoolterm_add_alert'));
				$form->link("/term?add=2", trans('comm.addok'));
			} else {
				$form->submit(trans('comm.save'));
			}
			if (!$request->get('save')) {
				$form->link("/term", trans('comm.addcancel'));
			}
		}

		$form->saved(function() use ($form, $id)
		{
			$schoolterm = SchoolTerm::current_term();
			$schoolterm->cache_put();
			$form->message(trans('comm.saveok'));
			$form->link("/term", trans('comm.back'));
		});

		$path = "";
		return view('admin/school_term/form', compact('form', 'path'));
	}
}
