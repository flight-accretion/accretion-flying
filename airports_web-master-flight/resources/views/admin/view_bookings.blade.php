@extends('layouts.admin_header')
@section('content')
  <section class="content booking-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Bookings</h3>
            <div id="search-booking" class="pull-right"></div>
          </div>
          <div class="box-body">
            <table id="table-booking" class="table table-bordered table-striped">
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
									<th>User</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 1; ?>
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
										<td>{{ $booking->name }}</td>
										<td style="vertical-align:middle"><a class="btn btn-info btn-xs" href="/booking/details?id={{ $booking->id }}">View</a></td>
									</tr>
								@endforeach
							</tbody>
            </table>
            <div class="row">
              <div id="page-link-wrapper" class="col-md-12 text-center"></div>	
            </div>
          </div>
        </div>
      </div> 
    </div>
    
  </section>
  <script type="text/javascript">
		$(function(){
			$('#table-booking').dataTable( {				
				"bLengthChange": false,
				"bSort": true,
				"iDisplayLength": 6,
				"infoEmpty": "<center><div class='text-info'><br>No booking available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No booking available</div></center>",
          "sSearch": "",
          "oPaginate": {
            "sNext": '>',
            "sLast": '>|',
            "sFirst": '|<',
            "sPrevious": '<'
          }
        } 					 
			});
			$('.dataTables_filter input').attr("placeholder", "Search");
			$('.dataTables_filter input').removeClass("input-sm");
      $('.dataTables_filter input').addClass("form-control");
			$("#table-airport_info").detach().appendTo('#page-link-wrapper');
			$("#table-airport_paginate").detach().appendTo('#page-link-wrapper');
			$("#table-airport_filter").detach().appendTo('#search-booking');  
		});
	</script>
@endsection
