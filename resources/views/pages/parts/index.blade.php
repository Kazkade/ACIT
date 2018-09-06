@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10 ">
      <h3>
        Parts
      </h3>
      @if(Auth::user()->account_type = 2)
        <a href="#" id="add_new_row" class="btn btn-outline-primary">❖ Add New Part</a>
        <a href="#" id="save_changes" class="btn btn-outline-success">✔ Save Changes</a>
        <span class="text-success ml-2" id="return-message"></span>
      @endif
      <div id="report-table" class="mt-3"></div>
    </div>
  </div>
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>

<script>
var data = [
  @foreach($parts as $row)
    {
      "id": "{{$row->id}}",
      "status": "unchanged",
      "updated_at":"{{date('m/d/y @ H:i', strtotime($row->updated_at))}}",
      "part_name": "{{$row->part_name}}",
      "part_serial": "{{$row->part_serial}}",
      "part_color": "{{$row->part_color}}",
      "part_version": "{{$row->part_version}}",
      "part_cleaned": "{{$row->part_cleaned}}",
      "part_mass": "{{$row->part_mass}}",
      "part_stock": "{{$row->inventory}}",
      "part_bags": "{{$row->bag_count}}",
      "part_total": "{{$row->total}}",
      "part_view": "{{$row->id}}",
    },
  @endforeach
];
  
var editable = @if(Auth::user()->account_type = 2) true @else false @endif ;
  
$("#report-table").tabulator({
  layout:"fitColumns", //fit columns to width of table (optional)
  placeholder:"No parts to show. :( ",
  columns:[
    {title:"ID", field:"id", align:"center", visible: false, editor: false},
    {title:"Status", field:"status", align:"center", visible: false, editor: false},
    {title:"Updated", field:"updated_at", align:"center", width: 150, editor: false},
    {title:"Name", field:"part_name", download: false, align: "left", editor: editable, width: 250},
    {title:"Serial", field:"part_serial", align:"center", width: 110, editor: editable},
    {title:"Color", field:"part_color", align:"center", width: 120, editor:"select", editorParams:{
      @foreach($filaments as $filament)
        "{{$filament->filament_name}}":"{{$filament->filament_name}}",
      @endforeach
    }},
    {title:"Version", field:"part_version", align:"center", editor: editable},
    {title:"Cleaned", field:"part_cleaned", align:"center", formatter:"tickCross", editor: editable},
    {title:"Mass", field:"part_mass", align:"center", editor: editable},
    {title:"Stock", field:"part_stock", align:"center", editor: false},
    {title:"Bags", field:"part_bags", align:"center", editor: false},
    {title:"Total", field:"part_total", align:"center", editor: false},
    {title:"View", field:"part_view", width:80, formatter:function(cell, formatterParams){
     var value = cell.getValue();
      if(value > 0){
        return "<a class='btn btn-sm btn-outline-info d-block' href='parts/"+value+"'>View</span>";
      }else{
        return "<a class='btn btn-sm btn-outline-info disabled d-block' disabled='disabled' href='#'>View</span>";
      }
    }},
    {title:"", field:"blank", align:"center", editor: false, width: 80, headerSort:false},
  ],
  rowFormatter:function(row){
    //row - row component

    var data = row.getData();

    if(data.col == "blue"){
        row.getElement().css({"background-color":"#A6A6DF"});
    }
  },
  cellEdited:function(cell){
    var change = cell.getRow().getData();
    change.status = "update";
    $("#report-table").tabulator("updateData", [change]);
  },
});
  
$("#report-table").tabulator("setData",data);
  
var updatedRows = [];


$('#save_changes').on('click', function() {
  
  updatedRows.data = [];
  
  $('.tabulator-row').each(function(i, el){
    
    var $tds =  $(this).find('.tabulator-cell');
    if($tds.eq(1).text() != 'unchanged')
    {
      var ar = {
        "id": $tds.eq(0).text(),
        "status": $tds.eq(1).text(),
        "part_name": $tds.eq(3).text(),
        "part_serial": $tds.eq(4).text(),
        "part_color": $tds.eq(5).text(),
        "part_version": $tds.eq(6).text(),
        "part_cleaned": $tds.eq(7).attr("aria-checked"),
        "part_mass": $tds.eq(8).text(),
      }
      
      updatedRows.data.push(ar);
    }
  });
  
  $.ajax({
    type: "GET",
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}",
    },
    dataType: "JSON",
    url: "parts/update_or_create/"+JSON.stringify(updatedRows.data),
    success: function(msg) {
      $('#return-message').text(msg);
      location.reload();
    }
  });
});
  
$('#add_new_row').on('click', function() {
  var o = {
    "id": 0,
    "status":"new", 
    "part_name":"New Part", 
    "part_serial":"XX-XX0000", 
    "part_color": "Black",
    "part_version": "0.0.0",
    "part_mass": 0,
    "part_cleaned":0, 
    "part_stock": 0, 
    "part_bags": 0, 
    "part_total": 0
  }
  $("#report-table").tabulator("addRow", o, true);
});

</script>
@endsection