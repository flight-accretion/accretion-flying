@extends('layouts.plane_header')
@section('content')
	@if($trip_type == 2)
		<style>
			#plane-page-top header {
				min-height: 75%;
			}
		</style>
	@endif
	<header>
    <div class="header-content">
      <div class="header-content-inner">
        <form id="search-planes" role="form" method="GET" action="{{ url('/plane/search') }}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
          <input id="old-departure" type="hidden" name="old-departure" value="{{ $departure }}" />
          <input id="old-arrival" type="hidden" name="old-arrival" value="{{ $arrival }}" />
          <input id="old-dep-latitude" type="hidden" name="old-dep-latitude" value="{{ $dep_latitude }}" />
          <input id="old-dep-longitude" type="hidden" name="old-dep-longitude" value="{{ $dep_longitude }}" />
          <input id="old-arr-latitude" type="hidden" name="old-arr-latitude" value="{{ $arr_latitude }}" />
          <input id="old-arr-longitude" type="hidden" name="old-arr-longitude" value="{{ $arr_longitude }}" />
          <input id="old-helicopter-departure" type="hidden" name="old-helicopter-departure" value="{{ $helicopter_departure }}" />
          <input id="old-helicopter-arrival" type="hidden" name="old-helicopter-arrival" value="{{ $helicopter_arrival }}" />
          <input id="old-plane-type" type="hidden" name="old-plane-type" value="{{ $plane_type }}" />
          <input id="old-trip-type" type="hidden" name="old-trip-type" value="{{ $trip_type }}" />
          <input id="old-adults" type="hidden" name="old-adults" value="{{ $adults }}" />
          <input id="old-date" type="hidden" name="old-date" value="{{ $date }}" />
          <input id="old-total-adults" type="hidden" name="old-total-adults" value="{{ $total_adults }}" />
          <input id="gt1" type="hidden" name="gt1" value="{{ $gt1 }}" />
          <input id="gt2" type="hidden" name="gt2" value="{{ $gt2 }}" />
          <div class="row">
            <div class="col-md-2 col-md-offset-1 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="trips" name="trips">
                  <option value="0" <?php if($trip_type == 0) echo "selected"; ?>>Single Trip</option>
                  <option value="1" <?php if($trip_type == 1) echo "selected"; ?>>Round Trip</option>
                  <option value="2" <?php if($trip_type == 2) echo "selected"; ?>>Multi Trip</option>
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
					<div class="@if($trip_type == 2) hide @endif"  id="non-multi-trip-div">
						<div class="row">
							<div class="col-md-2 col-md-offset-1 no-padding">
								<div id="plane-air-dep-div" class="form-group">
									<select class="form-control search-planes-element select2" id="departure" name="departure">
										<option value="0" selected>Departure</option>
										@foreach($airports as $airport)
											@if($airport->id == $departure)
												<option value="{{ $airport->id}}" data-id="{{ $airport->id }}" data-lat="{{ $airport->latitude }}"  data-long="{{ $airport->longitude }}"selected>{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id]->name }} @endif</option>
											@else
												<option value="{{ $airport->id}}" data-id="{{ $airport->id }}" data-lat="{{ $airport->latitude }}"  data-long="{{ $airport->longitude }}" >{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id]->name }} @endif</option>
											@endif                   
										@endforeach
									</select>
									<span class="text-danger">{{$errors->first('departure')}}</span>
								</div>
								<div id="helicopter-dep-div" class="form-group @if($plane_type != 2) hide @endif">
									<input type="hidden" id="dep-latitude" name="dep-latitude"  value="{{ $dep_latitude }}">
									<input type="hidden" id="dep-longitude" name="dep-longitude"  value="{{ $dep_longitude }}">
									<input type="text" class="form-control search-planes-element margin-auto" id="dep-helicopter" placeholder="Departure" name="helicopter-departure" value="{{ $helicopter_departure }}">
									<span class="text-danger">{{$errors->first('helicopter-departure')}}</span>
								</div>
							</div>
							<div class="col-md-2 no-padding">
								<div id="plane-air-arr-div" class="form-group">
									<select class="form-control search-planes-element select2" id="plane-air-arrival" name="arrival">
										<option value="0" selected>Arrival</option>
										@foreach($airports as $airport)
											@if($airport->id == $arrival)
												<option value="{{ $airport->id}}" data-arr-id="{{ $airport->id }}" data-arr-lat="{{ $airport->latitude }}" data-arr-long="{{ $airport->longitude }}" selected>{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id]->name }} @endif</option>
											@else
												<option value="{{ $airport->id}}" data-arr-id="{{ $airport->id }}" data-arr-lat="{{ $airport->latitude }}" data-arr-long="{{ $airport->longitude }}">{{ $airport->name }}@if(isset($cities[$airport->city_id])), {{ $cities[$airport->city_id]->name }} @endif</option>
											@endif 
										@endforeach
									</select>
									<span class="text-danger">{{$errors->first('arrival')}}</span>
								</div>
								<div id="helicopter-arr-div" class="form-group @if($plane_type != 2) hide @endif">
									<input type="hidden" id="arr-latitude" name="arr-latitude"  value="{{ $arr_latitude }}">
									<input type="hidden" id="arr-longitude" name="arr-longitude"  value="{{ $arr_longitude }}">
									<input type="text" class="form-control search-planes-element margin-auto" id="arr-helicopter" placeholder="Arrival" name="helicopter-arrival" value="{{ $helicopter_arrival }}">
									<span class="text-danger">{{$errors->first('helicopter-arrival')}}</span>
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
						<div class="row @if($trip_type != 1) hide @endif" id="round-trip">
							<div class="col-md-2 col-md-offset-1 no-padding">
								<div id="plane-air-dep-round-div" class="form-group">
									<select class="form-control search-planes-element select2" id="plane-air-departure-round" name="round-departure">
										<option value="0" selected>Departure</option>
										@foreach($airports as $airport)
											<option value="{{ $airport->id}}" data-lat-round="{{ $airport->latitude }}" data-long-round="{{ $airport->longitude }}">{{ $airport->name }}@if(isset($cities[$airport->city_id])), {{ $cities[$airport->city_id]->name }} @endif</option>
										@endforeach
									</select>
									<input type="hidden" id="dep-latitude-round" name="dep-latitude-round" value="{{ $arr_latitude }}">
									<input type="hidden" id="dep-longitude-round" name="dep-longitude-round" value="{{ $arr_longitude}}">
									<span class="error-font text-danger">{{ $errors->first('round-departure')}}</span>
								</div>
								<div id="helicopter-dep-round-div" class="form-group @if($plane_type != 2) hide @endif">
									<input type="text" class="form-control search-planes-element margin-auto" id="helicopter-dep-round" placeholder="Departure" name="round-helocopter-departure" value="{{ $helicopter_arrival ?: 'Select From Map' }}">
									<span class="error-font text-danger">{{ $errors->first('round-helocopter-departure')}}</span>
								</div>
							</div>
							<div class="col-md-2 no-padding">
								<div id="plane-air-arr-round-div" class="form-group">
									<select class="form-control search-planes-element select2" id="plane-air-arrival-round" name="round-arrival">
										<option value="0" selected>Arrival</option>
										@foreach($airports as $airport)
											<option value="{{ $airport->id}}" data-lat-arr-round="{{ $airport->latitude }}" data-long-arr-round="{{ $airport->longitude }}">{{ $airport->name }}@if(isset($cities[$airport->city_id])), {{ $cities[$airport->city_id]->name }} @endif</option>
										@endforeach
									</select>
									<span class="error-font text-danger">{{ $errors->first('round-arrival')}}</span>
								</div>
								<div id="helicopter-arr-round-div" class="form-group @if($plane_type != 2) hide @endif">
									<input type="text" class="form-control search-planes-element margin-auto" id="helicopter-arr-round" placeholder="Arrival" name="round-helocopter-arrival" value="{{ $helicopter_departure ?: 'Select From Map' }}">
									<span class="error-font text-danger">{{ $errors->first('round-helocopter-arrival')}}</span>
								</div>
							</div>
							<div class="col-md-2 no-padding">
								<div class="form-group">
									<input type="number" class="form-control search-planes-element" id="round-adults" placeholder="Adults" name="round-adults" value="{{ $round_adults }}" min=1 oninput="validity.valid||(value='');">
									<span class="error-font text-danger">{{ $errors->first('round_adults')}}</span>
								</div>
							</div>
							<div class="col-md-2 no-padding">
								<div class="input-group date">								
									<input type="text" class="form-control date-picker search-planes-element" id="round-date" name="round-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY"  value="{{ $round_date }}" tabindex="2">
									
								</div>
								<span class="error-font text-danger">{{ $errors->first('round-date') }}</span>
							</div>
							<div class="col-md-2 no-padding hide" id="btn-search-round">
								<button type="submit" class="btn btn-search-plane">SEARCH</button>
							</div>
						</div>
					</div>
          <div class="@if($trip_type != 2) hide @endif" id="multi-trip-div">
						<div class="multi-trip-div-row">
							@if($dep_multi_latitude == '')
								<div class="row multi-row-0">
									<div class="col-md-2 col-md-offset-1 no-padding">
										<div id="plane-dep-multi" class="plane-dep-multi form-group">
											<select class="form-control search-planes-element plane-air-departure-multi select2" data-index="0" id="plane-air-departure-multi-0" name="multi-departure[0]">
												<option value="0" selected>Departure</option>
												@foreach($airports as $airport)
													<option value="{{ $airport->id}}" data-id="{{ $airport->id }}" data-lat="{{ $airport->latitude }}" data-long="{{ $airport->longitude }}">{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id] }} @endif</option>
												@endforeach
											</select>
										</div>
										<div id="helicopter-dep-multi" data-type="1" data-index="0" class="helicopter-dep-multi form-group @if($plane_type != 2) hide @endif">
											<input type="hidden" id="dep-latitude-0" name="dep-multi-latitude[0]">
											<input type="hidden" id="dep-longitude-0" name="dep-multi-longitude[0]">
											<input type="text" autocomplete="off" class="form-control search-planes-element margin-auto" id="dep-helicopter-multi-0" placeholder="Departure" name="helicopter-multi-departure[0]" value="Select From Map" readonly>
										</div>
									</div>
									<div class="col-md-2 no-padding">
										<div id="plane-arr-multi" class="plane-arr-multi form-group">
											<select class="form-control search-planes-element select2 plane-air-arrival-multi" data-index="0" id="plane-air-arrival-multi-0" name="multi-arrival[0]">
												<option value="0" selected>Arrival</option>
												@foreach($airports as $airport)
													<option value="{{ $airport->id}}" data-arr-id="{{ $airport->id }}" data-arr-lat="{{ $airport->latitude }}" data-arr-long="{{ $airport->longitude }}" >{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id] }} @endif</option>
												@endforeach
											</select>
										</div>
										<div id="helicopter-arr-multi" data-type="2" data-index="0" class="helicopter-arr-multi form-group @if($plane_type != 2) hide @endif">
											<input type="hidden" id="arr-latitude-0" name="arr-multi-latitude[0]">
											<input type="hidden" id="arr-longitude-0" name="arr-multi-longitude[0]">
											<input type="text" autocomplete="off" class="form-control search-planes-element margin-auto" id="arr-helicopter-multi-0" placeholder="Arrival" name="helicopter-multi-arrival[0]" value="Select From Map" readonly>
										</div>
									</div>
									<div class="col-md-2 col-xs-12 no-padding">
										<div class="form-group">
											<input type="number" class="form-control search-planes-element margin-auto" placeholder="Adults" name="multi-adults[0]" value="" min=1 oninput="validity.valid||(value='');">
											<span class="error-font text-danger">{{ $errors->first('adults')}}</span>
										</div>
									</div>
									<div class="col-md-2 col-xs-12 no-padding">
										<div class="input-group date margin-auto " >								
											<input type="text" class="form-control date-time-picker search-planes-element" name="multi-date[0]" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="{{ date('d-m-Y H:m')}}" tabindex="2">
											<!--span class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</span-->
										</div>
										<span class="error-font text-danger">{{ $errors->first('from-date') }}</span>
									</div>
									<div class="col-md-2 col-md-offset-0 col-xs-6 col-xs-offset-3 no-padding" id="btn-search-multi">
										<button type="submit" class="btn btn-search-plane margin-auto">SEARCH</button>
									</div>
								</div>
							@elseif(is_array($dep_multi_latitude))
								<?php $keys = array_keys($dep_multi_latitude); ?>
								@foreach($dep_multi_latitude as $index => $dep_m_latitude)
									<div class="row multi-row-{{ $index }}">
										<div class="col-md-2 col-md-offset-1 no-padding">
											<div id="plane-dep-multi" class="plane-dep-multi form-group">
												<select class="form-control search-planes-element plane-air-departure-multi select2" data-index="{{ $index }}" id="plane-air-departure-multi-{{ $index }}" name="multi-departure[{{ $index }}]">
													<option value="0" selected>Departure</option>
													@foreach($airports as $airport)
														<option value="{{ $airport->id}}" data-id="{{ $airport->id }}" data-lat="{{ $airport->latitude }}" data-long="{{ $airport->longitude }}" @if(isset($multi_departure[$index]) && $multi_departure[$index] == $airport->id) selected @endif>{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id]->name }} @endif</option>
													@endforeach
												</select>
											</div>
											<div id="helicopter-dep-multi" data-type="1" data-index="{{ $index }}" class="helicopter-dep-multi form-group @if($plane_type != 2) hide @endif">
												<input type="hidden" id="dep-latitude-{{ $index }}" value="{{ $dep_m_latitude }}" name="dep-multi-latitude[{{ $index }}]">
												<input type="hidden" id="dep-longitude-{{ $index }}" value="@if(isset($dep_multi_longitude[$index])){{$dep_multi_longitude[$index]}}@endif" name="dep-multi-longitude[{{ $index }}]">
												<input type="text" autocomplete="off" class="form-control search-planes-element margin-auto" id="dep-helicopter-multi-{{ $index }}" placeholder="Departure" value="{{ isset($helicopter_multi_departure[$index]) && $helicopter_multi_departure[$index] ? $helicopter_multi_departure[$index] : 'Select From Map' }}" name="helicopter-multi-departure[{{ $index }}]" readonly>
											</div>
										</div>
										<div class="col-md-2 no-padding">
											<div id="plane-arr-multi" class="plane-arr-multi form-group">
												<select class="form-control search-planes-element select2 plane-air-arrival-multi" data-index="{{ $index }}" id="plane-air-arrival-multi-{{ $index }}" name="multi-arrival[{{ $index }}]">
													<option value="0" selected>Arrival</option>
													@foreach($airports as $airport)
														<option value="{{ $airport->id}}" data-arr-id="{{ $airport->id }}" data-arr-lat="{{ $airport->latitude }}" data-arr-long="{{ $airport->longitude }}" @if(isset($multi_arrival[$index]) && $multi_arrival[$index] == $airport->id) selected @endif>{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id]->name }} @endif</option>
													@endforeach
												</select>
											</div>
											<div id="helicopter-arr-multi" data-type="2" data-index="{{ $index }}" class="helicopter-arr-multi form-group @if($plane_type != 2) hide @endif">
												<input type="hidden" id="arr-latitude-{{ $index }}" value="@if(isset($arr_multi_latitude[$index])){{$arr_multi_latitude[$index]}}@endif" name="arr-multi-latitude[{{ $index }}]">
												<input type="hidden" id="arr-longitude-{{ $index }}" value="@if(isset($arr_multi_longitude[$index])){{$arr_multi_longitude[$index]}}@endif" name="arr-multi-longitude[{{ $index }}]">
												<input type="text" autocomplete="off" value="{{ isset($helicopter_multi_arrival[$index]) && $helicopter_multi_arrival[$index] ? $helicopter_multi_arrival[$index] : 'Select From Map' }}" class="form-control search-planes-element margin-auto" id="arr-helicopter-multi-{{ $index }}" placeholder="Arrival" name="helicopter-multi-arrival[{{ $index }}]" readonly>
											</div>
										</div>
										<div class="col-md-2 col-xs-12 no-padding">
											<div class="form-group">
												<input type="number" class="form-control search-planes-element margin-auto" placeholder="Adults" name="multi-adults[{{ $index }}]" value="@if(isset($multi_adults[$index])){{$multi_adults[$index]}}@endif" min=1 oninput="validity.valid||(value='');">
												<span class="error-font text-danger">{{ $errors->first('adults')}}</span>
											</div>
										</div>
										<div class="col-md-2 col-xs-12 no-padding">
											<div class="input-group date margin-auto " >								
												<input type="text" class="form-control date-time-picker search-planes-element" name="multi-date[{{ $index }}]" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="@if(isset($multi_date[$index])){{$multi_date[$index]}}@endif" tabindex="2">
												
											</div>
											<span class="error-font text-danger">{{ $errors->first('from-date') }}</span>
										</div>
										<div class="col-md-2 col-md-offset-0 col-xs-6 col-xs-offset-3 no-padding @if(!(end($keys) == $index))hide @endif">
											<button type="submit" class="btn btn-search-plane margin-auto">SEARCH</button>
										</div>
										<div class="col-md-1 col-md-offset-0 col-xs-6 col-xs-offset-3 no-padding">
											<div class="form-group"></div>
											<a class="btn btn-primary delete-trip" data-index="{{ $index }}"><i class="fa fa-minus"></i></a>
										</div>
									</div>
								@endforeach
							@endif
						</div>
						<div class="row text-right">
							<div class="col-md-11">
								<a class="btn btn-primary" id="add-more"><i class="fa fa-plus"></i></a>
							</div>
						</div>
          </div>
        </form>
      </div>
    </div>
  </header>

  <section id="contact">
    <div class="container">
      <div class="row">
		<div class="col-md-2 col-md-offset-8">
          <div class="form-group">
            <label for="price-filter">Price</label>
            <select class="form-control" id="price-filter">
              <option id="price-asc" value="0">Ascending</option>
              <option id="price-desc" value="1">Descending</option>
            </select>
          </div>
        </div>
		<div class="col-md-2">
          <div class="form-group cst">
            <label for="plane-subtypes-filter">Plane Subtype</label>
            <select class="form-control select2" id="plane-subtypes-filter" multiple>
				@foreach($plane_subtypes as $id => $plane_subtype)
                      <option value="{{ $plane_subtype->id }}" data-id="{{ $plane_subtype->id }}">{{ $plane_subtype->sub_type }}</option>
                @endforeach
            </select>
          </div>
        </div>
        <div class="col-lg-12 text-center">
          <h2 class="section-heading">Machines</h2>
          <hr>
        </div>
      </div>
      <div id="plane-list"></div>
    </div>
  </section>
  
