<?php

namespace Kjjdion\LaravelAdminPanel\Controllers;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:Access Admin Panel', 'can:Read Activity Logs']);
    }

    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            $activity_logs = app(config('lap.models.activity_log'))->with('user');
            $datatable = datatables($activity_logs)
                ->editColumn('actions', function ($activity_log) {
                    return view('lap::activity_logs.datatable.actions', compact('activity_log'));
                })
                ->rawColumns(['actions']);

            return $datatable->toJson();
        }

        $html = $builder->columns([
            ['title' => 'Created At', 'data' => 'created_at'],
            ['title' => 'Message', 'data' => 'message'],
            ['title' => 'User', 'data' => 'user.name'],
            ['title' => '', 'data' => 'actions', 'searchable' => false, 'orderable' => false],
        ]);
        $html->orderBy(0, 'desc');
        $html->setTableAttribute('id', 'activity_logs_datatable');

        return view('lap::activity_logs.index', compact('html'));
    }

    public function read($id)
    {
        $activity_log = app(config('lap.models.activity_log'))->findOrFail($id);

        return view('lap::activity_logs.read', compact('activity_log'));
    }
}