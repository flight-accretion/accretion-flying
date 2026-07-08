@extends('layouts.admin_header')
@section('content')
  <section class="content booking-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Booking Details of <b>{{ $user->name }}</b></h3>
            <div id="search-booking" class="pull-right">{{ date('d M, Y', strtotime($booking->created_at)) }}</div>
          </div>
          <div class="box-body">	
						<div class="row">
							<div class="col-md-12">
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
										<?php $flights = (array)json_decode($booking->flights); ?>
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
											<td class="text-right">{{ $booking->flying_hours }} Hrs {{ $booking->flying_mins }} Mins</td>
										</tr>
										@if(($booking->stay_hours + $booking->stay_mins) != 0)
											<tr>
												<td>Stay Time</td>
												<td class="text-right">{{ $booking->stay_hours }} Hrs {{ $booking->stay_mins }} Mins</td>
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
											<td class="text-right">{{ round($booking->total_flying_cost) }}</td>
										</tr>
										<tr>
											<td>Ground Handling</td>
											<td class="text-right">{{ $booking->ground_handling }}</td>
										</tr>
										<tr>
											<td>Crew Handling</td>
											<td class="text-right">{{ $booking->crew_handling }}</td>
										</tr>
										<tr>
											<td>Other Charges</td>
											<td class="text-right">As per actual</td>
										</tr>
										<tr>
											<td>Sub Total</td>
											<td class="text-right">{{ $booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling}}</td>
										</tr>
										<tr>
											<td>GST @ 18%</td>
											<td class="text-right">{{ round((($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling ) * 18/100)) }}</td>
										</tr>
										<tr>
											<td>Grand Total</td>
											<?php $grand_total = round(($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling + (($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling ) * 18/100))); ?>
											<td class="text-right">{{  round(($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling + (($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling ) * 18/100)))}}</td>
										</tr>
										<tr>
											<td>Points Earned</td>
											<td class="text-right">{{ $booking->points_earned }}</td>
										</tr>
										<tr>
											<td>Points Redeemed</td>
											<td class="text-right">{{ $booking->points_redeemed }}</td>
										</tr>
											<tr>
												<td>Final Total</td>
												<td class="text-right">{{ $grand_total - (float)$booking->points_redeemed }}</td>
											</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-4 text-center">
								<a href="/booking" class="btn btn-primary">Back</a>
							</div>
						</div>
          </div>
        </div>
      </div> 
    </div>
  </section>
@endsection