<!-- ========== COMING SOON MODAL ========== -->
<div role="dialog" id="coming-soon-modal" class="modal fade">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <h2>Coming Soon</h2>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- ========== COMING SOON MODAL END ========== -->

<!-- ========== HELICOPTER PICK MODAL========== -->
<!-- Helicopter Pick Modal -->
<div id="helicopter-pick-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">PICK DEPARTURE LOCATION</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Latitude</label>
            <input type="text" id="lat" class="form-control">
          </div>
          <div class="col-md-6 form-group">
            <label>Longitude</label>
            <input type="text" id="long" class="form-control">
          </div>
        </div>
        <div class="row my-3">
          <div class="col-md-12 text-center">
            <input id="search-location" class="form-control mb-3" placeholder="Search location...">
            <div id="map" style="height: 400px; border-radius: 6px; border: 1px solid #ccc;"></div>
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-4 col-md-offset-4 text-center">
            <button id="btn-pick" class="btn btn-success btn-block">SELECT</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- =================================== -->

<!-- ========== HELICOPTER DROP MODAL========== -->
<!-- Helicopter Drop Modal -->
<div id="helicopter-drop-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">PICK ARRIVAL LOCATION</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Latitude</label>
            <input type="text" id="lat-drop" class="form-control">
          </div>
          <div class="col-md-6 form-group">
            <label>Longitude</label>
            <input type="text" id="long-drop" class="form-control">
          </div>
        </div>
        <div class="row my-3 px-3">
          <div class="col-md-12 text-center">
            <input id="search-location-drop" class="form-control mb-3" placeholder="Search location...">
            <div id="map-drop" style="height: 400px; border-radius: 6px; border: 1px solid #ccc;"></div>
            <input type="hidden" id="latitude-drop" name="latitude-drop">
            <input type="hidden" id="longitude-drop" name="longitude-drop">
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-4 col-md-offset-4 text-center">
            <button id="btn-drop" class="btn btn-success btn-block">SELECT</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- =================================== -->

<!-- ========== MULTI TRIP PICK MODAL========== -->
<!-- Multi Trip Pick Modal -->
<div id="multi-trip-pick-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Pick Multi Trip Pickup Location</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Latitude</label>
            <input type="text" id="lat-multi" class="form-control">
          </div>
          <div class="col-md-6 form-group">
            <label>Longitude</label>
            <input type="text" id="long-multi" class="form-control">
          </div>
        </div>
        <div class="row px-3">
          <input id="search-location-multi" class="form-control mb-2" placeholder="Search location">
          <div id="map-multi" style="height: 400px; margin: 15px 0;"></div>

          <!-- These are required for your backend -->
          <input type="hidden" id="latitude-multi" name="latitude-multi">
          <input type="hidden" id="longitude-multi" name="longitude-multi">
        </div>
        <div class="row mt-3">
          <div class="col-md-4 col-md-offset-4 text-center">
            <button id="btn-multi" type="button" class="btn btn-success btn-block">SELECT</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
  window.initialize = window.initialize || function () {};
