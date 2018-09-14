@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2">
      @if(Auth::user()->account_type == 2)
      <h3>
        New Location
      </h3>
      <form action="{{route('locations.store')}}" class="w-100" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="card w-100 mb-3">
          <div class="card-header h4">
            Create Location
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="location_name">Location Name</label>
              <input type="text" class="form-control" name="location_name" id="location_name" placeholder="Location Name">
            </div>
            <div class="form-group">
              <label for="exampleFormControlTextarea1">Location Description</label>
              <textarea class="form-control" placeholder="General description of the location in cluster." id="location_description" name="location_description" rows="3"></textarea>
            </div>
            <div class="form-group">
              <div class="form-check">
                <input class="form-check-input" checked="checked" type="checkbox" id="default_checkbox" name="location_default" value="option1" aria-label="...">
                <label class="form-check-label" for="default_checkbox">Admin Only?</label>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-outline-primary">Create</button>
          </div>
        </div>
      </form>
      @endif
    </div>
    <div class="col-8">
      <h3>
        Locations
      </h3>
      <table class="table table-striped table-sm table-hover text-center ">
        <thead>
          <tr>
            <th scope="col text-center">Updated</th>
            <th scope="col">Name</th>
            <th scope="col text-left">Description</th>
            <th scope="col">Admin Only</th>
            <th scope="col text-center">Total Parts</th>
            <th scope="col text-center">View</th>
          </tr>
        </thead>
        <tbody>
          @if(count($locations) > 0)
            @foreach($locations as $location)
              <tr>
                <td scope="row">{{date('d/m/y @ H:i', strtotime($location->updated_at))}}</td>
                <td class="align-middle text-left">{{$location->location_name}}</td>
                <td class="align-middle text-left">{{$location->location_description}}</td>
                
                @if($location->admin_only == 1)
                  <td class="align-middle text-success">&#10003</td>
                @else
                  <td class="align-middle text-danger">&#10007</td>
                @endif
                
                <td class="align-middle">0</td>
                
                <td class="align-middle"><a href="{{route('locations.show', $location->id)}}" class="btn btn-outline-secondary d-block">&#10070</a></td>
              </tr>
            @endforeach 
          @else
            <td colspan=9>
              There are no locations yet. You'll need to add one.
            </td>
          @endif
        </tbody>
      </table>
    </div>
    <div class="col-2"></div>
  </div>
</div>
@endsection