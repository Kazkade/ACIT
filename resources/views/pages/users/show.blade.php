@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2"></div>
    <div class="col-8 ">
      <div id="message"></div>
      <h3>
        Users
      </h3>
      <div id="user_table"></div>
    </div>
  </div>
  
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>
<!--<script>
var user_data = [
  @foreach($users as $user)
    {
      last_updated: "{{$user->updated_at}}", 
      id:{{$user->id}}, 
      first_name:"{{$user->first_name}}",
      last_name: "{{$user->last_name}}", 
      username:"{{$user->username}}", 
      email:"{{$user->email}}", 
      active:"{{$user->active}}", 
      admin:"{{$user->admin}}"
  },
  @endforeach
]

var editable = 
    @if(Auth::user()->admin == 1)
      true;
    @else
      false;
    @endif
  
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
  data: user_data,
	tooltips:false,
	addRowPos:"top",
	history:true,
	pagination:"local",
	paginationSize:10,
	movableColumns:false,
	resizableRows:false,
	initialSort:[
		{column:"first_name", dir:"asc"},
    {column:"last_name", dir:"asc"}
	],
	columns:[
		{title:"Last Updated",  align: "center", field:"last_updated", width: 250, editor: false},
		{title:"First", field:"first_name", align: "center", editor: editable},
    {title:"Last", field:"last_name", align: "center", editor: editable},
		{title:"Username", field:"username", align: "center", editor: editable},
		{title:"Email", field:"email", align: "center", width: 300, editor: editable},
		{title:"Active", field:"active", width:120,  align:"center", formatter:"tickCross", sorter:"boolean", editor: editable},
    {title:"Admin", field:"admin", width:120,  align:"center", formatter:"tickCross", sorter:"boolean", editor: editable},
    {title:"View",  align: "center", field:"id", width:100, formatter:function(cell, formatterParams){
      var value = cell.getValue();
      if(value > 0){
        return "<a class='btn btn-sm btn-outline-dark d-block' href='users/"+value+"'>View</span>";
      }else{
        return "<a class='btn btn-sm btn-outline-dark disabled d-block' disabled='disabled' href='#'>View</span>";
      }
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
      url: "users/json_update/"+JSON.stringify(row),
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
</script>
-->
<script>
var config = {
  type: 'pie',
  data: {
    datasets: [{
      data: [
        @foreach($data as $row)
          
        @endforeach
      ],
      backgroundColor: [
        @foreach($filaments as $filament)
          @if($filament->filament->name == "Black")
            "#000000",        
          @else
            {{$filament->background_color}},
          @endif
        @endforeach
      ],
      label: 'User QC Rates'
    }],
    labels: [
      @foreach($filaments as $filament)
        {{$filament->filament_name}},
      @endforeach
    ]
  },
  options: {
    responsive: true
  }
};
</script>
@endsection