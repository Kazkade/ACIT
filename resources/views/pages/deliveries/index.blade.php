<!-- Deliveries -->
@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-1">
    </div>
    <!-- Order Browser -->
    <div class="col-5">
      <h3>
        Open Orders
      </h3>
      <hr>
      <!-- Fillable Orders -->
      <h4>
        <span class="text-success">Available Stock</span>
      </h4>
      <table class="table table-striped table-sm table-hover text-center ">
        <thead>
          <tr class="text-center">
            <th scope="col">Updated</th>
            <th scope="col" style="text-align: left !important">Part Name</th>
            <th scope="col">Serial</th>
            <th scope="col">Ordered</th>
            <th scope="col">Bags</th>
            <th scope="col">Show</th>
          </tr>
        </thead>
        <tbody>
          @if(count($orders) > 0)
            @foreach($orders as $order)
              @if($order->bag_count > 0)
                @if($order->priority == 2)
                  <tr style="background-color: #ffcdd2;">
                @elseif($order->priority == 1)
                  <tr style="background-color: #fff9c4;">
                @else
                  <tr style="background-color: #b9f6ca;">
                @endif 
                <td>{{$order->created_at}}</td>
                @foreach($parts as $part)
                  @if($part->id == $order->part_id)
                    <td style="text-align: left !important">{{$part->part_name}}</td>
                    <td>{{$part->part_serial}}</td>
                    <td>{{$order->ordered}}</td>
                    <td>{{$order->bag_count}}</td>
                    <td><a href="#" onclick="FilterBags({{$part->id}})" class="delivery-a btn btn-sm btn-outline-dark">&#10070</a></td>
                  @endif
                @endforeach
              </tr>            
              @endif
            @endforeach
          @else
            <td colspan=9>
              There are no orders yet. You'll need to create one.
            </td>
          @endif
        </tbody>
      </table>
      <!-- No Bags to Fill Orders -->
      <h4>
        <span class="text-danger">Unavailable Stock</span>
      </h4>
      <table class="table table-striped table-sm table-hover text-center ">
        <thead>
          <tr class="text-center">
            <th scope="col">Updated</th>
            <th scope="col" style="text-align: left !important">Part Name</th>
            <th scope="col">Serial</th>
            <th scope="col">Ordered</th>
            <th scope="col">Bags</th>
            <th scope="col">Show</th>
          </tr>
        </thead>
        <tbody>
          @if(count($orders) > 0)
            @foreach($orders as $order)
              @if($order->bag_count < 1)
                @if($order->priority == 2)
                  <tr style="background-color: #ffcdd2;">
                @elseif($order->priority == 1)
                  <tr style="background-color: #fff9c4;">
                @else
                  <tr style="background-color: #b9f6ca;">
                @endif 
                <td>{{$order->created_at}}</td>
                @foreach($parts as $part)
                  @if($part->id == $order->part_id)
                    <td style="text-align: left !important">{{$part->part_name}}</td>
                    <td>{{$part->part_serial}}</td>
                    <td>{{$order->ordered}}</td>
                    <td>{{$order->bag_count}}</td>
                    <td><a href="#" onclick="FilterBags({{$part->id}})" class="delivery-a btn btn-sm btn-outline-dark">&#10070</a></td>
                  @endif
                @endforeach
              </tr>            
              @endif
            @endforeach
          @else
            <td colspan=9>
              There are no orders yet. You'll need to create one.
            </td>
          @endif
        </tbody>
      </table>
    </div>
    <!-- Bag Browser -->
    <div class="col-5">
      <h3>
        Bags Available
      </h3>
      <table class="table table-striped table-sm table-hover text-center ">
        <thead>
          <tr class="text-center">
            <th scope="col">Created At</th>
            <th scope="col" >Created By</th>
            <th scope="col" style="text-align: left !important">Part Name</th>
            <th scope="col">Serial</th>
            <th scope="col">Quantity</th>
            <th scope="col">Mark</th>
            <th scope="col">Unmark</th>
          </tr>
        </thead>
        <tbody>
          @if(count($bags) > 0)
            @foreach($bags as $bag)
              <tr hidden class="bag part_id_{{$bag->part_id}}">
                <td>{{$bag->created_at}}</td>
                @foreach($users as $user)
                  @if($user->id == $bag->created_by)
                    <td>{{$user->first_name}} {{$user->last_name}}</td>
                  @endif
                @endforeach
                @foreach($parts as $part)
                  @if($part->id == $bag->part_id)
                    <td style="text-align: left !important">{{$part->part_name}}</td>
                    <td>{{$part->part_serial}}</td>
                  @endif
                @endforeach
                <td>{{$bag->quantity}}</td>
                <td>
                  @if($bag->marked == 0)
                    <form id="mark_bag_{{$bag->id}}" action="/bags/mark/{{$bag->id}}" method="POST">
                      <input type="submit" class="mark btn btn-sm btn-outline-danger" value="&#10070">
                      <input hidden name="bag_id" value="{{$bag->id}}">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                  @endif
                </td>
                <td>
                  @if($bag->marked == 1)
                    <form id="unmark_bag_{{$bag->id}}" action="/bags/unmark/{{$bag->id}}" method="POST">
                      <input type="submit" class="mark btn btn-sm btn-outline-success" value="&#10070">
                      <input hidden name="bag_id" value="{{$bag->id}}">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                  @endif
                </td>
              </tr>            
            @endforeach
          @else
            <td colspan=9>
              There are no bags available to deliver yet. You'll need to create one.
            </td>
          @endif
        </tbody>
      </table>
    </div>
    <div class="col-1"></div>
  </div>
  <div class="row py-5">
    
  </div>
</div>
<script>
// Filtering
$(document).ready(function(){
  
  $('.deliver-a').on('click', function(event) {
    event.preventDefault();
  });
  // Filter Bags
  // Initial Filter
  var partId = new URL(window.location).searchParams.get("part_id");
  console.log(partId);
  if(partId != null) 
  {
    $('.part_id_'+partId).removeAttr('hidden');
  }
});
  
function FilterBags(part_id)
{
  $('.bag').attr('hidden', 'hidden');
  $('.part_id_'+part_id).removeAttr('hidden');
}
</script>
@endsection