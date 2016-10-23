<?php

namespace App\Http\Controllers;

use App\ClassTeacher;
use Illuminate\Http\Request;

use App\Http\Requests;
use Validator;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Hash;

class ClassTeacherController extends TermController {

	use AuthenticatesAndRegistersUsers, ThrottlesLogins;

	public function afterSaved(Request $request, $object) {

		$user = \App\User::where('name', $request->get('name'))->first();
		if (!$user) {
			if (!$request->get('password')) {
				$request->merge([
					'password' => str_random(8),
				]);
			}
			$this->create($request->all());
		} else if ($user->email != $request->get('email')) {
			$user->email = $request->get('email');
			$user->update();
		}
		return parent::afterSaved($request, $object);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	protected function create(array $data)
	{
		return User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
		]);
	}
}
