<?php

namespace App\Http\Controllers;

use App\ProDepartment;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProDepartmentController extends Controller
{
	public function grid(Request $request) {
		return TermController::grid($request, new ProDepartment(), 'depart');
	}

	public function form(Request $request) {
		return TermController::form($request, new ProDepartment(), 'depart');
	}

	public function export(Request $request) {
		TermController::export($request, new ProDepartment());
	}
}
