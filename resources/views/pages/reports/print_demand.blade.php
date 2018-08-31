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
  <div class="col-10">
    <h3>
      Print List
    </h3>
    
    <div id="report-table"></div>
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
      "part_priority": "{{$row->priority}}",
      "part_priority_value": "{{$row->priority_value}}",
      "part_version": "{{$row->part_version}}",
      @for($i = 0; $i < count($row->profile); $i++)
        @if($row->profile[$i]->active == 0 && $row->profile[$i]->lead_time == 0 )
          "{{strtolower($row->profile[$i]->printer_name)}}": "✘",
        @else
          "{{strtolower($row->profile[$i]->printer_name)}}": "✓ | {{date('H:i', mktime(0,$row->profile[$i]->lead_time))}} | {{$row->profile[$i]->prints}} ❖ ",
        @endif
      @endfor
      "on_order": "{{$row->on_order}}",
      "to_print": "{{$row->remaining}}",
      <?php
        $pods = '';
        for($i = 0; $i < $row->pods; $i++)
        {
          $pods .= '▢';
          if($i > 10)
          {
            break;
          }
        }
        echo '"pods_left": "'.$pods.'",'
      ?>
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
    {
      title: "Part Info",
      columns: [
        {title:"Name", field:"part_name", download: false, align: "left", width: 200},
        {title:"Serial", field:"part_serial", align:"center", width: 110},
        {title:"Color", field:"part_color", align:"center", width: 120},
        {title:"Priority", field:"priority", align: "center", visible: false},
        {title:"Priority Value", field:"priority_value", align: "center", visible: false},
        {title:"Version", field:"part_version", align:"center", visible:false},
      ]
    },
    {
      title: "Print Profiles",
      columns: [
        @foreach($printers as $printer)
          {title:"{{$printer->name}}", field:"{{strtolower($printer->name)}}", align:"center", width: 150},
        @endforeach
      ]
    },
    {
      title: "Demand",
      columns: [
        {title:"On Order", field:"on_order", align:"center", width: 120},
        {title:"To Print", field:"to_print", align:"center", width: 120},
        {title:"Pods", field:"pods_left", align:"left", widthGrow: 5},
        {title:"", field:"spare", align: "center" , width: 20}
      ]
    }
  ],
  rowFormatter:function(row){
        //row - row component

        var data = row.getData();

        if(data.col == "blue"){
            row.getElement().css({"background-color":"#A6A6DF"});
        }
    },
});

$("#report-table").tabulator("setData",data);
</script>
@endsection