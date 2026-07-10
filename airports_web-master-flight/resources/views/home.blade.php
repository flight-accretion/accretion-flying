@extends('layouts.header')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.3/leaflet.css" />
<link rel="stylesheet" href="https://maps.locationiq.com/v2/libs/leaflet-geocoder/1.9.6/leaflet-geocoder-locationiq.min.css" />
<style>
  .btn-submit {
    margin-top: 30px;
  }
  .home-map-results {
    position: absolute;
    left: 15px;
    right: 15px;
    top: 38px;
    z-index: 1000;
    max-height: 220px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #333;
    display: none;
    text-align: left;
  }
  .home-map-results div {
    padding: 8px 10px;
    cursor: pointer;
    border-bottom: 1px solid #f1f1f1;
  }
  .home-map-results div:hover {
    background: #f5f5f5;
  }
  #trips option:disabled,
  .select2-results__option[aria-disabled="true"] {
    background: #d9d9d9 !important;
    color: #777 !important;
    cursor: not-allowed;
  }
  </style>
  <header>
    <div class="header-content">
      <div class="header-content-inner">
        <form id="search-planes" role="form" method="GET" action="{{ url('/plane/search') }}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
          <div class="row">
            <div class="col-md-2 col-md-offset-1 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="trips" name="trips">
                  <option value="0" @if(old('trips') == 0) selected @endif>Single Trip</option>
                  <option value="1" @if(old('trips') == 1) selected @endif>Round Trip</option>
                  <option value="2" @if(old('trips') == 2) selected @endif>Multi Trip</option>
                </select>          
                <span class="text-danger">{{$errors->first('trips')}}</span>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="planes" name="planes">
                  <option value="0" selected>Machine Type</option>
                  @foreach($plane_types as $plane_type)
                    <option value="{{ $plane_type->id}}" data-id="{{ $plane_type->id }}"  @if(old('planes') == $plane_type->id) selected @endif>{{ $plane_type->name }}</option>
                  @endforeach
                  <option value="flower-shower" data-id="flower-shower" data-flower-shower="1">Flower Shower</option>
                </select>
                <span class="text-danger">{{$errors->first('planes')}}</span>
              </div>
            </div>
          </div>
					<div class="@if(old('trips') == 2) hide @endif"  id="non-multi-trip-div">
						<div class="row">
							<div class="col-md-2 col-md-offset-1 no-padding">
								<div id="plane-air-dep-div" class="form-group">
									<select class="form-control search-planes-element select2" id="plane-air-departure" name="departure">
										<option value="0" class="map-select-option" data-map-option="1">Select from map</option>
										<option value="" selected>Departure</option>
										@foreach($airports as $airport)
											<option value="{{ $airport->id}}" data-id="{{ $airport->id }}" data-lat="{{ $airport->latitude }}" data-long="{{ $airport->longitude }}" @if(old('departure') == $airport->id) selected @endif>{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id] }} @endif</option>
										@endforeach
									</select>
									<span class="text-danger">{{$errors->first('departure')}}</span>
								</div>
								<div id="helicopter-dep-div" class="form-group hide">
									<input type="hidden" id="dep-latitude" name="dep-latitude"  value="{{old('dep-latitude')}}">
									<input type="hidden" id="dep-longitude" name="dep-longitude"  value="{{old('dep-longitude')}}">
									<input type="hidden" id="dep-helicopter" name="helicopter-departure" value="{{ old('helicopter-departure') }}">
									<span class="text-danger">{{$errors->first('helicopter-departure')}}</span>
								</div>
							</div>
							<div class="col-md-2 no-padding">
								<div id="plane-air-arr-div" class="form-group">
									<select class="form-control search-planes-element select2" id="plane-air-arrival" name="arrival">
										<option value="0" class="map-select-option" data-map-option="1">Select from map</option>
										<option value="" selected>Arrival</option>
										@foreach($airports as $airport)
											<option value="{{ $airport->id}}" data-arr-id="{{ $airport->id }}" data-arr-lat="{{ $airport->latitude }}" data-arr-long="{{ $airport->longitude }}"  @if(old('arrival') == $airport->id) selected @endif>{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id] }} @endif</option>
										@endforeach
									</select>
									<span class="text-danger">{{$errors->first('arrival')}}</span>
								</div>
								<div id="helicopter-arr-div" class="form-group hide">
									<input type="hidden" id="arr-latitude" name="arr-latitude"  value="{{old('arr-latitude')}}">
									<input type="hidden" id="arr-longitude" name="arr-longitude"  value="{{old('arr-longitude')}}">
									<input type="hidden" id="arr-helicopter" name="helicopter-arrival" value="{{ old('helicopter-arrival') }}">
									<span class="text-danger">{{$errors->first('helicopter-arrival')}}</span>
								</div>
							</div>
							<div class="col-md-2 col-xs-12 no-padding">
								<div class="form-group">
									<input type="number" class="form-control search-planes-element margin-auto" id="adults" placeholder="Adults" name="adults" value="{{ old('adults') }}" min=1 oninput="validity.valid||(value='');">
									<span class="error-font text-danger">{{ $errors->first('adults')}}</span>
								</div>
							</div>
							<div class="col-md-2 col-xs-12 no-padding">
								<div class="input-group date margin-auto " id="from-date">								
									<input type="text" class="form-control date-time-picker search-planes-element" id="date" name="date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="{{ date('d-m-Y H:m')}}" tabindex="2">
									
								</div>
								<span class="error-font text-danger">{{ $errors->first('date') }}</span>
							</div>
							<div class="col-md-2 col-md-offset-0 col-xs-6 col-xs-offset-3 no-padding @if(old('trips') == 1) hide @endif" id="btn-search-single">
								<button type="submit" class="btn btn-search-plane margin-auto">SEARCH</button>
							</div>
						</div>
						<div class="row @if(old('trips') != 1) hide @endif" id="round-trip">
							<div class="col-md-2 col-md-offset-1 no-padding">
								<div id="plane-air-dep-round-div" class="form-group">
									<select class="form-control search-planes-element select2" id="plane-air-departure-round" name="round-departure" readonly>
										<option value="0" class="map-select-option" data-map-option="1">Select from map</option>
										<option value="" selected>Departure</option>
										@foreach($airports as $airport)
											<option value="{{ $airport->id}}" data-lat-round="{{ $airport->latitude }}" data-long-round="{{ $airport->longitude }}" @if(old('departure') == $airport->id) selected @endif>{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id] }} @endif</option>
										@endforeach
									</select>
									<input type="hidden" id="dep-latitude-round" name="dep-latitude-round"  value="{{old('dep-latitude-round')}}">
									<input type="hidden" id="dep-longitude-round" name="dep-longitude-round"  value="{{old('dep-longitude-round')}}">
									<span class="error-font text-danger">{{ $errors->first('round-departure')}}</span>
								</div>
								<div id="helicopter-dep-round-div" class="form-group hide">
									<input type="hidden" id="helicopter-dep-round" name="round-helocopter-departure" value="{{ old('round-helocopter-departure') ?: 'Select From Map' }}">
									<span class="error-font text-danger">{{ $errors->first('round-helocopter-departure')}}</span>
								</div>
							</div>
							<div class="col-md-2 no-padding">
								<div id="plane-air-arr-round-div" class="form-group">
									<select class="form-control search-planes-element select2" id="plane-air-arrival-round" name="round-arrival" readonly>
										<option value="0" class="map-select-option" data-map-option="1">Select from map</option>
										<option value="" selected>Arrival</option>
										@foreach($airports as $airport)
											<option value="{{ $airport->id}}" data-lat-arr-round="{{ $airport->latitude }}" data-long-arr-round="{{ $airport->longitude }}" @if(old('departure') == $airport->id) selected @endif>{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id] }} @endif</option>
										@endforeach
									</select>
									<span class="error-font text-danger">{{ $errors->first('round-arrival')}}</span>
								</div>
								<div id="helicopter-arr-round-div" class="form-group hide">
									<input type="hidden" id="helicopter-arr-round" name="round-helocopter-arrival" value="{{ old('round-helocopter-arrival') ?: 'Select From Map' }}">
									<span class="error-font text-danger">{{ $errors->first('round-helocopter-arrival')}}</span>
								</div>
							</div>
							<div class="col-md-2 no-padding">
								<div class="form-group">
									<input type="number" class="form-control search-planes-element" id="round-adults" placeholder="Adults" name="round-adults" value="" min=1 oninput="validity.valid||(value='');">
									<span class="error-font text-danger">{{ $errors->first('round-adults')}}</span>
								</div>
							</div>
							<div class="col-md-2 no-padding">
								<div class="input-group date">								
									<input type="text" class="form-control date-picker search-planes-element" id="round-date" name="round-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY"  value="{{ date('d-m-Y H:m')}}" tabindex="2">
								
								</div>
								<span class="error-font text-danger">{{ $errors->first('round-date') }}</span>
							</div>
							<div class="col-md-2 no-padding" id="btn-search-round">
								<button type="submit" class="btn btn-search-plane">SEARCH</button>
							</div>
						</div>
          </div>
          <div class="@if(old('trips') != 2) hide @endif" id="multi-trip-div">
						<div class="multi-trip-div-row">
							<div class="row multi-row-0">
								<div class="col-md-2 col-md-offset-1 no-padding">
									<div id="plane-dep-multi" class="plane-dep-multi form-group">
										<select class="form-control search-planes-element plane-air-departure-multi select2" data-index="0" id="plane-air-departure-multi-0" name="multi-departure[0]">
											<option value="0" class="map-select-option" data-map-option="1">Select from map</option>
											<option value="" selected>Departure</option>
											@foreach($airports as $airport)
												<option value="{{ $airport->id}}" data-id="{{ $airport->id }}" data-lat="{{ $airport->latitude }}" data-long="{{ $airport->longitude }}">{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id] }} @endif</option>
											@endforeach
										</select>
										<span class="error-font text-danger">{{ $errors->first('multi-departure.0') ?: $errors->first('multi-departure') }}</span>
									</div>
									<div id="helicopter-dep-multi" data-type="1" data-index="0" class="helicopter-dep-multi form-group hide">
										<input type="hidden" id="dep-latitude-0" name="dep-multi-latitude[0]">
										<input type="hidden" id="dep-longitude-0" name="dep-multi-longitude[0]">
										<input type="hidden" id="dep-helicopter-multi-0" name="helicopter-multi-departure[0]" value="Select From Map">
										<span class="error-font text-danger">{{ $errors->first('helicopter-multi-departure.0') }}</span>
									</div>
								</div>
								<div class="col-md-2 no-padding">
									<div id="plane-arr-multi" class="plane-arr-multi form-group">
										<select class="form-control search-planes-element select2 plane-air-arrival-multi" data-index="0" id="plane-air-arrival-multi-0" name="multi-arrival[0]">
											<option value="0" class="map-select-option" data-map-option="1">Select from map</option>
											<option value="" selected>Arrival</option>
											@foreach($airports as $airport)
												<option value="{{ $airport->id}}" data-arr-id="{{ $airport->id }}" data-arr-lat="{{ $airport->latitude }}" data-arr-long="{{ $airport->longitude }}" >{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id] }} @endif</option>
											@endforeach
										</select>
										<span class="error-font text-danger">{{ $errors->first('multi-arrival.0') ?: $errors->first('multi-arrival') }}</span>
									</div>
									<div id="helicopter-arr-multi" data-type="2" data-index="0" class="helicopter-arr-multi form-group hide">
										<input type="hidden" id="arr-latitude-0" name="arr-multi-latitude[0]">
										<input type="hidden" id="arr-longitude-0" name="arr-multi-longitude[0]">
										<input type="hidden" id="arr-helicopter-multi-0" name="helicopter-multi-arrival[0]" value="Select From Map">
										<span class="error-font text-danger">{{ $errors->first('helicopter-multi-arrival.0') }}</span>
									</div>
								</div>
								<div class="col-md-2 col-xs-12 no-padding">
									<div class="form-group">
										<input type="number" class="form-control search-planes-element margin-auto" placeholder="Adults" name="multi-adults[0]" value="" min=1 oninput="validity.valid||(value='');">
										<span class="error-font text-danger">{{ $errors->first('multi-adults.0') ?: $errors->first('multi-adults')}}</span>
									</div>
								</div>
								<div class="col-md-2 col-xs-12 no-padding">
									<div class="input-group date margin-auto " >								
										<input type="text" class="form-control date-time-picker search-planes-element" name="multi-date[0]" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="{{ date('d-m-Y H:m')}}" tabindex="2">
										
									</div>
									<span class="error-font text-danger">{{ $errors->first('multi-date.0') ?: $errors->first('multi-date') }}</span>
								</div>
								<div class="col-md-2 col-md-offset-0 col-xs-6 col-xs-offset-3 no-padding">
									<button type="submit" class="btn btn-search-plane margin-auto">SEARCH</button>
								</div>
								<div class="col-md-1 col-md-offset-0 col-xs-6 col-xs-offset-3 no-padding">
									<div class="form-group"></div>
									<a class="btn btn-primary delete-trip" data-index="0"><i class="fa fa-minus"></i></a>
								</div>
							</div>
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

