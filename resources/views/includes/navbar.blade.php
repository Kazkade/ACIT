<nav class="no-print navbar navbar-expand-lg navbar-dark fixed-top"
   @if(Auth::guest())
      style="background-color: #263238"
   @else
      @if(Auth::user()->admin == 1)
        style="background-color: #3F729B"
      @else
        style="background-color: #42f46e"
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
        <!-- Operations -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Operations
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="/transfers/create?transfer_type=1">Collections</a>
            <a class="dropdown-item" href="/transfers/create?transfer_type=2">Processing</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="/orders">Orders</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="/deliveries">Delivery Prep</a>
            <a class="dropdown-item" href="/overages">Overages</a>
          </div>
        </li>
        <!-- Data -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Data
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="{{route('parts.index')}}">Parts</a>
            @if(Auth::user()->admin == 1)
              <a class="dropdown-item" href="/profiles">Profiles</a>
              @if($config["show_locations"] == 1 || $config["dev_mode"] == 1)
                <a class="dropdown-item" href="{{route('locations.index')}}">Locations</a>
              @endif
              <div class="dropdown-divider"></div>
            @endif
            <a class="dropdown-item" href="{{route('transfers.index')}}">Transfers</a>
            <a class="dropdown-item" href="/bags">Bags</a>
            @if(Auth::user()->admin == 1)
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/orders">Orders</a>
              <a class="dropdown-item" href="/deliveries/all">Deliveries</a>
              <a class="dropdown-item" href="/overages">Overages</a>
            @endif
          </div>
        </li>
        <!-- Reports -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Reports
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="/reports/print_list">Print List</a>
            <a class="dropdown-item" href="/deliveries/all">Delivery Report</a>
            <a class="dropdown-item" href="/reports/filament_usage">Filament Usage Report</a>
            <a class="dropdown-item" href="/reports/weekly_scrap">Weekly Scrap Report</a>
          </div>
        </li>
        @if(Auth::user()->admin == 1)
        <!-- Admin -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Admin
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="{{route('configuration.index')}}">Configuration</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="/users">Users</a>
          </div>
        </li>
        @endif
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
                    <a class="dropdown-item disabled" disabled="disabled" href=""><i class="fa fa-btn fa-file-text-o mr-3"></i>Stats</a>
                    <a class="dropdown-item" href=""><i class="fa fa-btn fa-info mr-3 "></i>Wiki</a>
                    <a class="dropdown-item" href=""><i class="fa fa-btn fa-question mr-3"></i>About ACIT</a>
                    <a class="dropdown-item" href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out mr-3"></i>Logout</a>
                  </div>
              </li>
          @endif
      </ul>
    </div>
  </nav>
<div style="height: 80px;">
  
</div>