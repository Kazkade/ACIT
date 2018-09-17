@extends('layouts.app') 
@section('content')
<!-- Configuration -->
<div class="container-fluid">
  <div class="row">
    <div class="col-2">
      <canvas id="filament_pie_chart" height=500></canvas>
    </div>
    <div class="col-8">
      <div class="row">
        <div class="col-6">
          
        </div>
        <div class="col-6"></div>
      </div>
      <div class="row">
        <div class="col">
          <canvas id="production_line_chart"></canvas>
        </div>
      </div>
      <div class="row">
        <span class="p-5"></span>
      </div>
    </div>
    <div class="col-2">
      Unresolved Issues
      <div id="unresolved_issues"></div>
    </div>
  </div>
</div>
<script>
// Production Chart
var timelineChart = document.getElementById("production_line_chart");
timelineChart.height = 500;
var production_line_chart = new Chart(timelineChart, {
    type: 'line',
    data: {
        labels: [
          @foreach($days as $day)
            "{{$day}}",
          @endforeach
        ],
        datasets: [
          @foreach($filaments as $filament)
          {
            @if($filament->filament_name == "Black")
              backgroundColor: '#000',
            @else
              backgroundColor: '{{$filament->background_color}}',
            @endif
            data: [
              @foreach($filament->production as $prod)  {{$prod->total}}, @endforeach
            ],
            label: '{{$filament->filament_name}}',
            fill: true
          },
          @endforeach
        ], 
    },
    options: {
			maintainAspectRatio: false,
			spanGaps: false,
			elements: {
				line: {
					tension: 0.000001
				}
			},
			scales: {
				yAxes: [{
					stacked: true
				}]
			},
			plugins: {
				filler: {
					propagate: false
				},
				'samples-filler-analyser': {
					target: 'chart-analyser'
				}
			}
		}
});
</script>
<script>
var filamentDistrobution = document.getElementById("filament_pie_chart");
var filament_pie_chart = new Chart(filamentDistrobution,{
    type: 'pie',
    data: {
      labels:[
        @foreach($filament_by_production as $fbp)
            "{{$fbp->part_color}}",
          @endforeach
      ],
      datasets: [{
        data: [
          @foreach($filament_by_production as $fbp)
            {{$fbp->total}},
          @endforeach
        ],
        backgroundColor: [
          @foreach($filament_by_production as $fbp)
            @if($fbp->part_color == "Black")
              "#000",
            @else
              "{{$fbp->background_color}}",
            @endif
          @endforeach
        ],
      }],
    },
});
</script>
<script>
// Unresolved Issues
var messages = [
  @foreach($messages as $message)
  {
    header: "{{$message->header}}",
    message: "{{$message->message}}",
    alert_type: "{{$message->alert_type}}",
    view: "{{$message->link}}",
  },
  @endforeach
];
if(messages.count() > 0)
{
  for(msg in messages)
  {
    $('#unresolved_issues').append(`
    <div class="card text-white bg-`+msg.alert_type+` mb-3">
      <div class="card-body">
        <h5 class="card-title">`+msg.header+`</h5>
        <p class="card-text">`+msg.message+`</p>
      </div>
      <div class="card-footer">
        <a class="btn btn-sm btn-outline-light" href="`+msg.view+`">View</a>
      </div>
    </div>
    `);
  }
}
else
{
  $('#unresolved_issues').append('There are no message.');
}

</script>
@endsection