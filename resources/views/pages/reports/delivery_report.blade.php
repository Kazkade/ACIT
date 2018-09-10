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
    <div class="col">
      <h3>
        Filament Usage Report<br>
      </h3>
      <a href="#" id="download_as_pdf" class="btn btn-outline-success no-print">Download as PDF</a>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div id="report-table"></div>
    </div>
  </div>
</div>

<script>
  
// Filter

//Trigger setFilter function with correct parameters
function updateFilter(){
  
  if($("#filter-field").val() == "function" ){
      $("#filter-type").prop("disabled", true);
      $("#filter-value").prop("disabled", true);
  }else{
      $("#filter-type").prop("disabled", false);
      $("#filter-value").prop("disabled", false);
  }

  $("#report-table").tabulator("setFilter", $("#filter-field").val(), $("#filter-type").val(), $("#filter-value").val());
}

//Update filters on value change
$("#filter-field, #filter-type").change(updateFilter);
$("#filter-value").keyup(updateFilter);

//Clear filters on "Clear Filters" button click
$("#filter-clear").click(function(){
    $("#filter-field").val("");
    $("#filter-type").val("=");
    $("#filter-value").val("");

    $("#report-table").tabulator("clearFilter");
});
  
$("#download_as_pdf").on('click', function(){
  $("#report-table").tabulator("download", "pdf", "{{date('m.d.Y_H-i-s')}}.FUR.pdf", {
      orientation:"portrait", //set page orientation to portrait
      title:"Filament Usage Report", //add title to report
  });
});
  

var nestedData = [
  @foreach($report as $row)
    {
      "part_name": "{{$row->part_name}}",
      "part_serial": "{{$row->part_serial}}",
      "part_color": "{{$row->part_color}}",
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
  @endforeach
];

$("#report-table").tabulator({
  layout:"fitColumns", //fit columns to width of table (optional)
  groupBy:"part_color",
  placeholder:"No parts to show. :( ",
  columns:[
    {
      title: "Part Info",
      columns: [
        {title:"ID", field:"part_name", visible: false, download: false},
        {title:"Date", field:"date", align:"center", width: 100},
        {title:"Part", field:"part_name", align:"center", visible:false},
        {title:"Serial", field:"part_mass", align:"center", width: 100},
        {title:"Waste", field:"part_waste", align:"center", width: 100},
        {title:"Produced", field:"parts_created", align:"center", bottomCalc:"sum"},
        {title:"Failed", field:"parts_failed", align:"center", bottomCalc:"sum"},
      ]
    },
    {
      title: "Filament Usage",
      columns: [
        {title:"Passing", field:"passed_filament", align:"center", bottomCalc:"sum"},
        {title:"Fallout", field:"fallout_filament", align:"center", bottomCalc:"sum"},
        {title:"Waste", field:"scrap_filament", align:"center", bottomCalc:"sum"},
        {title:"In House Use", field:"inhouse_filament", align:"center", bottomCalc:"sum"},
        {title:"Total Filament", field:"total_filament", downloadName: "Total", align:"center", bottomCalc:"sum"}
      ]
    }
  ],
  rowFormatter:function(row){
    //row - row component
    
    var data = row.getData();

    switch(data.part_color) {
      @foreach($filaments as $filament)
        case "{{$filament->filament_name}}": 
          row.getElement().css(
            {"background-color":"{{$filament->background_color}}", "color": "{{$filament->text_color}}"}); 
          break;
      @endforeach
    };
  },
});

$("#report-table").tabulator("setData",nestedData);
</script>

@endsection