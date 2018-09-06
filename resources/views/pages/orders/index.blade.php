<!-- Orders -->
@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2">
    </div>
    <div class="col-8">
      @if(Auth::user()->account_type == 2)
      <h3>
        Upload New Orders
      </h3>
      <div class="col-md-6">
        <form class="md-form" id="upload_form" action="/orders/upload" enctype="multipart/form-data" method="POST">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="input-group mb-3 ">
            <div class="input-group-prepend">
              <span class="btn btn-info disabled" disabled="disabled"><i class="fa fa-paperclip" aria-hidden="true"></i></span>
            </div>
            <div class="custom-file">
              
              <input type="file" class="" name="orders_upload" id="file_upload">
              <label class="custom-file-label" for="file_upload">Choose File</label>
            </div>
            <div class="input-group-append">
              <input type="submit" class="btn btn-outline-success" value="Upload">
            </div>
          </div>
        </form>        
      </div>
      <h3>
        Outstanding Orders
      </h3>
      @if(count($orders) > 0)
        <a class="btn btn btn-outline-success d-block my-3" href="/deliver_order">Deliver All</a>
      @endif
      @endif
      <table class="table table-striped table-sm table-hover text-center ">
        <thead>
          <tr class="text-center">
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
          @if(count($orders) > 0)
            @foreach($orders as $order)
              <tr>
                <td>{{$order->part_serial}}</td>
                <td style="text-align: left !important">{{$order->part_name}}</td>
                <td>{{$order->quantity}}</td>
                <td>{{$order->filled}}</td>
                <td>{{$order->quantity - $order->filled}}</td>
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
          @else
            <td colspan=9>
              There are no orders. You'll need to upload some.
            </td>
          @endif
        </tbody>
      </table>
      @if(count($orders) > 0)
        <a class="btn btn btn-outline-success d-block my-3" href="/deliver_order">Deliver All</a>
      @endif
    </div>
    <div class="col-2"></div>
  </div>
</div>

<script>
  
  
$(document).ready(function() {
  
  $(function () {
    $('[data-toggle=popover]').popover()
  })
  
  $('.popover-dismiss').popover({
    trigger: 'focus'
  });
  
});
</script>

@endsection