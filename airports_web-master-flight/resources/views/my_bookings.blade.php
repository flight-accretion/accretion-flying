@extends('layouts.plane_header')
@section('content')
   <header>
    <div class="header-content text-center">
      <div class="header-content-inner"> 
				<div class="row">
					<div class="col-md-12 no-padding">
						<h1>My Bookings</h1>
					</div>
				</div>
			</div>
		</div>
  </header>
  <section>
    <div class="container">
    
			<div class="row form-group">
				<div class="col-md-12 text-right">
					<h4>My Points: {{ auth()->user()->points }}</h4>
				</div>
			</div>
			<div class="row">
				<table class="table table-hover table-bordered">
					<thead>
						<tr>
							<th>No.</th>
							<th>Plane</th>
							<th>Date</th>
							<th>Flight Time</th>
							<th>Stay Time</th>
							<th>Total Time</th>
							<th>Total Cost</th>
							<th>Points Earned</th>
							<th>Points Redeemed</th>
							<th>Final Cost</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php $i = 1; ?>
						@if(count($bookings) == 0)
							<tr>
								<td colspan="11" class="text-center">No bookings found</td>
							</tr>
						@endif
						@foreach($bookings as $booking)
							<?php $grand_total = round(($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling + (($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling ) * 18/100))); ?>
							<tr>
								<td>{{ $i++ }}</td>
								<td>{{ $booking->plane_name }}</td>
								<td>{{ date('d/m/Y', strtotime($booking->created_at)) }}</td>
								<td>{{ $booking->flying_hours.' Hrs '.$booking->flying_mins.' Mins'  }}</td>
								<td>{{ $booking->stay_hours.' Hrs '.$booking->stay_mins.' Mins' }}</td>
								<td>{{ $booking->total_hours.' Hrs '.$booking->total_mins.' Mins' }}</td>
								<td>{{ $grand_total }}</td>
								<td>{{ $booking->points_earned }}</td>
								<td>{{ $booking->points_redeemed }}</td>
								<td>{{ $grand_total - (float)$booking->points_redeemed }}</td>
								<td style="vertical-align:middle"><a href="/booking/view?id={{ $booking->id }}">View</a></td>
							</tr>
						@endforeach
					</tbody>
				</table>
      </div>
		</div>
	</section>
@endsection
