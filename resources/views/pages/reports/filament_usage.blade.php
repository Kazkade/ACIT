<!-- Orders -->
<head>
  <title>ACIT - Filament Usage Report</title>
</head>
@extends('layouts.app') 
@section('content')
<style>
  #report_body tr td {
    vertical-align: middle;
  }
</style>
<div class="container-fluid">
  <div class="row">
    <div class="col-1 no-print"></div>
    <div class="col-10 ">
      <h3>
        Filament Usage Report<br>
      </h3>
      <a href="#" id="download_as_pdf" class="btn btn-outline-success no-print">Download as PDF</a>
      <div id="report-table"></div>
    </div>
    <div class="col-1 no-print"></div>
  </div>
</div>

<script>
  
$("#download_as_pdf").on('click', function(){
  $("#report-table").tabulator("download", "pdf", "{{date('m.d.Y_H-i-s')}}.FUR.pdf", {
      orientation:"portrait", //set page orientation to portrait
      title:"Filament Usage Report", //add title to report
  });
});
  

var nestedData = [
  @foreach($colors as $color)
    {"filament_color": "{{$color}}", "usages": [
      @foreach($report as $row)
        @if($row->part_color == $color)
          {
          "part_name": "{{$row->part_name}}",
          "part_serial": "{{$row->part_serial}}",
          "part_mass": {{$row->part_mass}},
          "part_waste": {{$row->part_waste}},
          "parts_created": {{$row->parts_created}},
          "parts_failed": {{$row->parts_failed}},
          "passed_filament": {{$row->passed_filament}},
          "fallout_filament": {{$row->fallout_filament}},
          "scrap_filament": {{$row->scrap_filament}},
          "inhouse_filament": {{$row->inhouse_filament}},
          "total_filament": {{$row->total_filament}} ,
          },
        @endif
      @endforeach
    ]},
  @endforeach
];

$("#report-table").tabulator({
  layout:"fitColumns", //fit columns to width of table (optional)

  columns:[ //Define Table Columns
    {title:"Filament Color", field:"filament_color"},
  ],
  rowFormatter:function(row){
    //create and style holder elements
    var holderEl = $("<div></div>");
    var tableEl = $("<div></div>");

    holderEl.css({
      "box-sizing":"border-box",
      "padding":"10px 30px 10px 10px",
      "border-top":"1px solid #333",
      "border-bottom":"1px solid #333",
      "background":"#ddd",
    })

    tableEl.css({
      "border":"1px solid #333",
    })

    holderEl.append(tableEl);

    row.getElement().append(holderEl);

    //create nested table
    tableEl.tabulator({
      layout:"minWidth",
      resizableColumns:false,
      data:row.getData().usages,
      columns:[
        {
          title: "Part Info",
          columns: [
            {title:"Name", field:"part_name", download: false, align: "let", width:300},
            {title:"Serial", field:"part_serial", align:"center", width: 100},
            {title:"Mass", field:"part_mass", align:"center", width: 100},
            {title:"Waste", field:"part_waste", align:"center", width: 100},
            {title:"Produced", field:"parts_created", align:"center", bottomCalc:"sum"},
          ]
        },
        {
          title: "Filament Usage",
          columns: [
            {title:"Failed", field:"fallout_filament", align:"center", bottomCalc:"sum"},
            {title:"Passing", field:"passed_filament", align:"center", bottomCalc:"sum"},
            {title:"Scrap", field:"scrap_filament", align:"center", bottomCalc:"sum"},
            {title:"In House Use", field:"inhouse_filament", align:"center", bottomCalc:"sum"},
            {title:"Total Filament", field:"total_filament", downloadName: "Total", align:"center", bottomCalc:"sum"}
          ]
        }
      ]
    })
  },
});

$("#report-table").tabulator("setData",nestedData);
</script>

@endsection