<!-- Orders -->
@extends('layouts.app') 
@section('content')
<style>
  #report_body tr td {
    vertical-align: middle;
  }
</style>
<div class="row">
  <div class="col-1"></div>
  <div class="col-5">
    <h3>
      Delivery ({{$delivery->updated_at}} by {{$delivery->tech}})
    </h3>
    
    <div id="report-table"></div>
  </div>
  <div class="col-5">
    <h3>
      Overages Created
    </h3>
    
    <div id="overages-table"></div>
  </div>
  <div class="col-1"></div>
</div>

<script>
var data = [
  @foreach($report as $row)
    {
      "part_name": "{{$row->part_name}}",
      "part_serial": "{{$row->part_serial}}",
      "part_color": "{{$row->part_color}}",
      "delivered": "{{$row->delivered}}",
      "overage": "{{$row->overage}}",
      "resolved": "{{$row->resolved}}",
    },
  @endforeach
];

$("#report-table").tabulator({
  layout:"fitColumns", //fit columns to width of table (optional)
  initialSort: [
    
  ],
  groupBy:["part_priority","part_color"],
  placeholder:"No parts to show. :( ",
  columns:[
      {title:"Name", field:"part_name", download: false, align: "left", width: 200},
      {title:"Serial", field:"part_serial", align:"center", width: 110},
      {title:"Color", field:"part_color", align:"center", width: 120},
      {title:"Delivered", field:"delivered", align:"center", width: 110},
      {title:"Overage", field:"overage", align:"center", width: 110},
      {title:"Resolved", field:"resolved", align:"center", width: 110},
  ],
});

$("#report-table").tabulator("setData",data);
</script>
@endsection