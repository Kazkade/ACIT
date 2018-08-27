@extends('layouts.app') 

@section('content')
<div class="container">
  <div class="card">
      <div class="card-header">
          <span class="h2 m-2">{{$part->part_serial}} ({{$part->part_name}})</span>
        <div class="float-right text-right">
          Created: {{$part->created_at}}
          <br>
          Last Updated: {{$part->updated_at}}
        </div>
      </div>
      <div class="card-body">
        <h5 class="card-title">Version: <strong>{{$part->part_version}}</strong></h5>
        <h5 class="card-title">Color: <strong>{{$part->part_color}}</strong></h5>
        <h5 class="card-title">Weight: <strong>{{$part->part_weight}}g</strong></h5>
        @if($part->part_cleaned == 0) 
          <h5 class="card-title">Cleaned: <strong>No</strong></h5>
        @else 
          <h5 class="card-title">Cleaned: <strong>Yes</strong></h5>
        @endif
        <h5 class="card-title">Inventory: <strong>{{$part->part_quantity}}</strong></h5>
        <br>
        <table class="table text-center">
          <thead>
            <th colspan=8>Inventory Breakdown</th>
          </thead>
          <thead>
              <th class="text-warning" title="There isn't a way to account for parts in collections, yet.">Collections</th>
              <th class="text-primary">Processing</th>
              <th class="text-success">Backstock</th>
              <th class="text-danger">Failed</th>
          </thead>
          <tbody>
            <tr>
              <td class="text-secondary">0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table text-center">
          <thead>
            <th colspan=8>Part History</th>
          </thead>
          <thead>
            <th>Ordered</th>
            <th>Created</th> <!-- Just the raw totals -->
            <th>Delivered</th>
            <th>Fail Rate</th> <!-- Calculation: Failed / Created -->
            <th>Waste</th> <!-- Calculation: Failed * Weight -->
          </thead>
          <!-- Sort through locations and list all inventories and total -->
          <tbody>
            <tr>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0%</td>
              <td>0g</td><!-- Total Filament Grams wasted -->
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        <a href="/parts" class="btn btn-outline-primary">Go Back</a>
        @if(Auth::user()->account_type == 2)
          <form action="{{ route('locations.destroy' , $location->id)}}" class="float-right" method="POST">
            <a href="{{route('locations.edit', $location->id)}}" class="btn btn-outline-info">Edit</a>
              <input name="_method" type="hidden" value="DELETE">
              {{ csrf_field() }}
            <button type="submit" class="btn btn-outline-danger ">&#10006 Delete</button>
          </form>
        @endif
      </div>
  </div>
</div>
@endsection