<!-- ========== HELICOPTER PICK MODAL========== -->
<div id="helicopter-pick-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">PICK LOCATION</h4>
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
        <div class="row my-4" >
          <div class="col-md-12 text-center">
            <input id="search-location" class="form-control mb-3" placeholder="Search Box" style="margin-bottom: 10px;margin-left: 0px;width: 100%;">
            <div id="map" style="height: 400px; border: 1px solid #ccc; border-radius: 5px;"></div>
            <input type="hidden" id="address">
          </div>
        </div>
        <div class="row mt-4" style="margin-top: 10px;">
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
<!-- helicopter-drop-modal structure -->
<div id="helicopter-drop-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">DROP LOCATION</h4>
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
        <div class="row my-4">
          <div class="col-md-12 text-center">
            <input id="search-location-drop" class="form-control mb-3" placeholder="Search Box" style="margin-bottom: 10px;margin-left: 0px;width: 100%;">
            <div id="map-drop" style="height: 400px; border: 1px solid #ccc; border-radius: 5px;"></div>
            <input type="hidden" id="address-drop">
          </div>
        </div>
        <div class="row mt-4" style="margin-top: 10px;">
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
<div id="multi-trip-pick-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">PICK LOCATION</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="field-type" value="dep"> <!-- or 'arr' -->
        <input type="hidden" id="current-index" value="1"> <!-- dynamic -->
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
        <div class="row my-4">
          <div class="col-md-12 text-center">
            <input id="search-location-multi" class="form-control mb-3" placeholder="Search Box">
            <div id="map-multi" style="height: 400px; border: 1px solid #ccc; border-radius: 5px;"></div>
            <input type="hidden" id="address-multi">
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-md-4 col-md-offset-4 text-center">
            <button id="btn-multi" class="btn btn-success btn-block">SELECT</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Flower Shower Modal with Leaflet -->
