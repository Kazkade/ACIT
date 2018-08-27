<!-- Orders -->
@extends('layouts.app') 
@section('content')
<style>
  #report_body tr td {
    vertical-align: middle;
  }
</style>
<div class="row">
  <div class="col-1"></div>
  <div class="col-10">
    <h3>
      Print List
    </h3>
  <table class="table table-sm table-striped table-hover text-center">
    <thead>
      <tr>
        <th scope="col" rowspan=2 class="text-left">Part Name</th>
        <th scope="col" rowspan=2>Serial</th>
        <th scope="col" rowspan=2>Version</th>
        @foreach($printers as $printer)
          <th>{{$printer->name}}</th>
        @endforeach
        
        <th scope="col" rowspan=3>On Order</th>
        <th scope="col" rowspan=3>Remaining</th>
        
        <th scope="col" rowspan=3 class="text-left">Pods</th>
      </tr>
      <tr>
        <!-- Only to match up double rows. -->
        @foreach($printers as $printer)
          <th>Lead</th>
        @endforeach
      </tr>
    </thead>
    <tbody id="report_body">  
      <!-- I need to create JSON objects for Filament Colors -->
      @if(count($report) == 0)
        <tr><td colspan=30>No parts to print. Creat orders to get started.</td></tr>
      @else
        @foreach($report as $row)
          <tr>
            <td class="text-left">{{$row->part_name}}</td>
            <td>{{$row->part_serial}}</td>
            <td>{{$row->part_version}}</td>
            @for($i = 0; $i < count($row->profile); $i++)
              @if($row->profile[$i]->active == 0)
                <td><span class="text-danger">&#10008</span> <br>{{$row->profile[$i]->lead_time}}</td>
              @else
                <td><span class="text-success">&#10004</span><br>{{$row->profile[$i]->lead_time}}</td>
              @endif
            @endfor
            <td >{{$row->on_order}}</td>
            <td >{{$row->remaining}}</td>
            <td class="text-left">
              @for($i = 0; $i < $row->pods; $i++)
                <a class="btn btn-sm btn-outline-danger disabled" disabled="disabled" href="#">&#10070</a>
                @if($i > 10)
                  @break
                @endif
              @endfor
            </td>
          </tr>
        @endforeach
      @endif
    </tbody>
  </table>
  </div>
  <div class="col-1"></div>
</div>
@endsection