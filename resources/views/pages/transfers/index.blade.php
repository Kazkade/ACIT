@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2">
    </div>
    <div class="col-8">
      <h3>
        Transfer Log
      </h3>
      <table class="table table-striped table-sm table-hover text-center ">
        <thead>
          <tr class="text-center">
            <th scope="col">Updated</th>
            <th scope="col">Tech</th>
            <th scope="col" style="text-align: left !important">Part Name</th>
            <th scope="col">Serial</th>
            <th scope="col">Quantity</th>
            <th scope="col">From</th>
            <th scope="col"></th>
            <th scope="col">To</th>
            <th scope="col">Reverse</th>
          </tr>
        </thead>
        <tbody>
          @if(count($transfers) > 0)
            @foreach($transfers as $transfer)
              @if($transfer->reversal == 1)
                <tr class="text-danger">
              @else
                <tr>
              @endif
                <td scope="row">{{date('d/m/y @ H:i', strtotime($transfer->updated_at))}}</td>
                <td>
                  @foreach($users as $user)
                    @if($user->id == $transfer->user_id)
                      {{$user->first_name}} {{$user->last_name}}
                    @endif
                  @endforeach
                </td>
                <td style="text-align: left !important">
                  @foreach($parts as $part)
                    @if($part->id == $transfer->part_id)
                      {{$part->part_name}}
                    @endif
                  @endforeach
                </td>
                <td>
                  @foreach($parts as $part)
                    @if($part->id == $transfer->part_id)
                      {{$part->part_serial}}
                    @endif
                  @endforeach
                </td>
                <td>{{$transfer->quantity}}</td>
                <td>
                  @foreach($locations as $location)
                    @if($location->id == $transfer->from_location_id)
                      {{$location->location_name}}
                    @endif
                  @endforeach
                </td>
                @if($transfer->reversal == 1)
                  <td>&#8656</td>
                @else
                  <td>&#8658</td>
                @endif
                <td>
                  @foreach($locations as $location)
                    @if($location->id == $transfer->to_location_id)
                      {{$location->location_name}}
                    @endif
                  @endforeach
                </td>
                <td>
                  @if($transfer->reversal == 0)
                    <a href="/transfers/reverse/{{$transfer->id}}" class="btn btn-sm btn-outline-secondary d-block">&#8652</a>
                  @endif
                </td>
              </tr>
            @endforeach 
          @else
            <td colspan=9>
              There are no locations yet. You'll need to add one.
            </td>
          @endif
        </tbody>
      </table>
      {{$transfers->links()}}
    </div>
    <div class="col-2"></div>
  </div>
</div>
@endsection