<?php

namespace Kjjdion\LaravelAdminPanel\Controllers;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;

class DocController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:Access Admin Panel'])->except(['frontend']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:Create Docs')->only(['createForm', 'create']);
        $this->middleware('can:Read Docs')->only(['index', 'read']);
        $this->middleware('can:Update Docs')->only(['updateForm', 'update', 'move']);
        $this->middleware(['can:Delete Docs', 'not_system_doc'])->only('delete');
    }

    public function frontend($id = null, $slug = null)
    {
        if (!$id) {
            $doc = app(config('lap.models.doc'))->where('type', 'Index')->first();
        }
        else if (!$doc = app(config('lap.models.doc'))->where('type', '!=', '404')->find($id)) {
            $doc = app(config('lap.models.doc'))->where('type', '404')->first();
        }

        $docs = app(config('lap.models.doc'))->withDepth()->where('type', '!=', '404')->defaultOrder()->get()->toFlatTree();

        return view('lap::layouts.docs', compact('doc', 'docs'));
    }

    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            $docs = app(config('lap.models.doc'))->withDepth()->defaultOrder()->get()->toFlatTree();
            $datatable = datatables($docs)
                ->editColumn('title', function ($doc) {
                    return view('lap::docs.datatable.title', compact('doc'));
                })
                ->editColumn('actions', function ($doc) {
                    return view('lap::docs.datatable.actions', compact('doc'));
                })
                ->rawColumns(['title', 'actions']);

            return $datatable->toJson();
        }

        $html = $builder->columns([
            ['title' => 'Title', 'data' => 'title'],
            ['title' => 'Type', 'data' => 'type'],
            ['title' => '', 'data' => 'actions', 'searchable' => false, 'orderable' => false],
        ]);
        $html->setTableAttribute('id', 'docs_datatable');
        $html->ordering(false);

        return view('lap::docs.index', compact('html'));
    }

    public function createForm()
    {
        $docs = app(config('lap.models.doc'))->withDepth()->where('system', false)->defaultOrder()->get();

        return view('lap::docs.create', compact('docs'));
    }

    public function create()
    {
        $this->validate(request(), [
            'type' => 'required',
            'parent_id' => 'nullable|exists:docs,id',
            'title' => 'required',
        ]);

        $data = array_merge(request()->all(), ['slug' => str_slug(request()->input('title'))]);
        $doc = app(config('lap.models.doc'))->create($data);

        activity('Created Doc: ' . $doc->title, request()->all(), $doc);
        flash(['success', 'Doc created!']);

        if (request()->input('_submit') == 'redirect') {
            return response()->json(['redirect' => session()->pull('url.intended', route('admin.docs'))]);
        }
        else {
            return response()->json(['reload_page' => true]);
        }
    }

    public function read($id)
    {
        $doc = app(config('lap.models.doc'))->findOrFail($id);

        return view('lap::docs.read', compact('doc'));
    }

    public function updateForm($id)
    {
        $doc = app(config('lap.models.doc'))->findOrFail($id);
        $docs = app(config('lap.models.doc'))->withDepth()->where('system', false)->defaultOrder()->get();

        return view('lap::docs.update', compact('doc', 'docs'));
    }

    public function update($id)
    {
        $this->validate(request(), [
            'title' => 'required',
            'parent_id' => 'nullable|exists:docs,id',
        ]);

        $data = array_merge(request()->all(), ['slug' => str_slug(request()->input('title'))]);
        $doc = app(config('lap.models.doc'))->findOrFail($id);
        $doc->update($data);

        activity('Updated Doc: ' . $doc->title, request()->all(), $doc);
        flash(['success', 'Doc updated!']);

        if (request()->input('_submit') == 'redirect') {
            return response()->json(['redirect' => session()->pull('url.intended', route('admin.docs'))]);
        }
        else {
            return response()->json(['reload_page' => true]);
        }
    }

    public function move($id)
    {
        $doc = app(config('lap.models.doc'))->findOrFail($id);

        if (request()->input('_submit') == 'up') {
            $doc->up();
        }
        else {
            $doc->down();
        }

        return response()->json(['reload_datatables' => true]);
    }

    public function delete($id)
    {
        $doc = app(config('lap.models.doc'))->findOrFail($id);
        $doc->delete();

        activity('Deleted Doc: ' . $doc->title, $doc->toArray());
        $flash = ['success', 'Doc deleted!'];

        if (request()->input('_submit') == 'reload_datatables') {
            return response()->json([
                'flash' => $flash,
                'reload_datatables' => true,
            ]);
        }
        else {
            flash($flash);

            return redirect()->route('admin.docs');
        }
    }
}