<!-- Deliveries -->
@extends('layouts.app') 
@section('content')
<div class="container">
  <div class="row">
    <div class="col-3">
      
    </div>
    <!-- Order Browser -->
    <div class="col">
      <h3>
        All Deliveries
      </h3>
      <table class="table table-striped table-sm table-hover text-center ">
        <thead>
          <tr class="text-center">
            <th scope="col">Created</th>
            <th scope="col">Tech</th>
            <th scope="col">Total Parts</th>
            <th scope="col">View</th>
          </tr>
        </thead>
        <tbody>
          @if(count($deliveries) > 0)
            @foreach($deliveries as $delivery)
              @if($delivery->updated_at > 0)
              <tr>
                <td>{{date( 'm-d-Y @ h:i', strtotime($delivery->updated_at))}}</td>
                <td>{{$delivery->first_name}} {{$delivery->last_name}}</td>
                <td>{{$delivery->total}}</td>
                <td><a href="/deliveries/{{$delivery->id}}" class="btn btn-sm btn-outline-dark">View</a></td>
              </tr>
              @endif
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
    <div class="col-3">
      
    </div>
  </div>
  <div class="row py-5">
    
  </div>
</div>
<script>
</script>
@endsection