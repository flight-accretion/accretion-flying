<div class="location-map-widget"
     data-lat-input-id="{{ $latInputId ?? 'latitude' }}"
     data-lng-input-id="{{ $lngInputId ?? 'longitude' }}"
     data-map-id="{{ $mapId ?? 'map' }}"
     data-default-lat="{{ $defaultLat ?? 19.0549990 }}"
     data-default-lng="{{ $defaultLng ?? 72.8692035 }}"
     data-zoom="{{ $zoom ?? 13 }}"
     data-locationiq-key="{{ env('LOCATIONIQ_API_KEY', '') }}">
  <div class="row">
    <div class="form-group col-md-6">
      <label for="address">Latitude *</label>
      <input type="text" class="form-control" id="{{ $latInputId ?? 'latitude' }}" name="{{ $latInputName ?? 'latitude' }}" value="{{ $latInputValue ?? old($latInputName ?? 'latitude') ?? ($defaultLat ?? '') }}">
      <span class="error-font text-danger">{{ $errors->first($latInputName ?? 'latitude') }}</span>
    </div>
    <div class="form-group col-md-6">
      <label for="address">Longitude *</label>
      <input type="text" class="form-control" id="{{ $lngInputId ?? 'longitude' }}" name="{{ $lngInputName ?? 'longitude' }}" value="{{ $lngInputValue ?? old($lngInputName ?? 'longitude') ?? ($defaultLng ?? '') }}">
      <span class="error-font text-danger">{{ $errors->first($lngInputName ?? 'longitude') }}</span>
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-12">
      <div style="position: relative; height: 400px;">
        <div id="{{ $mapId ?? 'map' }}" style="height: 100%; width: 100%;"></div>
      </div>
    </div>
  </div>
</div>
