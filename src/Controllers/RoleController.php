<?php

namespace Kjjdion\LaravelAdminPanel\Controllers;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:Access Admin Panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:Create Roles')->only(['createForm', 'create']);
        $this->middleware('can:Read Roles')->only(['index', 'read']);
        $this->middleware('can:Update Roles')->only(['updateForm', 'update']);
        $this->middleware(['can:Delete Roles', 'not_admin_role'])->only('delete');
    }

    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            $permissions_count = app(config('lap.models.permission'))->count();
            $roles = app(config('lap.models.role'))->withCount('permissions');
            $datatable = datatables($roles)
                ->editColumn('permissions', function ($role) use ($permissions_count) {
                    return ($role->admin ? $permissions_count : $role->permissions_count) . ' / ' . $permissions_count;
                })
                ->editColumn('actions', function ($role) {
                    return view('lap::roles.datatable.actions', compact('role'));
                })
                ->rawColumns(['actions']);

            return $datatable->toJson();
        }

        $html = $builder->columns([
            ['title' => 'Name', 'data' => 'name'],
            ['title' => 'Permissions', 'data' => 'permissions', 'searchable' => false, 'orderable' => false],
            ['title' => '', 'data' => 'actions', 'searchable' => false, 'orderable' => false],
        ]);
        $html->setTableAttribute('id', 'roles_datatable');

        return view('lap::roles.index', compact('html'));
    }

    public function createForm()
    {
        $group_permissions = app(config('lap.models.permission'))->all()->groupBy('group');

        return view('lap::roles.create', compact('group_permissions'));
    }

    public function create()
    {
        $this->validate(request(), [
            'name' => 'required|unique:roles',
        ]);

        $role = app(config('lap.models.role'))->create(request()->all());
        $role->permissions()->sync(request()->input('permissions'));

        activity('Created Role: ' . $role->name, request()->all(), $role);
        flash(['success', 'Role created!']);

        if (request()->input('_submit') == 'redirect') {
            return response()->json(['redirect' => session()->pull('url.intended', route('admin.roles'))]);
        }
        else {
            return response()->json(['reload_page' => true]);
        }
    }

    public function read($id)
    {
        $role = app(config('lap.models.role'))->findOrFail($id);

        return view('lap::roles.read', compact('role'));
    }

    public function updateForm($id)
    {
        $role = app(config('lap.models.role'))->findOrFail($id);
        $group_permissions = app(config('lap.models.permission'))->all()->groupBy('group');

        return view('lap::roles.update', compact('role', 'group_permissions'));
    }

    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        $role = app(config('lap.models.role'))->findOrFail($id);
        $role->update(request()->all());
        $role->permissions()->sync(request()->input('permissions'));

        activity('Updated Role: ' . $role->name, request()->all(), $role);
        flash(['success', 'Role updated!']);

        if (request()->input('_submit') == 'redirect') {
            return response()->json(['redirect' => session()->pull('url.intended', route('admin.roles'))]);
        }
        else {
            return response()->json(['reload_page' => true]);
        }
    }

    public function delete($id)
    {
        $role = app(config('lap.models.role'))->findOrFail($id);
        $role->delete();

        activity('Deleted Role: ' . $role->name, $role->toArray());
        $flash = ['success', 'Role deleted!'];

        if (request()->input('_submit') == 'reload_datatables') {
            return response()->json([
                'flash' => $flash,
                'reload_datatables' => true,
            ]);
        }
        else {
            flash($flash);

            return redirect()->route('admin.roles');
        }
    }
}