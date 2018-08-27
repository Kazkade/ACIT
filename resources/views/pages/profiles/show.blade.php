@extends('layouts.app') 

@section('content')
<div class="container">
  <div class="card">
      <div class="card-header">
          <span class="h2 m-2">{{$part->part_serial}} ({{$part->part_name}})</span>
        <div class="float-right text-right">
          Created: {{$part->created_at}}
          <br>
          Last Updated: {{$part->updated_at}}
        </div>
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
                  <td>Weight: </td>
                  <td><strong>{{$part->part_weight}}g</strong></td>
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
            @foreach($bags as $bag)
              <a href="{{route('orders.index')}}" id="bag_{{$bag->id}}"
                 title="Created By: {{$bag->user_name}} on {{$bag->updated_at}}"  class="btn btn-outline-dark"
                 >&#10070 | {{$bag->quantity}}</a>
            @endforeach
            <table class="table table-striped table-hover table-sm">
              <thead>
                <th>Bag</th>
                <th>Tech</th>
                <th>Made On</th>
              </thead>
              <tbody>
                @foreach($bags as $bag)
                  <tr>
                    <td><a href="{{route('orders.index')}}" id="bag_{{$bag->id}}">&#10070 | {{$bag->quantity}}</a></td>
                    <td>{{$bag->user_name}}</td>
                    <td>{{$bag->updated_on}}</td>
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
                <th class="text-center">Lead</th>
              </thead>
              <tbody>
                @foreach($printers as $printer)
                  <tr>
                    <td>{{$printer->name}}</td>
                    @if($printer->profile_active == 0)
                      <td class="text-center"><span class="text-danger">&#10008</span></td>
                    @else
                      <td class="text-center"><span class="text-warning">&#10004</span></td>
                    @endif
                    <td class="text-center">{{$printer->lead_time}}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <br>
        <table class="table text-center">
          <thead>
            <th colspan=8>Inventory Breakdown</th>
          </thead>
          <thead>
            <th class="text-info">Created</th>
            <th class="text-primary">Processing</th>
            <th class="text-success">Backstock</th>
            <th class="text-danger">Failed</th>
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
                        {{$inventory->total}}
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
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table text-center">
          <thead>
            <th colspan=8>Part History</th>
          </thead>
          <thead>
            <th>Ordered</th>
            <th>Delivered</th>
            <th>Remaining</th>
            <th>Fail Rate</th> <!-- Calculation: Failed / Created -->
            <th>Waste</th> <!-- Calculation: Failed * Weight -->
          </thead>
          <!-- Sort through locations and list all inventories and total -->
          <tbody>
            <tr>
              <td>
                {{$part->ordered}}
              </td>
              <td>
                {{$part->delivered}}
              </td>
              <td>
                {{$part->remaining}}
              </td>
              <td>0%</td>
              <td>0g</td><!-- Total Filament Grams wasted -->
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        <a href="/parts" class="btn btn-outline-primary">Go Back</a>
        @if(Auth::user()->account_type == 2)
          <form action="{{ route('parts.destroy' , $part->id)}}" class="float-right mx-2" method="POST">
            <input name="_method" type="hidden" value="DELETE">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-outline-danger ">&#10006 Delete</button>
          </form>
          <a href="{{route('parts.edit', $part->id)}}" class="btn btn-outline-info float-right mx-2">Edit</a>
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
  </div>
</div>
@endsection