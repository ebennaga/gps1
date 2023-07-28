@extends('layouts.admin')
@section('content')
<div class="card" style="margin-top: 20px;">
    <div class="card-header" style="padding: 20px; font-size: 18px; font-weight: 700;">
        Database - {{ $ship->ship_ids }} {{ $ship->name }}
    </div>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="ship_history_ships" style="padding-top: 20px;">
            
            @includeif('admin.ships.relationships.shipHistoryShips', ['historyShips' => $ship->shipHistoryShips])
    
        </div>
    </div>
</div>
@endsection