<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;

use App\Http\Requests;

class StudentController extends Controller
{
	public function grid(Request $request) {
		return TermController::grid($request, new Student());
	}

	public function form(Request $request) {
		return TermController::form($request, new Student());
	}
}
