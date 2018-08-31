@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-1"></div>
    
    <div class="col-10">
      <div class="row">
        <div class="col">
          <h3>
            Print Profiles
          </h3>
      </div>
      <br>
        {{$parts->links()}}
        <table class="table table-striped table-sm table-hover text-center align-middle">
          <thead>
            <tr>
              <th scope="col" >Updated</th>
              <th scope="col" style="text-align: left !important;">Name</th>
              <th scope="col" >Serial</th>
              @foreach($printers as $printer)
                <th style="width: 15%;">{{$printer->name}}</th>
              @endforeach
              <th>Update</th>
            </tr>
          </thead>
          <tbody>
            @if(count($parts) > 0)
              @foreach($parts as $part)
                @if($part->in_moratorium == 0)
                  <tr id="part_{{$part->id}}">
                @else
                  <tr style="background-color: #ddd; opacity: 0.5" id="part_{{$part->id}}">
                @endif    
                  <form action="/profiles/update" method="POST">
                    <td class="align-middle" scope="row">{{date('d/m/y @ H:i', strtotime($part->updated_at))}}</td>
                    <td class="align-middle" style="text-align: left !important;">{{$part->part_name}}</td>
                    <td class="align-middle" >{{$part->part_serial}}</td>
                    @foreach($part->profiles as $profile)
                      <td class="">
                        <div class="form-group row align-middle">
                          <label for="profile-prints-{{$profile->printer_id}}" class="col-sm-2 col-form-label">Prints</label>
                          <div class="col-sm-10">
                            <input type="number" step=0.001
                                   class="form-control form-control-sm" 
                                   name="profile-prints-{{$profile->printer_id}}" 
                                   id="profile-prints-{{$profile->printer_id}}" 
                                   value="{{$profile->prints}}">
                          </div>
                        </div>
                        <div class="form-group row align-middle">
                          <label for="profile-lead-{{$profile->printer_id}}" class="col-sm-2 col-form-label">Lead</label>
                          <div class="col-sm-10">
                            <input type="number" step=0.001
                                   class="form-control form-control-sm" 
                                   name="profile-lead-{{$profile->printer_id}}" 
                                   id="profile-lead-{{$profile->printer_id}}" 
                                   value="{{$profile->lead_time}}">
                          </div>
                        </div>
                      </td>
                    @endforeach
                    <td class="align-middle h-100"><input class="btn btn-outline-success w-100 h-100" type="submit" value="Update"></td>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="part_id" value="{{$part->id}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  </form>
                </tr>
              @endforeach 
            @else
              <td colspan=9>
                There are no parts yet. You'll need to add one.
              </td>
            @endif
          </tbody>
        </table>

        {{$parts->links()}}
      </div>
    </div>
  </div>
    
  <div class="row">
    
    <span class="p-5"></span>
  </div>
</div>
<script>
$(document).ready(function() {
  function scrollTo(hash) {
    var id = 1;
    @if(isset($part_id))
      var id = {{$part_id->part_id}};
    @endif
    location.hash = "#part_"+id;
  }
});
</script>
@endsection