</script>
<script src="https://maps.googleapis.com/maps/api/js?libraries=geometry,places&key=AIzaSyAzltLLaZzQXcGVsgfBHNa30FkSLIPqpaA&callback=initialize" defer></script>
<script>
  $(function () {
		var routes_data = <?php echo json_encode($routes_data);?>;
		var plane_type = $('#planes').val()
		var trip_id = $('#trips').val();
		function syncSelect2MapText(selector, text, fallbackText) {
			var $select = $(selector);
			if(!$select.length) {
				return;
			}
			var displayText = $.trim(text || fallbackText);
			var $mapOption = $select.find('option[value="0"]').first();
			if($mapOption.length) {
				$mapOption.text(displayText);
			} else {
				$select.prepend($('<option>', { value: 0, text: displayText }));
			}
			$select.val(0).trigger('change.select2');
		}
		function syncMultiTripMode() {
			if(String(trip_id) === '2') {
				if(String(plane_type) === '2') {
					$('.helicopter-arr-multi').removeClass("hide");
					$('.helicopter-dep-multi').removeClass("hide");
					$('.plane-arr-multi').removeClass('hide');
					$('.plane-dep-multi').removeClass('hide');
				} else {
					$('.helicopter-arr-multi').addClass("hide");
					$('.helicopter-dep-multi').addClass("hide");
					$('.plane-arr-multi').removeClass('hide');
					$('.plane-dep-multi').removeClass('hide');
				}
			}

			$('#add-more').closest('.row').removeClass('hide');
			$('#multi-trip-div .delete-trip').closest('[class*="col-"]').removeClass('hide');
			$('#multi-trip-div .helicopter-dep-multi input[type="text"], #multi-trip-div .helicopter-arr-multi input[type="text"]').prop('readonly', true);
		}
		$('#add-more').click(function(){
			$('#multi-trip-div .btn-search-plane').closest('div').addClass('hide');
			var element_index = 0;
			if($('.helicopter-dep-multi').last().length > 0) {
				element_index = $('.helicopter-dep-multi').last().data('index');
				element_index++;
			}
			var multi_html = '';
			multi_html += '<div class="row multi-row-'+element_index+'">';
			multi_html += '<div class="col-md-2 col-md-offset-1 no-padding">';
			multi_html += '		<div id="plane-dep-multi" class="plane-dep-multi form-group">';
			multi_html += '			<select class="form-control search-planes-element plane-air-departure-multi select2" id="plane-air-departure-multi-'+element_index+'" data-index="'+element_index+'"  name="multi-departure['+element_index+']">';
			multi_html += '				<option value="0" selected>Departure</option>';
			multi_html += '				@foreach($airports as $airport)';
			multi_html += '					<option value="{{ $airport->id}}" data-id="{{ $airport->id }}" data-lat="{{ $airport->latitude }}" data-long="{{ $airport->longitude }}">{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id]->name }} @endif</option>';
			multi_html += '				@endforeach';
			multi_html += '			</select>';
			multi_html += '		</div>';
			multi_html += '		<div id="helicopter-dep-multi" data-type="1" data-index="'+element_index+'" class="helicopter-dep-multi form-group '+(plane_type != 2 ? 'hide': '')+'">';
			multi_html += '			<input type="hidden" id="dep-latitude-'+element_index+'" name="dep-multi-latitude['+element_index+']">';
			multi_html += '			<input type="hidden" id="dep-longitude-'+element_index+'" name="dep-multi-longitude['+element_index+']">';
			multi_html += '			<input type="text" autocomplete="off"  class="form-control search-planes-element margin-auto" id="dep-helicopter-multi-'+element_index+'" placeholder="Departure" name="helicopter-multi-departure['+element_index+']" value="Select From Map" readonly>';
			multi_html += '		</div>';
			multi_html += '	</div>';
			multi_html += '	<div class="col-md-2 no-padding">';
			multi_html += '		<div id="plane-arr-multi" class="plane-arr-multi form-group">';
			multi_html += '			<select class="form-control  search-planes-element plane-air-arrival-multi select2" data-index="'+element_index+'" id="plane-air-arrival-multi-'+element_index+'" name="multi-arrival['+element_index+']">';
			multi_html += '				<option value="0" selected>Arrival</option>';
			multi_html += '				@foreach($airports as $airport)';
			multi_html += '					<option value="{{ $airport->id}}" data-arr-id="{{ $airport->id }}" data-arr-lat="{{ $airport->latitude }}" data-arr-long="{{ $airport->longitude }}"  >{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id]->name }} @endif</option>';
			multi_html += '				@endforeach';
			multi_html += '			</select>';
			multi_html += '		</div>';
			multi_html += '		<div id="helicopter-arr-multi" data-type="2" data-index="'+element_index+'" class="helicopter-arr-multi form-group '+(plane_type != 2 ? 'hide': '')+'">';
			multi_html += '			<input type="hidden" id="arr-latitude-'+element_index+'" name="arr-multi-latitude['+element_index+']">';
			multi_html += '			<input type="hidden" id="arr-longitude-'+element_index+'" name="arr-multi-longitude['+element_index+']">';
			multi_html += '			<input type="text" autocomplete="off" class="form-control search-planes-element margin-auto" id="arr-helicopter-multi-'+element_index+'" placeholder="Arrival" name="helicopter-multi-arrival['+element_index+']" value="Select From Map" readonly>';
			multi_html += '		</div>';
			multi_html += '	</div>';
			multi_html += '	<div class="col-md-2 col-xs-12 no-padding">';
			multi_html += '		<div class="form-group">';
			multi_html += '			<input type="number" class="form-control search-planes-element margin-auto" placeholder="Adults" name="multi-adults['+element_index+']"  min=1>';
			multi_html += '			<span class="error-font text-danger"></span>';
			multi_html += '		</div>';
			multi_html += '	</div>';
			multi_html += '	<div class="col-md-2 col-xs-12 no-padding">';
			multi_html += '		<div class="input-group date margin-auto " >	';							
			multi_html += '			<input type="text" class="form-control date-time-picker search-planes-element" name="multi-date['+element_index+']" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="" tabindex="2">';
			multi_html += '			<!--span class="input-group-addon">';
			multi_html += '				<span class="glyphicon glyphicon-calendar"></span>';
			multi_html += '			</span-->';
			multi_html += '		</div>';
			multi_html += '		<span class="error-font text-danger"></span>';
			multi_html += '	</div>';
			multi_html += '	<div class="col-md-2 col-md-offset-0 col-xs-6 col-xs-offset-3 no-padding">';
			multi_html += '		<button type="submit" class="btn btn-search-plane margin-auto">SEARCH</button>';
			multi_html += '	</div>';
			multi_html += '	<div class="col-md-1 col-md-offset-0 col-xs-6 col-xs-offset-3 no-padding">';
			multi_html += '		<div class="form-group"></div>';
			multi_html += '		<a class="btn btn-primary delete-trip" data-index="'+element_index+'"><i class="fa fa-minus"></i></a>';
			multi_html += '	</div>';
			multi_html += '</div>';
			
			$('.multi-trip-div-row').append(multi_html);
			$(".select2").select2();
			$(".date-time-picker").datetimepicker({
				format: "dd-mm-yyyy hh:ii",
				autoclose: true,
			});
			syncMultiTripMode();
		
		});
		$(".date-time-picker").datetimepicker({
			format: "dd-mm-yyyy hh:ii",
			autoclose: true,
		});
		
    $("#date").datetimepicker({
      format: "dd-mm-yyyy hh:ii",
      autoclose: true,
    });
     $("#round-date").datetimepicker({
      format: "dd-mm-yyyy hh:ii",
      autoclose: true,
    });
    
    //Initialize Select2 Elements
    $(".select2").select2();      
 
    $('.btn').on('click', function(){
      $('.btn').removeClass('selected');
      $(this).addClass('selected');
    });
    
		$('#planes').change(function(){
			plane_type = $("option:selected", this).data('id');
			// plane_type values
			// 1-plane, 2-helicopter, 3-air-ambulance
			if(plane_type == 2){
				if(trip_id != 2){
					$("#plane-air-dep-div").removeClass('hide');
					$("#helicopter-dep-div").removeClass("hide");
					$("#plane-air-arr-div").removeClass("hide");
					$("#helicopter-arr-div").removeClass("hide");
					$("#plane-air-dep-round-div").removeClass('hide');
					$("#helicopter-dep-round-div").removeClass("hide");
					$("#plane-air-arr-round-div").removeClass("hide");
					$("#helicopter-arr-round-div").removeClass("hide");          
				} else {
					$('.helicopter-arr-multi').removeClass("hide");
					$('.helicopter-dep-multi').removeClass("hide");
					$('.plane-arr-multi').removeClass('hide');
					$('.plane-dep-multi').removeClass('hide');
				}
			} else {
				if(trip_id != 2){
					$("#plane-air-dep-div").removeClass("hide");
					$("#helicopter-dep-div").addClass("hide");
					$("#plane-air-arr-div").removeClass("hide");
					$("#helicopter-arr-div").addClass("hide");
					$("#plane-air-dep-round-div").removeClass('hide');
					$("#helicopter-dep-round-div").addClass("hide");
					$("#plane-air-arr-round-div").removeClass("hide");
					$("#helicopter-arr-round-div").addClass("hide");        
				} else {
					$('.helicopter-arr-multi').addClass("hide");
					$('.helicopter-dep-multi').addClass("hide");
					$('.plane-arr-multi').removeClass('hide');
					$('.plane-dep-multi').removeClass('hide');
				}
			}
			syncMultiTripMode();
		});
			
		$(document).on('click', ".delete-trip", function() {
			if($(this).data('index') == 0){
				//console.log($('.helicopter-dep-multi').first().data('index'))
			}
			$('.multi-row-'+$(this).data('index')).remove();
			$('.multi-row-'+$('.helicopter-dep-multi').last().data('index')).find('.btn-search-plane').closest('div').removeClass('hide');
		});
		
		$(document).on('change', ".plane-air-departure-multi", function() {
			var selectedOption = $('option:selected', this);
			var latitude = selectedOption.data('lat');
			var longitude = selectedOption.data('long'); 
			var index = $(this).data('index');				
			if(selectedOption.val() == 0) {
				$("#dep-latitude-"+index).val('');
				$("#dep-longitude-"+index).val('');
				$("#dep-helicopter-multi-"+index).val('Select From Map');
				return;
			}
			$("#dep-latitude-"+index).val(latitude);
			$("#dep-longitude-"+index).val(longitude);
			$("#dep-helicopter-multi-"+index).val(selectedOption.text().trim());
		});
		
		$(document).on('change', ".plane-air-arrival-multi", function() {
			var selectedOption = $('option:selected', this);
			var latitude = selectedOption.data('arr-lat');
			var longitude = selectedOption.data('arr-long');
			var index = $(this).data('index');
			if(selectedOption.val() == 0) {
				$("#arr-latitude-"+index).val('');
				$("#arr-longitude-"+index).val('');
				$("#arr-helicopter-multi-"+index).val('Select From Map');
				return;
			}
			$("#arr-latitude-"+index).val(latitude);
			$("#arr-longitude-"+index).val(longitude);
			$("#arr-helicopter-multi-"+index).val(selectedOption.text().trim());
		});
		
		$(document).on('click', '.helicopter-dep-multi, .helicopter-arr-multi', function () {
  $('#field-type').val($(this).data('type'));
  $('#current-index').val($(this).data('index'));

  const index = $(this).data('index');
  const type = $(this).data('type');
  let lat = '', lng = '';

  if (type == 1) {
    lat = $("#dep-latitude-" + index).val();
    lng = $("#dep-longitude-" + index).val();
  } else {
    lat = $("#arr-latitude-" + index).val();
    lng = $("#arr-longitude-" + index).val();
  }

  $("#lat-multi").val(lat);
  $("#long-multi").val(lng);
  $('#multi-trip-pick-modal').modal('show');
  $('#long-multi').trigger('change');
});

		
		$('#dep-helicopter').on('click', function() {
			if(plane_type == 2){
				$('#helicopter-pick-modal').modal('show');
			}
		}); 
		
		$('#arr-helicopter').on('click', function() {
			if(plane_type == 2){
				$('#helicopter-drop-modal').modal('show');
			}
		}); 

		$('#helicopter-dep-round').on('click', function() {
			if(plane_type == 2){
				$('#helicopter-drop-modal').modal('show');
			}
		});

		$('#helicopter-arr-round').on('click', function() {
			if(plane_type == 2){
				$('#helicopter-pick-modal').modal('show');
			}
		});
		
		$("#btn-multi").click(function(e){
			e.preventDefault();
			var field_type = $('#field-type').val();
			var current_index = $('#current-index').val();
			
			var pick_lat = $("#lat-multi").val();
			var pick_long = $("#long-multi").val();
			var searchbox = document.getElementById('search-location-multi').value;
			if(field_type == 1){
				$("#dep-latitude-"+current_index).val(pick_lat);
				$("#dep-longitude-"+current_index).val(pick_long);
				$("#dep-helicopter-multi-"+current_index).val(searchbox);
				syncSelect2MapText("#plane-air-departure-multi-"+current_index, searchbox, "Departure");
			}
			else {
				$("#arr-latitude-"+current_index).val(pick_lat);
				$("#arr-longitude-"+current_index).val(pick_long);
				$("#arr-helicopter-multi-"+current_index).val(searchbox);
				syncSelect2MapText("#plane-air-arrival-multi-"+current_index, searchbox, "Arrival");
				$("#dep-latitude-"+(parseInt(current_index)+1)).val(pick_lat);
				$("#dep-longitude-"+(parseInt(current_index)+1)).val(pick_long);
				$("#dep-helicopter-multi-"+(parseInt(current_index)+1)).val(searchbox);
				syncSelect2MapText("#plane-air-departure-multi-"+(parseInt(current_index)+1), searchbox, "Departure");
			}
			$('#multi-trip-pick-modal').modal('hide');
		});
			
		$("#btn-pick").click(function(e){
			e.preventDefault();
			var pick_lat = $("#lat").val();
			var pick_long = $("#long").val();
			var searchbox = document.getElementById('search-location').value;
			$("#dep-latitude").val(pick_lat);
			$("#dep-longitude").val(pick_long);
			$("#dep-helicopter").val(searchbox);
			syncSelect2MapText("#departure", searchbox, "Departure");
			syncSelect2MapText("#plane-air-arrival-round", searchbox, "Arrival");
			$("#helicopter-arr-round").val(searchbox);
			$('#helicopter-pick-modal').modal('hide');
		});
		
		$("#btn-drop").click(function(e){
			e.preventDefault();
			var drop_lat = $("#lat-drop").val();
			var drop_long = $("#long-drop").val();
			var searchbox = document.getElementById('search-location-drop').value;
			$("#arr-latitude").val(drop_lat);
			$("#arr-longitude").val(drop_long);
			$("#dep-latitude-round").val(drop_lat);
			$("#dep-longitude-round").val(drop_long);
			$("#arr-helicopter").val(searchbox);
			syncSelect2MapText("#plane-air-arrival", searchbox, "Arrival");
			syncSelect2MapText("#plane-air-departure-round", searchbox, "Departure");
			$("#helicopter-dep-round").val(searchbox);
			$('#helicopter-drop-modal').modal('hide');
		});
      
		$('#trips').change(function(){
			trip_id = $(this).val();
			// trip_id values
			// 0-Single Trip, 1-Round Trip, 2-Multi Trip
			if(trip_id == 0) {
				$('#btn-search').removeClass('hide');
				$('#btn-search-round').addClass('hide');
				$('#round-trip').addClass('hide');
				$('#non-multi-trip-div').removeClass('hide');
				$('#multi-trip-div').addClass('hide');
			} else if(trip_id == 1) {
				$('#btn-search').addClass('hide');
				$('#btn-search-round').removeClass('hide');  
				$('#round-trip').removeClass('hide');
				$('#non-multi-trip-div').removeClass('hide');
				$('#multi-trip-div').addClass('hide');
			}
			else {
				$('#non-multi-trip-div').addClass('hide');
				$('#multi-trip-div').removeClass('hide');
			}
			$('#planes').change();
		});
    
    $("#departure").on('change', function() {
      var latitude = $('option:selected', this).data('lat');
      var longitude = $('option:selected', this).data('long');
      var departureText = $('option:selected', this).text().trim();
      //console.log(latitude);
      $("#dep-latitude").val(latitude);
      $("#dep-longitude").val(longitude);
      $("#dep-helicopter").val(departureText);
      
      var dep_id = $("option:selected", this).data('id');
      $('#plane-air-arrival-round').val(dep_id);
      $('#plane-air-arrival-round').select2().val(dep_id).trigger('change');
    });  
    
    $("#plane-air-arrival").on('change', function() {
			
			var latitude = $('option:selected', this).data('arr-lat');
			var longitude = $('option:selected', this).data('arr-long');
			var arrivalText = $('option:selected', this).text().trim();
			
			$("#arr-latitude").val(latitude);
			$("#arr-longitude").val(longitude);
			$("#dep-latitude-round").val(latitude);
			$("#dep-longitude-round").val(longitude);
			$("#arr-helicopter").val(arrivalText);
			/*	
      var latitude_round = $('option:selected', this).data('lat-round');
      var longitude_round = $('option:selected', this).data('long-round');
      $("#dep-latitude-round").val(latitude_round);
      $("#dep-longitude-round").val(longitude_round);
      */
      var arr_id = $("option:selected", this).data('arr-id');
      $('#plane-air-departure-round').val(arr_id);
      $('#plane-air-departure-round').select2().val(arr_id).trigger('change');
    });

		$("#plane-air-departure-round").on('change', function() {
			var selectedOption = $('option:selected', this);
			if(selectedOption.val() == 0) {
				$("#helicopter-dep-round").val('Select From Map');
				return;
			}
			$("#dep-latitude-round").val(selectedOption.data('lat-round'));
			$("#dep-longitude-round").val(selectedOption.data('long-round'));
			$("#helicopter-dep-round").val(selectedOption.text().trim());
		});

		$("#plane-air-arrival-round").on('change', function() {
			var selectedOption = $('option:selected', this);
			if(selectedOption.val() == 0) {
				$("#helicopter-arr-round").val('Select From Map');
				return;
			}
			$("#helicopter-arr-round").val(selectedOption.text().trim());
		});
    
		syncMultiTripMode();
		if($('#trips').val() == 2 ){
			getPlaneListMulti();
		}
		else{ 
			getPlaneList();
		}
    
	$('#plane-subtypes-filter').on('change',function(){
		getPlaneList();
		getPlaneListMulti();
	});

    //search-planes
    function getPlaneList()
    {
      var dep_latitude = $('#old-dep-latitude').val();
      var dep_longitude = $('#old-dep-longitude').val();
      var arr_latitude = $('#old-arr-latitude').val();
      var arr_longitude = $('#old-arr-longitude').val();
      var helicopter_departure = $('#old-helicopter-departure').val();
      var helicopter_arrival = $('#old-helicopter-arrival').val();
      var plane_type = $('#old-plane-type').val();
      var departure = $('#old-departure').val(); 
      var arrival = $('#old-arrival').val();
      var trip = $('#old-trip-type').val();
      var adults = $('#old-adults').val();
      var total_adults = $('#old-total-adults').val();
      var date = $('#old-date').val();
      var round_date = $('#round-date').val();
			var filter_id = $('#price-filter').val();
			var subtypes_filter_id = ($('#plane-subtypes-filter').val() || []).join(',');
      var helicopter_dep_lat = $('#dep-latitude').val();
      var helicopter_dep_long = $('#dep-longitude').val();
      var helicopter_arr_lat = $('#arr-latitude').val();
      var helicopter_arr_long = $('#arr-longitude').val();
      var gt1 = parseFloat($('#gt1').val()) || 0;
      var gt2 = parseFloat($('#gt2').val()) || 0;
      $.ajax({
				url: '/plane/plane-list?dep-latitude='+ dep_latitude +'&dep-longitude='+ dep_longitude+'&filter-id='+ filter_id +'&subtypes-filter-id='+ subtypes_filter_id +'&arr-latitude='+ arr_latitude +'&arr-longitude='+ arr_longitude +'&plane-type='+ plane_type+'&arrival='+arrival+'&departure='+departure+'&trip-type='+trip+'&helicopter-departure='+helicopter_departure+'&helicopter-arrival='+helicopter_arrival+'&total-adults='+total_adults+'&date='+$('#date').val(),
        type: 'GET',
        success: function(data, textStatus, jqXHR){      
          var plane_list = data['planes']; 
		  console.log(plane_list);
          var list = "";
          for(var i=0; i<plane_list.length; i++)
          {
            list += '<div id="plane-info-div-'+plane_list[i].id+'" class="row form-group">';
            list += '<div class="col-md-10 col-md-offset-1 plane-info">';
            if(plane_list[i].display_image != '')
            {   
              list += '<div class="col-md-6">';
              list += '<div class="plane-image" style="background-image:url(/uploads/'+plane_list[i].display_image+');">';
              list += '<div class="plane-main-image"></div>';
              list += '</div></div>';
            }
            else
            {
              list += '<div class="col-md-6">'
              list += '<i class="fa fa-picture-o fa-4x"></i>';
              list += '</div>';
            }
            list += ' <div class="col-md-6">';
            list += ' <div class="col-md-6 text-left no-padding">';
            list += '<h4 class="plane-name">'+plane_list[i].name+(plane_list[i].Call_Sign ? ' {'+plane_list[i].Call_Sign+'}' : '')+'</h4>';
            list += '</div>';
            list += ' <div class="col-md-6 text-right no-padding">';
            list += '<h5 class="plane-type">'+plane_list[i].plane_type+'</h5>';
            list += '<h5 class="plane-type">'+plane_list[i].subtype+'</h5>';
            list += '</div>';
            list += '<div class="clearfix"></div>';
            list += '<h5><label class="fixed-width"><b>Base</b></label> : '+plane_list[i].city_name+'</h5>';
            list += '<h5><label class="fixed-width"><b>Route</b></label> : '+plane_list[i].path+'</h5>';
            
            var avail_planes_city_id = plane_list[i].city_id;
            var type_id = plane_list[i].type_id;
            var trip_type = plane_list[i].trip_type; 
            
            var air_departure_lat = plane_list[i].air_departure_lat;
            var air_departure_lng = plane_list[i].air_departure_lng;
            var air_arrival_lat = plane_list[i].air_arrival_lat;
            var air_arrival_lng = plane_list[i].air_arrival_lng;
            
            var avail_planes_lat = plane_list[i].avail_planes_lat;
            var avail_planes_lng = plane_list[i].avail_planes_lng;
            
            var departure_city_id = plane_list[i].departure_city_id; 
            var arrival_city_id = plane_list[i].arrival_city_id; 
            
            var speed_coefficient;
            if(plane_list[i].speed_coefficient == 0){
              speed_coefficient = 1;
            } else {
              speed_coefficient = plane_list[i].speed_coefficient;
            }
            var avail_distance = plane_list[i].avail_distance;
            var handling_charges = plane_list[i].handling_charges;
						if(type_id == 2){
							handling_charges = 0;
						}
            var tax = plane_list[i].tax;
            
            var air_departure_geo = new google.maps.LatLng(air_departure_lat, air_departure_lng);
						if(plane_type == 2){
							//air_departure_geo = new google.maps.LatLng(helicopter_dep_lat, helicopter_dep_long);
						} 
						if(plane_list[i].distance <= 25){
							helicopter_dep_lat = plane_list[i].avail_planes_lat;
							helicopter_dep_long = plane_list[i].avail_planes_lng;	
						}
            var air_arrival_geo = new google.maps.LatLng(air_arrival_lat, air_arrival_lng);
            var avail_geo = new google.maps.LatLng(avail_planes_lat, avail_planes_lng);

						var plane_distance = 0.539957 * calcDistance(avail_geo, air_departure_geo);
						var plane_distance_single = 0.539957 * calcDistance(air_arrival_geo, avail_geo); 
						var travel_distance = 0.539957 * calcDistance(air_departure_geo, air_arrival_geo);
						var distance = 0;
            var total_distance = parseFloat(calcDistance(air_departure_geo, air_arrival_geo));
            var total_time = 0; 
            var time_single = 0; 
						var flight_cost = 0;
						var additional_days = 0;
						var crew_handling_additional_days = 0;
            var price_per_hour = plane_list[i].price_per_hour;
            var speed = plane_list[i].speed;
						var total_time_array = {};
						var additional_time = 0;
						//when plane base and departure are different
						
            if(avail_planes_city_id != departure_city_id)
            {
							//if round trip
              if(trip_type == 1)
              {
								//avail_geo avail_planes_lat, avail_planes_lng
								//air_departure_geo air_departure_lat, air_departure_lng
								//air_arrival_geo air_arrival_lat, air_arrival_lng
                var temp_dis = calcDistance(avail_geo, air_departure_geo); // distance between base and departure
								distance = parseFloat(calcDistance(air_departure_geo, air_arrival_geo));//distance between departure and arrival  
								
								distance = distance * 0.539957; 
								temp_dis = temp_dis * 0.539957;
                
								if(temp_dis >= 100) {
									time_temp1 = gt1 + gt2 + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((temp_dis-200)/(speed/60));
								}
								else{
									time_temp1 = gt1 + (temp_dis/((speed*speed_coefficient)/60))+gt2;
								}
								
								//base -- dep
								if(plane_type != 2){
									if(routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng] != undefined){
										if(routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].location_2_latitude ==  air_departure_lat && routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].location_2_longitude == air_departure_lng){
											time_temp1 = routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].time;
											if(routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].distance > 0){
												temp_dis = routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].distance;
											}
										}
									}
								}
								var ctime_temp1 = time_temp1;
								if(time_temp1 < 120){
									if(temp_dis !=0 ){
										//ctime_temp1 = 120;
										if(type_id == 2){
											handling_charges += 15000;
										}
									}
								}
								
								total_distance = parseFloat(temp_dis);
								if(distance >= 100){
									time_distance1 = gt1 + gt2 + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((distance-200)/(speed/60));
								}
								else{
									time_distance1 = gt1 + (distance/((speed*speed_coefficient)/60))+gt2;
								}
								
								var time_add = 0;
								//--dep to arr
								if(plane_type != 2){
									if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng] != undefined){
										if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].location_2_latitude == air_arrival_lat  && routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].location_2_longitude == air_arrival_lng){
											time_distance1 = routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].time;
											if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].distance > 0){
												distance = routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].distance;
											}
										}
									}
								}
								time_add = time_distance1;
								
								var ctime_distance1 = time_distance1;
								if(time_distance1 < 120){
									//ctime_distance1 = 120;
									if(type_id == 2){
										handling_charges += 15000;
									}
								}
								total_distance += parseFloat(distance);
								
								distance = parseFloat(calcDistance(air_departure_geo, air_arrival_geo));//distance between departure and arrival  
								distance = distance * 0.539957; 
								
								if(distance >= 100){
									time_distance2 = gt1 + gt2 + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((distance-200)/(speed/60));
								}
								else{
									time_distance2 = gt1 + (distance/((speed*speed_coefficient)/60))+gt2;
								}
								//arr -- dep
								if(plane_type != 2){
									if(routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng] != undefined){
										if(routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].location_2_latitude ==  air_departure_lat && routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].location_2_longitude == air_departure_lng){
											time_distance2 = routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].time;
											if(routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].distance > 0){
												distance = routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].distance;
											}
										}
									}
								}
								
								var ctime_distance2 = time_distance2;
								if(time_distance2 < 120){
									//ctime_distance2 = 120;
									if(type_id == 2){
										handling_charges += 15000;
									}
								}
								
								total_distance += parseFloat(distance);
								temp_dis = calcDistance(avail_geo, air_departure_geo);
								temp_dis = temp_dis * 0.539957;
								
								if(temp_dis >= 100) {
									time_temp2 = gt1 + gt2 + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((temp_dis-200)/(speed/60));
								}
								else{
									time_temp2 = gt1 + (temp_dis/((speed*speed_coefficient)/60))+gt2;
								}
								//dep -- base   
								if(plane_type != 2){
									if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng] != undefined){
										if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].location_2_latitude ==  avail_planes_lat && routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].location_2_longitude == avail_planes_lng){
											time_temp2 = routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].time;
											if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].distance > 0){
												temp_dis = routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].distance;
											}
										}
									}
								}
								
								var ctime_temp2 = time_temp2;
								if(time_temp2 < 120){
									if(temp_dis != 0){
										//ctime_temp2 = 120;
										if(type_id == 2){
											handling_charges += 15000;
										}
									}
								}
								
								total_distance += parseFloat(temp_dis);
								//total_time = time_temp1 + time_distance1 + time_temp2 + time_distance2;
								
								hours = Math.floor( Math.round(time_temp1) / 60);        
								minutes = Math.round(time_temp1) % 60; 
								flight_cost = (Math.floor( Math.round(ctime_temp1) / 60) * price_per_hour) + ((Math.round( ctime_temp1) % 60 / 60) * price_per_hour);
								
								hours += Math.floor( Math.round(time_distance1) / 60);        
								minutes += Math.round(time_distance1) % 60; 
								flight_cost += (Math.floor( Math.round(ctime_distance1) / 60) * price_per_hour) + ((Math.round( ctime_distance1) % 60 / 60) * price_per_hour);
								
								hours += Math.floor( Math.round(time_temp2) / 60);        
								minutes += Math.round(time_temp2) % 60; 
								flight_cost += (Math.floor( Math.round(ctime_temp2) / 60) * price_per_hour) + ((Math.round( ctime_temp2) % 60 / 60) * price_per_hour);
								
								hours += Math.floor( Math.round(time_distance2) / 60);        
								minutes += Math.round(time_distance2) % 60;
								
								var start = moment($('#date').val(), "DD-MM-YYYY").add(Math.floor(time_add), 'minutes');
								start = moment(start.format("DD-MM-YYYY"), "DD-MM-YYYY");
								var end = moment($('#round-date').val(), "DD-MM-YYYY");
								// end - start returns difference in milliseconds 
                
								total_time_array[moment($('#date').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] = total_time_array[moment($('#round-date').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] = 0;
								total_time_array[moment($('#date').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] += Math.round(time_temp1) + Math.round(time_distance1 )
								total_time_array[moment($('#round-date').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] += Math.round(time_temp2) + Math.round(time_distance2)
								
								$.each(total_time_array, function(index, value){
									if(parseFloat(value) < 120){
										additional_time += (120 - value)
									}
								})	
								
								crew_handling_additional_days = additional_days = end.diff(start, "days");
								if(crew_handling_additional_days <= 0){
									crew_handling_additional_days = 1
								}
								else{
									additional_days--;
								}
								
								flight_cost += price_per_hour * 2 * additional_days;
								flight_cost += (Math.floor( Math.round(additional_time) / 60) * price_per_hour) + ((Math.round( additional_time) % 60 / 60) * price_per_hour);
								flight_cost += (Math.floor( Math.round(ctime_distance2) / 60) * price_per_hour) + ((Math.round( ctime_distance2) % 60 / 60) * price_per_hour);
								
              }
              else
              {
								//var air_departure_geo = new google.maps.LatLng(air_departure_lat, air_departure_lng);            
								//var air_arrival_geo = new google.maps.LatLng(air_arrival_lat, air_arrival_lng);
								//var avail_geo = new google.maps.LatLng(avail_planes_lat, avail_planes_lng);
                var temp1 = calcDistance(avail_geo, air_departure_geo); // distance between base and departure
								distance = parseFloat(calcDistance(air_departure_geo, air_arrival_geo));//distance between departure and arrival
                var temp2 = calcDistance(air_arrival_geo, avail_geo); // distance between arrival and base
								
								//convert km to nm
								distance = distance * 0.539957; 
								temp1 = temp1 * 0.539957; 
								temp2 = temp2 * 0.539957; 
								
								time_temp1 = 0;
								if(Math.round(temp1) != 0){
									if(temp1 >= 100)	{
										time_temp1 = gt1 + gt2 + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((temp1-200)/(speed/60));  
									}
									else{
										time_temp1 = gt1 + (temp1/((speed*speed_coefficient)/60))+gt2;
									}
								}
								
								//base -- dep
								if(plane_type != 2){
									if(routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng] != undefined){
										if(routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].location_2_latitude ==  air_departure_lat && routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].location_2_longitude == air_departure_lng){
											time_temp1 = routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].time;
											if(routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].distance > 0){
												temp1 = routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].distance;
											}
										}
									}
								}
								
								var ctime_temp1 = time_temp1;
								if(time_temp1 < 120){
									if(temp1 != 0){
										//ctime_temp1 = 120;
										if(type_id == 2){
											handling_charges += 15000;
										}
									}
								}
								
								if(distance >= 100){
									time_distance = gt1 + gt2 + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((distance-200)/(speed/60)); 
								}
								else{
									time_distance = gt1 + (distance/((speed*speed_coefficient)/60))+gt2;
								}
								//console.log(time_distance+'---'+ plane_list[i].path+'---'+ plane_list[i].name);
								//dep -- arr
								if(plane_type != 2){
									if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng] != undefined){
										if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].location_2_latitude ==  air_arrival_lat && routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].location_2_longitude == air_arrival_lng){
											time_distance = routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].time;
											if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].distance > 0){
												distance = routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].distance;
											}
										}
									}
								}
								
								var ctime_distance = time_distance;
								if(time_distance < 120){
									if(distance != 0){
										//ctime_distance = 120;
										if(type_id == 2){
											handling_charges += 15000;
										}
									}
								}
								
								if(temp2 >= 100){
									time_temp2 = gt1 + gt2 + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((temp2-200)/(speed/60)); 
								}
								else{
									time_temp2 = gt1 + (temp2/((speed*speed_coefficient)/60))+gt2;
								}
								//total_time = time_temp1 + time_distance + time_temp2;
								
								
								//arr -- base
								if(plane_type != 2){
									if(routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng] != undefined){
										if(routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].location_2_latitude ==  avail_planes_lat && routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].location_2_longitude == avail_planes_lng){
											time_temp2 = routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].time;
											if(routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].distance > 0){
												temp2 = routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].distance;
											}
										}
									}
								}
								if(plane_list[i].city_id == arrival_city_id){
									time_temp2 = temp2 = 0;
								}
								
								var ctime_temp2 = time_temp2;
								if(time_temp2 < 120){
									if(temp2 != 0){
										//ctime_temp2 = 120;
										if(type_id == 2){
											handling_charges += 15000;
										}
									}
								}
								
								
								hours = Math.floor( Math.round(time_temp1) / 60);        
								minutes =  Math.round(time_temp1) % 60;
								flight_cost = (Math.floor( Math.round(ctime_temp1) / 60) * price_per_hour) + ((Math.round( ctime_temp1) % 60 / 60 )* price_per_hour);
								hours += Math.floor( Math.round(time_distance) / 60);        
								minutes +=  Math.round(time_distance) % 60; 
								flight_cost += (Math.floor( Math.round(ctime_distance) / 60) * price_per_hour) + ((Math.round( ctime_distance) % 60 / 60) * price_per_hour);
								
								hours += Math.floor( Math.round(time_temp2) / 60);        
								minutes +=  Math.round(time_temp2) % 60; 
								
								flight_cost += (Math.floor( Math.round(ctime_temp2) / 60) * price_per_hour) + ((Math.round( ctime_temp2) % 60 / 60 ) * price_per_hour);
								
								if((time_temp1 + time_distance + time_temp2) < 120){
									additional_time = 120 - (time_temp1 + time_distance + time_temp2);
								}
								
                total_distance = parseFloat(distance) + parseFloat(temp1) + parseFloat(temp2);
              } 
            } else {
							
              distance = parseFloat(calcDistance(air_arrival_geo, air_departure_geo));//distance between arrival and departure
							//base and departure are same, departure to arrival and arrival to departure(base)
              
							distance = distance * 0.539957; 
							distance_r = distance;
							if(distance >= 100){
								time_single = gt1 + gt2 + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((distance-200)/(speed/60)); 
							}
							else{
								time_single = gt1 + (distance/((speed*speed_coefficient)/60))+gt2;
							}
							//total_time = 2 * time_single; 
							time_single_r = time_single;
							//arr -- base
							if(plane_type != 2){
								if(routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng] != undefined){
									if(routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].location_2_latitude ==  air_departure_lat && routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].location_2_longitude == air_departure_lng){
										time_single = routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].time;
										if(routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].distance > 0){
											distance = routes_data[plane_list[i].id+'-'+air_arrival_lat+'-'+air_arrival_lng].distance;
										}
									}
								}
								if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng] != undefined){
									if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].location_2_latitude ==  air_arrival_lat && routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].location_2_longitude == air_arrival_lng){
										time_single_r = routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].time;
										if(routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].distance > 0){
											distance_r = routes_data[plane_list[i].id+'-'+air_departure_lat+'-'+air_departure_lng].distance;
										}
									}
								}
							}
							var start = moment($('#date').val(), "DD-MM-YYYY").add(Math.floor(time_single), 'minutes');
							start = moment(start.format("DD-MM-YYYY"), "DD-MM-YYYY");
							var end = moment($('#round-date').val(), "DD-MM-YYYY");
							// end - start returns difference in milliseconds 
              
							var diff = new Date(end - start);
							if(trip_type == 1) {
								crew_handling_additional_days = additional_days = end.diff(start, "days");
							}
							if(crew_handling_additional_days <= 0){
								crew_handling_additional_days = 1
							}
							else{
								additional_days--;
							}
							
							//flight_cost = price_per_hour * 2 * additional_days;
							
							var ctime_single = time_single;
							if(time_single < 120){
								if(distance != 0 ){
									//ctime_single = 120;
									if(type_id == 2){
										handling_charges += 15000;
									}
								}
							}
							
							var ctime_single_r = time_single_r;
							if(time_single_r < 120){
								if(distance_r != 0 ){
									//ctime_single_r = 120;
									if(type_id == 2){
										handling_charges += 15000;
									}
								}
							}
							
							total_time_array[moment($('#date').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] = total_time_array[moment($('#round-date').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] = 0
							total_time_array[moment($('#date').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] += Math.round(time_single)
							total_time_array[moment($('#round-date').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] += Math.round(time_single_r)
							
							$.each(total_time_array, function(index, value){
								if(parseFloat(value) < 120){
									additional_time += (120 - value)
								}
							})
								
							hours = Math.floor( Math.round(time_single) / 60);        
							minutes =  Math.round(time_single) % 60;
							flight_cost += (Math.floor( Math.round(ctime_single) / 60) * price_per_hour) + ((Math.round(ctime_single) % 60/ 60) * price_per_hour);
							
							hours += Math.floor( Math.round(time_single_r) / 60);        
							minutes +=  Math.round(time_single_r) % 60; 
							flight_cost += (Math.floor(Math.round( ctime_single_r) / 60) * price_per_hour) + ((Math.round( ctime_single_r) % 60 / 60 ) * price_per_hour);
							total_distance = parseFloat(distance) + parseFloat(distance_r);
            }
						//total_distance = total_distance * 0.539957;
            
            
            //var time = (distance / speed);
            flight_cost += (Math.floor(Math.round( additional_time) / 60) * price_per_hour);
            minutes += additional_time;
            if(end != undefined) {
              var stay_time = stay_time_hours = stay_time_minutes = 0;
              var total_days = end.diff(start, "days") + 1;
              if((total_days * 120) > (minutes + hours *60)){
                stay_time = (total_days * 120) - (minutes + hours *60);
                stay_time_hours = Math.floor(stay_time / 60);
                stay_time_minutes = (stay_time % 60);
                flight_cost += price_per_hour * stay_time / 60;
              }
              minutes += stay_time;
            }
						hours = hours + Math.floor( Math.round(minutes) / 60);        
            minutes = Math.round(minutes) % 60;
            flight_cost = (hours * price_per_hour) + (minutes / 60 * price_per_hour);
             
            //var flight_cost = (distance / speed) * price_per_hour;
            
           /* var hours = parseInt(String(time).split('.')[0]);
            time = (time - hours) * 100;
            var minutes = (time * 60)/100;
            time = String(time).split('.');*/
           
            var crew_handling_charges = 0;
						if(trip_type == 1) {
							crew_handling_charges = 25000 * crew_handling_additional_days;
						}
            var sub_total = flight_cost + handling_charges + crew_handling_charges;           
           // console.log("Sub_total" +sub_total);
            
            var tax_amount = (tax/100) * sub_total;
            //console.log("tax_amount" +tax_amount);
            
            //fixed medical team cost
            if(type_id == 3){
              var final_amount = sub_total + 40000;
              //console.log("final_amount" +final_amount);
            } else{
              var final_amount = tax_amount + sub_total;
              //console.log("final_amount" +final_amount);
            }
            
            
            list += '<h5><label class="fixed-width"><b>Flying Cost</b></label> : <i class="fa fa-rupee"></i>'+flight_cost.toFixed()+' (For '+hours+' Hrs '+minutes.toFixed()+' min.)</h5>';
            list += '<h5><label class="fixed-width"><b>Distance</b></label> : '+total_distance.toFixed(2)+' NM</h5>';
            list += '<h5><label class="fixed-width"><b>Airport Handling Charges</b></label> : <i class="fa fa-rupee"></i>'+handling_charges.toFixed()+'</h5>';
						if(trip_type == 1) {
							list += '<h5><label class="fixed-width"><b>Crew Handling Charges</b></label> : <i class="fa fa-rupee"></i>'+crew_handling_charges.toFixed()+'</h5>';
						}
            list += '<h5><label class="fixed-width"><b>Sub Total</b></label> : <i class="fa fa-rupee"></i>'+sub_total.toFixed()+'</h5>';
            
            if(type_id == 3){
              list += '<h5><label class="fixed-width"><b>Fixed Medical Team Cost</b></label> : <i class="fa fa-rupee"></i>'+40000+'</h5>';
            } else{
              list += '<h5><label class="fixed-width"><b>GST (18%)</b></label> : <i class="fa fa-rupee"></i>'+tax_amount.toFixed()+'</h5>';
            }
						
            list += '<h5 class="grand-total" data-id="'+plane_list[i].id+'" data-total="'+final_amount.toFixed()+'"><label class="fixed-width"><b>Grand Total</b></label> : <i class="fa fa-rupee"></i>'+final_amount.toFixed()+'</h5>';
            list += '<div class="col-md-12 text-right">';
            
            list += '<a href="/plane/plane?plane-id='+plane_list[i].id+'&arrival='+arrival+'&departure='+departure+'&adults='+adults+'&date='+date+'&round-date='+round_date+'&plane-type='+plane_type+'&latitude='+ dep_latitude +'&longitude='+ dep_longitude +'&trip-type='+ trip_type+'&helicopter-departure='+helicopter_departure+'&helicopter-arrival='+helicopter_arrival+'&travel-distance='+ travel_distance+'&plane-distance='+ plane_distance+'&plane-distance-single='+ plane_distance_single +'&helicopter-dep-lat='+helicopter_dep_lat+'&helicopter-dep-long='+helicopter_dep_long+'&helicopter-arr-lat='+helicopter_arr_lat+'&helicopter-arr-long='+helicopter_arr_long+'&speed_coefficient='+speed_coefficient+'&additional-days='+additional_days+'&crew-additional-days='+crew_handling_additional_days+'&ground-handling='+handling_charges+'&additional-time='+additional_time+'" class="btn btn-view">View Details</a>';
            list += '</div>';
            list += '</div>';
            list += '</div>';
            list += '</div>';
          }
          $('#plane-list').html(list);
					sortMachines();
        },
        error: function(jqXHR, textStatus, errorThrown){
          $('.btn-search-plane').attr('disabled', false);
          //$("#search-loading").hide();  
          var errResponse = JSON.parse(jqXHR.responseText);
          if (errResponse.error) {
            $.each(errResponse.error, function(index, value)
            { 	
              if (value.length != 0)
              {
                var $inpElm = $("#" + index);
                $inpElm.closest('.form-group').addClass('has-error');
                $inpElm.closest('.form-group').append('<span class="text-danger">' + value + '</span>');
              }
            });
          }
        },
      });
    }
		
		function getPlaneListMulti(){
			var plane_type = $('#old-plane-type').val();
			var filter_id = $('#price-filter').val();
			var subtypes_filter_id = ($('#plane-subtypes-filter').val() || []).join(',');
      var gt1 = parseFloat($('#gt1').val()) || 0;
      var gt2 = parseFloat($('#gt2').val()) || 0;
			$.ajax({
				url: '/plane/plane-list-multi?filter-id='+ filter_id +'&subtypes-filter-id='+ subtypes_filter_id +'&plane-type='+ plane_type+'&dep-multi-latitude='+'<?php echo json_encode($dep_multi_latitude); ?>'+'&arr-multi-latitude='+'<?php echo json_encode($arr_multi_latitude); ?>'+'&dep-multi-longitude='+'<?php echo json_encode($dep_multi_longitude); ?>'+'&arr-multi-longitude='+'<?php echo json_encode($arr_multi_longitude); ?>'+'&arrival='+'<?php echo json_encode($multi_arrival); ?>'+'&departure='+'<?php echo json_encode($multi_departure); ?>'+'&date='+'<?php echo json_encode($multi_date); ?>'+'&adults='+'<?php echo json_encode($multi_adults); ?>'+'&helicopter-multi-departure='+'<?php echo json_encode($helicopter_multi_departure); ?>'+'&helicopter-multi-arrival='+'<?php echo json_encode($helicopter_multi_arrival); ?>',
        type: 'GET',
        success: function(data, textStatus, jqXHR){
					var plane_list = data['planes']; 
          var list = "";
          for(var i=0; i<plane_list.length; i++)
          {
            list += '<div id="plane-info-div-'+plane_list[i].id+'" class="row form-group">';
            list += '<div class="col-md-10 col-md-offset-1 plane-info">';
            if(plane_list[i].display_image != '')
            {   
              list += '<div class="col-md-6">';
              list += '<div class="plane-image" style="background-image:url(/uploads/'+plane_list[i].display_image+');">';
              list += '<div class="plane-main-image"></div>';
              list += '</div></div>';
            }
            else
            {
              list += '<div class="col-md-6">'
              list += '<i class="fa fa-picture-o fa-4x"></i>';
              list += '</div>';
            }
            list += ' <div class="col-md-6">';
            list += ' <div class="col-md-6 text-left no-padding">';
            list += '<h4 class="plane-name">'+plane_list[i].name+(plane_list[i].Call_Sign ? ' {'+plane_list[i].Call_Sign+'}' : '')+'</h4>';
            list += '</div>';
            list += ' <div class="col-md-6 text-right no-padding">';
            list += '<h5 class="plane-type">'+plane_list[i].plane_type+'</h5>';
            list += '<h5 class="plane-type">'+plane_list[i].subtype+'</h5>';
            list += '</div>';
            list += '<div class="clearfix"></div>';
            list += '<h5><label class="fixed-width"><b>Base</b></label> : '+plane_list[i].city_name+'</h5>';
            list += '<h5><label class="fixed-width"><b>Route</b></label> : '+plane_list[i].path+'</h5>';
            
            var avail_planes_city_id = plane_list[i].city_id;
            var type_id = plane_list[i].type_id;
            var trip_type = plane_list[i].trip_type; 
            
            var air_departure_lat = plane_list[i].air_departure_lat;
            var air_departure_lng = plane_list[i].air_departure_lng;
            var air_arrival_lat = plane_list[i].air_arrival_lat;
            var air_arrival_lng = plane_list[i].air_arrival_lng;
            var departure = plane_list[i].departure;
            var arrival = plane_list[i].arrival;
            
            var avail_planes_lat = plane_list[i].avail_planes_lat;
            var avail_planes_lng = plane_list[i].avail_planes_lng;
            
            var departure_city_id = plane_list[i].departure_city_id; 
            
            var speed_coefficient;
            if(plane_list[i].speed_coefficient == 0){
              speed_coefficient = 1;
            } else {
              speed_coefficient = plane_list[i].speed_coefficient;
            }
            var avail_distance = plane_list[i].avail_distance;
            var handling_charges = plane_list[i].handling_charges;
            var tax = plane_list[i].tax;
            var avail_geo = new google.maps.LatLng(avail_planes_lat, avail_planes_lng);
						
						var distance = 0;
						var total_distance = 0;
            var total_days = 0; 
            var total_time = 0; 
						var flight_cost = 0;
						var crew_handling_charges = 0;
            var price_per_hour = plane_list[i].price_per_hour;
            var speed = plane_list[i].speed;
						var json_data = {};
						var total_time_array = {};
						var additional_time = 0;
						json_data["base"] = {};
						json_data["arr"] = {};
						var counter = 0;
						var first_index = 0;
						var previous_index = 0;

						$( air_departure_lat ).each(function( index,d_latitude ) {
							first_index = index;
							return false;
						});
						
						$( air_departure_lat ).each(function( index,d_latitude ) {
							json_data[index] = {};
						});
						
						$( JSON.parse('<?php echo json_encode(array_values($multi_date)); ?>') ).each(function( index,value ) {
							total_time_array[moment(value, "DD-MM-YYYY").format("DD-MM-YYYY")] = 0;
						});
						
						urltime_distance = time_distance = 0;
						gt = JSON.parse('<?php echo json_encode($gt); ?>');
						
						//when plane base and departure are different
            if(avail_planes_city_id != departure_city_id)
            {
							var air_arrival_geo = new google.maps.LatLng(air_departure_lat[0], air_departure_lng[0]);
							var air_departure_geo = new google.maps.LatLng(avail_planes_lat, avail_planes_lng);
							distance = parseFloat(calcDistance(air_departure_geo, air_arrival_geo));
							distance = parseFloat(distance) * 0.539957;
							if(Math.round(distance) != 0){
								depgt = arrgt = 0;
								if(departure[0] != undefined && gt[departure[0]]){
									arrgt = parseFloat(gt[departure[0]]);
								}
								if(plane_list[i].airport_id != 0 && gt[plane_list[i].airport_id]){
									depgt = parseFloat(gt[plane_list[i].airport_id]);
								}
								time_dist = 0;
								if(Math.round(distance) >= 1){
									if(distance >= 100){
										time_dist = depgt + arrgt + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((distance-200)/(speed/60)); 
									}
									else{
										time_dist = depgt + (distance/((speed*speed_coefficient)/60)) + arrgt;
									}
								}
								if(plane_type != 2){
									if(routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng] != undefined){
										if(routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].location_2_latitude ==  air_departure_lat[0] && routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].location_2_longitude == air_departure_lng[0]){
											time_dist = routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].time;
											if(routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].distance > 0){
												distance = routes_data[plane_list[i].id+'-'+avail_planes_lat+'-'+avail_planes_lng].distance;
											}
										}
									}
								}
								time_dist = Math.round(time_dist);
								var ctime_dist = time_dist;
								if(time_dist < 120){
									if(Math.round(distance) != 0){
										//ctime_dist = 120;
									}
								}
								
								hours = Math.floor( Math.round(time_dist) / 60);        
								minutes =  Math.round(time_dist) % 60; 
								flight_cost += (Math.floor( Math.round(ctime_dist) / 60) * price_per_hour) + (( Math.round(ctime_dist) % 60 / 60) * price_per_hour);
								json_data['base']['distance'] = distance;
								json_data['base']['time'] = time_dist;
								json_data['base']['cost'] = (Math.floor( Math.round(ctime_dist) / 60) * price_per_hour) + (( Math.round(ctime_dist) % 60 / 60) * price_per_hour);
							
								
								total_time_array[moment($('input[name="multi-date['+first_index+']"]').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] += Math.round(time_dist)
								
								time_distance += parseFloat(time_dist);
								urltime_distance += parseFloat(ctime_dist);
								total_distance += parseFloat(distance);
							}
						}

						last_index = 0;
						$( air_departure_lat ).each(function( index,d_latitude ) {
							if(air_departure_lng[index] != undefined && air_arrival_lat[index] != undefined && air_arrival_lng[index] != undefined){
								var air_departure_geo = new google.maps.LatLng(d_latitude, air_departure_lng[index]);
								var air_arrival_geo = new google.maps.LatLng(air_arrival_lat[index], air_arrival_lng[index]);
								distance = parseFloat(calcDistance(air_departure_geo, air_arrival_geo));
								depgt = arrgt = 0;
								if(departure[index] != undefined && gt[departure[index]]){
								depgt = parseFloat(gt[departure[index]]);
								}
								if(arrival[index] != undefined && gt[arrival[index]]){
								arrgt = parseFloat(gt[arrival[index]]);
								}
								
								distance = parseFloat(distance) * 0.539957; 
								
								if(distance >= 100){
									time_dist = depgt + arrgt + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((distance-200)/(speed/60)); 
								}
								else{
									time_dist = depgt + (distance/((speed*speed_coefficient)/60)) + arrgt;
								}
								if(plane_type != 2){
									if(routes_data[plane_list[i].id+'-'+d_latitude+'-'+air_departure_lng[index]] != undefined){
										if(routes_data[plane_list[i].id+'-'+d_latitude+'-'+air_departure_lng[index]].location_2_latitude ==  air_arrival_lat[index] && routes_data[plane_list[i].id+'-'+air_departure_lat[index]+'-'+air_departure_lng[index]].location_2_longitude == air_arrival_lng[index]){
											time_dist = routes_data[plane_list[i].id+'-'+air_departure_lat[index]+'-'+air_departure_lng[index]].time;
											if(routes_data[plane_list[i].id+'-'+air_departure_lat[index]+'-'+air_departure_lng[index]].distance > 0){
												distance = routes_data[plane_list[i].id+'-'+air_departure_lat[index]+'-'+air_departure_lng[index]].distance;
											}
										}
									}
								}
								
								time_dist = Math.round(time_dist);
								json_data[index]['actual_time'] = time_dist;
								var ctime_dist = time_dist;
								if(time_dist < 120){
									if(Math.round(distance) != 0){
										//ctime_dist = 120;
									}
								}
								
								hours = Math.floor(  Math.round(time_dist) / 60);        
								minutes =  Math.round(time_dist) % 60; 
								flight_cost += (Math.floor(  Math.round(ctime_dist) / 60) * price_per_hour) + (( Math.round(ctime_dist) % 60 / 60) * price_per_hour);
								json_data[index]['distance'] = distance;
								json_data[index]['time'] = time_dist;
								json_data[index]['cost'] = (Math.floor(  Math.round(ctime_dist) / 60) * price_per_hour) + (( Math.round(ctime_dist) % 60 / 60) * price_per_hour);
								
								if(index != first_index){
									if(json_data[previous_index]['time'] != undefined){
										var start = moment($('input[name="multi-date['+previous_index+']"]').val(), "DD-MM-YYYY").add(Math.floor(json_data[previous_index]['actual_time']), 'minutes');
										start = moment(start.format("DD-MM-YYYY"), "DD-MM-YYYY");
										var end = moment($('input[name="multi-date['+index+']"]').val(), "DD-MM-YYYY");
										crew_handling_additional_days = additional_days = end.diff(start, "days");
                    total_days += end.diff(start, "days") + 1;
										if(crew_handling_additional_days <= 0){
											crew_handling_additional_days = 1;
										}
										else{
											additional_days--;
										}
										json_data[index]['additional_days'] = additional_days;
										json_data[index]['crew_handling_additional_days'] = crew_handling_additional_days;
										crew_handling_charges += (25000 * crew_handling_additional_days);
										flight_cost += additional_days * price_per_hour * 2;
										
									}
								}
								total_time_array[moment($('input[name="multi-date['+index+']"]').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] += Math.round(time_dist);
								time_distance += parseFloat(time_dist);
								urltime_distance += parseFloat(ctime_dist);
								total_distance += parseFloat(distance);
								last_index = index;
							}
							previous_index = index;
						});
						
						if(plane_list[i].id == 35) {
							/* console.log(air_arrival_lat, air_arrival_lat[last_index] ,avail_planes_lat ,air_arrival_lng[last_index], avail_planes_lng); */
						}
						if(air_arrival_lat[last_index] != undefined && air_arrival_lng[last_index] != undefined){
							
							if(air_arrival_lat[last_index] != avail_planes_lat || air_arrival_lng[last_index] != avail_planes_lng){
								var air_departure_geo = new google.maps.LatLng(air_arrival_lat[last_index], air_arrival_lng[last_index]);
								var air_arrival_geo = new google.maps.LatLng(avail_planes_lat, avail_planes_lng);
								distance = parseFloat(calcDistance(air_departure_geo, air_arrival_geo));
								distance = distance * 0.539957;
								
								depgt = arrgt = 0;
								if(arrival[last_index] != undefined && gt[arrival[last_index]]){
									depgt = parseFloat(gt[arrival[last_index]]);
								}
								if(plane_list[i].airport_id != 0 && gt[plane_list[i].airport_id]){
									arrgt = parseFloat(gt[plane_list[i].airport_id]);
								}
								
								if(distance >= 100){
									time_dist = depgt + arrgt + (100/((speed * speed_coefficient)/60)) + (100/((speed * speed_coefficient)/60)) + ((distance-200)/(speed/60));
								}
								else{
									time_dist = depgt + (distance/((speed*speed_coefficient)/60)) + arrgt;
								}
								
								if(plane_type != 2){
									if(routes_data[plane_list[i].id+'-'+air_arrival_lat[last_index]+'-'+air_arrival_lng[last_index]] != undefined){
										if(routes_data[plane_list[i].id+'-'+air_arrival_lat[last_index]+'-'+air_arrival_lng[last_index]].location_2_latitude ==  avail_planes_lat && routes_data[plane_list[i].id+'-'+air_arrival_lat[last_index]+'-'+air_arrival_lng[last_index]].location_2_longitude == avail_planes_lng){
											time_dist = routes_data[plane_list[i].id+'-'+air_arrival_lat[last_index]+'-'+air_arrival_lng[last_index]].time;
											if(routes_data[plane_list[i].id+'-'+air_arrival_lat[last_index]+'-'+air_arrival_lng[last_index]].distance > 0){
												distance = routes_data[plane_list[i].id+'-'+air_arrival_lat[last_index]+'-'+air_arrival_lng[last_index]].distance;
											}
										}
									}
								}
							
								time_dist = Math.round(time_dist);
								var ctime_dist = time_dist;
								if(time_dist < 120){
									if(Math.round(distance) != 0){
										//ctime_dist = 120;
									}
								}
								
								hours = Math.floor( Math.round(time_dist) / 60);        
								minutes =  Math.round(time_dist) % 60; 
								flight_cost += (Math.floor( Math.round(ctime_dist) / 60) * price_per_hour) + (( Math.round(ctime_dist) % 60 / 60) * price_per_hour);
								
								json_data['arr']['distance'] = distance;
								json_data['arr']['time'] = time_dist;
								json_data['arr']['cost'] = (Math.floor( Math.round(ctime_dist) / 60) * price_per_hour) + (( Math.round(ctime_dist) % 60 / 60) * price_per_hour);
								
								total_time_array[moment($('input[name="multi-date['+last_index+']"]').val(), "DD-MM-YYYY").format("DD-MM-YYYY")] += Math.round(time_dist)
							
								time_distance += parseFloat(time_dist);
								urltime_distance += parseFloat(ctime_dist);
								total_distance += parseFloat(distance);
							}
						}
						
						hours = Math.floor(Math.round(time_distance) / 60);
						minutes =  Math.round(time_distance) % 60; 
						
						urlhours = Math.floor(Math.round(urltime_distance) / 60);
						urlminutes =  Math.round(urltime_distance) % 60; 
						//flight_cost = (Math.floor(hours) * price_per_hour) + ((minutes / 60) * price_per_hour);            

						var total_length = Object.keys(json_data).length
						total_length = (Object.keys(json_data.arr).length == 0) ? total_length-1 : total_length;
						total_length = (Object.keys(json_data.base).length == 0) ? total_length-1 : total_length;
						if(type_id == 2){
							handling_charges = total_length * 15000;
						}
            $.each(total_time_array, function(index, value){
							if(parseFloat(value) < 120){
								additional_time += (120 - value)
							}
						})

            var stay_time = stay_time_hours = stay_time_minutes = 0;
            
            if((total_days * 120) > (minutes + hours *60)){
              stay_time = (total_days * 120) - (minutes + hours *60);
              stay_time_hours = Math.floor(stay_time / 60);
              stay_time_minutes = (stay_time % 60);
              flight_cost += price_per_hour * stay_time / 60;
            }
            
            minutes += stay_time;
						hours = hours + Math.floor( Math.round(minutes) / 60);        
            minutes = Math.round(minutes) % 60;
            flight_cost = (hours * price_per_hour) + (minutes / 60 * price_per_hour);         
						var sub_total = flight_cost + handling_charges + crew_handling_charges;           
          
            var tax_amount = (tax/100) * sub_total;
            
            if(type_id == 3){
              var final_amount = sub_total + 40000;
            } else{
              var final_amount = tax_amount + sub_total;
            }
						
            list += '<h5><label class="fixed-width"><b>Flying Cost</b></label> : <i class="fa fa-rupee"></i>'+flight_cost.toFixed()+' (For '+hours+' Hrs '+minutes.toFixed()+' min.)</h5>';
            list += '<h5><label class="fixed-width"><b>Distance</b></label> : '+total_distance.toFixed(2)+' NM</h5>';
            list += '<h5><label class="fixed-width"><b>Airport Handling Charges</b></label> : <i class="fa fa-rupee"></i>'+handling_charges.toFixed()+'</h5>';
            list += '<h5><label class="fixed-width"><b>Crew Handling Charges</b></label> : <i class="fa fa-rupee"></i>'+crew_handling_charges.toFixed()+'</h5>';
            list += '<h5><label class="fixed-width"><b>Sub Total</b></label> : <i class="fa fa-rupee"></i>'+sub_total.toFixed()+'</h5>';
            
            if(type_id == 3){
              list += '<h5><label class="fixed-width"><b>Fixed Medical Team Cost</b></label> : <i class="fa fa-rupee"></i>'+40000+'</h5>';
            } else{
              list += '<h5><label class="fixed-width"><b>GST (18%)</b></label> : <i class="fa fa-rupee"></i>'+tax_amount.toFixed()+'</h5>';
            }
						/* if(plane_list[i].id == 35){
							console.log(plane_list[i].id,json_data,JSON.stringify(json_data));
						} */
            list += '<h5 class="grand-total" data-id="'+plane_list[i].id+'" data-total="'+final_amount.toFixed()+'"><label class="fixed-width"><b>Grand Total</b></label> : <i class="fa fa-rupee"></i>'+final_amount.toFixed()+'</h5>';
            list += '<div class="col-md-12 text-right">';
            //list += '<a href="/plane/plane?plane-id=&json='+JSON.stringify(json_data) +'" class="btn btn-view">View Details</a>';
            list += '<a href="/plane/plane-multi?plane-id='+plane_list[i].id+'&arrival='+escape('<?php echo json_encode(array_values($multi_arrival)); ?>')+'&departure='+escape('<?php echo json_encode(array_values($multi_departure)); ?>')+'&adults='+escape('<?php echo json_encode(array_values($multi_adults)); ?>')+'&date='+escape('<?php echo json_encode(array_values($multi_date)); ?>')+'&plane-type='+plane_type+'&latitude='+ escape('<?php echo json_encode(array_values($dep_multi_latitude)); ?>') +'&longitude='+ escape('<?php echo json_encode(array_values($dep_multi_longitude)); ?>') +'&trip-type=2&helicopter-departure='+escape('<?php echo json_encode(array_values($helicopter_multi_departure)); ?>')+'&helicopter-arrival='+escape('<?php echo json_encode(array_values($helicopter_multi_arrival)); ?>')+'&speed_coefficient='+speed_coefficient+'&json-data='+escape(JSON.stringify(json_data))+'&ground_handling='+handling_charges+'&flight_cost='+flight_cost+'&total_hours='+urlhours+'&total_minutes='+urlminutes+'&additional-time='+additional_time+'" class="btn btn-view">View Details</a>';
						
						
            list += '</div>';
            list += '</div>';
            list += '</div>';
            list += '</div>';
          }
          $('#plane-list').html(list);
					sortMachines();
				}
			})
		}
		
		
		
		function sortMachines(){
			var div_json = new Array();
			$.each($('.grand-total'), function(index, value){ 	
				div_json.push({id: $(this).data('id'), val: $(this).data('total')});
			});
			
			div_json.sort(function(a,b) {
				return parseFloat(a.val) - parseFloat(b.val);
			});
			
			if($('#price-filter').val() == 1){
				div_json = Object.assign([], div_json).reverse();
			}
			
			var plane_html = '';
			$.each(div_json, function(index, value){
				plane_html += $('#plane-info-div-'+value.id).wrap('<p/>').parent().html();
			});
			
			$('#plane-list').html(plane_html);
				
		}
		
    //calculates distance between two points in km's
    function calcDistance(p1, p2) {
			if(google.maps.geometry == undefined){
				location.reload(true);
			}
      var distance = (google.maps.geometry.spherical.computeDistanceBetween(p1, p2) / 1000);
      return distance;
    }
		
		$("#price-filter").change(function(){
			if($('#trips').val() == 2 ){
				getPlaneListMulti();
			}
			else{ 
				getPlaneList();
			}
		});
  });
