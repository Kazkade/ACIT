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
      <!--
      <span class="float-right mb-3">
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <select id="filter-field" class="btn btn-outline-dark">
              <option href="#">Name</option>
              <option href="#">Serial</option>
              <option href="#">Color</option>
              <option href="#">Mass</option>
              <option href="#">Total Filament</option>
            </select>
            <select  id="filter-type" class="btn btn-outline-dark">
              <option value="=">=</option>
              <option value="<">&lt;</option>
              <option value="<=">&lt;=</option>
              <option value=">">&gt;</option>
              <option value=">=">&gt;=</option>
              <option value="!=">!=</option>
              <option selected value="like">like</option>
            </select>
          </div>
          <input type="text" class="form-control" id="filter-value" placeholder="Criteria" aria-label="Text input with segmented dropdown button">
          <div class="input-group-append">
            <button id="filter-clear" class="btn btn-outline-dark">Clear</button>
          </div>
        </div>
      </span>
      -->
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
        {title:"Name", field:"part_name", download: false, align: "let", width:300},
        {title:"Serial", field:"part_serial", align:"center", width: 100},
        {title:"Color", field:"part_color", align:"center", visible:false},
        {title:"Mass", field:"part_mass", align:"center", width: 100},
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
});

$("#report-table").tabulator("setData",nestedData);
</script>

@endsection