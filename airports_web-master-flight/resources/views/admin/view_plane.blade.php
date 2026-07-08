@extends('layouts.admin_header')
@section('content')
  <section class="content plane-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">View Plane</h3>
          </div>            
          <form id="form-add-plane" role="form" method="POST" action="{{ url('/plane/edit') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="plane">Plane Name</label>
                  <input type="hidden" name="plane-id" value="{{ $plane->id }}">
                  <input type="text" class="form-control" id="plane" placeholder="Enter plane name" name="plane" value="{{ $plane->name }}" disabled>
                  <span class="error-font text-danger">{{ $errors->first('plane')}}</span>
                </div>
                 <div class="form-group col-md-4">
                  <label for="type">Type</label>
                  <select class="form-control" id="type" name="type" disabled>
                    @foreach($plane_types as $id => $type)
                      @if($plane->type_id == $id)
                        <option value="{{$id}}" selected>{{$type}}</option>
                      @else
                        <option value="{{$id}}">{{$type}}</option>
                      @endif
                    @endforeach
                  </select>
                   <span class="error-font text-danger">{{ $errors->first('type')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="speed_coefficient">Take Off / Landing Speed Coefficient</label>
                  <input type="text" class="form-control" id="speed_coefficient" placeholder="Enter speed coefficient" name="speed_coefficient" value="{{ $plane->speed_coefficient }}" disabled>
                  <span class="error-font text-danger">{{ $errors->first('price')}}</span>
                </div>
              </div>                  
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="price">Price Per Hour</label>
                  <input type="text" class="form-control" id="price" placeholder="Enter plane price" name="price" value="{{ $plane->price_per_hour }}" disabled>
                  <span class="error-font text-danger">{{ $errors->first('price')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="seats">Seats</label>
                  <input type="text" class="form-control" id="seats" placeholder="Enter number of seats" name="seats" value="{{ $plane->seats }}" disabled>
                  <span class="error-font text-danger">{{ $errors->first('seats')}}</span>
                </div>
                <div class="form-group col-md-4">
                 <label for="speed">Speed Per Hour</label>
                  <input type="text" class="form-control" id="speed" placeholder="Enter plane speed" name="speed" value="{{ $plane->speed }}" disabled>
                  <span class="error-font text-danger">{{ $errors->first('speed')}}</span>
                </div>
              </div> 
              <div class="row">
                <div class="form-group col-md-4">
                  <label>Note</label>
                  <textarea name="note" class="form-control" rows="2" placeholder="Enter description in short" disabled>{{ $plane->note }}</textarea>
                </div>
                <div class="form-group col-md-4">
                  <label for="lavatory">Lavatory</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="lavatory" value="0" <?php if($plane->lavatory == 0){ echo 'checked';} ?> disabled>
                      Yes
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="lavatory" value="1" <?php if($plane->lavatory == 1){ echo 'checked';} ?> disabled>
                      No
                    </label>
                  </div>
                </div> 
                <div class="form-group col-md-4 flower-shower">
                  <label for="flower-shower">Flower Shower ?</label><br/>
                  <input type="checkbox" id="flower-shower" name="flower-shower" value="1" <?php if($plane->flower_shower == 1){ echo 'checked';} ?> disabled> 
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
                          <input type="text" class="form-control" value="{{ $owner->name }}" disabled>
                        </div>
                        <div class="form-group col-md-4">
                          <label for="owner">Contact</label>
                           <input type="text" class="form-control" id="contact" name="contact" value="{{ $owner->contact_number_1 }}" disabled>
                        </div>
                        <div class="form-group col-md-4">
                          <label for="owner">Email</label>
                           <input type="text" class="form-control" id="email" name="email" value="{{ $owner->email_1 }}" disabled>
                        </div>
                      </div> 
                      @if($owner_secondary_contacts != '')
                      <div class="row form-group">
                        <div class="col-md-12">
                          <div class="box box-danger">
                            <div class="box-header with-border">
                              <h5 class="box-title">Secondary Contact Details</h5>
                            </div>
                          </div>
                          <div class="box-body secondary-contacts">
                            @foreach($owner_secondary_contacts as $contact)
                              <div class="row form-group">
                                <div class="form-group col-md-4">
                                  <label for="owner">Name</label>
                                  <input type="text" class="form-control" id="name" name="name" value="{{ $contact->name }}" disabled>
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="number">Contact</label>
                                  <input type="text" class="form-control" id="number" name="number" value="{{ $contact->contact }}" disabled>
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="email-id">Email</label>
                                  <input type="text" class="form-control" id="email-id" name="email-id" value="{{ $contact->email }}" disabled>
                                </div>
                            </div> 
                            @endforeach
                          </div> 
                        </div> 
                      </div> 
                      @endif
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
                          <select class="form-control select2" id="city" name="city" disabled>
                            @foreach($cities as $id => $city)
                              @if($plane->city_id == $id)
                                <option value="{{$id}}" selected>{{$city}}</option>
                              @else
                                <option value="{{$id}}">{{$city}}</option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="city">Airport</label>
                          <select class="form-control" id="airport" name="airport" disabled>
                            @foreach($airports as $airport)
                              <option data-id="{{ $airport->id }}" value="{{$airport->id}}" @if($plane->airport_id == $airport->id) selected @endif>{{$airport->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label for="latitude">Latitude</label>
                          <input type="text" class="form-control" id="lat" name="lat" value="{{ $plane->latitude }}" readonly>
                        </div>               
                        <div class="col-md-6">
                          <label for="longitude">Longitude</label>
                          <input type="text" class="form-control" id="long" name="long" value="{{ $plane->longitude }}" readonly>
                        </div>               
                      </div>  
                      <div class="row form-group">
                        <div class="col-md-12 text-center">	
                            <input id="search-location" class="controls" type="text" placeholder="Search Box">
                            <div id="map"></div>
                            <input id="latitude" name="latitude" type="hidden">
                            <input id="longitude" name="longitude" type="hidden">
                        </div> 
                      </div>	             
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
                          <input type="text" class="form-control" id="gt" name="gt" value="{{ $plane->gt }}" disabled>
                        </div> 
                        <div class="col-md-6">
                          <label for="from-date">From Date *</label>
                          <div class="input-group date" id="from-date">								
                            <input type="text" class="form-control from-date" id="from-date" name="from-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="{{ ($plane->from_date != '0000-00-00 00:00:00' ? date('d-m-Y', strtotime($plane->from_date)) : '') }}" tabindex="2" disabled>
                            <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                          </div>
                          <span class="error-font text-danger">{{ $errors->first('from-date') }}</span>
                        </div>  
                        <div class="col-md-6">
                          <label for="to-date">To Date *</label>
                          <div class="input-group date" id="to-date">								
                            <input type="text" class="form-control to-date" id="to-date" name="to-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="{{ ($plane->to_date != '0000-00-00 00:00:00' ? date('d-m-Y', strtotime($plane->to_date)) : '') }}" tabindex="2" disabled>
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
                          <input type="text" class="form-control" id="lat-hel" name="lat-hel" value="{{ $plane->temp_latitude }}" disabled>
                        </div>               
                        <div class="col-md-6">
                          <label for="longitude-hel">Longitude</label>
                          <input type="text" class="form-control" id="long-hel" name="long-hel" value="{{ $plane->temp_longitude }}" disabled>
                        </div>                
                      </div>  
                      <div class="row form-group">
                        <div class="col-md-12 text-center">	
                          <input id="search-location-hel" class="controls" type="text" placeholder="Search Box" disabled>
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
                                <span>
                                <?php 
                                if($plane->display_image != ''){
                                  echo '<img class=""  src="/uploads/'. $plane->display_image.'" height="100%" />';
                                }
                                else{
                                  echo'<i class="fa fa-picture-o fa-4x"></i>';
                                }
                                ?>
                                </span> 
                                <input class="fileupload" type="file" name="files[]" data-id="1" data-url="/plane/upload" multiple disabled>
                                <input id="image-1" type="hidden" name="display-image" value="{{$plane->display_image}}">
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
                      <h3 class="box-title">Plane Images &nbsp;<span class="text-danger">(Recommended size: 445 x 350 px or same aspect ratio)</span></h3>
                    </div>
                    <div class="box-body">	
                      <div class="row">
                         <?php $i=2;?>
                          @foreach($plane_images as $image_id => $plane_image)
                            <div class="col-md-3 ">
                              <div class="form-group">
                                <label id="image-box-{{$i}}" class="button image-box" style="
                                  <?php if($plane_images !="") {
                                    echo "background-image: url('/uploads/".$plane_image ."'); background-color: #FFF;"; }
                                    ?>"><span class="dummy-image-{{$i}} <?php if($plane_image != "" ) {echo 'hide';}?>"><i class="fa fa-picture-o fa-4x fa-picture-set"></i></span>
                                  <?php					
                                  if($plane_image != "") {?>
                                    <a id="delete-image-modal-link-{{$i}}" class="delete-image" href="#delete-image-modal" data-toggle="modal" data-dismiss="modal"  data-target="#delete-image-modal" data-image-id="<?php echo $image_id ; ?>"></a>
                                  <?php } else { ?>									
                                    <a id="delete-image-modal-link-{{$i}}" class="delete-image hide" data-image-id="0" data-index="{{$i}}"></a>
                                  <?php } ?>																	
                                    <input class="fileupload" type="file" name="files[]" data-id="{{$i}}" data-url="/plane/upload" multiple disabled>
                                    <input id="image-{{$i}}" type="hidden" name="images[]" value="<?php if(isset($plane_image)) { echo $plane_image; } ?>">
                                  <div id="progress-{{$i}}">
                                    <div class="bar" style="width: 0%;"></div>
                                  </div>
                                  </label>
                              </div>
                            </div> 
                            <?php $i++;  ?>
                          @endforeach
                          @for($j=$i; $j<6; $j++)
                            <div class="col-md-3 ">
                              <div class="form-group">
                                <label id="image-box-{{$j}}" class="button image-box"><span><i class="fa fa-picture-o fa-4x fa-picture-set"></i></span> 
                                  <input class="fileupload" type="file" name="files[]" data-id="{{$j}}" data-url="/plane/upload" multiple>
                                  <input id="image-{{$j}}" type="hidden" name="images[]">
                                <div id="progress-{{$j}}">
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
              <a href="/plane" class="btn btn-primary btn-back-fixed">Back</a>
            </div>
          </form>  
        </div>
      </div>
    </div>
  </section>
  
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

  <script>
    $(function() {
      $(".select2").select2();
      $("#from-date, #to-date").datepicker({ format: "dd-mm-yyyy", autoclose: true });

      // Variables
      var airports = <?php echo json_encode($airports) ?>;
      var plane_type = <?php echo $plane->type_id; ?>;

      // Initialize Maps
      var mainLat = $('#lat').val() || 20.5937;
      var mainLng = $('#long').val() || 78.9629;

      var helLat = $('#lat-hel').val() || mainLat;
      var helLng = $('#long-hel').val() || mainLng;

      var mainMap = L.map('map').setView([mainLat, mainLng], 13);
      var helMap  = L.map('map-hel').setView([helLat, helLng], 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(mainMap);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(helMap);

      // Initialize Markers
      var mainMarker = L.marker([$('#lat').val(), $('#long').val()], { draggable: true }).addTo(mainMap);
      var helMarker  = L.marker([$('#lat-hel').val(), $('#long-hel').val()], { draggable: true }).addTo(helMap);
      if (plane_type == 2) {
        $(".helicopter").removeClass("hide");

        setTimeout(function () {
          helMap.invalidateSize();
          helMap.setView(
            [$('#lat-hel').val(), $('#long-hel').val()],
            13
          );
        }, 300);
      }


      // Marker Drag → Update Inputs & Nearest Airport
      function enableMarkerDrag(marker, latInput, longInput, map, airportSelect, citySelect) {
        marker.on('dragend', function() {
          var pos = marker.getLatLng();
          $(latInput).val(pos.lat.toFixed(6));
          $(longInput).val(pos.lng.toFixed(6));
          map.setView(pos);

          // AJAX: Find nearest airport (like old Google Maps)
          $.ajax({
            url: '/plane/locationwise-airport',
            type: 'GET',
            data: { lat: pos.lat, long: pos.lng },
            success: function(data) {
              var result = (typeof data === 'string') ? JSON.parse(data) : data;
              if(result.distance <= 50){
                if(result.city_id != $(citySelect).val()){
                  $(citySelect).val(result.city_id).trigger('change');
                } else {
                  $(citySelect).val(result.city_id);
                }
                $(airportSelect).html('<option value="'+result.id+'">'+result.name+'</option>');
              } else {
                $(citySelect).val(0).trigger('change');
                $(airportSelect).html('');
                alert('City not found near this location. Please select a city.');
              }
            },
            error: function(){ alert("Error finding nearest airport."); }
          });
        });
      }

      enableMarkerDrag(mainMarker, '#lat', '#long', mainMap, '#airport', '#city');
      enableMarkerDrag(helMarker, '#lat-hel', '#long-hel', helMap, '#airport-hel', '#city-hel');

      // Manual Lat/Lng Change → Marker Moves
      $('#lat, #long').on('change', function() {
        mainMarker.setLatLng([$('#lat').val(), $('#long').val()]);
        mainMap.setView([$('#lat').val(), $('#long').val()], 13);
      });
      $('#lat-hel, #long-hel').on('change', function() {
        helMarker.setLatLng([$('#lat-hel').val(), $('#long-hel').val()]);
        helMap.setView([$('#lat-hel').val(), $('#long-hel').val()], 13);
      });

      // Airport Dropdown → Marker Moves
      $('#airport').change(function() {
        var airport_id = $(this).val();
        if(airport_id && airports[airport_id]){
          var lat = airports[airport_id].latitude;
          var lng = airports[airport_id].longitude;
          $('#lat').val(lat);
          $('#long').val(lng);
          mainMarker.setLatLng([lat,lng]);
          mainMap.setView([lat,lng],13);
        }
      });
      $('#airport-hel').change(function() {
        var airport_id = $(this).val();
        if(airport_id && airports[airport_id]){
          var lat = airports[airport_id].latitude;
          var lng = airports[airport_id].longitude;
          $('#lat-hel').val(lat);
          $('#long-hel').val(lng);
          helMarker.setLatLng([lat,lng]);
          helMap.setView([lat,lng],13);
        }
      });

      var selectedMainAirportId = {{ isset($plane->airport_id) ? $plane->airport_id : 'null' }};
      var selectedHelAirportId  = {{ isset($plane->temporary_airport_id) ? $plane->temporary_airport_id : 'null' }};

      // City Dropdown → Load Airports via AJAX
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

      function loadCityAirports(citySelector, airportSelector, selectedAirportId){
        $(citySelector).off('change').on('change', function() {
          var city_id = $(this).val();
          if(city_id != 0 && city_id != null){
            $.ajax({
              url: '/plane/citywise-airports',
              type: 'GET',
              data: { 'city-id': city_id },
              dataType: 'json',
              success: function(data){
                $(airportSelector).empty().append('<option value="0">Please select</option>');

                $.each(normalizeAirportResponse(data), function(i, airport){
                  let selected = Number(airport.id) === Number(selectedAirportId) ? 'selected' : '';
                  $(airportSelector).append(
                    '<option value="'+airport.id+'" '+selected+'>'+airport.name+'</option>'
                  );
                });

                if(selectedAirportId){
                  $(airportSelector).val(selectedAirportId).trigger('change');
                }
              },
              error: function(){ alert("Error loading airports"); }
            });
          }
        });
      }
      loadCityAirports('#city', '#airport', selectedMainAirportId);
      $('#city').trigger('change');

      loadCityAirports('#city-hel', '#airport-hel', selectedHelAirportId);
      $('#city-hel').trigger('change');


      // Helicopter Section Toggle
      if(plane_type == 2) $(".helicopter").removeClass("hide");

      $('#type').change(function() {
        plane_type = $(this).val();

        if (plane_type == 2) {
          $(".helicopter").removeClass("hide");
          setTimeout(function () {
            helMap.invalidateSize();
            helMap.setView(
              [$('#lat-hel').val(), $('#long-hel').val()],
              13
            );
          }, 300);

          $('#lat, #long').attr("readonly", false);
        } else {
          $(".helicopter").addClass("hide");
          $('#lat, #long').attr("readonly", true);
        }
      });

      // Search Box → Move Marker
      var mainGeocoder = L.Control.Geocoder.nominatim();
      $('#search-location').on('keypress', function(e){
        if(e.which != 13) return;
        e.preventDefault();
        mainGeocoder.geocode($(this).val(), function(results){
          if(results && results.length > 0){
            var latlng = results[0].center;
            mainMarker.setLatLng(latlng);
            $('#lat').val(latlng.lat.toFixed(6));
            $('#long').val(latlng.lng.toFixed(6));
            mainMap.setView(latlng,13);
          }
        });
      });

      var helGeocoder = L.Control.Geocoder.nominatim();
      $('#search-location-hel').on('keypress', function(e){
        if(e.which != 13) return;
        e.preventDefault();
        helGeocoder.geocode($(this).val(), function(results){
          if(results && results.length > 0){
            var latlng = results[0].center;
            helMarker.setLatLng(latlng);
            $('#lat-hel').val(latlng.lat.toFixed(6));
            $('#long-hel').val(latlng.lng.toFixed(6));
            helMap.setView(latlng,13);
          }
        });
      });

    });
  </script>


  @stop
