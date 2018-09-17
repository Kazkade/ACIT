<!-- Orders -->
@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2">
    </div>
    <div class="col-4">
      @if(Auth::user()->account_type == 2)
        <h3>
          Upload New Orders
        </h3>
          <form class="md-form" id="upload_form" action="/orders/upload" enctype="multipart/form-data" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="input-group mb-3 ">
              <div class="input-group-prepend">
                <span class="btn btn-info disabled" disabled="disabled"><i class="fa fa-paperclip" aria-hidden="true"></i></span>

              </div>
              <div class="custom-file">
                <input type="file" class="form-control" name="orders_upload" id="file_upload">
              </div>
              <div class="input-group-append">
                <input type="submit" class="btn btn-outline-success" value="Upload">
              </div>
            </div>
          </form>        
      </div>
      <div class='col-4'>
        <h3>
          Create New Orders
        </h3>
          <form class="md-form" id="create_form" action="/orders/store" method="POST">
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="input-group mb-3 ">
              <div class="input-group-prepend">
                 <span class="btn btn-secondary disabled" disabled="disabled">MO/</span>
              </div>
              <input class="form-control" autofocus id="mo" name="mo" required type="text" placeholder="01234">
              <input 
               type="text" 
               maxlength="9"
               style="text-transform:uppercase" 
               class="form-control" 
               name="part_serial" 
               id="part_serial" 
               placeholder="XX-XX0000" required />
              <input class="form-control" id="quantity" name="quantity" required type="number" step="1" placeholder="Quantity">
              <select class="form-control" id="priority" required name="priority">
                <option selected value=0>Low</option>
                <option value=1>Medium</option>
                <option value=2>High</option>
              </select>
              <div class="input-group-append">
                <input type="submit" class="btn btn-outline-success" value="Create">
              </div>
            </div>
          </form>        
        </div>
    </div>
    <div class="row">
      <div class='col-2'></div>
      <div class="col-8">
        <h3>
          Outstanding Orders
        </h3>
      @endif
      @if(count($orders) > 0)
        <a class="btn btn btn-outline-success d-block my-3" href="/deliver_order">Deliver All</a>
      @endif
      <table class="table table-striped table-sm table-hover text-center ">
        <thead>
          <tr class="text-center">
            <th scope='col'>View</th>
            <th scope='col'>MOs</th>
            <th scope="col">Serial</th>
            <th scope="col" style="text-align: left !important">Part Name</th>
            <th scope="col">On Order</th>
            <th scope="col">Filled</th>
            <th scope="col">Remaining</th>
            <th scope="col">TBD</th>
            <th scope="col" style="text-align: left !important">Marked Bags</th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $order)
            <tr>
              <td><a href="parts/{{$order->part_id}}" class="btn btn-sm btn-outline-dark">View Part</a></td>
              <td class="text-right">
                @foreach($order->mos as $mo)
                  @if($mo->priority == 0)
                    <span class="table-success">{{$mo->mo}} ({{$mo->quantity}})
                  @elseif($mo->priority == 1)
                    <span class="table-warning">{{$mo->mo}} ({{$mo->quantity}})
                  @elseif($mo->priority == 2)
                    <span class="table-danger">{{$mo->mo}} ({{$mo->quantity}})
                  @else
                    <span class="">{{$mo->mo}} ({{$mo->quantity}})
                  @endif
                      <form action="{{route('orders.destroy', $mo->id)}}" class="float-right " method="POST">
                        <input name="_method" type="hidden" value="DELETE">
                        {{ csrf_field() }}
                        <input class="btn btn-sm btn-outline-danger" type="submit" value="x">  
                      </form>
                    </span>
                    <br><br>
                @endforeach
              </td>
              <td>{{$order->part_serial}}</td>
              <td style="text-align: left !important">{{$order->part_name}}</td>
              <td>{{$order->total}}</td>
              <td>{{$order->filled}}</td>
              <td>{{$order->total - $order->filled}}</td>
              <td>{{$order->tbd}}</td>
              <td style="text-align: left !important">
                @foreach($bags as $bag)
                  @if($bag->part_id == $order->part_id)
                    <a class="btn btn-sm btn-outline-dark" id="bag_{{$bag->id}}"
                       title="Created By: {{$bag->user_name}} on {{$bag->updated_at}}" href="/deliveries?part_id={{$bag->part_id}}"
                       >&#10070 | {{$bag->quantity}}</a>
                  @endif
                @endforeach
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <hr>
      <h3>
        Closed Orders  
      </h3>    
      <table class="table table-striped table-sm table-hover text-center ">
        <thead>
          <tr class="text-center">
            <th scope='col'>MOs</th>
            <th scope="col">Serial</th>
            <th scope="col" style="text-align: left !important">Part Name</th>
            <th scope="col">Quantity</th>
            <th scope="col">Closed On</th>
            <th scope='col'>View</th>
          </tr>
        </thead>
        <tbody>
          @if(count($closed))
            @foreach($closed as $order)
              <tr>
                <td class="">{{$order->mo}}</td>
                <td>{{$order->part_serial}}</td>
                <td style="text-align: left !important">{{$order->part_name}}</td>
                <td>{{$order->quantity}}</td>
                <td>{{date('m-d-Y @ H:i', strtotime($order->updated_at))}}</td>
                <td><a href="parts/{{$order->part_id}}" class="btn btn-sm btn-outline-dark">View Part</a></td>
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
    <div class="col-2"></div>
  </div>
</div>

<script>
  
  
$(document).ready(function() {
  // Initialize Masks
  $('#part_serial').mask('AA-AA0000');
  $('#mo').mask('00000');
  
  $(function () {
    $('[data-toggle=popover]').popover()
  })
  
  $('.popover-dismiss').popover({
    trigger: 'focus'
  });
  
});
</script>

@endsection