</script>

<script>
  $(document).ready(function(){
    $("select.country").change(function(){
        var selectedCountry = $(".country option:selected").val();
        alert("You have selected the country - " + selectedCountry);
    });
    
    if($('#trips option:selected').attr('value')){ 
      var trip_type = $('#trips option:selected').attr('value');
      if(trip_type == 1){
        $("#round-trip").removeClass();
        $("#round-trip").addClass("row");
        $("#btn-search-round").removeClass();
        $("#btn-search-round").addClass("col-md-2");
        $("#btn-search-round").addClass("no-padding");
        $("#btn-search").addClass("hide");
      }
      else{
        $("#round-trip").addClass("hide");
        $("#btn-search-round").addClass("hide");
        $("#btn-search").removeClass();
        $("#btn-search").addClass("col-md-2");
        $("#btn-search").addClass("no-padding");
      }
    }
    
	if($('#departure option:selected').attr('value')){ 
    var departure = $('#departure option:selected').attr('value');
    var dep_id = $("option:selected", '#departure').data('id');
    $('#plane-air-arrival-round').val(dep_id);
    $('#plane-air-arrival-round').select2().val(dep_id).trigger('change');
  }
  
	if($('#plane-air-arrival option:selected').attr('value')){ 
    var arrival = $('#plane-air-arrival option:selected').attr('value');
    var arr_id = $("option:selected", '#plane-air-arrival').data('arr-id');
    $('#plane-air-departure-round').val(arr_id);
    $('#plane-air-departure-round').select2().val(arr_id).trigger('change');
  }
  
});
</script>

