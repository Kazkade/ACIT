@extends('layouts.app') 

@section('content')
<div class="container">
  <div class="card mb-5">
      <div class="card-header">
          <span class="h2 m-2">{{$locations->location_name}}</span>
          @if($locations->admin_only == 1)
            <span class="text-danger">(Admin Only)</span>
          @endif
        <div class="float-right text-right">
          Created: {{$locations->created_at}}
          <br>
          Last Updated: {{$locations->updated_at}}

        </div>
      </div>
      <div class="card-header">
        <a href="{{route('locations.index')}}" class="btn btn-primary">Go Back</a>
        <form action="{{ route('locations.destroy' , $locations->id)}}" class="float-right" method="POST">
          <a href="{{route('locations.edit', $locations->id)}}" class="btn btn-outline-info">Edit</a>
            <input name="_method" type="hidden" value="DELETE">
            {{ csrf_field() }}
          <button type="submit" class="btn btn-outline-danger ">&#10006 Delete</button>
        </form>
      </div>
      <div class="card-body">
        <h5 class="card-title">Desription: <strong>{{$locations->location_description}}</strong></h5>
        <h5 class="card-title">Total Inventory: <strong>0</strong></h5>
        <br>
        <table class="table table-sm table-highlight table-bordered text-center">
          <thead>
            <th colspan=9>Inventory Breakdown</th>
          </thead>
          <thead>
            <th>View</th>
            <th class="text-left">Name</th>
            <th class="">Serial</th>
            <th class="">Version</th>
            <th class="" colspan=5>Transfers</th>
          </thead>
          <thead>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>In</th>
            <th><span class="text-dark">&#10140</span></th>
            <th>Current</th>
            <th><span class="text-dark">&#10140</span></th>
            <th>Out</th>
          </thead>
          <tbody>
              @if($parts->count() > 0)
                @foreach($parts as $part)
                  @if($part->has_transfers == 1)
                    <tr>
                      <td><a href="{{route('parts.show', $part->id)}}" class="btn btn-sm btn-outline-secondary d-block">&#10070</a></td>
                      <td class="text-left">{{$part->part_name}}</td>
                      <td>{{$part->part_serial}}</td>
                      <td>{{$part->part_version}}</td>
                      <td>{{$part->to_transfers}}</td> 
                      <td><span class="text-dark">&#10140</span></td> 
                      <td>{{$part->current_count}}</td> 
                      <td><span class="text-dark">&#10140</span></td>
                      <td>{{$part->from_transfers}}</td>
                    </tr>
                  @endif
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