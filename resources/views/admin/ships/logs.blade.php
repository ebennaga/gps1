@extends('layouts.admin')
@section('content')
<style>
    #exampleAll_filter {
        display: none;
    }
    .dataTables_length{
        margin-top: -34px;
    }
    div.dt-buttons {
        margin-top: -40px;
    }
    
     #exampleAll_wrapper {
        display: none;
    }
</style>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.1/css/dataTables.dateTime.min.css">
    <div class="card">
        <div class="card-header">
            Logs List
        </div>
    <div class="tab-content">
      <div class="tab-pane" role="tabpanel" id="logs" style="padding-top: 20px;">
        <div class="card-body">
            <div class="table-responsive">
                <table border="0" cellspacing="5" cellpadding="5" style="float: right;">
                <tbody>
                    <tr>
                        <td style="font-weight: normal;">Tahun dan bulan:</td>
                        <td>
                            <select class="form-control" id="myInput" name="datep">
                            <option value="xxxxxxxxxx"></option>
                            <option value="2022-01-">2022-01</option>    
                            <option value="2022-02-">2022-02</option>
                            <option value="2022-03-">2022-03</option>
                            <option value="2022-04-">2022-04</option>
                            <option value="2022-05-">2022-05</option>
                            <option value="2022-06-">2022-06</option>
                            <option value="2022-07-">2022-07</option>
                            <option value="2022-08-">2022-08</option>
                            <option value="2022-09-">2022-09</option>
                            <option value="2022-10-">2022-10</option>
                            <option value="2022-11-">2022-11</option>
                            <option value="2022-12-">2022-12</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table><br>
                <table class="table table-bordered table-striped table-hover datatable display nowrap" id="exampleAll">
                    <thead>
                    <tr>
                        <th></th>
                        <th>
                            {{ trans('cruds.ship.fields.id') }}
                        </th>
                        <th style="min-width: 70px;">
                            &nbsp;
                        </th>
                        <th style="min-width: 100px;">
                            Last Sent Time
                        </th>

                        <th hidden="true">
                            Created at
                        </th>
                        <th width="150">
                            Last Sent Destination
                        </th>
                        <th style="min-width: 100px;">
                            Last Sent Status
                        </th>
                        <th style="min-width: 180px;">
                            Subject
                        </th>
                        <th style="min-width: 200px;">
                            Filename (.chr)
                        </th>
                        <th style="min-width: 670px;">
                            Content
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ships as $key => $ship)
                        <tr>
                            <td></td>
                            <td>
                                {{$key +  1}}
                            </td>
                            <td>
                                <button type="button" class="open-AddBookDialog btn btn-primary" data-toggle="modal"
                                        data-target="#exampleModal"
                                        data-id={{$ship->id}} data-destination="{{$ship->last_sent_destination}}"
                                        data-subject="{{$ship->subject}}" data-filename="{{$ship->filename_chr}}"
                                        data-content="{{$ship->content}}" data-backdrop="static" data-keyboard="false">
                                    Send Manual
                                </button>
                            </td>
                            <td width="150" style="text-align: center;">
                                {{date('d F Y H:i', strtotime($ship->created_at))}}
                            </td>
                            <td hidden="true">
                                {{$ship->created_at}}
                            </td>
                            <td width="150">
                                @if($ship->last_sent_destination )

                                    @foreach(json_decode($ship->last_sent_destination , true) as $json)
                                        {{$json['email'] ?? $json}}<br>
                                    @endforeach
                                @endif
                            </td>
                            <td width="150" style="text-align: center;">
                                {{$ship->last_sent_status ?? ''}}
                            </td>
                            <td>
                                {{$ship->subject ?? ''}}
                            </td>
                            <td width="150">
                                {{$ship->filename_chr}}
                            </td>
                            <td width="40">
                                {{$ship->content}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Send Email Manual</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ url('admin/ships/logs') }}" enctype="multipart/form-data" id="idForm">
                    @csrf
                    <div class="modal-body">
                        <span id="noClose"></span>
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="sin">Last Sent Destination</label>
                            <input class="form-control {{ $errors->has('last_seen_destination') ? 'is-invalid' : '' }}"
                                   type="text" name="last_seen_destination" id="last_seen_destination"
                                   value="{{ old('last_seen_destination', '') }}">
                            @if($errors->has('last_seen_destination'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('last_seen_destination') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.historyShip.fields.sin_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="sin">Subject</label>
                            <input class="form-control {{ $errors->has('subject') ? 'is-invalid' : '' }}" type="text"
                                   name="subject" id="subject" value="{{ old('subject', '') }}">
                            @if($errors->has('subject'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('subject') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.historyShip.fields.sin_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="sin">Filename Chr</label>
                            <input class="form-control {{ $errors->has('filename_chr') ? 'is-invalid' : '' }}"
                                   type="text" name="filename_chr" id="filename_chr"
                                   value="{{ old('filename_chr', '') }}">
                            @if($errors->has('filename_chr'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('filename_chr') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.historyShip.fields.sin_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="sin">Content</label>
                            <input class="form-control {{ $errors->has('content') ? 'is-invalid' : '' }}" type="text"
                                   name="content" id="content" value="{{ old('content', '') }}">
                            @if($errors->has('content'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('content') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.historyShip.fields.sin_helper') }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    @parent
    <script>
        
    $(document).ready(function() {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            var table = $('#exampleAll').DataTable({ buttons: dtButtons, pageLength: 100, order: [[ 1, 'desc' ]],});
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
            
        setTimeout(function() {
        $('#logs').fadeIn(700);
        }, 500);
        
        $('#exampleAll_wrapper').hide();
  
  
        $('#myInput').change( function() {
        $('#exampleAll_wrapper').show();
            table.search($(this).val()).draw();
        } );

      // Event listener to the two range filtering inputs to redraw on input
      

      /*$('#mois1, #annee1').on('change', function(){
           table.draw();   
        });
      
      $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var month = parseInt( $('#mois1').val(), 10 );
            var year = parseInt( $('#annee1').val(), 10  );
            var date = data[4].split('-');
            console.log(year)
            if ( ( isNaN( year ) && isNaN( month ) ) ||
                 ( isNaN( month ) && year == date[0] ) ||
                 ( date[1] == month && isNaN( year ) ) ||
                 ( date[1] == month && year == date[0] ) 
                )
            {
                return true;
            }
            return false;
        }
    );*/
    });

    </script>
    <script>

        $(document).on("click", ".open-AddBookDialog", function () {
            var id = $(this).data('id');

            var destination = $(this).data('destination');
            var subject = $(this).data('subject');
            var filename = $(this).data('filename');
            var content = $(this).data('content')

            var data = '';
            for (var n in destination) {
                if(destination[n]['email']) {
                    data = data + destination[n]['email'] + ';'
                }else{
                    data = data + destination[n] + ';'
                }
            }
            $('#last_seen_destination').val(data)
            $('#id').val(id)
            $('#subject').val(subject)
            $('#filename_chr').val(filename)
            $('#content').val(content)
        });

        $("#idForm").submit(function(e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.
            alert('Jangan di close, data lagi dalam proses pengiriman');
            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function(data)
                {
                    $('#exampleModal').modal('hide')
                    console.log(data)

                    if(!alert('Data Sudah Terkirim')){window.location.reload();}
                    $('#noClose').html('');
                }
            });


        });


    </script>
@endsection
