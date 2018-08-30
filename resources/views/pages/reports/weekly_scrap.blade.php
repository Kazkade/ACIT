<!-- Orders -->
@extends('layouts.app') 
@section('content')
<style>
  #report_body tr td {
    vertical-align: middle;
  }
</style>
<div class="row">
  <div class="col-2"></div>
  <div class="col-8">
    <h3> Weekly Scrap </h3>
    @foreach($colors as $color)
      <h5> {{$color}} </h5>
      <div id="{{str_replace(' ', '', $color)}}_filament"></div>
    @endforeach
    <table class="table table-sm table-striped table-hover text-center">
      <thead>

      </thead>
      <tbody id="report_body">  

      </tbody>
    </table>
  </div>
  <div class="col-1"></div>
</div>

<script>
@foreach($colors as $color)
    var {{str_replace(' ', '', $color)}}_filament = [
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
    ];
    $("#{{str_replace(' ', '', $color)}}_filament").tabulator({
      layout:"minWidth", //fit columns to width of table (optional)
      columns:[ //Define Table Columns
          {title:"Name", field:"part_name", align: "let", width:300},
          {title:"Serial", field:"part_serial", align:"center", width: 100},
          {title:"Mass", field:"part_mass", width: 100},
          {title:"Waste", field:"part_waste", align:"center", width: 100},
          {title:"Created", field:"parts_created", align:"center", bottomCalc:"sum"},
          {title:"Failed", field:"fallout_filament", align:"center", bottomCalc:"sum"},
          {title:"Passing", field:"passed_filament", align:"center", bottomCalc:"sum"},
          {title:"Scrap", field:"scrap_filament", align:"center", bottomCalc:"sum"},
          {title:"In House Use", field:"inhouse_filament", align:"center", bottomCalc:"sum"},
          {title:"Total Filament", field:"total_filament", align:"center", bottomCalc:"sum"},
      ],
      rowClick:function(e, row){ //trigger an alert message when the row is clicked

      },
    });
    $("#{{str_replace(' ', '', $color)}}_filament").tabulator("setData", {{str_replace(' ', '', $color)}}_filament);
@endforeach  
</script>

@endsection