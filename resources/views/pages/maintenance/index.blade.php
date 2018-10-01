@extends('layouts.app') 
@section('content')
<div class="container">
  <div class="row">
    <div class="col-12 ">
      <div id="report-table" class="mt-3"></div>
    </div>
  </div>
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>

<script>
  
var data = [
  @foreach($log as $row)
    {
      "id": "{{$row->id}}",
      "updated_at":"{{date('m/d/y @ H:i', strtotime($row->updated_at))}}",
      "machine_serial": "{{$row->machine_serial}}",
      "printer_name": "{{$row->printer_name}}",
      "user_name": "{{$row->first_name}} {{$row->last_name}}",
      "task": "{{$row->task}}",
      "machine_id", "{{$row->machine_id}}",
    },
  @endforeach
];
  
$("#report-table").tabulator({
  layout:"fitColumns", //fit columns to width of table (optional)
  placeholder:"Log is empty. :( ",
  pagination:"local",
  paginationSize:20,
  columns:[
    {title:"ID", field:"id", align:"center", visible: false, editor: false},
    {title:"Updated", field:"updated_at", align:"center", width: 180, editor: false},
    {title:"Serial", field:"machine_seiral", download: false, align: "left", editor: false, width: 350},
    {title:"Type", field:"printer_name", align:"center", width: 150, editor: false},
    {title:"User", field:"user_name", align:"center", editor: false},
    {title:"Task", field:"task", align:"center", formatter:"tickCross", editor: false},
    {title:"", field:"machine_id", width:100, formatter:function(cell, formatterParams){
     var value = cell.getValue();
      if(value > 0){
        return "<a class='btn btn-sm btn-outline-dark d-block' href='parts/"+value+"'>View</span>";
      }else{
        return "<a class='btn btn-sm btn-outline-dark disabled d-block' disabled='disabled' href='#'>View</span>";
      }
    }},
  ],
});
  
$("#report-table").tabulator("setData",data);

</script>
@endsection