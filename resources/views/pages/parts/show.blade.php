@extends('layouts.app') 

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">
      <div class="card mb-5">
          <div class="card-header">
            <span class="h2 m-2">{{$part->part_serial}} ({{$part->part_name}})</span>
            <div class="float-right text-right">
              Last Updated: {{ date('m-d-Y @ H:i', strtotime($part->updated_at)) }}
            </div>
          </div>
          <div class="card-header">
            <a href="/parts" class="btn btn-outline-primary">All Parts</a>
            @if(\App\PermissionEnforcer::Protect("parts_delete"))
              <form action="{{ route('parts.destroy' , $part->id)}}" class="float-right mx-2" method="POST">
                <input name="_method" type="hidden" value="DELETE">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-outline-danger ">&#10006 Delete</button>
              </form>
            @endif
            @if(\App\PermissionEnforcer::Protect("parts_moratorium"))
              <form action="{{ route('parts.moratorium' , $part->id)}}" class="float-right mx-2" method="POST">
                {{ csrf_field() }}
                @if($part->in_moratorium == 0)
                  <button type="submit" class="btn btn-outline-dark">Move to Moratorium</button>
                @else
                  <button type="submit" class="btn btn-outline-dark">Move to Production</button>
                @endif
              </form>  
            @endif
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col">
                <h5>
                  Stats:
                </h5>
                <table class="table table-striped table-hover table-sm">
                  <tbody>
                    <tr>
                      <td>Version: </td>
                      <td><strong>{{$part->part_version}}</strong></td>
                    </tr>
                    <tr>
                      <td>Color: </td>
                      <td><strong>{{$part->part_color}}</strong></td>
                    </tr>
                    <tr>
                      <td>Mass: </td>
                      <td><strong>{{$part->part_mass}}g</strong></td>
                    </tr>
                    <tr>
                      <td>Waste: </td>
                      <td><strong>{{$part->part_waste}}g</strong></td>
                    </tr>
                    @if($part->part_cleaned == 0) 
                      <tr>
                        <td>Cleaned: </td>
                        <td><strong>No</strong></td>
                      </tr>
                    @else 
                      <tr>
                        <td>Cleaned: </td>
                        <td><strong>Yes</strong></td>
                      </tr>
                    @endif
                    @if($part->in_moratorium == 0) 
                      <tr>
                        <td>In Moratorium: </td>
                        <td><strong>No</strong></td>
                      </tr>
                    @else 
                      <tr>
                        <td>In Moratorium: </td>
                        <td><strong>Yes</strong></td>
                      </tr>
                    @endif
                  </tbody>
                </table>
              </div>
              <div class="col">
                <h5 class="card-title">Bags:</h5>
                <table class="table table-striped table-hover table-sm">
                  <thead>
                    <th>Bag</th>
                    <th>Tech</th>
                    <th>Made On</th>
                  </thead>
                  <tbody>
                    @foreach($bags as $bag)
                      <tr>
                        <td><a href="/bags" class="btn btn-sm btn-outline-info" id="bag_{{$bag->id}}">&#10070 | {{$bag->quantity}}</a></td>
                        <td>{{$bag->user_name}}</td>
                        <td>{{date('m-d-Y @ H:i', strtotime($bag->updated_at))}}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <div class="col">
                <h5>
                    Print Profiles:
                </h5>
                <table class="table table-striped table-hover table-sm">
                  <thead>
                    <th>Printer</th>
                    <th class="text-center">Active</th>
                    <th class="text-center">Prints</th>
                    <th class="text-center">Lead</th>
                  </thead>
                  <tbody>
                    @foreach($printers as $printer)
                      <tr>
                        <td>{{$printer->name}}</td>
                        @if($printer->profile_active == 0)
                          <td class="text-center"><span class="text-danger">&#10008</span></td>
                        @else
                          <td class="text-center"><span class="text-success">&#10004</span></td>
                        @endif
                        <td class="text-center">{{$printer->prints}}</td>
                        <td class="text-center">{{$printer->lead_time}}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <br>
            <table class="table table-striped table-highlight text-center">
              <thead>
                <th colspan=10 class='table-primary'>Part Statistics</th>
              </thead>
              <thead class="table-primary">
                <th class="text-info">Created</th>
                <th class="text-primary">Processing</th>
                <th class="text-success">Backstock</th>
                <th class="text-danger">Failed</th>
                <th><a href="/orders" class="text-dark"><u>Ordered</u></a></th>
                <th><a href="/deliveries/all" class="text-dark"><u>Delivered</u></a></th>
                <th><a href="/orders" class="text-dark"><u>Remaining</u></a></th>
                <th><a href="/overages" class="text-dark"><u>Overages</u></a></th>
                <th>Fail Rate</th> <!-- Calculation: Failed / Created -->
                <th>Waste</th> <!-- Calculation: Failed * Weight -->
              </thead>
              <tbody>
                <tr>
                  <td>
                    @foreach($locations as $location)
                      @if($location->location_name == 'Collections')
                        @foreach($inventories as $inventory)
                          @if($inventory->location_id == $location->id)
                            {{$inventory->from_total}}
                          @endif
                        @endforeach
                      @endif
                    @endforeach
                  </td>
                  <td>
                    @foreach($locations as $location)
                      @if($location->location_name == 'Processing')
                        @foreach($inventories as $inventory)
                          @if($inventory->location_id == $location->id)
                            {{$inventory->total}}
                          @endif
                        @endforeach
                      @endif
                    @endforeach
                  </td>
                  <td>
                    @foreach($locations as $location)
                      @if($location->location_name == 'Backstock')
                        @foreach($inventories as $inventory)
                          @if($inventory->location_id == $location->id)
                            {{$inventory->total}} ({{$part->bagged}}) 
                          @endif
                        @endforeach
                      @endif
                    @endforeach
                  </td>
                  <td>
                    @foreach($locations as $location)
                      @if($location->location_name == 'Fails')
                        @foreach($inventories as $inventory)
                          @if($inventory->location_id == $location->id)
                            {{$inventory->total}}
                          @endif
                        @endforeach
                      @endif
                    @endforeach
                  </td>
                                    <td>
                    {{$part->ordered}}
                  </td>
                  <td>
                    {{$part->delivered}}
                  </td>
                  <td>
                    {{$part->remaining}}
                  </td>
                  <td>
                    {{$part->overages}}
                  </td>
                  <td>{{$part->fail_rate}}%</td>
                  <td>{{$part->total_waste}}g</td><!-- Total Filament Grams wasted -->
                </tr>
                
              </tbody>
              <thead>
                <tr>
                  <th colspan=30 class="table-primary">Transfer History</th>
                </tr>
                <tr>
                  <th colspan=3>Date</th>
                  <th colspan=3>Tech</th>
                  <th colspan=2>Quantity</th>
                  <th>To</th>
                  <th>From</th>
                </tr>
              </thead>
              <tbody>
                @foreach($transfers as $transfer)
                  <tr>
                    <td colspan=3>{{date('m-d-Y @ H:i', strtotime($transfer->updated_at))}}</td>
                    <td colspan=3>{{$transfer->first_name}} {{$transfer->last_name}}</td>
                    <td colspan=2>{{$transfer->quantity}}</td>
                    <td><a href="/locations/{{$transfer->from_location_id}}">{{$transfer->from_location_name}}</a></td>
                    <td><a href="/locations/{{$transfer->to_location_id}}">{{$transfer->to_location_name}}</a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

      </div>
    </div>
  </div>
</div>
@endsection