<div id="flower-shower-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">PICK LOCATION</h4>
      </div>
      <form id="search-planes" method="GET" action="{{ url('/plane/search') }}">
        {{-- @csrf --}}
        <input type="hidden" name="flower-shower" value="1" />
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 form-group">
              <label>Latitude</label>
              <input type="text" id="lat-flower-shower" name="lat-flower-shower" class="form-control">
            </div>
            <div class="col-md-6 form-group">
              <label>Longitude</label>
              <input type="text" id="long-flower-shower" name="long-flower-shower" class="form-control">
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 text-center">
              <div class="form-group">
                <input id="search-location-flower-shower" class="form-control mb-2" placeholder="Search Box">
              </div>
              <div id="map-flower-shower" style="height: 400px;"></div>
              <input type="hidden" id="latitude-flower-shower" name="latitude-flower-shower">
              <input type="hidden" id="longitude-flower-shower" name="longitude-flower-shower">
              <input type="hidden" id="location-flower-shower" name="location-flower-shower">
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-4 col-md-offset-4 text-center">
              <div class="btn-submit">
                <button type="submit" class="btn btn-success">SELECT</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

  <script>
    $(function () {
			
      var plane_type = $('#planes option:selected').data('id') || $('#planes').val();
      var previous_plane_type = plane_type;
      var trip_id = $('#trips').val();
      var syncingMapSelectText = false;
      function isHelicopterSearch() {
        return String(plane_type) === '2';
      }

      function isFlowerShowerSelection(value) {
        return String(value) === 'flower-shower';
      }

      function isMapSelectOption($option) {
        return String($option.data('map-option')) === '1';
      }

      function setMapOptionAvailability() {
        var isHelicopter = isHelicopterSearch();
        $('.map-select-option').prop('disabled', !isHelicopter).prop('hidden', !isHelicopter);

        if(!isHelicopter) {
          $('.map-select-option:selected').each(function() {
            $(this).closest('select').val('').trigger('change.select2');
          });
        }

        $('.select2').trigger('change.select2');
      }

      function syncSelect2MapText(selector, text, fallbackText) {
        var $select = $(selector);
        if(!$select.length) {
          return;
        }
        var displayText = $.trim(text || fallbackText || 'Select from map');
        var $mapOption = $select.find('option[data-map-option="1"]').first();
        if($mapOption.length) {
          $mapOption.text(displayText).prop('disabled', false).prop('hidden', false);
        } else {
          $select.prepend($('<option>', { value: 0, text: displayText, class: 'map-select-option' }).attr('data-map-option', '1'));
        }
        syncingMapSelectText = true;
        $select.val('0').trigger('change').trigger('change.select2');
        syncingMapSelectText = false;
        $select.next('.select2-container').find('.select2-selection__rendered').attr('title', displayText).text(displayText);
        $select.next('.select2-container').find('.select2-chosen').text(displayText);
      }

      function resetSearchSelect(selector) {
        var $select = $(selector);
        if(!$select.length) {
          return;
        }

        $select.find('option[data-map-option="1"]').text('Select from map');
        $select.val('').trigger('change.select2');

        var placeholder = $.trim($select.find('option[value=""]').first().text() || '');
        if(placeholder !== '') {
          $select.next('.select2-container').find('.select2-selection__rendered').attr('title', placeholder).text(placeholder);
          $select.next('.select2-container').find('.select2-chosen').text(placeholder);
        }
      }

      function resetMachineDependentSearchState() {
        $('.search-validation-error').remove();
        $('.has-error').removeClass('has-error');
        $('.map-select-option').text('Select from map');

        resetSearchSelect('#plane-air-departure');
        resetSearchSelect('#plane-air-arrival');
        resetSearchSelect('#plane-air-departure-round');
        resetSearchSelect('#plane-air-arrival-round');
        $('.plane-air-departure-multi, .plane-air-arrival-multi').each(function() {
          resetSearchSelect('#' + this.id);
        });

        $('#dep-latitude, #dep-longitude, #arr-latitude, #arr-longitude, #dep-latitude-round, #dep-longitude-round').val('');
        $('#dep-helicopter, #arr-helicopter').val('');
        $('#helicopter-dep-round, #helicopter-arr-round').val('Select From Map');
        $('[id^="dep-latitude-"], [id^="dep-longitude-"], [id^="arr-latitude-"], [id^="arr-longitude-"]').val('');
        $('[id^="dep-helicopter-multi-"], [id^="arr-helicopter-multi-"]').val('Select From Map');
        $('#lat, #long, #lat-drop, #long-drop, #lat-multi, #long-multi, #search-location, #search-location-drop, #search-location-multi, #address, #address-drop, #address-multi').val('');
      }

      function getMapLocationText(searchSelector, addressSelector, lat, lng, fallbackText) {
        var searchText = $.trim($(searchSelector).val() || '');
        var addressText = $.trim($(addressSelector).val() || '');
        var latitude = $.trim(lat || '');
        var longitude = $.trim(lng || '');

        if(!isSearchBlank(searchText)) {
          return searchText;
        }

        if(!isSearchBlank(addressText)) {
          return addressText;
        }

        if(latitude !== '' && longitude !== '') {
          return latitude + ', ' + longitude;
        }

        return fallbackText || 'Select from map';
      }

      function initializeMapSelectionsFromHidden() {
        var depText = $.trim($('#dep-helicopter').val() || '');
        var arrText = $.trim($('#arr-helicopter').val() || '');

        if(!isSearchBlank(depText)) {
          syncSelect2MapText('#plane-air-departure', depText, 'Departure');
          syncSelect2MapText('#plane-air-arrival-round', depText, 'Arrival');
        }

        if(!isSearchBlank(arrText)) {
          syncSelect2MapText('#plane-air-arrival', arrText, 'Arrival');
          syncSelect2MapText('#plane-air-departure-round', arrText, 'Departure');
        }

        $('.plane-air-departure-multi').each(function() {
          var index = $(this).data('index');
          var mapText = $.trim($('#dep-helicopter-multi-' + index).val() || '');
          if(!isSearchBlank(mapText)) {
            syncSelect2MapText('#plane-air-departure-multi-' + index, mapText, 'Departure');
          }
        });

        $('.plane-air-arrival-multi').each(function() {
          var index = $(this).data('index');
          var mapText = $.trim($('#arr-helicopter-multi-' + index).val() || '');
          if(!isSearchBlank(mapText)) {
            syncSelect2MapText('#plane-air-arrival-multi-' + index, mapText, 'Arrival');
          }
        });
      }

      function syncMultiTripMode() {
        if(String(trip_id) === '2') {
          $('.helicopter-arr-multi').addClass("hide");
          $('.helicopter-dep-multi').addClass("hide");
          $('.plane-arr-multi').removeClass('hide');
          $('.plane-dep-multi').removeClass('hide');
        }

        $('#add-more').closest('.row').removeClass('hide');
        $('#multi-trip-div .delete-trip').closest('[class*="col-"]').removeClass('hide');
        $('#multi-trip-div .helicopter-dep-multi input[type="text"], #multi-trip-div .helicopter-arr-multi input[type="text"]').prop('readonly', true);
        setMapOptionAvailability();
      }

      function toggleTripSections(selectedTripId) {
        if(selectedTripId == 0) {
          $('#btn-search-single').removeClass('hide');
          $('#btn-search-round').addClass('hide');
          $('#round-trip').addClass('hide');
          $('#non-multi-trip-div').removeClass('hide');
          $('#multi-trip-div').addClass('hide');
        } else if(selectedTripId == 1) {
          $('#btn-search-single').addClass('hide');
          $('#btn-search-round').removeClass('hide');  
          $('#round-trip').removeClass('hide');
          $('#non-multi-trip-div').removeClass('hide');
          $('#multi-trip-div').addClass('hide');
        } else {
          $('#non-multi-trip-div').addClass('hide');
          $('#multi-trip-div').removeClass('hide');
        }
      }

      function enforceAirAmbulanceTripSelection() {
        var isAirAmbulance = String(plane_type) === '3';
        $('#trips option[value="1"], #trips option[value="2"]').prop('disabled', isAirAmbulance);

        if(isAirAmbulance && String($('#trips').val()) !== '0') {
          trip_id = '0';
          $('#trips').val('0');
          toggleTripSections(trip_id);
        } else {
          trip_id = $('#trips').val();
        }

        $('#trips').trigger('change.select2');
      }

      function isSearchBlank(value) {
        value = $.trim(value || '');
        return value === '' || value.toLowerCase() === 'select from map';
      }

      function hasSearchCoordinate(value) {
        return $.trim(value || '') !== '';
      }

      function addSearchError($field, message, state) {
        state.valid = false;
        if(!state.firstField.length) {
          state.firstField = $field;
        }

        var $group = $field.closest('.form-group');
        if(!$group.length) {
          $group = $field.closest('[class*="col-"]');
        }

        $group.addClass('has-error');
        if($group.find('.search-validation-error').length === 0) {
          $group.append('<span class="text-danger search-validation-error">' + message + '</span>');
        }
      }

      function validateSearchForm($form) {
        if($form.find('input[name="flower-shower"]').length) {
          return true;
        }

        $form.find('.search-validation-error').remove();
        $form.find('.has-error').removeClass('has-error');

        var state = { valid: true, firstField: $() };
        var currentTrip = String($('#trips').val() || '');
        var currentPlane = String($('#planes').val() || $('#planes option:selected').data('id') || '');

        if(currentPlane === '' || currentPlane === '0') {
          addSearchError($('#planes'), 'Select machine type.', state);
        }

        if(currentTrip !== '2') {
          var $departureSelect = $('#plane-air-departure').length ? $('#plane-air-departure') : $('#departure');
          var $arrivalSelect = $('#plane-air-arrival');

          if(currentPlane === '2') {
            if(isSearchBlank($('#dep-helicopter').val()) || !hasSearchCoordinate($('#dep-latitude').val()) || !hasSearchCoordinate($('#dep-longitude').val())) {
              addSearchError($departureSelect, 'Please select departure destination.', state);
            }

            if(isSearchBlank($('#arr-helicopter').val()) || !hasSearchCoordinate($('#arr-latitude').val()) || !hasSearchCoordinate($('#arr-longitude').val())) {
              addSearchError($arrivalSelect, 'Please select arrival destination.', state);
            }
          } else {
            if(!$departureSelect.val() || $departureSelect.val() === '0' || !hasSearchCoordinate($('#dep-latitude').val()) || !hasSearchCoordinate($('#dep-longitude').val())) {
              addSearchError($departureSelect, 'Please select departure airport.', state);
            }

            if(!$arrivalSelect.val() || $arrivalSelect.val() === '0' || !hasSearchCoordinate($('#arr-latitude').val()) || !hasSearchCoordinate($('#arr-longitude').val())) {
              addSearchError($arrivalSelect, 'Please select arrival airport.', state);
            }
          }

          if(!$('#adults').val() || Number($('#adults').val()) < 1) {
            addSearchError($('#adults'), 'Please enter valid no. of adults.', state);
          }

          if(!$('#date').val()) {
            addSearchError($('#date'), 'Please select departure date.', state);
          }

          if(currentTrip === '1') {
            if(!$('#round-date').val()) {
              addSearchError($('#round-date'), 'Please select return date.', state);
            }

            if($('#round-adults').val() !== '' && Number($('#round-adults').val()) < 1) {
              addSearchError($('#round-adults'), 'Please enter valid no. of adults.', state);
            }
          }
        } else {
          var rowCount = 0;
          $('#multi-trip-div .multi-trip-div-row > .row').each(function() {
            rowCount++;
            var $row = $(this);
            var $depSelect = $row.find('.plane-air-departure-multi');
            var $arrSelect = $row.find('.plane-air-arrival-multi');
            var index = $depSelect.data('index');
            var $adults = $row.find('input[name^="multi-adults"]');
            var $date = $row.find('input[name^="multi-date"]');

            if(currentPlane === '2') {
              var $depMap = $('#dep-helicopter-multi-' + index);
              var $arrMap = $('#arr-helicopter-multi-' + index);
              if(isSearchBlank($depMap.val()) || !hasSearchCoordinate($('#dep-latitude-' + index).val()) || !hasSearchCoordinate($('#dep-longitude-' + index).val())) {
                addSearchError($depSelect, 'Please select departure destination.', state);
              }

              if(isSearchBlank($arrMap.val()) || !hasSearchCoordinate($('#arr-latitude-' + index).val()) || !hasSearchCoordinate($('#arr-longitude-' + index).val())) {
                addSearchError($arrSelect, 'Please select arrival destination.', state);
              }
            } else {
              if(!$depSelect.val() || $depSelect.val() === '0' || !hasSearchCoordinate($('#dep-latitude-' + index).val()) || !hasSearchCoordinate($('#dep-longitude-' + index).val())) {
                addSearchError($depSelect, 'Please select departure airport.', state);
              }

              if(!$arrSelect.val() || $arrSelect.val() === '0' || !hasSearchCoordinate($('#arr-latitude-' + index).val()) || !hasSearchCoordinate($('#arr-longitude-' + index).val())) {
                addSearchError($arrSelect, 'Please select arrival airport.', state);
              }
            }

            if(!$adults.val() || Number($adults.val()) < 1) {
              addSearchError($adults, 'Please enter valid no. of adults.', state);
            }

            if(!$date.val()) {
              addSearchError($date, 'Please select departure date.', state);
            }
          });

          if(rowCount === 0) {
            addSearchError($('#add-more'), 'Please add at least one trip.', state);
          }
        }

        if(!state.valid && state.firstField.length) {
          var $scrollTarget = state.firstField.closest('.form-group');
          if(!$scrollTarget.length) {
            $scrollTarget = state.firstField.closest('[class*="col-"]');
          }
          if($scrollTarget.length) {
            $('html, body').animate({ scrollTop: $scrollTarget.offset().top - 100 }, 200);
          }
        }

        return state.valid;
      }

      $('form[action$="/plane/search"]').on('submit', function(e) {
        if(!validateSearchForm($(this))) {
          e.preventDefault();
          return false;
        }
      });

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
				multi_html += '				<option value="0" class="map-select-option" data-map-option="1">Select from map</option>';
				multi_html += '				<option value="" selected>Departure</option>';
				multi_html += '				@foreach($airports as $airport)';
				multi_html += '					<option value="{{ $airport->id}}" data-id="{{ $airport->id }}" data-lat="{{ $airport->latitude }}" data-long="{{ $airport->longitude }}">{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id] }} @endif</option>';
				multi_html += '				@endforeach';
				multi_html += '			</select>';
				multi_html += '		</div>';
				multi_html += '		<div id="helicopter-dep-multi" data-type="1" data-index="'+element_index+'" class="helicopter-dep-multi form-group hide">';
				multi_html += '			<input type="hidden" id="dep-latitude-'+element_index+'" name="dep-multi-latitude['+element_index+']">';
				multi_html += '			<input type="hidden" id="dep-longitude-'+element_index+'" name="dep-multi-longitude['+element_index+']">';
				multi_html += '			<input type="hidden" id="dep-helicopter-multi-'+element_index+'" name="helicopter-multi-departure['+element_index+']" value="Select From Map">';
				multi_html += '		</div>';
				multi_html += '	</div>';
				multi_html += '	<div class="col-md-2 no-padding">';
				multi_html += '		<div id="plane-arr-multi" class="plane-arr-multi form-group">';
				multi_html += '			<select class="form-control  search-planes-element plane-air-arrival-multi select2" data-index="'+element_index+'" id="plane-air-arrival-multi-'+element_index+'" name="multi-arrival['+element_index+']">';
				multi_html += '				<option value="0" class="map-select-option" data-map-option="1">Select from map</option>';
				multi_html += '				<option value="" selected>Arrival</option>';
				multi_html += '				@foreach($airports as $airport)';
				multi_html += '					<option value="{{ $airport->id}}" data-arr-id="{{ $airport->id }}" data-arr-lat="{{ $airport->latitude }}" data-arr-long="{{ $airport->longitude }}"  >{{ $airport->name }}@if(isset($cities[$airport->city_id])),  {{ $cities[$airport->city_id] }} @endif</option>';
				multi_html += '				@endforeach';
				multi_html += '			</select>';
				multi_html += '		</div>';
				multi_html += '		<div id="helicopter-arr-multi" data-type="2" data-index="'+element_index+'" class="helicopter-arr-multi form-group hide">';
				multi_html += '			<input type="hidden" id="arr-latitude-'+element_index+'" name="arr-multi-latitude['+element_index+']">';
				multi_html += '			<input type="hidden" id="arr-longitude-'+element_index+'" name="arr-multi-longitude['+element_index+']">';
				multi_html += '			<input type="hidden" id="arr-helicopter-multi-'+element_index+'" name="helicopter-multi-arrival['+element_index+']" value="Select From Map">';
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
        setMapOptionAvailability();
      
			});
			
			$(document).on('click', ".delete-trip", function() {
				$('.multi-row-'+$(this).data('index')).remove();
				$('.multi-row-'+$('.helicopter-dep-multi').last().data('index')).find('.btn-search-plane').closest('div').removeClass('hide');
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
      
      $("#date-helicopter").datetimepicker({
        format: "dd-mm-yyyy hh:ii",
        autoclose: true,
      });
       
      $("#date-helicopter-round").datetimepicker({
        format: "dd-mm-yyyy hh:ii",
        autoclose: true,
      });
      
      //Initialize Select2 Elements
      $(".select2").select2();
      setMapOptionAvailability();
      if(isHelicopterSearch()) {
        initializeMapSelectionsFromHidden();
      }
        
     
      $('.btn').on('click', function(){
        $('.btn').removeClass('selected');
        $(this).addClass('selected');
      });
      
      $('#trips').change(function(){
        trip_id = $(this).val();
        if(String(plane_type) === '3' && trip_id !== '0') {
          trip_id = '0';
          $(this).val('0').trigger('change.select2');
        }

        // trip_id values
        // 0-Single Trip, 1-Round Trip, 2-Multi Trip
        toggleTripSections(trip_id);
				$('#planes').change();
      });
      
      $('#planes').change(function(){
        var selected_plane_type = $("option:selected", this).data('id') || $(this).val();
        if(isFlowerShowerSelection(selected_plane_type)) {
          $('#flower-shower-modal').modal('show');
          $(this).val(previous_plane_type || '0').trigger('change.select2');
          plane_type = previous_plane_type || '0';
          return;
        }

        var machineTypeChanged = String(selected_plane_type) !== String(previous_plane_type);
        plane_type = selected_plane_type;
        if(machineTypeChanged) {
          resetMachineDependentSearchState();
        }
        previous_plane_type = plane_type;
        enforceAirAmbulanceTripSelection();
        // plane_type values
        // 1-plane, 2-helicopter, 3-air-ambulance
        if(plane_type == 2){
					if(trip_id != 2){
						$("#plane-air-dep-div").removeClass('hide');
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

      function openMultiTripMapModal(fieldType, index) {
        $('#field-type').val(fieldType);
        $('#current-index').val(index);

        if(fieldType == 1) {
          if($('#dep-longitude-' + index).val() != '') {
            $('#long-multi').val($('#dep-longitude-' + index).val());
            $('#lat-multi').val($('#dep-latitude-' + index).val());
          }
        } else {
          if($('#arr-longitude-' + index).val() != '') {
            $('#long-multi').val($('#arr-longitude-' + index).val());
            $('#lat-multi').val($('#arr-latitude-' + index).val());
          }
        }

        $('#long-multi').change();
        $('#multi-trip-pick-modal').modal('show');
      }
      
      $("#plane-air-departure").on('change', function() {
        var selectedOption = $('option:selected', this);
        var latitude = selectedOption.data('lat');
        var longitude = selectedOption.data('long');
        var dep_id = selectedOption.data('id');
        if(isMapSelectOption(selectedOption)) {
          if(syncingMapSelectText) {
            return;
          }

          if(isHelicopterSearch()) {
            $('#helicopter-pick-modal').modal('show');
            return;
          }

          $(this).val('').trigger('change.select2');
          return;
        }
        if (!selectedOption.val() || selectedOption.val() == 0) {
          $("#dep-latitude").val('');
          $("#dep-longitude").val('');
          $("#dep-helicopter").val('');
          return;
        }
        $("#dep-latitude").val(latitude);
        $("#dep-longitude").val(longitude);
        $("#dep-helicopter").val(selectedOption.text().trim());
        $('#plane-air-arrival-round').val(dep_id);
        $('#plane-air-arrival-round').select2().val(dep_id).trigger('change');
      });
      
      $("#plane-air-arrival").on('change', function() {
        var selectedOption = $('option:selected', this);
        var latitude = selectedOption.data('arr-lat');
        var longitude = selectedOption.data('arr-long');
        var arr_id = selectedOption.data('arr-id');
        if(isMapSelectOption(selectedOption)) {
          if(syncingMapSelectText) {
            return;
          }

          if(isHelicopterSearch()) {
            $('#helicopter-drop-modal').modal('show');
            return;
          }

          $(this).val('').trigger('change.select2');
          return;
        }
        if (!selectedOption.val() || selectedOption.val() == 0) {
          $("#arr-latitude").val('');
          $("#arr-longitude").val('');
          $("#arr-helicopter").val('');
          return;
        }
        $("#arr-latitude").val(latitude);
        $("#arr-longitude").val(longitude);
        $("#dep-latitude-round").val(latitude);
        $("#dep-longitude-round").val(longitude);
        $("#arr-helicopter").val(selectedOption.text().trim());
        $('#plane-air-departure-round').val(arr_id);
        $('#plane-air-departure-round').select2().val(arr_id).trigger('change');
      });

      $("#plane-air-departure-round").on('change', function() {
        var selectedOption = $('option:selected', this);
        if(isMapSelectOption(selectedOption)) {
          if(syncingMapSelectText) {
            return;
          }

          if(isHelicopterSearch()) {
            $('#helicopter-drop-modal').modal('show');
            return;
          }

          $(this).val('').trigger('change.select2');
          return;
        }
        if (!selectedOption.val() || selectedOption.val() == 0) {
          $("#helicopter-dep-round").val('Select From Map');
          return;
        }
        $("#dep-latitude-round").val(selectedOption.data('lat-round'));
        $("#dep-longitude-round").val(selectedOption.data('long-round'));
        $("#helicopter-dep-round").val(selectedOption.text().trim());
      });

      $("#plane-air-arrival-round").on('change', function() {
        var selectedOption = $('option:selected', this);
        if(isMapSelectOption(selectedOption)) {
          if(syncingMapSelectText) {
            return;
          }

          if(isHelicopterSearch()) {
            $('#helicopter-pick-modal').modal('show');
            return;
          }

          $(this).val('').trigger('change.select2');
          return;
        }
        if (!selectedOption.val() || selectedOption.val() == 0) {
          $("#helicopter-arr-round").val('Select From Map');
          return;
        }
        $("#helicopter-arr-round").val(selectedOption.text().trim());
      });
			
      $(document).on('change', ".plane-air-departure-multi", function() {
        var selectedOption = $('option:selected', this);
        var latitude = selectedOption.data('lat');
        var longitude = selectedOption.data('long'); 
				var index = $(this).data('index');				
        if(isMapSelectOption(selectedOption)) {
          if(syncingMapSelectText) {
            return;
          }

          if(isHelicopterSearch()) {
            openMultiTripMapModal(1, index);
            return;
          }

          $(this).val('').trigger('change.select2');
          return;
        }
        if(!selectedOption.val() || selectedOption.val() == 0) {
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
        if(isMapSelectOption(selectedOption)) {
          if(syncingMapSelectText) {
            return;
          }

          if(isHelicopterSearch()) {
            openMultiTripMapModal(2, index);
            return;
          }

          $(this).val('').trigger('change.select2');
          return;
        }
        if(!selectedOption.val() || selectedOption.val() == 0) {
          $("#arr-latitude-"+index).val('');
          $("#arr-longitude-"+index).val('');
          $("#arr-helicopter-multi-"+index).val('Select From Map');
          return;
        }
        $("#arr-latitude-"+index).val(latitude);
        $("#arr-longitude-"+index).val(longitude);
        $("#arr-helicopter-multi-"+index).val(selectedOption.text().trim());
      });

      $('#plane-air-departure').on('select2:select', function(e) {
        var selectedOption = $(e.params.data.element);
        if(isMapSelectOption(selectedOption) && isHelicopterSearch()) {
          $('#helicopter-pick-modal').modal('show');
        }
      });

      $('#plane-air-arrival').on('select2:select', function(e) {
        var selectedOption = $(e.params.data.element);
        if(isMapSelectOption(selectedOption) && isHelicopterSearch()) {
          $('#helicopter-drop-modal').modal('show');
        }
      });

      $('#plane-air-departure-round').on('select2:select', function(e) {
        var selectedOption = $(e.params.data.element);
        if(isMapSelectOption(selectedOption) && isHelicopterSearch()) {
          $('#helicopter-drop-modal').modal('show');
        }
      });

      $('#plane-air-arrival-round').on('select2:select', function(e) {
        var selectedOption = $(e.params.data.element);
        if(isMapSelectOption(selectedOption) && isHelicopterSearch()) {
          $('#helicopter-pick-modal').modal('show');
        }
      });

      $(document).on('select2:select', '.plane-air-departure-multi', function(e) {
        var selectedOption = $(e.params.data.element);
        if(isMapSelectOption(selectedOption) && isHelicopterSearch()) {
          openMultiTripMapModal(1, $(this).data('index'));
        }
      });

      $(document).on('select2:select', '.plane-air-arrival-multi', function(e) {
        var selectedOption = $(e.params.data.element);
        if(isMapSelectOption(selectedOption) && isHelicopterSearch()) {
          openMultiTripMapModal(2, $(this).data('index'));
        }
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
      
      $(document).on('click', '.helicopter-dep-multi, .helicopter-arr-multi', function() {
				var readonly = $(this).attr("readonly");
				$('#field-type').val($(this).data('type'))
				$('#current-index').val($(this).data('index'))
        $('#multi-trip-pick-modal').modal('show');
				if($(this).data('type') == 1){
					if($('#dep-longitude-'+$(this).data('index')).val() != ''){
						$('#long-multi').val($('#dep-longitude-'+$(this).data('index')).val());
						$('#lat-multi').val($('#dep-latitude-'+$(this).data('index')).val());
					}
				}
				else{
					if($('#arr-longitude-'+$(this).data('index')).val() != ''){
						$('#long-multi').val($('#arr-longitude-'+$(this).data('index')).val());
						$('#lat-multi').val($('#arr-latitude-'+$(this).data('index')).val());
					}
				}
				$('#long-multi').change()
      }); 
      
      $("#btn-multi").click(function(e){
        e.preventDefault();
        var field_type = $('#field-type').val();
        var current_index = $('#current-index').val();
				
        var pick_lat = $("#lat-multi").val();
        var pick_long = $("#long-multi").val();
        var searchbox = getMapLocationText('#search-location-multi', '#address-multi', pick_lat, pick_long, 'Select From Map');
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
        var searchbox = getMapLocationText('#search-location', '#address', pick_lat, pick_long, 'Select From Map');
        $("#dep-latitude").val(pick_lat);
        $("#dep-longitude").val(pick_long);
        $("#dep-helicopter").val(searchbox);
        syncSelect2MapText("#plane-air-departure", searchbox, "Departure");
        syncSelect2MapText("#plane-air-arrival-round", searchbox, "Arrival");
        $("#helicopter-arr-round").val(searchbox);
        $('#helicopter-pick-modal').modal('hide');
      });
      
      $("#btn-drop").click(function(e){
        e.preventDefault();
        var drop_lat = $("#lat-drop").val();
        var drop_long = $("#long-drop").val();
        var searchboxdrop = getMapLocationText('#search-location-drop', '#address-drop', drop_lat, drop_long, 'Select From Map');
        $("#arr-latitude").val(drop_lat);
        $("#arr-longitude").val(drop_long);
        $("#dep-latitude-round").val(drop_lat);
        $("#dep-longitude-round").val(drop_long);
        $("#arr-helicopter").val(searchboxdrop);
        syncSelect2MapText("#plane-air-arrival", searchboxdrop, "Arrival");
        syncSelect2MapText("#plane-air-departure-round", searchboxdrop, "Departure");
        $("#helicopter-dep-round").val(searchboxdrop);
        $('#helicopter-drop-modal').modal('hide');
      });

      $('#flower-shower-modal form').on('submit', function() {
        var flowerLat = $('#lat-flower-shower').val();
        var flowerLong = $('#long-flower-shower').val();
        var flowerLocation = getMapLocationText('#search-location-flower-shower', '#location-flower-shower', flowerLat, flowerLong, 'Selected Location');
        $('#location-flower-shower').val(flowerLocation);
      });
      
      $("select.country").change(function(){
          var selectedCountry = $(".country option:selected").val();
          alert("You have selected the country - " + selectedCountry);
      });
      $('#planes').change();
      
      
      $('#plane-air-departure').on('change', function() {
        var dept = $(this).val();
        if(plane_type != 2){
          $('#plane-air-arrival-round').select2().val(dept).trigger('change');
        }
      });
      
      $('#plane-air-arrival').on('change', function() {
        var dept = $(this).val();
        if(plane_type != 2){
          $('#plane-air-departure-round').select2().val(dept).trigger('change');
        }
      }); 
      
    });
    
  </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.3/leaflet.js"></script>
<script src="https://maps.locationiq.com/v2/libs/leaflet-geocoder/1.9.6/leaflet-geocoder-locationiq.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  if (typeof L === "undefined") {
    return;
  }

  const locationIqKey = @json(env('LOCATIONIQ_API_KEY', ''));
  const mapState = {};

  const modalMaps = [
    {
      modalId: "helicopter-pick-modal",
      mapId: "map",
      latId: "lat",
      lngId: "long",
      searchId: "search-location",
      addressId: "address",
      zoom: 13
    },
    {
      modalId: "helicopter-drop-modal",
      mapId: "map-drop",
      latId: "lat-drop",
      lngId: "long-drop",
      searchId: "search-location-drop",
      addressId: "address-drop",
      zoom: 13
    },
    {
      modalId: "multi-trip-pick-modal",
      mapId: "map-multi",
      latId: "lat-multi",
      lngId: "long-multi",
      searchId: "search-location-multi",
      addressId: "address-multi",
      zoom: 13
    },
    {
      modalId: "flower-shower-modal",
      mapId: "map-flower-shower",
      latId: "lat-flower-shower",
      lngId: "long-flower-shower",
      searchId: "search-location-flower-shower",
      addressId: "location-flower-shower",
      secondaryLatId: "latitude-flower-shower",
      secondaryLngId: "longitude-flower-shower",
      zoom: 2
    }
  ];

  function getNumber(value, fallback) {
    const parsed = parseFloat(value);
    return isNaN(parsed) ? fallback : parsed;
  }

  function searchUrl(query) {
    if (locationIqKey) {
      return "https://us1.locationiq.com/v1/autocomplete.php?key=" + encodeURIComponent(locationIqKey) + "&q=" + encodeURIComponent(query) + "&format=json";
    }

    return "https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=5&q=" + encodeURIComponent(query);
  }

  function addTileLayer(map) {
    if (locationIqKey) {
      L.tileLayer("https://{s}-tiles.locationiq.com/v2/obk/r/{z}/{x}/{y}.png?key=" + locationIqKey, {
        attribution: "&copy; OpenStreetMap contributors; Geocoding by LocationIQ"
      }).addTo(map);
      return;
    }

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors"
    }).addTo(map);
  }

  function updateText(config, text) {
    const searchInput = document.getElementById(config.searchId);
    const addressInput = config.addressId ? document.getElementById(config.addressId) : null;

    if (searchInput) {
      searchInput.value = text || "";
    }

    if (addressInput) {
      addressInput.value = text || "";
    }
  }

  function updateFields(config, lat, lng) {
    document.getElementById(config.latId).value = lat;
    document.getElementById(config.lngId).value = lng;

    if (config.secondaryLatId && document.getElementById(config.secondaryLatId)) {
      document.getElementById(config.secondaryLatId).value = lat;
    }

    if (config.secondaryLngId && document.getElementById(config.secondaryLngId)) {
      document.getElementById(config.secondaryLngId).value = lng;
    }
  }

  function reverseGeocode(config, lat, lng) {
    fetch("https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=" + encodeURIComponent(lat) + "&lon=" + encodeURIComponent(lng))
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        if (data.display_name) {
          updateText(config, data.display_name);
        }
      })
      .catch(function () {});
  }

  function hideResults(resultsBox) {
    resultsBox.innerHTML = "";
    resultsBox.style.display = "none";
  }

  function setMapPosition(config, lat, lng, label) {
    const state = mapState[config.mapId];
    if (!state || isNaN(lat) || isNaN(lng)) {
      return;
    }

    state.map.setView([lat, lng], 15);
    state.marker.setLatLng([lat, lng]);
    state.initializedFromInput = true;
    updateFields(config, lat, lng);

    if (label) {
      updateText(config, label);
    }

    hideResults(state.resultsBox);
  }

  function searchLocations(config, chooseFirst) {
    const state = mapState[config.mapId];
    const input = document.getElementById(config.searchId);
    const query = input ? input.value.trim() : "";

    if (!state || query.length < 3) {
      if (state) hideResults(state.resultsBox);
      return;
    }

    fetch(searchUrl(query))
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        const results = Array.isArray(data) ? data : [];
        const resultsBox = state.resultsBox;
        resultsBox.innerHTML = "";

        if (results.length === 0) {
          hideResults(resultsBox);
          return;
        }

        if (chooseFirst) {
          const item = results[0];
          setMapPosition(config, parseFloat(item.lat), parseFloat(item.lon), item.display_name || item.name || "");
          return;
        }

        results.forEach(function (item) {
          const row = document.createElement("div");
          row.textContent = item.display_name || item.name || "Unknown location";
          row.addEventListener("click", function () {
            setMapPosition(config, parseFloat(item.lat), parseFloat(item.lon), row.textContent);
          });
          resultsBox.appendChild(row);
        });

        resultsBox.style.display = "block";
      })
      .catch(function () {
        hideResults(state.resultsBox);
      });
  }

  function refreshFromInputs(config) {
    const state = mapState[config.mapId];
    const latInput = document.getElementById(config.latId);
    const lngInput = document.getElementById(config.lngId);
    const lat = getNumber(latInput.value, 19.0760);
    const lng = getNumber(lngInput.value, 72.8777);
    const hasInput = latInput.value !== "" && lngInput.value !== "";

    state.map.invalidateSize();
    state.map.setView([lat, lng], hasInput ? 15 : config.zoom);
    state.marker.setLatLng([lat, lng]);
    state.initializedFromInput = hasInput;
  }

  function initMap(config) {
    const mapElement = document.getElementById(config.mapId);
    const latInput = document.getElementById(config.latId);
    const lngInput = document.getElementById(config.lngId);
    const searchInput = document.getElementById(config.searchId);

    if (!mapElement || !latInput || !lngInput || !searchInput) {
      return;
    }

    if (mapState[config.mapId]) {
      setTimeout(function () {
        refreshFromInputs(config);
      }, 200);
      return;
    }

    const mapParent = searchInput.parentElement;
    if (mapParent && window.getComputedStyle(mapParent).position === "static") {
      mapParent.style.position = "relative";
    }

    const initialLat = getNumber(latInput.value, 19.0760);
    const initialLng = getNumber(lngInput.value, 72.8777);
    const initializedFromInput = latInput.value !== "" && lngInput.value !== "";
    const map = L.map(config.mapId).setView([initialLat, initialLng], initializedFromInput ? 15 : config.zoom);
    addTileLayer(map);

    const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);
    const resultsBox = document.createElement("div");
    resultsBox.className = "home-map-results";
    searchInput.parentElement.appendChild(resultsBox);

    mapState[config.mapId] = {
      map: map,
      marker: marker,
      resultsBox: resultsBox,
      initializedFromInput: initializedFromInput
    };

    marker.on("dragend", function () {
      const position = marker.getLatLng();
      updateFields(config, position.lat, position.lng);
      reverseGeocode(config, position.lat, position.lng);
    });

    map.on("click", function (event) {
      const position = event.latlng;
      marker.setLatLng(position);
      updateFields(config, position.lat, position.lng);
      reverseGeocode(config, position.lat, position.lng);
    });

    [latInput, lngInput].forEach(function (input) {
      input.addEventListener("change", function () {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);

        if (!isNaN(lat) && !isNaN(lng)) {
          setMapPosition(config, lat, lng);
          reverseGeocode(config, lat, lng);
        }
      });
    });

    let debounceTimer;
    searchInput.addEventListener("input", function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () {
        searchLocations(config, false);
      }, 250);
    });

    searchInput.addEventListener("keydown", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();
        searchLocations(config, true);
      }
    });

    document.addEventListener("click", function (event) {
      if (!searchInput.parentElement.contains(event.target)) {
        hideResults(resultsBox);
      }
    });

    setTimeout(function () {
      map.invalidateSize();
      reverseGeocode(config, initialLat, initialLng);
    }, 200);
  }

  modalMaps.forEach(function (config) {
    $("#" + config.modalId).on("shown.bs.modal", function () {
      initMap(config);
    });
  });
});
</script>

@endsection
