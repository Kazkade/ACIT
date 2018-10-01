@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2"></div>
    <div class="col-8 ">
      <h3>
        Machines
      </h3>
      <div id="message"></div>
      <span>Add New Machine</span>
      <form>
        <input hidden name="_token" required value="{{ csrf_token() }}">
        <input hidden name="_method" required  value="POST">
        <div class="input-group">
          <input class="form-control" autofocus style="text-transform:uppercase;" placeholder="Serial"  required name="machine_serial">
          <input class="form-control" style="text-transform:uppercase;" placeholder="Identifier"  required name="identifier">
          <select name="filament_name" required  class="custom-select">
            @foreach($filaments as $filament)
              <option value="{{$filament->filament_name}}">{{$filament->filament_name}}</option>
            @endforeach
          </select>
          <select name="printer_name"  required class="custom-select">
            @foreach($printers as $printer)
              <option value="{{$printer->name}}">{{$printer->name}}</option>
            @endforeach
          </select>
          <input type="submit" class="btn btn-outline-success form-control" value="Add">
        </div>
      </form>
    </div>
  </div>
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">
      <div id="report-table" class="mt-3"></div>
    </div>
  </div>
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>

<script>
  
var data = [
  @foreach($machines as $row)
    {
      "machine_serial": "{{$row->machine_serial}}",
      "identifier": "{{$row->identifier}}",
      "printer_name": "{{$row->printer_name}}",
      "filament_name": "{{$row->filament_name}}",
      @if($row->last_maintenance == "Never")
        "last_maintenance":"Never",
      @else
        "last_maintenance":"{{date('m/d/y @ H:i', strtotime($row->last_maintenance))}}",
      @endif
      "machine_id": "{{$row->id}}",
      "delete_id": "{{$row->id}}",
    },
  @endforeach
];
  
var filaments = {
  @foreach($filaments as $filament)
    "{{$filament->filament_name}}":"{{$filament->filament_name}}",
  @endforeach
};
  
var machine_types = {
  @foreach($printers as $printer)
    "{{$printer->name}}":"{{$printer->name}}",
  @endforeach
};
  
$("#report-table").tabulator({
  layout:"fitColumns", //fit columns to width of table (optional)
  placeholder:"Log is empty. :( ",
  pagination:"local",
  paginationSize:20,
  columns:[
    {title:"Serial", field:"machine_serial", headerFilter:"input", align: "center", editor: true, width: 300},
    {title:"Identifier", field:"identifier", editor:true, headerFilter:"input", align:"center", width: 200},
    {title:"Type", field:"printer_name", editor:'select', editorParams: machine_types, headerFilter:"input", align:"center", width: 200},
    {title:"Filament", field:"filament_name", editor:'select', editorParams: filaments, align:"center", headerFilter:"input",  width: 200 },
    {title:"Last Maintenance", field:"last_maintenance", align:"center", editor: false},
    {title:"", field:"machine_id", width:150, headerSort: false, formatter:function(cell, formatterParams){
     return "<a class='btn btn-sm btn-outline-dark d-block' href='maintenance/"+cell.getValue()+"'>Maintenance</span>";
    }},
    {title:"", field:"delete_id", width:150, headerSort: false, formatter:function(cell, formatterParams){
     return `
        <form action="machines/destroy/`+cell.getValue()+`" method="POST">
          <input type="hidden" value="{{ csrf_token() }}" name="_token">
          <input type="hidden" value="DELETE" name="_method">
          <input type="submit" class='btn btn-sm btn-outline-danger d-block' value="Delete">
        </form>`;
    }},
  ],
  cellEdited:function(cell){
    var row = cell.getData();
    console.log(JSON.stringify(row));
    $.ajax({
      type: "GET",
      headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}",
      },
      dataType: "JSON",
      url: "machines/ajaxupdate",
      data: row,
      success: function(msg) {
        row = cell.getElement()
          .animate({ backgroundColor: "#c7ffc4"}, 100)
          .animate({ backgroundColor: "#FFFFFF"}, 200);
      },
      error: function(xhr, err, msg)
      {
        console.log(data);
        $('#message').append(`
          <div class='alert alert-danger' role='alert'>
            There was an error saving data: <strong>`+msg+`</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        `);
        row = cell.getElement()
          .animate({ backgroundColor: "#ffbfc3"}, 100)
          .animate({ backgroundColor: "#FFFFFF"}, 200);
      }
    });
  }
});
  
$("#report-table").tabulator("setData",data);

</script>
@endsection