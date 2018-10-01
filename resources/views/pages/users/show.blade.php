@extends('layouts.app') 
@section('content')
<style>
.switch {
  position: relative;
  float: right;
  display: inline-block;
  width: 44px;
  height: 18px;
}
  
td {
  height: 50px;
  vertical-align: middle;
}

.switch input {display:none;}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  margin-bottom: -3px;
  margin-top: 3px;
  margin-right: 3px;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 12px;
  width: 12px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(23px);
  -ms-transform: translateX(23px);
  transform: translateX(23px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
<div class="container">
  <div class="row">
    <div class="col my-3">
      <div class="card">
        <div class="card-header">
          <h3>User <b>{{$user->username}}</b> ({{$user->first_name}} {{$user->last_name}})</h3>
        </div>
        <div class="card-header">
          <form class="float-right form-inline" action="/users/{{$user->id}}" method="POST">
            <input hidden name="_method" value="DELETE">
            <input hidden name="_token" value="{{csrf_token()}}">
            <input type="submit" class="btn btn-outline-danger" value="X Delete {{$user->first_name}}">
          </form>
        </div>
        <div class="card-body">
          <h4 class="card-title">User Statistics</h4>
          
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="card">
        
      </div>
    </div>
    <div class="col">
      <div class="card">
        
      </div>
      <div class="card">
        
      </div>
      <div class="card">
        
      </div>
    </div>
    <div class="col">
      <div class="card">
        <div class="card-header">
          Permissions
        </div>
        <div class="card-body">
          <table class="w-100 table-striped">
            <tbody>
              <tr>
                <td><input readonly class="form-control bg-primary text-light" disabled="disabled" style="margin-left: 5px;" value="admin"></td>
                @if(\App\PermissionEnforcer::Protect("users_change_permissions"))
                  <td>
                    <label class="switch">
                      <input type="checkbox" class="permission" user_id="{{$user->id}}" perm="admin"
                             @if(Auth::user()->admin == 1)
                             checked
                             @endif
                             >
                      <span class="slider round"></span>
                    </label>
                  </td>
                @endif
              </tr>
              @foreach($permissions as $permission)
                <tr>
                  <td><input class="form-control dislabed" disabled="disabled" style="margin-left: 5px;" value="{{$permission->permission}}"></td>
                  @if(\App\PermissionEnforcer::Protect("users_change_permissions"))
                    <td>
                      <label class="switch">
                        <input type="checkbox" class="permission" user_id="{{$user->id}}" perm="{{$permission->permission}}"
                               @if($permission->value == 1)
                               checked
                               @endif
                               >
                        <span class="slider round"></span>
                      </label>
                    </td>
                  @endif
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>
<script>
$('.permission').on('click', function() {
  
  var permission = $(this).attr('perm');
  var value = ($(this).prop('checked')) ? 1 : 0;
  var user = $(this).attr('user_id');
  
  $.ajax({
    type: "GET",
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}",
    },
    dataType: "JSON",
    url: "/users/update_permission/"+user+"/"+permission+"/"+value,
    success: function(msg) {
      console.log(msg);
    },
    error: function(xhr, err, msg) {
      console.log(err + ":" + msg);
    }
  });
  
});
</script>
@endsection