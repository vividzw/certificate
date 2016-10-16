<?php

namespace App\Http\Controllers;

use App\ClassTeacher;
use Illuminate\Http\Request;

use App\Http\Requests;

class ClassTeacherController extends Controller
{
	public function grid(Request $request) {
		return TermController::grid($request, new ClassTeacher());
	}

	public function form(Request $request) {
		return TermController::form($request, new ClassTeacher());
	}
}