// <script>
//     // This example adds a search box to a map, using the Google Place Autocomplete
//     // feature. People can enter geographical searches. The search box will return a
//     // pick list containing a mix of places and predicted search terms.

//     // This example requires the Places library. Include the libraries=places
//     // parameter when you first load the API. For example:
//     // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">  
// 	function initialize(){
// 		var map;
// 		var marker_pick, marker, marker_multi;
// 		var my_lat_lng = new google.maps.LatLng(16.987150,73.308478);
// 		var multi_lat_long = new google.maps.LatLng(16.987150,73.308478);
// 		var geo_coder = new google.maps.Geocoder();
// 		var info_window = new google.maps.InfoWindow();
		
// 		var map_options = {
// 			zoom: 15,
// 			center: my_lat_lng,
// 			map_type_id: google.maps.MapTypeId.ROADMAP
// 		};

// 		var map_pick = new google.maps.Map(document.getElementById("map"), map_options);

// 		// Create the search box and link it to the UI element.
// 		var input = document.getElementById('search-location');
// 		var searchBox = new google.maps.places.SearchBox(input);
// 		map_pick.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
		
// 		marker_pick = new google.maps.Marker({
// 			map: map_pick,
// 			position: my_lat_lng,
// 			draggable: true 
// 		}); 

