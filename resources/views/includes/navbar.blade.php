<nav class="navbar navbar-expand-lg navbar-dark fixed-top"
   @if(Auth::guest())
      style="background-color: #263238"
   @else
      @if(Auth::user()->account_type == 2)
        style="background-color: #3F729B"
      @else
        style="background-color: #263238"
      @endif
   @endif

     >
  <div class="container">
    <a class="navbar-brand" href="#">{{config('app.name', 'ACIT')}}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      @if(Auth::guest()) 
      @else
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="/dashboard">Dashboard</a>
        </li>
        <!-- Data Entry -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Transfers
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="/transfers/create?transfer_type=1">Collections</a>
            <a class="dropdown-item" href="/transfers/create?transfer_type=2">Processing</a>
            @if(Auth::user()->account_type == 2)
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/transfers/create?transfer_type=2">Audit</a>
            @endif
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="/deliveries">Deliveries</a>
          </div>
        </li>
        <!-- Data -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Data
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="{{route('parts.index')}}">Parts</a>
            <a class="dropdown-item" href="{{route('locations.index')}}">Locations</a>
            <a class="dropdown-item" href="{{route('transfers.index')}}">Transfers</a>
          </div>
        </li>
        @if(Auth::user()->account_type == 2)
        <!-- Admin -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Admin
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="/transfers/create?transfer_type=2">Audit</a>
            <a class="dropdown-item disabled" disabled="disabled" href="#">Overrides</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" disabled href="#">Configuration</a>
            
          </div>
        </li>
        @endif
        <!-- Reports -->
        <li class="nav-item">
          <a class="nav-link disabled" href="#">Reports</a>
        </li>
      </ul>
      @endif
    </div>
      <!-- Right Side Of Navbar -->
      <ul class="navbar-nav navbar-right">
          <!-- Authentication Links -->
          @if (Auth::guest())
              <li><a href="{{ url('/login') }}" class="btn btn-outline-light ml-2 mr-2">Login</a></li>
              <li><a href="{{ url('/register') }}" class="btn btn-outline-light ml-2 mr-2">Register</a></li>
          @else
              <li class="nav-item dropdown">
                  <a href="#" class="dropdown-toggle nav-link" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      {{Auth::user()->first_name}} {{Auth::user()->last_name}}<span class="caret"></span>
                  </a>
                  <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a>
                  </div>
              </li>
          @endif
      </ul>
    </div>
  </nav>
<div style="height: 80px;">
  
</div>