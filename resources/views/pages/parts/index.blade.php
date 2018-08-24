@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2">
      @if(Auth::user()->account_type == 2)
      <h3>
        Add New Part
      </h3>
      <form action="{{route('parts.store')}}", method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-row">
          <div class="form-group col-md-12">
            <label for="part_name">Part Name</label>
            <input type="text" class="form-control" name="part_name" id="part_name" placeholder="Part Name">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="inputAddress">Serial</label>
            <input type="text" class="form-control" name="part_serial" id="part_serial" placeholder="AS-PR0123">
          </div>
          <div class="form-group col-md-6">
            <label for="part_color">Color</label>
            <select id="part_color" name="part_color" class="form-control">
              <option selected>ABS Black</option>
              <option>ABS Green</option>
              <option>ABS Gray</option>
              <option>Flexy Black</option>
              <option>Flexy Green</option>
              <option>Other</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="part_version">Version</label>
            <input type="text" placeholder="0.2.4.6d" class="form-control" name="part_version"  id="part_version">
          </div>
          <div class="form-group col-md-6">
            <label for="part_weight">Weight (g)</label>
            <input type="number" placeholder="0.123" step="0.0001" class="form-control" name="part_weight" id="part_weight">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="inputAddress">Print Time</label>
            <input type="text" class="form-control" name="print_time" id="part_time" placeholder="123">
          </div>
          <div class="form-group col-md-6">
            <label for="inputAddress">Rec. Bagging</label>
            <input type="text" class="form-control" name="rec_bagging" id="part_bagging" placeholder="20">
          </div>
        </div>
        <div class="form-group">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="part_cleaned"  id="gridCheck">
            <label class="form-check-label" for="gridCheck">
              Does this part need extra cleaning?
            </label>
          </div>
        </div>
        <div class="form-group">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="part_moratorium"  id="gridCheck">
            <label class="form-check-label" for="gridCheck">
              Is this part in moratorium?
            </label>
          </div>
        </div>
        <button type="submit" class="btn btn-outline-primary w-100">&#10010 Add Part</button>
      </form>
      @endif
    </div>
    <div class="col-9 ">
      <h3>
        Parts
      </h3>
      <table class="table table-striped table-sm table-hover text-center align-middle">
        <thead>
          <tr>
            <th scope="col">Updated</th>
            <th scope="col" style="text-align: left !important;">Name</th>
            <th scope="col">Serial</th>
            <th scope="col">Color</th>
            <th scope="col">Version</th>
            <th scope="col">Cleaned</th>
            <th scope="col">Weight</th>
            <th scope="col">Stock</th>
            <th scope="col">Bags</th>
            <th scope="col">Total</th>
            <th scope="col">View</th>
            <th scope="col">Edit</th>
          </tr>
        </thead>
        <tbody>
          @if(count($parts) > 0)
            @foreach($parts as $part)
              @if($part->in_moratorium == 0)
                <tr>
              @else
                <tr style="background-color: #bbb; opacity: 0.5">
              @endif    
                <td scope="row">{{date('d/m/y @ H:i', strtotime($part->updated_at))}}</td>
                <td style="text-align: left !important;">{{$part->part_name}}</td>
                <td>{{$part->part_serial}}</td>
                <td>{{$part->part_color}}</td>
                <td>{{$part->part_version}}</td>
                @if($part->part_cleaned == 1)
                  <td><span class="text-success">&#10004</span></td>
                @else
                  <td><span class="text-danger">&#10008</span></td>
                @endif
                <td>{{$part->part_weight}}g</td>
                <td>{{$part->inventory}}</td>
                <td>{{$part->bag_count}}</td>
                <td>{{$part->total}}</td>
                @if($part->in_moratorium == 0)
                  <td><a href="{{route('parts.show', $part->id)}}" class="btn btn-sm btn-outline-secondary d-block">&#10070</a></td>
                @else
                  @if(Auth::user()->account_type == 2)
                    <td><a href="{{route('parts.show', $part->id)}}" class="btn btn-sm btn-outline-secondary d-block">&#10070</a></td>
                  @endif
                @endif
                @if(Auth::user()->account_type == 2)
                  <td><a href="{{route('parts.edit', $part->id)}}" class="btn btn-sm btn-outline-info d-block">&#9998</a></td>
                @endif
              </tr>
            @endforeach 
          @else
            <td colspan=9>
              There are no parts yet. You'll need to add one.
            </td>
          @endif
        </tbody>
      </table>
      <nav aria-label="page navigation example">
        {{$parts->links()}}
      </nav>
    </div>
  </div>
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>
@endsection