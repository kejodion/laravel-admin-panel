<div class="text-right text-nowrap">
    <a href="{{ route('admin.docs.read', $doc->id) }}" class="btn btn-link text-secondary p-1" title="Read"><i class="fal fa-lg fa-eye"></i></a>
    @can('Update Docs')
        <a href="{{ route('admin.docs.update', $doc->id) }}" class="btn btn-link text-secondary p-1" title="Update"><i class="fal fa-lg fa-edit"></i></a>
        <form method="POST" action="{{ route('admin.docs.move', $doc->id) }}" class="d-inline-block" novalidate data-ajax-form>
            @csrf
            @method('PATCH')
            <button type="submit" name="_submit" class="btn btn-link text-secondary p-1" title="Move Up" value="up">
                <i class="fal fa-lg fa-arrow-alt-up"></i>
            </button>
            <button type="submit" name="_submit" class="btn btn-link text-secondary p-1" title="Move Down" value="down">
                <i class="fal fa-lg fa-arrow-alt-down"></i>
            </button>
        </form>
    @endcan
    @can('Delete Docs')
        <form method="POST" action="{{ route('admin.docs.delete', $doc->id) }}" class="d-inline-block" novalidate data-ajax-form>
            @csrf
            @method('DELETE')
            <button type="submit" name="_submit" class="btn btn-link text-secondary p-1" title="Delete" value="reload_datatables" data-confirm{{ $doc->system ? ' disabled' : '' }}>
                <i class="fal fa-lg fa-trash-alt"></i>
            </button>
        </form>
    @endcan
</div>