<?php

namespace App\Http\Controllers;

use App\ProDepartment;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProDepartmentController extends TermController
{
	public function __construct()
	{
		parent::__construct();
		$this->path = 'depart';
	}
}
