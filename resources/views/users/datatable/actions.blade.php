<div class="text-right text-nowrap">
    <a href="{{ route('admin.users.read', $user->id) }}" class="btn btn-link text-secondary p-1" title="Read"><i class="fal fa-lg fa-eye"></i></a>
    @can('Update Users')
        <a href="{{ route('admin.users.update', $user->id) }}" class="btn btn-link text-secondary p-1" title="Update"><i class="fal fa-lg fa-edit"></i></a>
        <a href="{{ route('admin.users.password', $user->id) }}" class="btn btn-link text-secondary p-1" title="Change Password"><i class="fal fa-lg fa-unlock-alt"></i></a>
    @endcan
    @can('Delete Users')
        <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" class="d-inline-block" novalidate data-ajax-form>
            @csrf
            @method('DELETE')
            <button type="submit" name="_submit" class="btn btn-link text-secondary p-1" title="Delete" value="reload_datatables" data-confirm>
                <i class="fal fa-lg fa-trash-alt"></i>
            </button>
        </form>
    @endcan
</div>