// 		geo_coder.geocode({'latLng': my_lat_lng }, function(results, status) {
// 			if (status == google.maps.GeocoderStatus.OK) {
// 				if (results[0]) {
// 					$('#latitude,#longitude').show();
// 					$('#address').val(results[0].formatted_address);
// 					$('#latitude').val(marker_pick.getPosition().lat());
// 					$('#longitude').val(marker_pick.getPosition().lng());	
// 					$('#lat').val(marker_pick.getPosition().lat());
// 					$('#long').val(marker_pick.getPosition().lng());									
// 				}
// 			}
// 		});
		
// 		//google autocomplete
// 		var autocomplete_pick = new google.maps.places.Autocomplete(input);
// 		google.maps.event.addListener(autocomplete_pick, 'place_changed', function () 
// 		{
// 			var place = autocomplete_pick.getPlace();
// 			$('#address').val(place.name);
// 			$('#latitude').val(place.geometry.location.lat());
// 			$('#longitude').val(place.geometry.location.lng());
// 			$('#lat').val(place.geometry.location.lat());
// 			$('#long').val(place.geometry.location.lng());
			
			
// 			my_lat_lng = new google.maps.LatLng(place.geometry.location.lat(),place.geometry.location.lng());
			 
// 			map_options = {
// 			zoom: 15,
// 			center: my_lat_lng,
// 			map_type_id: google.maps.MapTypeId.ROADMAP
// 			};
// 			map_pick = new google.maps.Map(document.getElementById("map"), map_options);
// 			map_pick.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
// 			marker_pick = new google.maps.Marker({
// 					map: map_pick,
// 					position: my_lat_lng,
// 					draggable: true 
// 				});
// 				google.maps.event.addListener(marker_pick, 'dragend', function() {
// 				geo_coder.geocode({'latLng': marker_pick.getPosition()}, function(results, status) {
// 					if (status == google.maps.GeocoderStatus.OK) {
// 						if (results[0]) {
// 							$('#address').val(results[0].formatted_address);
// 							$('#latitude').val(marker_pick.getPosition().lat());
// 							$('#longitude').val(marker_pick.getPosition().lng());
// 							$('#lat').val(marker_pick.getPosition().lat());
// 							$('#long').val(marker_pick.getPosition().lng());
// 							$('#search-location').val(results[0].formatted_address);
// 						}
// 					}
// 				});
// 			});
// 		});
// 		//marker moved listener
// 		google.maps.event.addListener(marker_pick, 'dragend', function() {
// 			geo_coder.geocode({'latLng': marker_pick.getPosition()}, function(results, status) {
// 				if (status == google.maps.GeocoderStatus.OK) {
// 					if (results[0]) {
// 						$('#address').val(results[0].formatted_address);
// 						$('#latitude').val(marker_pick.getPosition().lat());
// 						$('#longitude').val(marker_pick.getPosition().lng());
// 						$('#lat').val(marker_pick.getPosition().lat());
// 						$('#long').val(marker_pick.getPosition().lng());
// 						$('#search-location').val(results[0].formatted_address);
// 					}
// 				}
// 			});
// 		});	
		
		
// 		// Map for drop location
// 		map = new google.maps.Map(document.getElementById("map-drop"), map_options);

