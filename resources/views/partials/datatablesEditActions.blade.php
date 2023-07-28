@can($editGate)
    <a class="btn btn-xs btn-primary" style="font-size: 14px;" href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
        {{ trans('global.edit') }}
    </a>
@endcan