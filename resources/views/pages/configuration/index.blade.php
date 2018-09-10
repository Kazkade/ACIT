@extends('layouts.app') 
@section('content')
<!-- Configuration -->
<div class="container-fluid">
  <div class="row">
    <div class="col-2"> </div>
    <div class="col-8">
      <h3>
        Configuration
      </h3>
      <div class="card">
        <div class="card-header">
          Printers
        </div>
        <div class="card-body">
          <form id="new_printer_form" action="printer/store" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <input type="submit" class="btn btn-outline-primary" value="Add New Printer">
              </div>
              <input type="text" class="form-control" placeholder="Printer Name" name="printer_name" required aria-label="Printer Name" aria-describedby="basic-addon1">
            </div>
          </form>
          <table class="text-center table table-sm">
            <thead>
              <th>Printer Name</th>
              <th>Status</th>
              <th>Toggle</th>
              <th>Delete</th>
            </thead>
            <tbody>
            @foreach($printers as $printer)
              <tr>
                <td>{{$printer->name}}</td>
                @if($printer->active == 1)
                  <td class="text-success">Active</td>
                  <td>
                    <form action="/printer/toggle/{{$printer->id}}" method="POST">
                      <input type="submit" class="btn btn-sm btn-outline-warning" value="Retire">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                  </td>
                @else
                  <td class="text-danger">Retired</td>
                  <td>
                    <form action="/printer/toggle/{{$printer->id}}" method="POST">
                      <input type="submit" class="btn btn-sm btn-outline-success" value="Activate">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                  </td>
                @endif
                <td>
                  <form action="/printer/destroy/{{$printer->id}}" method="POST">
                    <input type="submit" class="btn btn-sm btn-outline-danger" value="Delete">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  </form>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
          
        </div>
      </div>
    </div>
    <div class="col-2"></div>
  </div>
  <div class="row">
    <div class="col-2"> </div>
    <div class="col-8">
      <div class="card">
        <div class="card-header">
          Filaments
        </div>
        <div class="card-body">
          <form id="new_printer_form" action="filament/store" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Filament Name" name="filament_name" required aria-label="Filament Name" aria-describedby="basic-addon1">
              <input type="text" class="form-control" id="background_picker" aria-describedby="basic-addon1">
              <input type="text" class="form-control" id="background_color" placeholder="Background Color" name="background_color" required aria-label="Background Color" aria-describedby="basic-addon1">
              <input type="text" class="form-control" id="text_picker">
              <input type="text" class="form-control" id="text_color" placeholder="Text Color" name="text_color" required aria-label="Text Color" aria-describedby="basic-addon1">
              <div class="input-group-append">
                <input type="submit" class="btn btn-outline-primary" value="Add New Filament">
              </div>
            </div>
          </form>
          <table class="text-center table table-sm">
            <thead>
              <th>Filament Name</th>
              <th>Background Color</th>
              <th>Status</th>
              <th>Toggle</th>
              <th>Delete</th>
            </thead>
            <tbody>
            @foreach($filaments as $filament)
              <tr>
                <td>{{$filament->filament_name}}</td>
                <td style="background-color: {{$filament->background_color}}; color: {{$filament->text_color}};">
                  Background: {{$filament->background_color}} | 
                  Text: {{$filament->text_color}}
                </td>
                @if($filament->active == 1)
                  <td class="text-success">Active</td>
                  <td>
                    <form action="/filament/toggle/{{$filament->id}}" method="POST">
                      <input type="submit" class="btn btn-sm btn-outline-warning" value="Retire">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                  </td>
                @else
                  <td class="text-danger">Retired</td>
                  <td>
                    <form action="/filament/toggle/{{$filament->id}}" method="POST">
                      <input type="submit" class="btn btn-sm btn-outline-success" value="Activate">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                  </td>
                @endif
                <td>
                  <form action="/filament/destroy/{{$filament->id}}" method="POST">
                    <input type="submit" class="btn btn-sm btn-outline-danger" value="Delete">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  </form>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
          
        </div>
      </div>
    </div>
    <div class="col-2"></div>
  </div>
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>
<script>
$("#text_picker").spectrum({
    color: "#fff",
    change: function(color) {
        $("#text_color").val(color.toHexString());
    }
});
$("#background_picker").spectrum({
  color: "#000",
  change: function(color) {
      $("#background_color").val(color.toHexString());
  }
});
</script>
@endsection