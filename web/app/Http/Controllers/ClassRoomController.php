<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\ClassRoom;

class ClassRoomController extends Controller
{
	public function grid(Request $request) {
		return TermController::grid($request, new ClassRoom());
	}

	public function form(Request $request) {
		return TermController::form($request, new ClassRoom());
	}
}
