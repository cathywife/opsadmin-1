<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\User;

class PasswordController extends Controller {
	public $module = 'admin.password';
	public $parent_module = 'admin';

	public function __construct() {
		parent::__construct();
		\View::share('title', '密码重置');
	}

	public function password() {
		return view('admin.password');
	}

	public function passwordupdate(requests\PasswordUpdateFormRequest $request) {
		$admin = User::whereName('admin')->first();
		$admin->password = bcrypt($request->password);
		$admin->save();
		return back()->with('status', '重置管理密码成功！');
	}

}
