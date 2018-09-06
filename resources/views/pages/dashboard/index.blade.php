@extends('layouts.app') 
@section('content')
<!-- Configuration -->
<div class="container">
  <div class="row">
    <div class="col">
      <canvas id="production_line_chart"></canvas>
    </div>
  </div>
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>
<script>
// Production Chart
var ctx = document.getElementById("production_line_chart");
var production_line_chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
          @foreach($days as $day)
            "{{$day}}",
          @endforeach
        ],
        datasets: [{
            label: 'Produced Parts',
            data: [
              @foreach($production as $produced)
                {{$produced}},
              @endforeach
            ],
            backgroundColor: [
                'rgba(71, 242, 255, .35)',
            ],
            borderColor: [
                'rgb(60, 150, 200)',
            ],
            borderWidth: 2,
            lineTension: 0.15,
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>
@endsection