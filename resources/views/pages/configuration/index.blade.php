@extends('layouts.app') 
@section('content')
<!-- Configuration -->
<div class="container-fluid">
  <div class="row">
    <div class="col-2"> </div>
    <div class="col-8">
      @if(Auth::user()->account_type < 2)
      <h5>
        You need to be an admin to view this.
      </h5>
      @else
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
                      <input type="submit" class="btn btn-sm btn-outline-warning" value="Activate">
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
    @endif
    <div class="col-2"></div>
  </div>
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>
@endsection