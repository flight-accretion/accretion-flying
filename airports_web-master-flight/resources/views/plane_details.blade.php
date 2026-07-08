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
    <div class="header-content hide">
      <div class="header-content-inner">
        <form id="search-planes" role="form" method="GET" action="{{ url('/plane/search') }}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
          <div class="row">
            <div class="col-md-2 col-md-offset-1 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="trips" name="trips">
                  <option value="0" selected>Single Trip</option>
                  <option value="1">Round Trip</option>
                  <!--option value="2">Multi City Trip</option-->
                </select>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="planes" name="planes">
                  <option value="0" selected>Machine Type</option>
                  @foreach($plane_types as $id => $plane_type_name)
                    @if($id == $plane_type)
                      <option value="{{ $id}}" data-id="{{ $id }}" selected>{{ $plane_type_name }}</option>
                    @else
                      <option value="{{ $id}}" data-id="{{ $id }}">{{ $plane_type_name }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-2 col-md-offset-1 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="departure" name="departure">
                  <option value="0" selected>Departure</option>
                  @foreach($airports as $airport)
                    @if($airport->id == $departure)
                      <option value="{{ $airport->id}}" data-lat="{{ $airport->latitude }}"  data-long="{{ $airport->longitude }}"selected>{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id]->name }} @endif</option>
                    @else
                      <option value="{{ $airport->id}}" data-lat="{{ $airport->latitude }}"  data-long="{{ $airport->longitude }}" >{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id]->name }} @endif</option>
                    @endif                   
                  @endforeach
                  <input type="hidden" id="dep-latitude" name="dep-latitude"  value="{{ $latitude }}">
                  <input type="hidden" id="dep-longitude" name="dep-longitude"  value="{{ $longitude }}">
                </select>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="arrival" name="arrival">
                  <option value="0" selected>Arrival</option>
                  @foreach($airports as $airport)
                    @if($airport->id == $arrival)
                      <option value="{{ $airport->id}}" selected>{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id]->name }} @endif</option>
                    @else
                      <option value="{{ $airport->id}}" >{{ $airport->name }}@if(isset($cities[$airport->city_id])), {{ $cities[$airport->city_id]->name }} @endif</option>
                    @endif 
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <input type="number" class="form-control search-planes-element" id="adults" placeholder="Adults" name="adults" value="{{ $adults }}" min=1 oninput="validity.valid||(value='');">
                <span class="error-font text-danger">{{ $errors->first('adults')}}</span>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="input-group date" id="from-date">								
                <input type="text" class="form-control date-time-picker search-planes-element" id="date" name="date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY"  value="{{ $date }}" tabindex="2">
              </div>
              <span class="error-font text-danger">{{ $errors->first('date') }}</span>
            </div>
            <div class="col-md-2 no-padding" id="btn-search">
              <button type="submit" class="btn btn-search-plane">SEARCH</button>
            </div>
          </div>
          <div class="row hide" id="round-trip">
            <div class="col-md-2 col-md-offset-1 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="round-departure" name="round-departure">
                  <option value="0" selected>Departure</option>
                  @foreach($airports as $airport)
                    <option value="{{ $airport->id}}">{{ $airport->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="round-arrival" name="round-arrival">
                  <option value="0" selected>Arrival</option>
                  @foreach($airports as $airport)
                    <option value="{{ $airport->id}}">{{ $airport->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <input type="number" class="form-control search-planes-element" id="round-adults" placeholder="Adults" name="round-adults" value="{{ $adults }}" min=1 oninput="validity.valid||(value='');">
                <span class="error-font text-danger">{{ $errors->first('adults')}}</span>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="input-group date" id="from-date">								
                <input type="text" class="form-control date-picker search-planes-element" id="round-date" name="from-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY"  value="{{ $date }}" tabindex="2">
                
              </div>
              <span class="error-font text-danger">{{ $errors->first('from-date') }}</span>
            </div>
            <div class="col-md-2 no-padding hide" id="btn-search-round">
              <button type="submit" class="btn btn-search-plane">SEARCH</button>
            </div>
          </div>
        </form>
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
										@foreach($flights as $flight)
                    <tr>
                      <td>{{ date('d F, Y H:i', strtotime($flight['departure_time'])) }}</td>
                      <td>{{ $flight['departure'] }}</td>
                      <td>{{ ( $flight['hours'] != 0 ? $flight['hours'].' hour ' : '').($flight['minutes'] != 0 ?$flight['minutes'].' minute':'')  }}</td>
                      <td>{{ $flight['arrival'] }}</td>
                      <td>{{ date('d F, Y H:i', strtotime($flight['arrival_time'])) }}</td>
                      <td>{{ $flight['details'] }}</td>
                    </tr>
										@endforeach
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
										@foreach($flights as $flight)
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
										@endforeach
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
                      @if(isset($plane_type) && $plane_type==2)
                      <?php
                        $minutes = ( $total_hours != 0 ? $total_hours*60 : 0) + ($total_mins != 0 ? $total_mins :0);  
                        $fuel_hault = 0;
                      ?>
                      @while ($minutes>90) 
                        <?php
                        $fuel_hault += 30;
                        $minutes -= 90; 
                        ?> 
                      @endwhile
                      <?php
                        $fuel_cost = ($fuel_hault/60) *  $plane->price_per_hour  ;
                        $final_total_minutes = ($total_hours * 60) + $total_mins + $fuel_hault;
                        $final_total_hours = floor($final_total_minutes / 60);
                        $final_total_mins  = $final_total_minutes % 60;
                      ?>
                    <tr>
                      <td>Fuel Hault</td>
                      <td class="text-right">{{ (int)($fuel_hault/60) }} Hrs {{ (int)$fuel_hault%60 }} Mins</td>
                    </tr>
                   
                    @endif
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
												<td>Flying Cost </td>
												<?php 
													$total_flying_cost += $additional_cost; 
                          if(isset($plane_type) && $plane_type == 2){
                            $total_flying_cost += $fuel_cost;
                            }
													$flying_cost = $total_flying_cost; 
												?>
												<td class="text-right">{{ round($total_flying_cost) }}</td>
											</tr>
											<tr>
												<td>Ground Handling</td>
												<td class="text-right">{{ $ground_handling }}</td>
											</tr>
											<tr>
												<td>Crew Handling</td>
												<td class="text-right">{{ round($crew_handling) }}</td>
											</tr>
											<tr>
												<td>Other Charges</td>
												<td class="text-right">As per actual</td>
											</tr>
											<tr>
												<td>Sub Total</td>  
												<td class="text-right">{{ round($total_flying_cost + $ground_handling + $crew_handling) }}</td>
											</tr>
											@if($plane->type_id != 3)
												<tr>
													<td>GST @ 18%</td>
													<td class="text-right">{{ round((($total_flying_cost + $ground_handling + $crew_handling ) * 18/100),2) }}</td>
												</tr>
												<tr>
													<td>Grand Total</td>
													<?php $grand_total = round(($total_flying_cost + $ground_handling + $crew_handling + (($total_flying_cost + $ground_handling + $crew_handling ) * 18/100))); ?>
													<td class="text-right">{{  round(($total_flying_cost + $ground_handling + $crew_handling + (($total_flying_cost + $ground_handling + $crew_handling ) * 18/100)))}}</td>
												</tr>
											@else
												<tr>
													<td>Grand Total</td>
													<?php $grand_total = round(($total_flying_cost + $ground_handling + $crew_handling )); ?>
													<td class="text-right">{{  round(($total_flying_cost + $ground_handling + $crew_handling ))}}</td>
												</tr>
											@endif
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
          
        <div class="col-md-12 text-left" style="display: flex; justify-content: center;">
          <form id="flight-details" role="form" method="POST" action="{{ url('/plane/machine-details-report') }}">
						<br/>
            <button type="submit" class="btn btn-export text-white" style="width: 200px;">Export Details</button>
             <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
             <input id="plane-id" type="hidden" name="plane-id" value="{{ $plane->id }}" />
             <input id="plane-name" type="hidden" name="plane-name" value="{{ $plane->name }}" />
             <input id="total-hours" type="hidden" name="total-hours" value="{{ $total_hours }}" />
             <input id="total-mins" type="hidden" name="total-mins" value="{{ $total_mins }}" />
             <input id="total-flying-cost" type="hidden" name="total-flying-cost" value="{{ $total_flying_cost }}" />
             <input id="ground-handling" type="hidden" name="ground-handling" value="{{ $ground_handling }}" />
             <input id="crew-handling" type="hidden" name="crew-handling" value="{{ $crew_handling }}" />
             <input id="flights" type="hidden" name="flights" value="{{ json_encode($flights) }}" />
          </form>
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
