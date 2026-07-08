@extends('layouts.admin_header')
@section('content')
  <section class="content plane-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Add Machine</h3>
          </div>            
          <form id="form-add-plane" role="form" method="POST" action="{{ url('/plane/add') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="plane">Machine Name</label>
                  <input type="text" class="form-control" id="plane" placeholder="Enter Machine name" name="plane" value="{{ old('plane') }}">
                  <span class="error-font text-danger">{{ $errors->first('plane')}}</span>
                </div>
               <div class="form-group col-md-4">
                  <label for="Call_Sign">Call Sign(VT)</label>
                  <input type="text" class="form-control" id="Call_Sign" placeholder="Enter Call Sign" name="Call_Sign" value="{{ old('Call_Sign') }}" required>
                  <span class="error-font text-danger">{{ $errors->first('Call_Sign')}}</span>
                </div>

                <div class="form-group col-md-4">
                  <label for="type">Type</label>
                   <select id="type" class="form-control select2" name="type">
                      @foreach($plane_types as $plane_type)
                        <option value="{{ $plane_type->id }}" <?php if(old('type') == $plane_type->id ) {echo "selected";}?>>{{ $plane_type->name }}</option>
                      @endforeach
                   </select>
                   <span class="error-font text-danger">{{ $errors->first('type')}}</span>
                </div>
              
              </div>   
              <div class="row">
                  <div class="form-group col-md-4">
                  <label for="subtype">SubType</label>
                   <select id="subtype" class="form-control select2" name="subtype">
                      <option value="0">Select SubType</option>
                      @foreach($plane_subtypes as $plane_subtype)
                        <option value="{{ $plane_subtype->id }}" <?php if(old('subtype') == $plane_subtype->id ) {echo "selected";}?>>{{ $plane_subtype->sub_type }}</option>
                      @endforeach
                   </select>
                   <span class="error-font text-danger">{{ $errors->first('subtype')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="speed_coefficient">Take Off / Landing Speed Coefficient</label>
                  <input type="text" class="form-control" id="speed_coefficient" placeholder="Enter Machine name" name="speed_coefficient" value="0.65">
                  <span class="error-font text-danger">{{ $errors->first('speed_coefficient')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="price">Price Per Hour</label>
                  <input type="text" class="form-control" id="price" placeholder="Enter Machine price" name="price" value="{{ old('price') }}">
                  <span class="error-font text-danger">{{ $errors->first('price')}}</span>
                </div>
                
              </div> 
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="seats">Seats</label>
                  <input type="text" class="form-control" id="seats" placeholder="Enter number of seats" name="seats" value="{{ old('seats') }}">
                  <span class="error-font text-danger">{{ $errors->first('seats')}}</span>
                </div>
                <div class="form-group col-md-4">
                 <label for="speed">Speed Per Hour</label>
                  <input type="text" class="form-control" id="speed" placeholder="Enter Machine speed" name="speed" value="{{ old('speed') }}">
                  <span class="error-font text-danger">{{ $errors->first('speed')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label>Note</label>
                  <textarea name="note" class="form-control" rows="2" placeholder="Enter description in short">{{ old('note') }}</textarea>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="lavatory">Lavatory</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="lavatory" value="0"> &nbsp;&nbsp;Yes
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="lavatory" value="1"> &nbsp;&nbsp;No
                    </label>
                  </div>
                </div>
                <div class="form-group col-md-4 flower-shower">
                  <label for="flower-shower">Flower Shower ?</label><br/>
                  <input type="checkbox" id="flower-shower" name="flower-shower" value="1"> 
                </div>
              </div>    
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h5 class="box-title">Owner Details</h5>
                    </div>
                    <div class="box-body">
                      <div class="row form-group">
                        <div class="form-group col-md-4">
                          <label for="owner">Owner</label>
                         
                            <input type="text" id="owner" name="owner" class="form-control" value="{{ old('owner') }}" tabindex="5" min="0" required>
                            <input type="hidden" id="owner-id" name="owner-id">
                           <span class="error-font text-danger">{{ $errors->first('owner')}}</span>
                        </div>
                        <div class="form-group col-md-4">
                          <label for="owner">Contact</label>
                           <input type="text" class="form-control" id="contact" name="contact" value="{{ old('contact') }}">
                           <span class="error-font text-danger contact-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                          <label for="owner">Email</label>
                           <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}">
                        </div>
                      </div> 
                      <div class="row form-group">
                        <div class="col-md-12">
                          <div class="box box-danger">
                            <div class="box-header with-border">
                              <h5 class="box-title">Secondary Contact Details</h5>
                            </div>
                          </div>
                          <div class="box-body secondary-contacts">
                              <div class="row form-group">
                                <div class="form-group col-md-4">
                                  <label for="owner">Name</label>
                                  <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="number">Contact</label>
                                  <input type="text" class="form-control" id="number" name="number" value="{{ old('number') }}">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="email-id">Email</label>
                                  <input type="text" class="form-control" id="email-id" name="email-id" value="{{ old('email-id') }}">
                                </div>
                            </div> 
                          </div> 
                        </div> 
                      </div> 
                    </div> 
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h5 class="box-title">Location Details</h5>
                    </div>
                    <div class="box-body">
                      <div class="row form-group">
                        <div class="form-group col-md-6">
                          <label for="city">City</label>
                           <select id="city" class="form-control select2" name="city">
                              @foreach($cities as $city)
                                <option value="{{ $city->id }}" <?php if(old('city') == $city->id ) {echo "selected";}?>>{{ $city->name }}</option>
                              @endforeach
                           </select>
                          <span id="city-error" class="error-font text-danger">{!! $errors->first('city') !!}</span>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="city">Airport</label>
                          <select class="form-control" id="airport" name="airport">
                            @foreach($airports as $airport)
                              <option data-id="{{ $airport->id }}" value="{{$airport->id}}"  @if(old('airport', Auth::user()->airport_id) == $airport->id) selected
                              @endif>{{$airport->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <!-- <div class="row form-group">
                        <div class="col-md-6">
                          <label for="latitude">Latitude</label>
                          <input type="text" class="form-control" id="lat" name="lat" value="{{ old('latitude') }}" readOnly>
                        </div>               
                        <div class="col-md-6">
                          <label for="longitude">Longitude</label>
                          <input type="text" class="form-control" id="long" name="long" value="{{ old('longitude') }}" readOnly>
                        </div>                
                       
                      </div>  
                      <div class="row mt-3">
                        <div class="col-md-12">
                          <label>Pick Location</label>
                          <input id="search-location" class="form-control mb-2" type="text" placeholder="Search Location">
                          <div id="map" style="height: 400px; margin: 10px 0; border: 1px solid #ccc;"></div>
                          <input id="latitude" name="latitude" type="hidden">
                          <input id="longitude" name="longitude" type="hidden">
                        </div>
                      </div>              -->
                    @include('partials.location_map', [
                'latInputId' => 'lat',
                'lngInputId' => 'long',
                'mapId' => 'map',
                'latInputName' => 'lat',
                'lngInputName' => 'long',
                'latInputValue' => old('lat', old('latitude')),
                'lngInputValue' => old('long', old('longitude'))
              ])
                    </div>               
                  </div>               
                </div> 
              </div>  
              <div class="row helicopter hide">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h5 class="box-title">Helicopter Temporary Location Details</h5>
                    </div>
                    <div class="box-body">
                      <div class="row form-group">
                        <div class="col-md-4 hide">
                          <label for="gt">Ground Time</label>
                          <input type="text" class="form-control" id="gt" name="gt" value="{{ old('gt') }}">
                        </div> 
                        <div class="col-md-6">
                          <label for="description">From Date *</label>
                          <div class="input-group date" id="fromdate">								
                            <input type="text" class="form-control from-date" id="from-date" name="from-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY">
                            <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                          </div>
                          <span class="error-font text-danger">{{ $errors->first('from-date') }}</span>
                        </div>  
                        <div class="col-md-6">
                          <label for="description">To Date *</label>
                          <div class="input-group date" id="todate">								
                            <input type="text" class="form-control to-date" id="to-date" name="to-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY">
                            <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                          </div>
                          <span class="error-font text-danger">{{ $errors->first('to-date') }}</span>
                        </div>
                      </div> 
                      <div class="row form-group">
                        <div class="col-md-6">
                          <label for="latitude-hel">Latitude</label>
                          <input type="text" class="form-control" id="lat-hel" name="lat-hel" value="{{ old('latitude-hel') }}">
                        </div>               
                        <div class="col-md-6">
                          <label for="longitude-hel">Longitude</label>
                          <input type="text" class="form-control" id="long-hel" name="long-hel" value="{{ old('longitude-hel') }}">
                        </div>                
                      </div> 
                      <div class="row form-group">
                        <div class="form-group col-md-6">
                          <label for="city-hel">City</label>
                           <select id="city-hel" class="form-control select2" style="width:100%" name="city-hel">
                              @foreach($cities as $city)
                                <option value="{{ $city->id }}" <?php if(old('city-hel') == $city->id ) {echo "selected";}?>>{{ $city->name }}</option>
                              @endforeach
                           </select>
                          <span id="city-hel-error" class="error-font text-danger">{!! $errors->first('city-hel') !!}</span>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="airport-hel">Airport</label>
                          <select class="form-control" id="airport-hel" name="airport-hel">
                            @foreach($airports as $airport)
                              <option data-id="{{ $airport->id }}" value="{{$airport->id}}"  @if(old('airport-hel', Auth::user()->airport_id) == $airport->id) selected
                              @endif>{{$airport->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="row form-group">
                        <div class="col-md-12 text-center">	
                          <input id="search-location-hel" class="controls" type="text" placeholder="Search Box">
                          <div id="map-hel"></div>
                          <input id="latitude-hel" name="latitude-hel" type="hidden">
                          <input id="longitude-hel" name="longitude-hel" type="hidden">
                        </div> 
                      </div>	             
                    </div>               
                  </div>               
                </div> 
              </div>               
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h3 class="box-title">Display Image &nbsp;<span class="text-danger">(Recommended size: 1171 x 662 px or same aspect ratio)</span></h3>
                    </div>
                    <div class="box-body">					
                      <div class="row">		
                        <div class="col-md-6 col-md-offset-3">							
                          <div class="col-md-12">								
                            <div class="form-group property-image-container text-center" id="display-image">
                              <label id="image-box-1" class="button image-box">
                                <span><?php if(old('display-image')) { echo '<img src="/uploads/'. old("display-image").'" class="" height="100%"/>';}else{echo'<i class="fa fa-picture-o fa-4x fa-picture-set"></i>';}?></span> 
                                <input class="fileupload" type="file" name="files[]" data-id="1" data-url="/plane/upload">
                                <input id="image-1" type="hidden" name="display-image" value="{{ old('display-image') }}">
                                <div id="progress-1">
                                  <div class="bar" style="width: 0%;"></div>
                                </div>
                              </label>
                              <div>
                                <span class="error-font text-danger">{{ $errors->first('display-image')}}</span>
                              </div>
                            </div>
                          </div>
                        </div> 
                      </div>	
                    </div>              
                  </div>               
                </div> 
              </div>             
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h3 class="box-title">Machine Images &nbsp;<span class="text-danger">(Recommended size: 445 x 350 px or same aspect ratio)</span></h3>
                    </div>
                    <div class="box-body">	
                      <div class="row">
                        @for($i=2; $i<6; $i++)
                        <div class="form-group col-md-3">
                          <div class="form-group" id="images">
                            <label id="image-box-{{$i}}" class="button image-box"><span><i class="fa fa-picture-o fa-4x fa-picture-set"></i></span> 
                              <input class="fileupload" type="file" name="files[]" data-id="{{$i}}" data-url="/plane/upload">
                              <input id="image-{{$i}}" type="hidden" name="images[]" value="{{ old('images.' . ($i - 2)) }}">
                              <div id="progress-{{$i}}">
                                <div class="bar" style="width: 0%;"></div>
                              </div>
                            </label>
                          </div>
                        </div> 
                      @endfor
                      </div>																 
                    </div>             
                  </div>               
                </div> 
              </div>
            </div>           
            <div class="box-footer text-center">
              <button type="submit" class="btn btn-primary btn-fixed">Submit</button>
              <a href="/" type="submit" class="btn btn-primary btn-back-fixed">Back</a>
            </div>
          </form>  
        </div>
      </div>
    </div>
  </section>
  <script type="application/json" id="airports-json">{!! $airports->toJson() !!}</script>
  <script type="application/json" id="owners-json">{!! $owners->toJson() !!}</script>


  <script>
    document.addEventListener("DOMContentLoaded", function () {
  // Initialize Select2
  $(".select2").select2();

  // Initialize Datepickers
  $("#to-date, #from-date").datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    setDate: -1
  });

  // Initialize iCheck
  $('input[type="checkbox"], input[type="radio"]').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'iradio_square-blue',
    increaseArea: '20%'
  });

  // Plane type toggle
  $('#type').change(function () {
    const plane_type = $(this).val();

    if (plane_type == 2) {
      $(".helicopter").removeClass("hide");
      $("#lat, #long").attr("readonly", false);
      $("#speed_coefficient").val('0').attr("readonly", true);
    } else {
      $(".helicopter").addClass("hide");
      $("#lat, #long").attr("readonly", true);
      $("#speed_coefficient").val('0.65').attr("readonly", false);
    }

    if (plane_type == 3) {
      $(".flower-shower").addClass("hide");
    } else {
      $(".flower-shower").removeClass("hide");
    }
  }).trigger("change");

  function normalizeAirportResponse(data) {
    if (Array.isArray(data)) {
      return data;
    }

    if (data && Array.isArray(data.data)) {
      return data.data;
    }

    if (data && typeof data === 'object') {
      return Object.keys(data).map(function (key) {
        return data[key];
      });
    }

    return [];
  }

  const selectedMainAirportId = "{{ old('airport', Auth::user()->airport_id) }}";
  const selectedHelAirportId = "{{ old('airport-hel', Auth::user()->airport_id) }}";

  function fillAirportOptions(selector, data, selectedAirportId) {
    const airportList = normalizeAirportResponse(data);
    const $airport = $(selector);

    $airport.html('<option value="0">Please select</option>');

    airportList.forEach(function (airport) {
      if (airport && airport.id) {
        const selected = Number(airport.id) === Number(selectedAirportId) ? ' selected' : '';
        $airport.append('<option value="' + airport.id + '"' + selected + '>' + airport.name + '</option>');
      }
    });

    if (selectedAirportId && $airport.find('option[value="' + selectedAirportId + '"]').length) {
      $airport.val(selectedAirportId);
    }

    $airport.trigger('change');
  }

  // City change AJAX for airports
  $('#city').change(function () {
    const city_id = $(this).val();
    if (city_id != 0 && city_id != null) {
      $.ajax({
        url: '/plane/citywise-airports',
        type: 'GET',
        data: 'city-id=' + city_id,
        dataType: 'json',
        success: function (data) {
          fillAirportOptions('#airport', data, selectedMainAirportId);
        },
        error: function () {
          alert("Something went wrong");
        }
      });
    }
  }).trigger("change");

  // City (hel) change
  $('#city-hel').change(function () {
    const cityh_id = $(this).val();
    if (cityh_id != 0 && cityh_id != null) {
      $.ajax({
        url: '/plane/citywise-airports',
        type: 'GET',
        data: 'city-id=' + cityh_id,
        dataType: 'json',
        success: function (data) {
          fillAirportOptions('#airport-hel', data, selectedHelAirportId);
        },
        error: function () {
          alert("Something went wrong");
        }
      });
    }
  });

  // Airport select set lat/long
  const airports = JSON.parse(document.getElementById("airports-json").textContent || '{}');

  $('#airport').change(function () {
    const airport_id = $(this).val();
    if (airports[airport_id]) {
      $("#lat").val(airports[airport_id].latitude);
      $("#long").val(airports[airport_id].longitude);
    }
  }).trigger("change");

  $('#airport-hel').change(function () {
    const airport_id = $(this).val();
    if (airports[airport_id]) {
      $("#lat-hel").val(airports[airport_id].latitude);
      $("#long-hel").val(airports[airport_id].longitude);
    }
  });

  // Owner Autocomplete
  const owners = JSON.parse(document.getElementById("owners-json").textContent || '{}');

  $('#owner').autocomplete({
    source: function (request, response) {
      $.ajax({
        url: '/owner/all-owners',
        type: 'GET',
        dataType: 'json',
        data: {
          name_start_with: request.term
        },
        success: function (data) {
          if (typeof data === 'string') {
            try {
              data = JSON.parse(data);
            } catch (error) {
              data = {};
            }
          }
          data = data || {};
          const result = Object.entries(data).map(([id, name]) => ({ label: name, value: id }));
          response(result);
          if (result.length === 0) {
            resetOwnerFields();
          }
        }
      });
    },
    minLength: 0,
    select: function (event, ui) {
      $('#owner').val(ui.item.label);
      $('#owner-id').val(ui.item.value);
      fillOwnerFields(ui.item.value);
      return false;
    }
  });

  $('#owner').change(function () {
    fillOwnerFields($('#owner-id').val());
  });

  function fillOwnerFields(owner_id) {
    const owner = owners[owner_id];
    if (owner) {
      $('#contact').val(owner.contact_number_1);
      $('#email').val(owner.email_1);
      $('#name').val(owner.sec_name);
      $('#number').val(owner.contact);
      $('#email-id').val(owner.email);
    } else {
      resetOwnerFields();
    }
  }

  function resetOwnerFields() {
    $('#contact, #email, #name, #number, #email-id').val('');
  }
});

    </script>
<!-- Leaflet & Geocoder Libraries -->
<!-- <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script> -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.3/leaflet.css" />
  <link rel="stylesheet" href="https://maps.locationiq.com/v2/libs/leaflet-geocoder/1.9.6/leaflet-geocoder-locationiq.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.3/leaflet.js"></script>
  <script src="https://maps.locationiq.com/v2/libs/leaflet-geocoder/1.9.6/leaflet-geocoder-locationiq.min.js"></script>
  <script src="{{ asset('js/location-map.js') }}"></script>

@stop
