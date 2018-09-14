@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2"> </div>
    <div class="col-8">
      <form action="{{route('transfers.store')}}" id="transfer_form" class="w-100" method="POST">
        <input hidden readonly required name="transfer_type" value="" id="transfer_type" >
        <div class="card w-100 border-dark">
          <div class="card-header bg-dark text-white h4">
            <?php if($_GET['transfer_type'] == 0) {?>
               Default Transfer
            <?php } ?>
            <?php if($_GET['transfer_type'] == 1) {?>
               Collection Transfer
            <?php } ?>
            <?php if($_GET['transfer_type'] == 2) {?>
               Backstock Transfer
            <?php } ?>
          </div>
          <div class="card-body">
            <div class="form-row">
                <div class="col-md-4 mb-3">
                  <label for="part_name">Part Name</label>
                  <input type="text" class="form-control disabled" disabled="disabled" id="part_name" placeholder="Part Name" required>
                  <input hidden value="" name="part_id" id="part_id">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="part_serial" >Serial</label>
                  <input 
                         type="text" 
                         autofocus
                         maxlength="9"
                         style="text-transform:uppercase" 
                         class="form-control" 
                         name="part_serial" 
                         id="part_serial" 
                         placeholder="XX-XX0000" required />
                </div>
                <div class="col-md-4 mb-3">
                  <label for="tech_name">Technician</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="tech_name">&#9997</span>
                    </div>
                    <input 
                           type="text" class="form-control disabled"
                           disabled="disabled" id="tech_name" autocomplete="name"
                           value="{{Auth::user()->first_name}} {{Auth::user()->last_name}}"
                    >
                    <input hidden name="user_id" value="{{Auth::user()->id}}">
                    
                  </div>
                </div>
              </div>
              <div id="qa_row" class="form-row justify-content-center">
                <div class="col-md-3 mb-3">
                  <label for="quantity">Pass</label>
                  <input class="form-control alert-success" id="quantity" name="quantity" placeholder="Pass">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="fails">Fail</label>
                  <input class="form-control alert-danger" id="fails" name="fails" placeholder="Fails">
                  <input hidden id="fail_location_id" value="" name="fail_location_id">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="from_location_id">Transfer Form</label>
                  <select class="form-control 
                          @if(Auth::user()->admin == 1)
                            disabled"
                            disabled='disabled'
                          @endif
                          readonly
                          name="from_location_id" id="from_location_id">
                    @foreach($locations as $location)
                      <option value="{{$location->id}}" loc_name="$location->location_name">{{$location->location_name}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="to_location_id">Transfer To</label>
                  <select class="form-control
                         @if(Auth::user()->admin == 1)
                            disabled"
                            disabled='disabled'
                          @endif
                          readonly
                          name="to_location_id" id="to_location_id">
                    @foreach($locations as $location)
                      <option value="{{$location->id}}" loc_name="$location->location_name">{{$location->location_name}}</option>
                    @endforeach
                  </select>
                </div>

              </div>
              <div id="bagging_row" class="form-row justify-content-center">
                  <div class="col-md-3 mb-3">
                    <label for="quantity">Bag Amount</label>
                    <input class="form-control" id="bag_amount" name="bag_amount" value="0" placeholder="Bag Amount">
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="quantity">Before Bags</label>
                    <input class="form-control" readonly id="before_bags" name="before_bags" value=0 placeholder="-">
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="quantity">Created Bags</label>
                    <input class="form-control" readonly id="created_bags" name="created_bags" placeholder="0">
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="quantity">After Bags</label>
                    <input class="form-control" disabled id="after_bags" name="after_bags" placeholder="0">
                  </div>
              </div>
          </div>
          <div class="card-footer">
            <button type="submit" id="submit_button" class="btn btn-outline-primary">Create</button>
            <span class='lead ml-2' id="helper_text"></span>
          </div>
        </div>
      </form>
    </div>
    <div class="col-2"></div>
  </div>
  <div class="row my-3">
    <div class="col-2"></div>
    <div class="col-8">
      <div id="report-table"></div>
    </div>
    <div class="col-2"></div>
  </div>
</div>

<script type="text/javascript">
  
// Setup Transfer Types
var transfer_type = <?php if(!empty($_GET['transfer_type'])) { echo $_GET['transfer_type']; } else { echo 0; }?>;  
  
$(document).ready(function() {
  
  // Initialize Part Serial Mask
  $('#part_serial').mask('AA-AA0000');
  
  // Initialize The Form Transfer Type
  $('#transfer_type').attr('value', transfer_type);
  
  $('#qa_row').hide("blind", function() {});
  $('#bagging_row').hide("blind", function() {});
  
  // Part Info Container
  var partInfo;
  
  // Initialize Submit button to disabled.
  $('#submit_button')
    .attr('disabled', 'disabled')
    .addClass('disabled btn-outline-primary')
    .removeClass('btn-primary');
  
  // When the Serial Input changes.
  $('#part_serial').on('change', function() {
    $.ajax({
      url: "/ajax/part_info/"+$('#part_serial').val(),
      type:"GET",
      success:function(data){
          data = data[0];
          if(typeof data !== 'undefined' && data.id != null) {
            SetupTransferLocations(data.part_cleaned);
            UpdateForms(data);
            $('#part_name').val(data.part_name);
            $('input[name=part_id]').attr('value', data.id);
            $('#submit_button')
              .removeAttr('disabled')
              .removeClass('disabled btn-outline-primary')
              .addClass('btn-primary');
            $("#before_bags").val(data.backstock);
            $('#bag_amount').val(data.recommended_bagging);
            $('#quantity').focus();
          }
          else 
          {
            partInfo = null;
            SetupTransferLocations(2);
            $('#part_name').val('No Part Returned');
            $('input[name=part_id]').attr('value', 0);
            $('#submit_button')
              .attr('disabled', 'disabled')
              .addClass('disabled btn-outline-primary')
              .removeClass('btn-primary');
            $("#before_bags").val(0);
            $('#bag_amount').val(0);
            ResetCard();
          }
        
          
      },
      error:function(xhr, errorType, exception){
          ResetCard();
          $('#part_name').val('Error');
      }
    });
    
  });
  
  $("input[name=quantity]").on('change', function() {
    CalculateBags();
  });
  
  $('input[name=bag_amount]').on('change', function() {
    CalculateBags();
  });
  
  function SetupTransferLocations(part_cleaned)
  {
    var locations = {
      @foreach($locations as $location)
        {{$location->location_name}}:"{{$location->id}}",
      @endforeach
    }
                  
    $('#fail_location_id').attr("value", locations.Fails);
    
    if(transfer_type == 1 && part_cleaned == 0)
    {
      $('#from_location_id').val(locations.Collections);
      $('#to_location_id').val(locations.Backstock);
      $('#qa_row').show("highlight", function() {});
      $('#bagging_row').show("highlight", function() {});
    }
    
    if(transfer_type == 1 && part_cleaned == 1)
    {
      $('#from_location_id').val(locations.Collections);
      $('#to_location_id').val(locations.Processing);
      $('#qa_row').show("highlight", function() {});
      $('#bagging_row').hide("blind", function() {});
    }
    
    if(transfer_type == 2)
    {
      $('#from_location_id').val(locations.Processing);
      $('#to_location_id').val(locations.Backstock);
      $('#to_location_id').removeAttr('readonly');
      $('#qa_row').show("highlight", function() {});
      $('#bagging_row').show("highlight", function() {});
    }
    
    if(part_cleaned == 2)
    {
      $('#from_location_id').val(locations.Collections);
      $('#to_location_id').val(locations.Collections);
      $('#qa_row').hide("blind", function() {});
      $('#bagging_row').hide("blind", function() {});
    }
  }
  
  function CalculateBags() {
    var before = parseInt($('#before_bags').val());
    var quantity = parseInt($('#quantity').val());
    var after = parseInt($('#after_bags').val());
    var bagAmount = parseInt($('#bag_amount').val());
    $('#created_bags').val(Math.floor((before + quantity) / bagAmount));
    var createdBags = parseInt($('#created_bags').val());
    $('#after_bags').val((before + quantity) - (bagAmount * createdBags));
  }
  
  function UpdateForms(partData) {
    if(transfer_type == 1)
    {
      if(partData.part_cleaned == 1)
      {
        // Card and Helpers
        $('div.card').addClass('border-primary text-dark').removeClass('border-dark border-success text-light');
        $('div.card-header').addClass('bg-primary').removeClass('bg-dark bg-success');
        $('#helper_text').html('Cleaned - Goes to Blue Shelf.');
      }
      else
      {
        $('div.card').addClass('border-success text-dark').removeClass('border-dark border-primary text-light');
        $('div.card-header').addClass('bg-success').removeClass('bg-dark bg-primary');
        $('#helper_text').html('Not Cleaned - Goes to Green Shelf.');
      }
    }
  }
    // Change Card Color

  
  function ResetCard() {
      $('div.card').addClass('border-dark text-light').removeClass('border-info border-success text-light');
      $('div.card-header').addClass('bg-dark').removeClass('bg-success bg-primary');
      $('#helper_text').html('No Valid Part Serial Entered');
  }
  
});
  

</script>
<script>
var data = [
  @foreach($transfers as $row)
    {
      "updated_at":"{{date('m/d/y @ H:i', strtotime($row->updated_at))}}",
      "tech_name": "{{$row->tech_name}}",
      "part_name": "{{$row->part_name}}",
      "part_serial": "{{$row->part_serial}}",
      "quantity": "{{$row->quantity}}",
      "from_name": "{{$row->from_name}}",
      "to_name": "{{$row->to_name}}",
      "reversal": "{{$row->reversal}}",
      "id": "{{$row->part_id}}",
    },
  @endforeach
];
  
$("#report-table").tabulator({
  layout:"fitColumns", //fit columns to width of table (optional)
  placeholder:"No transfers to show. :( ",
  columns:[
    {title:"Updated", field:"updated_at", align:"center", width: 150, },
    {title:"Tech", field:"tech_name", align: "left", width: 150},
    {title:"Name", field:"part_name", align: "left", width: 200},
    {title:"Serial", field:"part_serial", align:"center", width: 110, },
    {title:"Quantity", field:"quantity", align:"center", },
    {title:"From", field:"from_name", align:"center", },
    {title:"To", field:"to_name", align:"center", },
    {title:"Reversed", field:"reversal", align:"center", formatter:"tickCross", },
    {title:"View", field:"id", width:100, formatter:function(cell, formatterParams){
     var value = cell.getValue();
      if(value > 0){
        return "<a class='btn btn-sm btn-outline-dark d-block' href='/parts/"+value+"'>View</span>";
      }else{
        return "<a class='btn btn-sm btn-outline-dark disabled d-block' disabled='disabled' href='#'>View</span>";
      }
    }},
  ],
});
  
$("#report-table").tabulator("setData",data);
  
$('#submit_button').on('click', function( event ) {
  event.preventDefault();
  $('#to_location_id').removeAttr('disabled').removeClass('disabled');
  $('#from_location_id').removeAttr('disabled').removeClass('disabled');
  $('#transfer_form').submit();
});
</script>
@endsection