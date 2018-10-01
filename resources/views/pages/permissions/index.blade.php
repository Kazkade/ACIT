@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2"></div>
    <div class="col-8 ">
      <div id="message"></div>
      @if(\App\PermissionEnforcer::Protect("perm_keys_create"))
      <h3>
        Permission Keys
      </h3>
        <span>Create Permission</span>
        <form id="new-form" class="my-3">
          <input hidden name="_token" required value="{{ csrf_token() }}">
          <input hidden name="_method" required  value="POST">
          <div class="input-group">
            <input class="form-control" autofocus style="text-transform:lowercase;" id="permission_key" placeholder="Permission Key"  required name="permission_key">
            <input class="form-control" placeholder="Default Value"  required name="default_value" id="default_value">
            <input class="form-control" placeholder="Description"  required name="description" id="description">
            <a id="submit-form" class="btn btn-outline-success form-control">Add</a>
          </div>
        </form>
      @endif
      <div id="user_table"></div>
    </div>
  </div>
  
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>
<script>

@if(\App\PermissionEnforcer::Protect("perm_keys_modify"))
  var editable = true;
@else
  var editable = false;
@endif
  
@if(\App\PermissionEnforcer::Protect("perm_keys_index"))
if(editable)
{
  $('#message').append(`
    <div class='alert alert-warning' role='alert'>
      This data is live and editable. Any changes made will be saved.
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  `);
}
$("#user_table").tabulator({
	layout:"fitColumns",
  ajaxURL: '/permissions/ajax',
  ajaxConfig:"GET",
	tooltips:false,
	addRowPos:"top",
	history:true,
	pagination:"local",
	paginationSize:50,
	movableColumns:false,
  variableHeight: true,
	resizableRows:false,
	initialSort:[
    {column:"key_name", dir:"asc"}
	],
	columns:[
		{title:"Permission Key",  align: "left", field:"key_name", headerFilter:"input", width: 250, editor: false},
		{title:"Default", field:"default_value", align: "center", headerFilter:"input", width: 150, editor: editable},
		{title:"Description", field:"description",headerFilter:"input",  align: "left", formatter:"textarea", editor: editable},
    @if(\App\PermissionEnforcer::Protect("perm_keys_delete"))
      {title:"Delete",  align: "center", field:"key_name", width:100, formatter:function(cell, formatterParams){
        var value = cell.getValue();
        @if(\App\PermissionEnforcer::Protect())
          return "<a class='btn btn-sm btn-outline-danger d-block' href='permissions/destroy/"+value+"'>Delete</span>";
        @endif

      }},
    @endif
	],
  cellEdited:function(cell){
    var row = cell.getData();
    if(row.description == "")
    {
      row.description = "None";
    }
    console.log(row);
    $.ajax({
      type: "GET",
      headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}",
      },
      dataType: "JSON",
      url: "permissions/update_permission/"+row.key_name+"/"+row.default_value+"/"+row.description,
      success: function(msg) {
        row = cell.getElement()
          .animate({ backgroundColor: "#c7ffc4"}, 100)
          .animate({ backgroundColor: "#FFFFFF"}, 200);
      },
      error: function(xhr, err, msg)
      {
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
  },
});
@else
</script>
<div class="jumbotron">You don't have permission to view permission keys.</span>
<script>
@endif
  
@if(\App\PermissionEnforcer::Protect("perm_keys_create"))
$('#submit-form').on('click', function(event) {
  event.preventDefault();

  var default_value = $('#default_value').val();
  var permission_key = $('#permission_key').val().replace(/ /g, "_");;
  var description = $('#description').val();
  
  if(valid_input(default_value) || valid_input(permission_key) || valid_input(description))
  {
    $('#message').append(`
        <div class='alert alert-danger' role='alert'>
          There was an error saving data: <strong>The form is incomplete.</strong>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      `);
    return null;
  }
  
  //return null;
  $.ajax({
      type: "GET",
      headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}",
      },
      dataType: "JSON",
      url: "permissions/"+permission_key+"/"+default_value+"/"+description,
      success: function(msg) {
        console.log(msg);
        $('#message').append(`
          <div class='alert alert-warning' role='alert'>
            Response was: <strong>`+msg.original+`</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        `);
        $("#user_table").tabulator("setData", "/permissions/ajax", {}, "GET");
      },
      error: function(xhr, err, msg)
      {
        $('#message').append(`
          <div class='alert alert-danger' role='alert'>
            There was an error saving data: <strong>`+msg+`</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        `);
      }
    });
});
function valid_input(input)
{
  if(input == "")
    {
      return true;
    }
    else
    {
      return false;
    }
}
@endif
</script>
@endsection