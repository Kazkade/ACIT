@if($errors->count() > 0)
  @foreach($errors->all() as $error)
    <div class="container">
      <div class="alert alert-danger alert-dismissible fade show">
        {{$error}}
      </div>
    </div>
  @endforeach
@endif

@if(session('success'))
  <div class="container">
    <div class="alert alert-success alert-dismissible fade show">
      {{session('success')}}  
    </div>
  </div>
@endif

@if(session('error'))
  <div class="container">
    <div class="alert alert-danger alert-dismissible fade show">
      {{session('error')}}  
    </div>
  </div>
@endif

@if(session('info'))
  <div class="container">
    <div class="alert alert-info alert-dismissible fade show">
      {{session('error')}}  
    </div>
  </div>
@endif

@if(session('warning'))
  <div class="container">
    <div class="alert alert-warning alert-dismissible fade show">
      {{session('error')}}  
    </div>
  </div>
@endif