// 		// Create the search box and link it to the UI element.
// 		var input_drop = document.getElementById('search-location-drop');
// 		var searchBox = new google.maps.places.SearchBox(input_drop);
// 		map.controls[google.maps.ControlPosition.TOP_LEFT].push(input_drop);
		
// 		marker = new google.maps.Marker({
// 			map: map,
// 			position: my_lat_lng,
// 			draggable: true 
// 		}); 

// 		geo_coder.geocode({'latLng': my_lat_lng }, function(results, status) {
// 			if (status == google.maps.GeocoderStatus.OK) {
// 				if (results[0]) {
// 					$('#latitude-drop,#longitude-drop').show();
// 					$('#address-drop').val(results[0].formatted_address);
// 					$('#latitude-drop').val(marker.getPosition().lat());
// 					$('#longitude-drop').val(marker.getPosition().lng());	
// 					$('#lat-drop').val(marker.getPosition().lat());
// 					$('#long-drop').val(marker.getPosition().lng());									
// 				}
// 			}
// 		});
		
// 		//google autocomplete
// 		var autocomplete = new google.maps.places.Autocomplete(input_drop);
// 		google.maps.event.addListener(autocomplete, 'place_changed', function () 
// 		{
// 			var place = autocomplete.getPlace();
// 			$('#address-drop').val(place.name);
// 			$('#latitude-drop').val(place.geometry.location.lat());
// 			$('#longitude-drop').val(place.geometry.location.lng());
// 			$('#lat-drop').val(place.geometry.location.lat());
// 			$('#long-drop').val(place.geometry.location.lng());
			
			
// 			my_lat_lng = new google.maps.LatLng(place.geometry.location.lat(),place.geometry.location.lng());
			 
// 			map_options = {
// 			zoom: 15,
// 			center: my_lat_lng,
// 			map_type_id: google.maps.MapTypeId.ROADMAP
// 			};
// 			map = new google.maps.Map(document.getElementById("map-drop"), map_options);
// 			map.controls[google.maps.ControlPosition.TOP_LEFT].push(input_drop);
// 			marker = new google.maps.Marker({
// 					map: map,
// 					position: my_lat_lng,
// 					draggable: true 
// 				});
// 				google.maps.event.addListener(marker, 'dragend', function() {
// 				geo_coder.geocode({'latLng': marker.getPosition()}, function(results, status) {
// 					if (status == google.maps.GeocoderStatus.OK) {
// 						if (results[0]) {
// 							$('#address-drop').val(results[0].formatted_address);
// 							$('#latitude-drop').val(marker.getPosition().lat());
// 							$('#longitude-drop').val(marker.getPosition().lng());
// 							$('#lat-drop').val(marker.getPosition().lat());
// 							$('#long-drop').val(marker.getPosition().lng());
// 							$('#search-location-drop').val(results[0].formatted_address);
// 						}
// 					}
// 				});
// 			});
// 		});
// 		//marker moved listener
// 		google.maps.event.addListener(marker, 'dragend', function() {
// 			geo_coder.geocode({'latLng': marker.getPosition()}, function(results, status) {
// 				if (status == google.maps.GeocoderStatus.OK) {
// 					if (results[0]) {
// 						$('#address-drop').val(results[0].formatted_address);
// 						$('#latitude-drop').val(marker.getPosition().lat());
// 						$('#longitude-drop').val(marker.getPosition().lng());
// 						$('#lat-drop').val(marker.getPosition().lat());
// 						$('#long-drop').val(marker.getPosition().lng());
// 						$('#search-location-drop').val(results[0].formatted_address);
// 					}
// 				}
// 			});
// 		});	
		
// 		// Map for multi location
		
// 		var map_multi_options = {
// 			zoom: 15,
// 			center: multi_lat_long,
// 			map_type_id: google.maps.MapTypeId.ROADMAP
// 		};
		
// 		var map_multi = new google.maps.Map(document.getElementById("map-multi"), map_multi_options);

// 		// Create the search box and link it to the UI element.
// 		var input_multi = document.getElementById('search-location-multi');
// 		var searchBoxmulti = new google.maps.places.SearchBox(input_multi);
// 		map_multi.controls[google.maps.ControlPosition.TOP_LEFT].push(input_multi);
		
// 		marker_multi = new google.maps.Marker({
// 			map: map_multi,
// 			position: multi_lat_long,
// 			draggable: true 
// 		}); 

// 		geo_coder.geocode({'latLng': multi_lat_long }, function(results, status) {
// 			if (status == google.maps.GeocoderStatus.OK) {
// 				if (results[0]) {
// 					$('#latitude-multi,#longitude-multi').show();
// 					$('#address-multi').val(results[0].formatted_address);
// 					$('#latitude-multi').val(marker_multi.getPosition().lat());
// 					$('#longitude-multi').val(marker_multi.getPosition().lng());	
// 					$('#lat-multi').val(marker_multi.getPosition().lat());
// 					$('#long-multi').val(marker_multi.getPosition().lng());									
// 				}
// 			}
// 		});
		
// 		//google autocomplete
// 		var autocomplete_multi = new google.maps.places.Autocomplete(input_multi);
// 		google.maps.event.addListener(autocomplete_multi, 'place_changed', function () 
// 		{ 
// 			var place = autocomplete_multi.getPlace();
// 			$('#address-multi').val(place.name);
// 			$('#latitude-multi').val(place.geometry.location.lat());
// 			$('#longitude-multi').val(place.geometry.location.lng());
// 			$('#lat-multi').val(place.geometry.location.lat());
// 			$('#long-multi').val(place.geometry.location.lng());
			 
// 			map_multi.setCenter(place.geometry.location);
// 			marker_multi.setPosition(place.geometry.location);
			
// 			/*
// 			multi_lat_long = new google.maps.LatLng(place.geometry.location.lat(),place.geometry.location.lng());
			 
// 			map_multi_options = {
// 				zoom: 15,
// 				center: multi_lat_long,
// 				map_type_id: google.maps.MapTypeId.ROADMAP
// 			};
// 			map_multi = new google.maps.Map(document.getElementById("map-multi"), map_multi_options);
// 			map_multi.controls[google.maps.ControlPosition.TOP_LEFT].push(input_multi);
// 			marker_multi = new google.maps.Marker({
// 					map: map_multi,
// 					position: multi_lat_long,
// 					draggable: true 
// 				});*/
// 				/*
// 				google.maps.event.addListener(marker_multi, 'dragend', function() {
// 				geo_coder.geocode({'latLng': marker_multi.getPosition()}, function(results, status) {
// 					if (status == google.maps.GeocoderStatus.OK) {
// 						if (results[0]) { 
// 							$('#address-multi').val(results[0].formatted_address);
// 							$('#latitude-multi').val(marker_multi.getPosition().lat());
// 							$('#longitude-multi').val(marker_multi.getPosition().lng());
							
// 							$('#lat-multi').val(marker_multi.getPosition().lat());
// 							$('#long-multi').val(marker_multi.getPosition().lng());
// 							$('#search-location-multi').val(results[0].formatted_address);
// 						}
// 					}
// 				});
// 			});
// 			*/
// 		});
// 		//marker moved listener
// 		google.maps.event.addListener(marker_multi, 'dragend', function() { 
// 			geo_coder.geocode({'latLng': marker_multi.getPosition()}, function(results, status) {
// 				if (status == google.maps.GeocoderStatus.OK) {
// 					if (results[0]) {
// 						$('#address-multi').val(results[0].formatted_address);
// 						$('#latitude-multi').val(marker_multi.getPosition().lat());
// 						$('#longitude-multi').val(marker_multi.getPosition().lng());
// 						$('#lat-multi').val(marker_multi.getPosition().lat());
// 						$('#long-multi').val(marker_multi.getPosition().lng());
// 						$('#lat-multi').trigger('change');
// 						$('#search-location-multi').val(results[0].formatted_address);
// 					}
// 				}
// 			});
// 		});	
		
// 		//Lat Long Chnaged event
// 		$('#lat').change(function () {
// 			geocodeLatLng(geo_coder, map_pick, info_window);
// 		});
		
// 		$('#long').change(function () {
// 			geocodeLatLng(geo_coder, map_pick, info_window);
// 		});
		
// 		$('#lat-drop').change(function () {
// 			geocodeLatLngDrop(geo_coder, map, info_window);
// 		});
		
