@extends('layouts.admin')
@section('content')
    @can('distributor_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("admin.distributors.create") }}">
                    {{ trans('global.add') }} Distributor
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            Distributor {{ trans('global.list') }}
        </div>

        <div class="card-body">

            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-User">
                <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.distributor.fields.id') }}
                    </th>
                    <th style="min-width: 130px">
                        &nbsp;
                    </th>
                    <th style="min-width: 150px;">
                        {{ trans('cruds.distributor.fields.name') }}
                    </th>
                    <th style="min-width: 100px;">
                        {{ trans('cruds.distributor.fields.username') }}
                    </th>
                    <th>
                        {{ trans('cruds.distributor.fields.roles') }}
                    </th>
                    <!--<th>
                        {{ trans('cruds.distributor.fields.terminal') }}
                    </th>-->
                    <!--<th>
                        {{ trans('cruds.distributor.fields.ship') }}
                    </th>-->
                    <th style="min-width:310px;">
                        {{ trans('cruds.distributor.fields.ship_id') }}
                    </th>
                    <th style="min-width: 140px;">
                        {{ trans('cruds.distributor.fields.destinasion-email') }}
                    </th>
                    <th>Total Terminal</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
                @can('distributor_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.distributors.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({selected: true}).data(), function (entry) {
                        return entry.id
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')

                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: {ids: ids, _method: 'DELETE'}
                        })
                            .done(function () {
                                location.reload()
                            })
                    }
                }
            }
            dtButtons.push(deleteButton)
                @endcan

            let dtOverrideGlobals = {
                    buttons: dtButtons,
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    aaSorting: [],
                    ajax: "{{ route('admin.distributors.index') }}",
                    columns: [
                        {data: 'placeholder', name: 'placeholder'},
                        {data: 'id', name: 'id'},
                        {data: 'actions', name: '{{ trans('global.actions') }}' ,"searchable": false,"orderable": false,},
                        {data: 'name', name: 'name'},
                        {data: 'username', name: 'username'},
                        {data: 'roles', name: 'roles.title'},
                        //{data: 'terminal', name: 'terminals.name'},
                        //{data: 'ship', name: 'ships.name'},
                        {data: 'ship_id', name: 'ships.ship_ids', data: 'ship_id', name: 'ships.name'},
                        {data: 'email', name: 'email.email'},
                        {data: 'total', name: 'total_terminal'}
                    ],
                    order: [[1, 'desc']],
                    pageLength: 100,
                };
            $('.datatable-User').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });

    </script>
@endsection
