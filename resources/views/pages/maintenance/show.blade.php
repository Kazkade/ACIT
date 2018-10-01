@extends('layouts.app') @section('content')
<div class="container">
  <div class="row">
    <div class="col-6">
      <form method="POST" action="{{route('machines.update', $machine->id)}}">
        <input hidden name="_method" value="PUT">
        <input hidden name="_token" value="{{CSRF_TOKEN()}}">
        <input hidden name="machine_id" value="{{$machine->id}}">
        <table class="table table-highlight table-sm">
          <thead>
            <th colspan=2>
              <h3>Machine Information</h3>
            </th>
          </thead>
            <tbody>
              <tr>
                <th>Serial:</th>
                <td>
                  @if(Auth::user()->admin==0)
                  <input class="form-control disabled" disabled="disabled" name="machine_serial" value="{{$machine->machine_serial}}"> @else
                  <input class="form-control" name="machine_serial" value="{{$machine->machine_serial}}"> @endif
                </td>
              </tr>
              <tr>
                <th>Identifier:</th>
                <td>
                  @if(Auth::user()->admin==0)
                  <input class="form-control disabled" disabled="disabled" name="identifier" value="{{$machine->identifier}}"> @else
                  <input class="form-control" name="identifier" value="{{$machine->identifier}}"> @endif
                </td>
              </tr>
              <tr>
                <th>Type:</th>
                <td>
                  @if(Auth::user()->admin == 0)
                  <input class="form-control disabled" disabled="disabled" name="printer_name" value="{{$machine->printer_name}}"> @else
                  <select class="form-control" name="printer_name">
                    @foreach($printers as $printer)
                      @if($machine->printer_name == $printer->name)
                        <option selected value="{{$printer->name}}">{{$printer->name}}</option>
                      @else
                        <option value="{{$printer->name}}">{{$printer->name}}</option>
                      @endif
                    @endforeach
                  </select> @endif
                </td>
              </tr>
              <tr>
                <th>Filament:</th>
                <td>
                  <select class="form-control" name="filament_name">
                    @foreach($filaments as $filament)
                      @if($machine->filament_name == $filament->filament_name)
                        <option selected value="{{$filament->filament_name}}">{{$filament->filament_name}}</option>
                      @else
                        <option value="{{$filament->filament_name}}">{{$filament->filament_name}}</option>
                      @endif
                    @endforeach
                  </select>
                </td>
              </tr>
              <tr>
                <th>Created</th>
                <td><input class="form-control disabled" disabled="disabled" value="{{$machine->created_at}}"></td>
              </tr>
              <tr>
                <th>Last Maintenance</th>
                <td>
                  <input class="form-control disabled" disabled="disabled" value="{{date('m-d-Y @ H:i', strtotime($machine->last_maintenance))}}" >
                </td>
              </tr>
            </tbody>
            <tfoot>
              <td colspan=2>
                <input type="submit" class="btn btn-outline-success w-100 " value="Update">
              </td>
            </tfoot>
        </table>
      </form>
    </div>
    <div class="col-6">
      <form method="POST" id="new_maintenance_form" action="/maintenance">
        <input hidden name="_method" value="POST">
        <input hidden name="_token" value="{{CSRF_TOKEN()}}">
        <input hidden name="machine_id" value="{{$machine->id}}">
        <input hidden name="user_id" value="{{Auth::user()->id}}">
        <input hidden name="json_parts" value="">
        <table class="table table-highlight table-sm">
          <thead>
            <th colspan=5>
              <h4>New Maintenance Task</h4>
            </th>
          </thead>
          <tbody id="table_body">
            <tr>
              <th>Task:</th>
              <td colspan=2>
                <textarea cols=40 rows=7 class="float-right" required name="task"></textarea>
              </td>
            </tr>
            <tr><td colspan=3>Used Parts <a href="#" id="add_new_row" class="btn btn-outline-primary btn-sm float-right">❖ Add Row</a></td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan=10>
                <input class="btn btn-outline-success w-100 " type="submit" id="submit_new" value="Record">
              </td>
            </tr>
          </tfoot>
        </table>
      </form>
    </div>
  </div>
  <div class="row">
    <div id="log-table" class="mt-3"></div>
  </div>
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>


<script>
  var log_data = [
    @foreach($log as $row) {
      "date": "{{date('m-d-Y @ H:i', strtotime($row->updated_at))}}",
      "user_name": "{{$row->first_name}} {{$row->last_name}}",
      "task": "{{$row->task}}",
    },
    @endforeach
  ];

  $("#log-table").tabulator({
    layout: "fitColumns", //fit columns to width of table (optional)
    placeholder: "Log is empty. :( ",
    pagination: "local",
    paginationSize: 20,
    columns: [{
      title: "Maintenance Log for {{$machine->machine_serial}}",
      columns: [
                {
          title: "Date",
          field: "date",
          headerFilter: "input",
          align: "center",
          width: 200,
          editor: false
        },
        {
          title: "User",
          field: "user_name",
          headerFilter: "input",
          align: "center",
          width: 200,
          editor: false
        },

        {
          title: "Task",
          field: "task",
          headerFilter: "input",
          align: "left"
        },
      ],
    }, ],
  });

  $("#log-table").tabulator("setData", log_data);
</script>

<script>
var counter = 0;
  
function used_part_row_template(id)
{
  var template = `
    <tr class="part_row" id='part_row_`+id+`'>
      <td>
        <input value='' class='part_input form-control' placeholder='XX-XX0000' style="text-transform:uppercase;" required>
      </td>
      <td>
        <input value='' class='quantity_input form-control' type='number' step=1 placeholder="Quantity" required>
      </td>
      <td>
        <a href='#' class='remove_part_button btn btn-outline-danger float-right' rowid='remove_part_`+id+`' class='float-right btn btn-outline-danger'>X</a>
      </td>
    </tr>
  `;
  $('#table_body').append(template);
  $('.part_input').mask('AA-AA0000');
}
  
$('#submit_new').on('click', function(event) {
  var parts = [];
  var rows = $('.part_row').map(function() {
    var tmp = {
      part: $(this).find('.part_input').val().toUpperCase(),
      quantity: $(this).find('.quantity_input').val(),
    }
    parts.push(tmp);
  });
  var json = JSON.stringify(parts);
  $('input[name=json_parts]').attr('value', json);
  return true;
});
  
$(document).on('click', '.remove_part_button', function() {
  console.log(this);
  $(this).parent().parent().remove();
});
  
$('#add_new_row').on('click', function() {
  $('#table_body').append(used_part_row_template(counter));
  counter++;
});

  
</script>
@endsection