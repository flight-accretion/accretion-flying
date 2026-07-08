@extends('layouts.plane_header')
@section('content')

   <header>
    <div class="header-content text-center">
      <div class="header-content-inner"> 
				<div class="row">
					<div class="col-md-12 no-padding">
						<h1>Machine Details</h1>
					</div>
				</div>
			</div>
		</div>
  </header>
  
  <section id="contact">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h2 class="section-heading">{{ $plane->name }}</h2>
          <hr>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 text-center">
          @if($plane->display_image != '')
            <div class="col-md-8 col-md-offset-2">
                <div class="plane-image gallery-image" style="background-image:url('/uploads/{{$plane->display_image}}');">
                  <div class="plane-details-image"></div>
                </div>
            </div>
          @else
            <i class="fa fa-picture-o fa-4x"></i>
          @endif
        </div>
      </div>
      <div class="row">
        <div class="plane-details">
          <ul class="nav nav-tabs nav-justified" role="tablist">
            <li role="presentation" class="active"><a href="#flight-details" aria-controls="flight-details" role="tab" data-toggle="tab"><h4>Flight Details</h4></a></li>
            <li role="presentation"><a href="#cost" aria-controls="cost" role="tab" data-toggle="tab"><h4>Cost Estimate</h4></a></li>
            <li role="presentation"><a href="#owner-details" aria-controls="owner-details" role="tab" data-toggle="tab"><h4>Owner Details</h4></a></li>
          </ul>
          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="flight-details">
              <div class="row">
                <h4>Machine : {{ $plane->name }} (<i class="fa fa-rupee"></i>{{ $plane->price_per_hour }} per hour)</h4>
                <table class="table table-hover table-bordered">
                  <thead>
                    <tr>
                      <th>Departure Time</th>
                      <th>Departure</th>
                      <th>Flight Time</th>
                      <th>Arrival</th>
                      <th>Arrival Time</th>
                      <th>Particular</th>
                    </tr>
                  </thead>
                  <tbody>
										@if(isset($flights['base']))
											<tr>
												<td>{{ date('d F, Y H:i', strtotime($flights['base']['departure_time'])) }}</td>
												<td>{{ $flights['base']['departure'] }}</td>
												<td>{{ ( $flights['base']['hours'] != 0 ? $flights['base']['hours'].' hour ' : '').($flights['base']['minutes'] != 0 ?$flights['base']['minutes'].' minute':'')  }}</td>
												<td>{{ $flights['base']['arrival'] }}</td>
												<td>{{ date('d F, Y H:i', strtotime($flights['base']['arrival_time'])) }}</td>
												<td>{{ $flights['base']['details'] }}</td>
											</tr>
										@endif
										@foreach($flights as $index => $flight)
											@if($index !== 'base' && $index !== 'arr')
												<tr>
													<td>{{ date('d F, Y H:i', strtotime($flight['departure_time'])) }}</td>
													<td>{{ $flight['departure'] }}</td>
													<td>{{ ( $flight['hours'] != 0 ? $flight['hours'].' hour ' : '').($flight['minutes'] != 0 ?$flight['minutes'].' minute':'')  }}</td>
													<td>{{ $flight['arrival'] }}</td>
													<td>{{ date('d F, Y H:i', strtotime($flight['arrival_time'])) }}</td>
													<td>{{ $flight['details'] }}</td>
												</tr>
											@endif
										@endforeach
										@if(isset($flights['arr']))
											<tr>
												<td>{{ date('d F, Y H:i', strtotime($flights['arr']['departure_time'])) }}</td>
												<td>{{ $flights['arr']['departure'] }}</td>
												<td>{{ ( $flights['arr']['hours'] != 0 ? $flights['arr']['hours'].' hour ' : '').($flights['arr']['minutes'] != 0 ?$flights['arr']['minutes'].' minute':'')  }}</td>
												<td>{{ $flights['arr']['arrival'] }}</td>
												<td>{{ date('d F, Y H:i', strtotime($flights['arr']['arrival_time'])) }}</td>
												<td>{{ $flights['arr']['details'] }}</td>
											</tr>
										@endif
                  </tbody>
                </table>
              </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="cost">
              <div class="row">
                <h4>Machine : {{ $plane->name }} (<i class="fa fa-rupee"></i>{{ $plane->price_per_hour }} per hour)</h4>
 
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
											<?php 
												$flying_hours = $total_hours;
												$flying_mins = $total_mins;
											?>
											@if(($stay_time_hours + $stay_time_minutes) != 0)
                        <tr>
                          <td>Stay Time</td>
                          <td class="text-right">{{ $stay_time_hours }} Hrs {{ $stay_time_minutes }} Mins</td>
                        </tr>
                        <?php 
                          $total_billing_minutes = ($stay_time_hours + $total_hours)*60 + $total_mins + $stay_time_minutes ;
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
												<?php $total_flying_cost = round($total_flying_cost); ?>
												<?php $total_flying_cost += round($additional_cost); ?>
												<td class="text-right">{{ round($flying_cost) }}</td>
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
												<td class="text-right">{{ $flying_cost + $ground_handling + $crew_handling}}</td>
											</tr>
											<tr>
												<td>GST @ 18%</td>
												<td class="text-right">{{ round((($flying_cost + $ground_handling + $crew_handling ) * 18/100)) }}</td>
											</tr>
											<tr>
												<td>Grand Total</td>
												<?php $grand_total = round(($flying_cost + $ground_handling + $crew_handling + (($flying_cost + $ground_handling + $crew_handling ) * 18/100))); ?>
												<td class="text-right">{{  round(($flying_cost + $ground_handling + $crew_handling + (($flying_cost + $ground_handling + $crew_handling ) * 18/100)))}}</td>
											</tr>
										</tbody>
									</table>
								</div>
              </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="owner-details">
              <div class="row">
                <h4>Machine : {{ $plane->name }} (<i class="fa fa-rupee"></i>{{ $plane->price_per_hour }} per hour)</h4>
                  <table class="table table-hover table-bordered">
                  <thead>
                    <tr>
                      <th colspan="2" class="text-center" width="50%">Owner Details</th>
                      <th colspan="2" class="text-center" width="50%">Secondary Contact Details</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th>Name</th>
                      <td>{{ $owner_details['name'] }}</td>
                      <th>Name</th>
                      <td>{{ $owner_details['sec_name'] }}</td>
                    </tr>
                    <tr>
                      <th>Email</th>
                      <td>{{ $owner_details['email1'] }}</td>
                      <th>Email</th>
                      <td>{{ $owner_details['sec_email'] }}</td>
                    </tr>
                    <tr>
                      <th>Contact</th>
                      <td>{{ $owner_details['contact1'] }}</td>
                      <th>Contact</th>
                      <td>{{ $owner_details['sec_contact'] }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      <div class="row">
        <div class="col-md-offset-2 col-md-6  text-left">
					<form role="form" method="POST" action="{{ url('/plane/book') }}">
						<div class="col-md-3 form-group">
							<label class="pull-left">Points for booking</label>
							<input style="background-color: #eee;" name="points" class="form-control" value="{{ round($grand_total * $points / 100) }}" readonly />
						</div>
						<div class="col-md-3 form-group">
							<label class="pull-left">Points Available</label>
							<input style="background-color: #eee;" class="form-control" value="{{ optional(auth()->user())->points ?? 0 }}" readonly />
						</div>
						<div class="col-md-3 form-group">
							<label class="pull-left">Redeem Points</label>
							<input name="redeem" type="number" class="form-control" max="{{ optional(auth()->user())->points ?? 0 }}" value=""  />
						</div>
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />

						<input id="plane-id" type="hidden" name="plane-id" value="{{ $plane->id }}" />
						<input id="plane-name" type="hidden" name="plane-name" value="{{ $plane->name }}" />
						<input id="total-hours" type="hidden" name="total-hours" value="{{ $total_hours }}" />
						<input id="total-mins" type="hidden" name="total-mins" value="{{ $total_mins }}" />
						<input id="total-hours" type="hidden" name="flying-hours" value="{{ $flying_hours }}" />
						<input id="total-mins" type="hidden" name="flying-mins" value="{{ $flying_mins }}" />
						<input id="total-hours" type="hidden" name="stay-hours" value="{{ $stay_time_hours }}" />
						<input id="total-mins" type="hidden" name="stay-mins" value="{{ $stay_time_minutes }}" />
						<input id="total-flying-cost" type="hidden" name="total-flying-cost" value="{{ $flying_cost }}" />
						<input id="ground-handling" type="hidden" name="ground-handling" value="{{ $ground_handling }}" />
						<input id="crew-handling" type="hidden" name="crew-handling" value="{{ $crew_handling }}" />
						<input id="flights" type="hidden" name="flights" value="{{ json_encode($flights) }}" />
						
						<div class="col-md-3 form-group"><br/>
							<button type="submit" class="btn btn-export btn-block text-white" style="width:100% !important">Book </button>
						</div>
					</form>
        </div>
        <div class=" col-md-3 text-left">
					<div class="row">
						<form id="flight-details" role="form" method="POST" action="{{ url('/plane/machine-details-report') }}">
						<br/>
							<button type="submit" class="btn btn-export text-white">Export Details</button>
							 <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
							 <input id="plane-id" type="hidden" name="plane-id" value="{{ $plane->id }}" />
							 <input id="plane-name" type="hidden" name="plane-name" value="{{ $plane->name }}" />
							 <input id="total-hours" type="hidden" name="total-hours" value="{{ $total_hours }}" />
							 <input id="total-mins" type="hidden" name="total-mins" value="{{ $total_mins }}" />
							 <input id="total-flying-cost" type="hidden" name="total-flying-cost" value="{{ $flying_cost }}" />
							 <input id="ground-handling" type="hidden" name="ground-handling" value="{{ $ground_handling }}" />
							 <input id="crew-handling" type="hidden" name="crew-handling" value="{{ $crew_handling }}" />
							 <input id="flights" type="hidden" name="flights" value="{{ json_encode($flights) }}" />
						</form>
          </div>
        </div>
      </div>
  </section>
  
  <section>
    <div class="container">
  </section>

  <!-- ========== IMAGE MODAL ========== -->
  <?php
    $carousel_images = [];
    foreach($plane_images as $image) {
      if(!empty($image->images)) {
        $carousel_images[] = $image->images;
      }
    }
    if(count($carousel_images) === 0 && !empty($plane->display_image)) {
      $carousel_images[] = $plane->display_image;
    }
  ?>

<div role="dialog" id="image-modal" class="modal fade">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        @if(count($carousel_images) > 0)
        <div id="carousel-example-generic" class="carousel slide" @if(count($carousel_images) > 1) data-ride="carousel" @endif>
          <!-- Indicators -->
          @if(count($carousel_images) > 1)
          <ol class="carousel-indicators">
            @for($n=0; $n<count($carousel_images); $n++)
              <li data-target="#carousel-example-generic" data-slide-to="{{ $n }}" class="{{ $n == 0 ? 'active' : '' }}"></li>
            @endfor
          </ol>
          @endif
               

          <!-- Wrapper for slides -->
          <div class="carousel-inner" role="listbox">
            @foreach($carousel_images as $key=>$image)
              <div class="item {{ $key == 0 ? 'active' : '' }}">
                <img src="/uploads/{{ $image }}" alt="{{ $plane->name }}">
                <div class="carousel-caption">
                  <h3>{{ $plane->name }}</h3>
                </div>
              </div>
            @endforeach
          </div>
               

          <!-- Controls -->
          @if(count($carousel_images) > 1)
          <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
          @endif
        </div>
        @else
          <div class="text-center"><i class="fa fa-picture-o fa-4x"></i></div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- =================================================== -->

<script>
     $(function () {
      $("#date").datepicker({
        format: "dd-mm-yyyy",
        autoclose: true,
      });
      
      $(document).on("click", ".gallery-image", function(){
		  $("#image-modal").modal('show');
		});
    
    //Initialize Select2 Elements
    $(".select2").select2();   
      
    });
   
    $('.btn').on('click', function(){
      $('.btn').removeClass('selected');
      $(this).addClass('selected');
  });
  
</script>

<script>
  $(document).ready(function(){
    $("select.country").change(function(){
        var selectedCountry = $(".country option:selected").val();
        alert("You have selected the country - " + selectedCountry);
    });
});
</script>

@endsection
