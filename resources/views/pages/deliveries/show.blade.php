@extends('layouts.app') 

@section('content')
<div class="container">
  <div class="card mb-5">
      <div class="card-header">
        <span class="h2 m-2">Delvery</span>
        <div class="float-right text-right">
          On: {{date( 'm-d-Y @ h:i', strtotime($delivery->date))}}
          <br>
          By: {{$delivery->first_name}} {{$delivery->last_name}}
        </div>
      </div>
      <div class="card-body">
        <table class="table table-sm table-highlight table-bordered text-center">
          <thead>
            <th colspan=9>Inventory Breakdown</th>
          </thead>
          <thead>
            <th>View</th>
            <th class="">Tech</th>
            <th class="">Part</th>
            <th class="">Serial</th>
            <th class="">Quantity</th>
            <th class="">Created</th>
          </thead>
          <tbody>
              @if(count($report) > 0)
                @foreach($report as $row)
                <tr>
                  <td><a href="/parts/{{$row->part_id}}" class="btn btn-sm btn-outline-dark">View Part</a></td>
                  <td>{{$row->first_name}} {{$row->last_name}}</td>
                  <td>{{$row->part_name}}</td>
                  <td>{{$row->part_serial}}</td>
                  <td>{{$row->quantity}}</td>
                  <td>{{date( 'm-d-Y @ h:i', strtotime($row->updated_at))}}</td>
                </tr>
                @endforeach
              @else
                <td class="text-center" colspan=9>There are no parts in this location.</td>
              @endif
          </tbody>
        </table>
      </div>
  </div>
</div>
@endsection