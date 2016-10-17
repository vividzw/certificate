<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Subject;

class SubjectController extends Controller
{
	public function grid(Request $request) {
		return TermController::grid($request, new Subject());
	}

	public function form(Request $request) {
		return TermController::form($request, new Subject());
	}

	public function export(Request $request) {
		TermController::export($request, new Subject());
	}

    public function gridx() {
		$grid = \DataGrid::source(Subject::with('name'));  //same source types of DataSet

		$grid->add('name', trans('comm.name'), true); //field name, label, sortable
		$grid->add('alias', trans('comm.title')); //relation.fieldname
		//$grid->add('{{ substr($body,0,20) }}...','Body'); //blade syntax with main field
		//$grid->add('{{ $author->firstname }}','Author'); //blade syntax with related field
		//$grid->add('body|strip_tags|substr[0,20]','Body'); //filter (similar to twig syntax)
		//$grid->add('body','Body')->filter('strip_tags|substr[0,20]'); //another way to filter
		$grid->edit('/subject/edit', 'Edit','modify|delete'); //shortcut to link DataEdit actions

		//cell closure
//		$grid->add('revision','Revision')->cell( function( $value, $row) {
//			return ($value != '') ? "rev.{$value}" : "no revisions for art. {$row->id}";
//		});
//
//		//row closure
//		$grid->row(function ($row) {
//			if ($row->cell('public')->value < 1) {
//				$row->cell('title')->style("color:Gray");
//				$row->style("background-color:#CCFF66");
//			}
//		});

		$grid->link('/subject/edit',"Add New", "TR");  //add button
		$grid->orderBy('id','desc'); //default orderby
		$grid->paginate(10); //pagination

		return view('admin/subject', compact('grid'));
	}

	public function formx() {
		//start with empty form to create new Article
		$form = \DataForm::source(new Article);

		//or find a record to update some value
		$form = \DataForm::source(Article::find(1));

		//add fields to the form
		$form->add('title','Title', 'text'); //field name, label, type
		$form->add('body','Body', 'textarea')->rule('required'); //validation

		//some enhanced field (images, wysiwyg, autocomplete, maps, etc..):
		$form->add('photo','Photo', 'image')->move('uploads/images/')->preview(80,80);
		$form->add('body','Body', 'redactor'); //wysiwyg editor
		$form->add('author.name','Author','autocomplete')->search(['firstname','lastname']);
		$form->add('categories.name','Categories','tags'); //tags field
		$form->add('map','Position','map')->latlon('latitude','longitude'); //google map


		//you can also use now the smart syntax for all fields:
		$form->text('title','Title'); //field name, label
		$form->textarea('body','Body')->rule('required'); //validation

		//change form orientation
		$form->attributes(['class'=>'form-inline']);


		$form->submit('Save');
		$form->saved(function() use ($form)
		{
			$form->message("ok record saved");
			$form->link("/another/url","Next Step");
		});

		return view('admin/subject_form', compact('form'));
	}

	public function FilterGrid() {
		$filter = \DataFilter::source(new Article);

		//simple like
		$filter->add('title','Title', 'text');

		//simple where with exact match
		$filter->add('id', 'ID', 'text')->clause('where')->operator('=');

		//custom query scope, you can define the query logic in your model
		$filter->add('search','Search text', 'text')->scope('myscope');

		//cool deep "whereHas" (you must use DeepHasScope trait bundled on your model)
		//this can build a where on a very deep relation.field
		$filter->add('search','Search text', 'text')->scope('hasRel','relation.relation.field');

		//closure query scope, you can define on the fly the where
		$filter->add('search','Search text', 'text')->scope( function ($query, $value) {
			return $query->whereIn('field', ["1","3",$value]);
		});

		$filter->submit('search');
		$filter->reset('reset');

		$grid = \DataGrid::source($filter);
		$grid->add('nome','Title', true);
		$grid->add('{{ substr($body,0,20) }}...','Body');
		$grid->paginate(10);

		return view('admin/subject_search', compact('filter', 'grid'));
	}
}