// 		$('#long-drop').change(function () {
// 			geocodeLatLngDrop(geo_coder, map, info_window);
// 		});
		
// 		$('#lat-multi').change(function () {
// 			geocodeLatLngMulti(geo_coder, map_multi, info_window);
// 		});
		
// 		$('#long-multi').change(function () {
// 			geocodeLatLngMulti(geo_coder, map_multi, info_window);
// 		});
     
// 		function geocodeLatLngMulti(geocoder, map_multi, infowindow) {
// 			var lat = document.getElementById('lat-multi').value;
// 			var lon = document.getElementById('long-multi').value;
// 			var latlng = {lat: parseFloat(lat), lng: parseFloat(lon)};
// 			geocoder.geocode({'location': latlng}, function(results, status) {
// 				if (status === 'OK') {
// 					if (results[0]) {
// 						map_multi.setZoom(15);
// 						map_multi.setCenter(latlng);
// 						marker_multi.setPosition(latlng);
// 						infowindow.setContent(results[0].formatted_address);
// 						$("#search-location-multi").val(results[0].formatted_address);
// 						infowindow.open(map_multi, marker_multi);
// 					} else {
// 						window.alert('No results found');
// 					}
// 				} else {
// 					window.alert('Geocoder failed due to: ' + status);
// 				}
// 			});
// 		}  
      
//     function geocodeLatLng(geocoder, map_pick, infowindow) {
//       var lat = document.getElementById('lat').value;
//       var lon = document.getElementById('long').value;
//       var latlng = {lat: parseFloat(lat), lng: parseFloat(lon)};
//       geocoder.geocode({'location': latlng}, function(results, status) {
//         if (status === 'OK') {
//           if (results[0]) {
//             map_pick.setZoom(15);
//             marker_pick.setPosition(latlng);
//             infowindow.setContent(results[0].formatted_address);
//             $("#search-location").val(results[0].formatted_address);
//             infowindow.open(map_pick, marker_pick);
//           } else {
//             window.alert('No results found');
//           }
//         } else {
//           window.alert('Geocoder failed due to: ' + status);
//         }
//       });
//     }
    
//     function geocodeLatLngDrop(geocoder, map, infowindow) {
//       var lat = document.getElementById('lat-drop').value;
//       var lon = document.getElementById('long-drop').value;
//       var latlng = {lat: parseFloat(lat), lng: parseFloat(lon)};
//       geocoder.geocode({'location': latlng}, function(results, status) {
//         if (status === 'OK') {
//           if (results[0]) {
//             map.setZoom(15);
//             marker.setPosition(latlng);
// 						//map.setCenter(latlng);
//             infowindow.setContent(results[0].formatted_address);
//             $("#search-location-drop").val(results[0].formatted_address);
//             infowindow.open(map, marker);
//           } else {
//             window.alert('No results found');
//           }
//         } else {
//           window.alert('Geocoder failed due to: ' + status);
//         }
//       });
//     }
//   }
    
//   </script>

  <!-- Leaflet Libraries -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  let map, marker;

  function setupHeliPickMap() {
    const defaultLatLng = [19.0549990, 72.8692035]; // Mumbai default

    if (map) {
      map.remove(); // Reset if reopened
    }

    map = L.map("map").setView(defaultLatLng, 13);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors"
    }).addTo(map);

    marker = L.marker(defaultLatLng, { draggable: true }).addTo(map);
    updateLatLng(defaultLatLng[0], defaultLatLng[1]);
    reverseGeocode(defaultLatLng[0], defaultLatLng[1]);

    marker.on("dragend", function () {
      const pos = marker.getLatLng();
      updateLatLng(pos.lat, pos.lng);
      reverseGeocode(pos.lat, pos.lng);
    });

    // Search handlers
    const input = document.getElementById("search-location");
    input.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        searchLocation(input.value);
      }
    });

    input.addEventListener("blur", function () {
      const val = input.value;
      if (val && val.length >= 3) searchLocation(val);
    });

    document.getElementById("lat").addEventListener("change", function () {
      const lat = parseFloat(this.value);
      const lng = parseFloat(document.getElementById("long").value);
      if (!isNaN(lat) && !isNaN(lng)) {
        map.setView([lat, lng], 13);
        marker.setLatLng([lat, lng]);
        reverseGeocode(lat, lng);
      }
    });

    document.getElementById("long").addEventListener("change", function () {
      const lng = parseFloat(this.value);
      const lat = parseFloat(document.getElementById("lat").value);
      if (!isNaN(lat) && !isNaN(lng)) {
        map.setView([lat, lng], 13);
        marker.setLatLng([lat, lng]);
        reverseGeocode(lat, lng);
      }
    });
  }

  function updateLatLng(lat, lng) {
    document.getElementById("lat").value = lat;
    document.getElementById("long").value = lng;
    document.getElementById("latitude").value = lat;
    document.getElementById("longitude").value = lng;
  }

  function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
      .then(res => res.json())
      .then(data => {
        if (data.display_name) {
          document.getElementById("search-location").value = data.display_name;
        }
      });
  }

  function searchLocation(query) {
    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1`)
      .then(res => res.json())
      .then(data => {
        if (data && data.length > 0) {
          const lat = parseFloat(data[0].lat);
          const lng = parseFloat(data[0].lon);
          marker.setLatLng([lat, lng]);
          map.setView([lat, lng], 13);
          updateLatLng(lat, lng);
        } else {
          alert("Location not found. Please refine your search.");
        }
      });
  }

  // When modal opens
  $('#helicopter-pick-modal').on('shown.bs.modal', function () {
    setTimeout(setupHeliPickMap, 200);
  });

  // On SELECT button
  document.getElementById("btn-pick").addEventListener("click", function () {
    const lat = document.getElementById("lat").value;
    const lng = document.getElementById("long").value;
    const place = document.getElementById("search-location").value;

    document.getElementById("dep-latitude").value = lat;
    document.getElementById("dep-longitude").value = lng;
    document.getElementById("dep-helicopter").value = place;
    document.getElementById("helicopter-arr-round").value = place;

    $('#helicopter-pick-modal').modal('hide');
  });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  let mapDrop, markerDrop;

  function setupHeliDropMap() {
    const defaultLatLng = [19.0549990, 72.8692035]; // Mumbai

    if (mapDrop) {
      mapDrop.remove();
    }

    mapDrop = L.map("map-drop").setView(defaultLatLng, 13);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors"
    }).addTo(mapDrop);

    markerDrop = L.marker(defaultLatLng, { draggable: true }).addTo(mapDrop);
    updateDropLatLng(defaultLatLng[0], defaultLatLng[1]);
    reverseGeocodeDrop(defaultLatLng[0], defaultLatLng[1]);

    markerDrop.on("dragend", function () {
      const pos = markerDrop.getLatLng();
      updateDropLatLng(pos.lat, pos.lng);
      reverseGeocodeDrop(pos.lat, pos.lng);
    });

    document.getElementById("lat-drop").addEventListener("change", function () {
      const lat = parseFloat(this.value);
      const lng = parseFloat(document.getElementById("long-drop").value);
      if (!isNaN(lat) && !isNaN(lng)) {
        mapDrop.setView([lat, lng], 13);
        markerDrop.setLatLng([lat, lng]);
        reverseGeocodeDrop(lat, lng);
      }
    });

    document.getElementById("long-drop").addEventListener("change", function () {
      const lng = parseFloat(this.value);
      const lat = parseFloat(document.getElementById("lat-drop").value);
      if (!isNaN(lat) && !isNaN(lng)) {
        mapDrop.setView([lat, lng], 13);
        markerDrop.setLatLng([lat, lng]);
        reverseGeocodeDrop(lat, lng);
      }
    });

    // search logic
    const inputDrop = document.getElementById("search-location-drop");
    inputDrop.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        searchDropLocation(inputDrop.value);
      }
    });

    inputDrop.addEventListener("blur", function () {
      const val = inputDrop.value;
      if (val && val.length >= 3) {
        searchDropLocation(val);
      }
    });
  }

  function updateDropLatLng(lat, lng) {
    document.getElementById("lat-drop").value = lat;
    document.getElementById("long-drop").value = lng;
    document.getElementById("latitude-drop").value = lat;
    document.getElementById("longitude-drop").value = lng;
  }

  function reverseGeocodeDrop(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
      .then(res => res.json())
      .then(data => {
        if (data.display_name) {
          document.getElementById("search-location-drop").value = data.display_name;
        }
      });
  }

  function searchDropLocation(query) {
    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1`)
      .then(res => res.json())
      .then(data => {
        if (data && data.length > 0) {
          const lat = parseFloat(data[0].lat);
          const lng = parseFloat(data[0].lon);
          markerDrop.setLatLng([lat, lng]);
          mapDrop.setView([lat, lng], 13);
          updateDropLatLng(lat, lng);
        } else {
          alert("Location not found. Try refining your search.");
        }
      });
  }

  $('#helicopter-drop-modal').on('shown.bs.modal', function () {
    setTimeout(setupHeliDropMap, 200);
  });

  document.getElementById("btn-drop").addEventListener("click", function () {
    const lat = document.getElementById("lat-drop").value;
    const lng = document.getElementById("long-drop").value;
    const place = document.getElementById("search-location-drop").value;

    document.getElementById("arr-latitude").value = lat;
    document.getElementById("arr-longitude").value = lng;
    document.getElementById("dep-latitude-round").value = lat;
    document.getElementById("dep-longitude-round").value = lng;
    document.getElementById("arr-helicopter").value = place;
    document.getElementById("helicopter-dep-round").value = place;

    $('#helicopter-drop-modal').modal('hide');
  });
});
</script>



<script>
document.addEventListener("DOMContentLoaded", function () {
  let mapMulti, markerMulti;

  function setupMultiTripMap() {
    const defaultLatLng = [19.0549990, 72.8692035]; // Mumbai default
    const input = document.getElementById("search-location-multi");

    if (mapMulti) mapMulti.remove(); // destroy old map if open
    mapMulti = L.map("map-multi").setView(defaultLatLng, 13);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors"
    }).addTo(mapMulti);

    markerMulti = L.marker(defaultLatLng, { draggable: true }).addTo(mapMulti);
    updateMultiModalFields(defaultLatLng[0], defaultLatLng[1], "");

    markerMulti.on("dragend", function () {
      const pos = markerMulti.getLatLng();
      updateMultiModalFields(pos.lat, pos.lng, "");
      reverseGeocodeMulti(pos.lat, pos.lng);
    });

    input.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        if (input.value.length >= 3) {
          searchMultiLocation(input.value);
        }
      }
    });

    input.addEventListener("blur", function () {
      if (input.value.length >= 3) {
        searchMultiLocation(input.value);
      }
    });

    document.getElementById("lat-multi").addEventListener("change", function () {
      const lat = parseFloat(this.value);
      const lng = parseFloat(document.getElementById("long-multi").value);
      if (!isNaN(lat) && !isNaN(lng)) {
        markerMulti.setLatLng([lat, lng]);
        mapMulti.setView([lat, lng], 15);
        reverseGeocodeMulti(lat, lng);
      }
    });

    document.getElementById("long-multi").addEventListener("change", function () {
      const lng = parseFloat(this.value);
      const lat = parseFloat(document.getElementById("lat-multi").value);
      if (!isNaN(lat) && !isNaN(lng)) {
        markerMulti.setLatLng([lat, lng]);
        mapMulti.setView([lat, lng], 15);
        reverseGeocodeMulti(lat, lng);
      }
    });

    function syncLeafletSelect2MapText(selector, text, fallbackText) {
      const displayText = (text || fallbackText || '').trim();
      const $select = $(selector);
      if (!$select.length) {
        return;
      }
      let $mapOption = $select.find('option[value="0"]').first();
      if ($mapOption.length) {
        $mapOption.text(displayText);
      } else {
        $select.prepend($('<option>', { value: 0, text: displayText }));
      }
      $select.val(0).trigger('change.select2');
    }

    document.getElementById("btn-multi").addEventListener("click", function () {
  const fieldType = document.getElementById("field-type").value;
  const currentIndex = document.getElementById("current-index").value;
  const lat = document.getElementById("lat-multi").value;
  const lng = document.getElementById("long-multi").value;
  const place = document.getElementById("search-location-multi").value;

  if (fieldType == 1) {
    $("#dep-latitude-" + currentIndex).val(lat);
    $("#dep-longitude-" + currentIndex).val(lng);
    $("#dep-helicopter-multi-" + currentIndex).val(place);
    syncLeafletSelect2MapText("#plane-air-departure-multi-" + currentIndex, place, "Departure");
  } else {
    $("#arr-latitude-" + currentIndex).val(lat);
    $("#arr-longitude-" + currentIndex).val(lng);
    $("#arr-helicopter-multi-" + currentIndex).val(place);
    syncLeafletSelect2MapText("#plane-air-arrival-multi-" + currentIndex, place, "Arrival");

    // Pre-fill next leg's departure
    const nextIndex = parseInt(currentIndex) + 1;
    $("#dep-latitude-" + nextIndex).val(lat);
    $("#dep-longitude-" + nextIndex).val(lng);
    $("#dep-helicopter-multi-" + nextIndex).val(place);
    syncLeafletSelect2MapText("#plane-air-departure-multi-" + nextIndex, place, "Departure");
  }

  $('#multi-trip-pick-modal').modal('hide');
});

  }

  function updateMultiModalFields(lat, lng, displayName) {
    document.getElementById("lat-multi").value = lat;
    document.getElementById("long-multi").value = lng;
    document.getElementById("latitude-multi").value = lat;
    document.getElementById("longitude-multi").value = lng;
    if (displayName) {
      document.getElementById("search-location-multi").value = displayName;
    }
    // document.getElementById("lat-multi").dispatchEvent(new Event("change")); // trigger chain updates
  }

  function reverseGeocodeMulti(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
      .then(res => res.json())
      .then(data => {
        if (data && data.display_name) {
          updateMultiModalFields(lat, lng, data.display_name);
        }
      });
  }

  function searchMultiLocation(query) {
    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1`)
      .then(res => res.json())
      .then(data => {
        if (data && data.length > 0) {
          const lat = parseFloat(data[0].lat);
          const lng = parseFloat(data[0].lon);
          updateMultiModalFields(lat, lng, data[0].display_name);
          markerMulti.setLatLng([lat, lng]);
          mapMulti.setView([lat, lng], 15);
        } else {
          alert("❌ Location not found. Please refine your search.");
        }
      });
  }

  $('#multi-trip-pick-modal').on('shown.bs.modal', function () {
    setTimeout(setupMultiTripMap, 200);
  });
});
</script>
@endsection
