<!-- Orders -->
@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2">
    </div>
    <div class="col-8">
      @if(count($overages) > 0)
      <table class="table table-striped table-sm table-hover text-center ">
        <thead>
          <tr class="alert-danger">
            <th colspan=12>Unresolved Overages</th>
          </tr>
          <tr class="text-center">
            <th scope="col">Status</th>
            <th scope="col" style="text-align: left !important">Part Name</th>
            <th scope="col">Serial</th>
            <th scope="col">Overage</th>
            <th scope="col">Delivery</th>
            <th scope="col">Resolve</th>
          </tr>
        </thead>
        <tbody>
          @foreach($overages as $overage)
            @if($overage->resolved == 0)
              <tr>
                <td class="text-red">Unresolved</td>
                <td style="text-align: left !important">{{$overage->part_name}}</td>
                <td>{{$overage->part_serial}}</td>
                <td>{{$overage->quantity}}</td>
                <td class="btn btn-sm btn-outline-info" href="/deliveries/{{$overage->delivery_id}}">
                  View
                </td>
                <td style="text-align: left !important">
                  <a href="/overage/resolve/{{$overage->id}}" class="btn btn-sm btn-outline-info">Resolve</a>
                </td>
              </tr>
            @endif
          @endforeach
        </tbody>
        <thead>
          <tr class="alert-success">
            <th colspan=12>Resolved Overages</th>
          </tr>
          <tr class="text-center">
            <th scope="col">Status</th>
            <th scope="col" style="text-align: left !important">Part Name</th>
            <th scope="col">Serial</th>
            <th scope="col">Overage</th>
            <th scope="col">Delivery</th>
          </tr>
        </thead>
        <tbody>
          @foreach($overages as $overage)
            @if($overage->resolved == 0)
              <tr>
                <td class="text-red">Unresolved</td>
                <td style="text-align: left !important">{{$overage->part_name}}</td>
                <td>{{$overage->part_serial}}</td>
                <td>{{$overage->quantity}}</td>
                <td class="btn btn-sm btn-outline-info" href="/deliveries/{{$overage->delivery_id}}">
                  View
                </td>
                <td style="text-align: left !important">
                  <a href="/overage/unresolve/{{$overage->id}}" class="btn btn-sm btn-outline-danger">Unresolve</a>
                </td>
              </tr>
            @endif
          @endforeach
        </tbody>
      </table>
      @else
        <span class="text-center">
          There are no overages recorded.
        </span>  
      @endif
    </div>
    <div class="col-2"></div>
  </div>
</div>
@endsection