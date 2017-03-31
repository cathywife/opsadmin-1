<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;

class PermissionController extends Controller {
	//http://ops2.app/admin/permission/attach/alert.delete/4
	public function attachPermissionToRole($premission, $roleid) {
		$permission = Permission::whereSlug($premission)->first();
		// dd($permission);
		$role = Role::find($roleid);
		// dd($role);
		$role->attachPermission($permission);
	}

	public $module = 'admin.permission';
	public $parent_module = 'admin';
	private $dataTable;

	public function __construct() {
		parent::__construct();
		\View::share('title', '权限管理');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return view('admin.permission.index')->with('sub_title', '权限列表');
	}

	public function ajaxIndex() {
		$permissions = Permission::select(['id', 'name', 'slug', 'description', 'updated_at']);
		return Datatables::of($permissions)
			->addColumn('action', function ($permission) {
				return '<a href="' . route('admin.permission.edit', $permission->id) . '" class="fa fa-fw fa-edit"></a>&nbsp;&nbsp;<a class="fa fa-trash" href="#" data-toggle="modal" data-target="#DeleteModal" data-name="' . $permission->name . '" data-action="' . route('admin.permission.destroy', $permission->id) . '"></a>';
			})
			->make(true);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		return view('admin.permission.create')->with('sub_title', '添加权限');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(requests\PermissionCreateFormRequest $request) {
		$permission = Permission::create([
			'name' => $request->name,
			'slug' => $request->slug,
			'description' => $request->description,
		]);
		$role = Role::whereSlug('admin')->first();
		$role->attachPermission($permission);

		return redirect()->route('admin.permission')->with([
			'status' => '创建权限成功： ' . $permission->name,
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		$permission = Permission::findOrFail($id);
		return view('admin.permission.edit', compact('permission'))->with('sub_title', '编辑权限');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(requests\PermissionEditFormRequest $request, $id) {
		$permission = Permission::findOrFail($id);
		$permission->update($request->all());
		return redirect()->route('admin.permission')->with([
			'status' => '编辑权限成功：' . $permission->name,
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$permission = Permission::findOrFail($id);
		$role = Role::whereSlug('admin')->first();
		$role->detachPermission($permission);
		$permission->delete();
		return redirect()->route('admin.permission')->with([
			'status' => '删除权限成功：' . $permission->name,
		]);
	}
}
