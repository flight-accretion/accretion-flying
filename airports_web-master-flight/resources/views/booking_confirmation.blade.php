@extends('layouts.plane_header')
@section('content')
   <header>
    <div class="header-content text-center">
      <div class="header-content-inner"> 
				<div class="row">
					<div class="col-md-12 no-padding">
						<h1>Booking Details</h1>
					</div>
				</div>
			</div>
		</div>
  </header>
  <section id="contact">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h2 class="section-heading">{{ $plane_name }}</h2>
          <hr>
        </div>
      </div>
			<div class="row">
				
				<table class="table table-hover table-bordered">
					<thead>
						<tr>
							<th>Departure Time</th>
							<th>Departure</th>
							<th>Flight Time</th>
							<th>Arrival</th>
							<th>Arrival Time</th>
							<th>Distance (In NM)</th>
							<th>Cost (In Rs.)</th>
							<th>Particular</th>
						</tr>
					</thead>
					<tbody>
						@if(isset($flights['base']))
							<?php	$flights['base'] = (array)$flights['base']; ?>
							<tr>
								<td>{{ date('d F, Y H:i', strtotime($flights['base']['departure_time'])) }}</td>
								<td>{{ $flights['base']['departure'] }}</td>
								<td>{{ ( $flights['base']['hours'] != 0 ? $flights['base']['hours'].' hour ' : '').($flights['base']['minutes'] != 0 ?$flights['base']['minutes'].' minute':'')  }}</td>
								<td>{{ $flights['base']['arrival'] }}</td>
								<td>{{ date('d F, Y H:i', strtotime($flights['base']['arrival_time'])) }}</td>
								<td>{{ round($flights['base']['distance'],2) }}</td>
								<td>{{ round($flights['base']['cost'],2) }}</td>
								<td>{{ $flights['base']['details'] }}</td>
							</tr>
						@endif
						@foreach($flights as $index => $flight)
							<?php	$flight = (array)$flight; ?>
							@if($index !== 'base' && $index !== 'arr')
								<tr>
									<td>{{ date('d F, Y H:i', strtotime($flight['departure_time'])) }}</td>
									<td>{{ $flight['departure'] }}</td>
									<td>{{ ( $flight['hours'] != 0 ? $flight['hours'].' hour ' : '').($flight['minutes'] != 0 ?$flight['minutes'].' minute':'')  }}</td>
									<td>{{ $flight['arrival'] }}</td>
									<td>{{ date('d F, Y H:i', strtotime($flight['arrival_time'])) }}</td>
									<td>{{ round($flight['distance'],2) }}</td>
									<td>{{ round($flight['cost'],2) }}</td>
									<td>{{ $flight['details'] }}</td>
								</tr>
							@endif
						@endforeach
						@if(isset($flights['arr']))
							<?php	$flights['arr'] = (array)$flights['arr']; ?>
							<tr>
								<td>{{ date('d F, Y H:i', strtotime($flights['arr']['departure_time'])) }}</td>
								<td>{{ $flights['arr']['departure'] }}</td>
								<td>{{ ( $flights['arr']['hours'] != 0 ? $flights['arr']['hours'].' hour ' : '').($flights['arr']['minutes'] != 0 ?$flights['arr']['minutes'].' minute':'')  }}</td>
								<td>{{ $flights['arr']['arrival'] }}</td>
								<td>{{ date('d F, Y H:i', strtotime($flights['arr']['arrival_time'])) }}</td>
								<td>{{ round($flights['arr']['distance'],2) }}</td>
								<td>{{ round($flights['arr']['cost'],2) }}</td>
								<td>{{ $flights['arr']['details'] }}</td>
							</tr>
						@endif
					</tbody>
				</table>
      </div>
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Cost Details</th>
								<th class="text-right">Amount</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Total Flight Time</td>
								<td class="text-right">{{ $total_hours }} Hrs {{ $total_mins }} Mins</td>
							</tr>
							@if(($stay_hours + $stay_mins) != 0)
								<tr>
									<td>Stay Time</td>
									<td class="text-right">{{ $stay_hours }} Hrs {{ $stay_mins }} Mins</td>
								</tr>
								<?php 
									$total_billing_minutes = ($stay_hours + $total_hours)*60 + $total_mins + $stay_mins;
									$total_hours = $billing_hours = floor($total_billing_minutes / 60);
									$total_mins = $billing_minutes = ($total_billing_minutes % 60);
								?>
								<tr>
									<td>Total Billing Time</td>
									<td class="text-right">{{ $billing_hours }} Hrs {{ $billing_minutes }} Mins</td>
								</tr>
							@endif
							<tr>
								<td>Flying Cost</td>
								<td class="text-right">{{ round($total_flying_cost) }}</td>
							</tr>
							<tr>
								<td>Ground Handling</td>
								<td class="text-right">{{ $ground_handling }}</td>
							</tr>
							<tr>
								<td>Crew Handling</td>
								<td class="text-right">{{ $crew_handling }}</td>
							</tr>
							<tr>
								<td>Other Charges</td>
								<td class="text-right">As per actual</td>
							</tr>
							<tr>
								<td>Sub Total</td>
								<td class="text-right">{{ $total_flying_cost + $ground_handling + $crew_handling}}</td>
							</tr>
							<tr>
								<td>GST @ 18%</td>
								<td class="text-right">{{ round((($total_flying_cost + $ground_handling + $crew_handling ) * 18/100)) }}</td>
							</tr>
							<tr>
								<td>Grand Total</td>
								<?php $grand_total = round(($total_flying_cost + $ground_handling + $crew_handling + (($total_flying_cost + $ground_handling + $crew_handling ) * 18/100))); ?>
								<td class="text-right">{{  round(($total_flying_cost + $ground_handling + $crew_handling + (($total_flying_cost + $ground_handling + $crew_handling ) * 18/100)))}}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-md-offset-5 text-center">
					<form role="form" method="POST" action="{{ url('/plane/book') }}">
						<div class="form-group">
							<label class="pull-left">Points earned</label>
							<input name="points" class="form-control" value="{{ round($grand_total * $points / 100) }}" readonly />
						</div>
						<div class="form-group">
							<label class="pull-left">Points Available</label>
							<input class="form-control" value="{{ auth()->user()->points }}" readonly />
						</div>
						<div class="form-group">
							<label class="pull-left">Redeem Points</label>
							<input name="redeem" type="number" class="form-control" max="{{ auth()->user()->points }}" value=""  />
						</div>
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<input type="hidden" name="request-data" value='{{ htmlspecialchars(json_encode($request_data)) }}' />
						<button type="submit" class="btn btn-export text-white">Book </button>
					</form>
				</div>
			</div>
		</div>
	</section>
@endsection