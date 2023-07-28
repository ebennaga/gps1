
   <!-- @can('history_ship_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("admin.history-ships.create") }}">
                    {{ trans('global.add') }} {{ trans('cruds.historyShip.title_singular') }}
                </a>
            </div>
        </div>
    @endcan--><br>
    <style>
        .dataTables_filter {
        display: none;
    }
    .dataTables_length{
        margin-top: -34px;
    }
    div.dt-buttons {
        margin-top: -40px;
    }
    .dataTables_wrapper .dataTables_processing {
        position: fixed !important;
        top: 42% !important; 
        height: 150px;
        border:none;
        z-index: 1;
        color:transparent;
        background: url('/images/icon-load4.gif') center center no-repeat; 
        background-size: 55% 70%;
    }
    </style>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.1/css/dataTables.dateTime.min.css">

        <div class="card-body">
          <div class="table-responsive">  
            <table border="0" cellspacing="5" cellpadding="5" style="float: right;">
                <tbody>
                    <tr>
                        <td style="font-weight: normal;">Tahun dan bulan:</td>
                        <td>
                            <select class="form-control" id="myInput" name="datep">
                            <option value="xxxxxxxxxxxxxxx"></option>
                            <option value="{{ $ship->ship_ids }} 2021-11">2021-11</option>
                            <option value="{{ $ship->ship_ids }} 2021-12">2021-12</option>
                            <option value="{{ $ship->ship_ids }} 2022-01">2022-01</option>    
                            <option value="{{ $ship->ship_ids }} 2022-02">2022-02</option>
                            <option value="{{ $ship->ship_ids }} 2022-03">2022-03</option>
                            <option value="{{ $ship->ship_ids }} 2022-04">2022-04</option>
                            <option value="{{ $ship->ship_ids }} 2022-05">2022-05</option>
                            <option value="{{ $ship->ship_ids }} 2022-06">2022-06</option>
                            <option value="{{ $ship->ship_ids }} 2022-07">2022-07</option>
                            <option value="{{ $ship->ship_ids }} 2022-08">2022-08</option>
                            <option value="{{ $ship->ship_ids }} 2022-09">2022-09</option>
                            <option value="{{ $ship->ship_ids }} 2022-10">2022-10</option>
                            <option value="{{ $ship->ship_ids }} 2022-11">2022-11</option>
                            <option value="{{ $ship->ship_ids }} 2022-12">2022-12</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table><br>
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-HistoryShip" style="text-align: center;">
                <thead>
                <tr>
                    <th></th>
                    <th>
                        {{ trans('cruds.historyShip.fields.id') }}
                    </th>
                    <th>Action</th>
                    <th style="min-width: 75px;">
                        Display To Map
                        <p style="font-size: 11px;">click to toogle</p>
                    </th>
                    <th style="min-width: 75px;">
                        {{ trans('cruds.historyShip.fields.history_ids') }}
                    </th>
                    <th style="min-width: 105px;">
                        {{ trans('cruds.historyShip.fields.ship') }}
                    </th>
                    <th>
                        {{ trans('cruds.historyShip.fields.sin') }}
                    </th>
                    <th>
                        {{ trans('cruds.historyShip.fields.min') }}
                    </th>
                    <th>
                        {{ trans('cruds.historyShip.fields.region_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.historyShip.fields.receive_utc') }}
                    </th>
                    <th>
                        {{ trans('cruds.historyShip.fields.message_utc') }}
                    </th>
                    <th>
                        {{ trans('cruds.ship.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.historyShip.fields.payload') }}
                    </th>
                    <th>
                        {{ trans('cruds.historyShip.fields.ota_message_size') }}
                    </th>
                        <!--<th>
                            &nbsp;
                        </th>-->
                    </tr>
                </thead>
            </table>
          </div>
        </div>

@section('scripts')
    @parent
    <script type="text/javascript">
    $(document).ready(function(){
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('history_ship_delete')
          let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
          let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.history-ships.massDestroy') }}",
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

      // DataTable
      var table = $('.datatable-HistoryShip').DataTable({
         buttons: dtButtons,
         retrieve: true,
         aaSorting: [],
         processing: true,
         serverSide: true,
         ajax: "{{route('admin.history-ships.index')}}",
         columns: [
            {data: 'placeholder', name: 'placeholder'},
            {data: 'id', name: 'id'},
            {data: 'actions', name: '{{ trans('global.actions') }}' ,"searchable": false,"orderable": false,},
            {data: 'display_to_map', name:'display_to_map'},
            {data: 'history_ids', name: 'history_ids'},
            {data: 'ship_ship_ids', name: 'ship.ship_ids'},
            {data: 'sin', name: 'sin'},
            {data: 'min', name: 'min'},
            {data: 'region_name', name: 'region_name'},
            {data: 'receive_utc', name: 'receive_utc'},
            {data: 'message_utc', name: 'message_utc'},
            {data: 'ship.name', name: 'ship.name'},
            {data: 'payload', name: 'payload'},
            {data: 'ota_message_size', name: 'ota_message_size'},
         ],

         order: [[1, 'desc']],
         pageLength: 100,
         search: {"search": "xxxxxxxxxxxxxxxxx" }
      });

        $('#myInput').on( 'change', function () {
            table.search( this.value ).draw();
        } );

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
        });

        setTimeout(function() {
        $('#ship_history_ships').fadeIn(700);
        }, 700);

    });
    </script>
@endsection
