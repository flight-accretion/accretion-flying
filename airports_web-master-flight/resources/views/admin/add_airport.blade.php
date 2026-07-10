@extends('layouts.admin_header')
@section('content')
  <section class="content airport-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Add Airport</h3>
          </div>            
          <form id="form-add-airport" role="form" method="POST" action="{{ url('/airport/add') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-3">
                  <label for="city">City *</label>
                  <select class="form-control select2" id="city" name="city">
                    @foreach($cities as $id => $city)
                      @if(old('city_id') == $id)
                        <option value="{{$id}}" selected>{{$city->name}}</option>
                      @else
                        <option value="{{$id}}">{{$city->name}}</option>
                      @endif
                    @endforeach
                  </select>
                  <span class="error-font text-danger">{{ $errors->first('city') }}</span>
                </div>
                <div class="form-group col-md-3">
                  <label>Airport Name *</label>
                  <input type="text" class="form-control" id="airport" placeholder="Enter Airport name" name="airport" value="{{ old('airport') }}">
                  <span class="error-font text-danger">{{ $errors->first('airport')}}</span>
                </div>
                <div class="form-group col-md-3">
                  <label>Handling Charge</label>
                  <input type="text" class="form-control" id="charges" placeholder="Enter Airport Handling Charge" name="charges" value="{{ old('charges') }}">
                  <span class="error-font text-danger">{{ $errors->first('charges')}}</span>
                </div>
                <div class="form-group col-md-3">
                  <label>Crew Handling</label>
                  <input type="text" class="form-control" id="crew_handling" placeholder="Enter Crew Handling Charge" name="crew_handling" value="{{ old('crew_handling', 25000) }}">
                  <span class="error-font text-danger">{{ $errors->first('crew_handling')}}</span>
                </div>
                <div class="form-group col-md-3">
                  <label for="gt">Ground Time (In minutes)</label>
                  <input type="number" min="0" class="form-control" id="gt" placeholder="Enter ground time" name="gt" value="{{ old('gt', 10) }}">
                  <span class="error-font text-danger">{{ $errors->first('gt')}}</span>
                </div>
                <div class="form-group col-md-3">
                  <label for="lavatory">Status</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="status" value="1" checked> &nbsp;&nbsp;Active
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="status" value="0"> &nbsp;&nbsp;Deactive
                    </label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-3">
                  <label>IATA</label>
                  <input type="text" class="form-control" id="iata" placeholder="Enter IATA" name="iata" value="{{ old('iata') }}">
                  <span class="error-font text-danger">{{ $errors->first('iata')}}</span>
                </div>
                <div class="form-group col-md-3">
                  <label>ICAO</label>
                  <input type="text" class="form-control" id="icao" placeholder="Enter ICAO" name="icao" value="{{ old('icao') }}">
                  <span class="error-font text-danger">{{ $errors->first('icao')}}</span>
                </div>
                <div class="form-group col-md-3">
                  <label>Open Time</label>
                  <div class="input-group bootstrap-timepicker timepicker">
                    <input id="open-time" type="text" class="form-control input-small" name="open-time" value="{{ Carbon\Carbon::parse(old('open-time'))->format('H:i') }}">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                  </div>
                  <span class="error-font text-danger">{{ $errors->first('open-time')}}</span>
                </div>
                <div class="form-group col-md-3">
                  <label>Close Time</label>
                  <div class="input-group bootstrap-timepicker timepicker">
                    <input id="close-time" type="text" class="form-control input-small" name="close-time" value="{{ Carbon\Carbon::parse(old('close-time'))->format('H:i') }}">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                  </div>
                  <span class="error-font text-danger">{{ $errors->first('close-time')}}</span>
                </div>
              </div>
              @include('partials.location_map', [
                'latInputId' => 'latitude',
                'lngInputId' => 'longitude',
                'mapId' => 'map',
                'latInputName' => 'latitude',
                'lngInputName' => 'longitude',
                'latInputValue' => old('latitude'),
                'lngInputValue' => old('longitude')
              ])
            </div>
            <div class="box-footer text-center">
              <button type="submit" class="btn btn-primary btn-fixed">Submit</button>
              <a href="/airport" type="submit" class="btn btn-primary btn-back-fixed">Back</a>
            </div>
          </form>  
        </div>
      </div>
    </div>
  </section>

  <script>
    $(function(){
      $(".select2").select2();
      $('#open-time').timepicker({ showMeridian: false, use24hours: true, format: 'HH:mm' });
      $('#close-time').timepicker({ showMeridian: false, use24hours: true, format: 'HH:mm' });
    });
  </script>

  <!-- Leaflet Integration -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.3/leaflet.css" />
  <link rel="stylesheet" href="https://maps.locationiq.com/v2/libs/leaflet-geocoder/1.9.6/leaflet-geocoder-locationiq.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.3/leaflet.js"></script>
  <script src="https://maps.locationiq.com/v2/libs/leaflet-geocoder/1.9.6/leaflet-geocoder-locationiq.min.js"></script>
  <script src="{{ asset('js/location-map.js') }}"></script>
  <style>
    .leaflet-locationiq-branding {
      display: none !important;
    }
  </style>
@stop
