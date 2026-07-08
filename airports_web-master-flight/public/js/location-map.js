(function () {
  function initLocationMap(widget) {
    if (!widget) return;

    const latInputId = widget.getAttribute('data-lat-input-id') || 'latitude';
    const lngInputId = widget.getAttribute('data-lng-input-id') || 'longitude';
    const mapId = widget.getAttribute('data-map-id') || 'map';
    const defaultLat = parseFloat(widget.getAttribute('data-default-lat') || 19.0549990);
    const defaultLng = parseFloat(widget.getAttribute('data-default-lng') || 72.8692035);
    const zoom = parseInt(widget.getAttribute('data-zoom') || 13, 10);
    const locationIqApiKey = widget.getAttribute('data-locationiq-key') || '';

    const latInput = document.getElementById(latInputId);
    const lngInput = document.getElementById(lngInputId);
    const mapDiv = document.getElementById(mapId);

    if (!latInput || !lngInput || !mapDiv) return;

    const mapContainer = mapDiv.parentElement || widget;
    const map = L.map(mapDiv).setView([defaultLat, defaultLng], zoom);

    if (locationIqApiKey) {
      L.tileLayer(`https://{s}-tiles.locationiq.com/v2/obk/r/{z}/{x}/{y}.png?key=${locationIqApiKey}`, {
        attribution: '&copy; OpenStreetMap contributors; Geocoding by LocationIQ'
      }).addTo(map);
    } else {
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);
    }

    const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function updateFields(lat, lng) {
      latInput.value = lat;
      lngInput.value = lng;
    }

    function updateMapFromInputs() {
      const lat = parseFloat(latInput.value);
      const lng = parseFloat(lngInput.value);
      if (!isNaN(lat) && !isNaN(lng)) {
        map.setView([lat, lng], 15);
        marker.setLatLng([lat, lng]);
      }
    }

    function reverseGeocode(lat, lng) {
      fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
        .then(res => res.json())
        .then(data => {
          if (data.display_name && searchInput) {
            searchInput.placeholder = data.display_name;
          }
        });
    }

    marker.on('dragend', function () {
      const pos = marker.getLatLng();
      updateFields(pos.lat, pos.lng);
      reverseGeocode(pos.lat, pos.lng);
    });

    latInput.addEventListener('blur', updateMapFromInputs);
    lngInput.addEventListener('blur', updateMapFromInputs);
    latInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        updateMapFromInputs();
      }
    });
    lngInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        updateMapFromInputs();
      }
    });

    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = locationIqApiKey ? 'Search location' : 'LocationIQ key missing';
    searchInput.style.position = 'absolute';
    searchInput.style.top = '10px';
    searchInput.style.right = '10px';
    searchInput.style.zIndex = '1000';
    searchInput.style.width = '260px';
    searchInput.style.padding = '8px 10px';
    searchInput.style.border = '1px solid #ccc';
    searchInput.style.borderRadius = '4px';
    searchInput.style.background = '#fff';
    searchInput.style.boxShadow = '0 1px 5px rgba(0,0,0,0.2)';
    mapContainer.appendChild(searchInput);

    const resultsBox = document.createElement('div');
    resultsBox.style.position = 'absolute';
    resultsBox.style.top = '48px';
    resultsBox.style.right = '10px';
    resultsBox.style.zIndex = '1000';
    resultsBox.style.width = '260px';
    resultsBox.style.maxHeight = '220px';
    resultsBox.style.overflowY = 'auto';
    resultsBox.style.background = '#fff';
    resultsBox.style.border = '1px solid #ddd';
    resultsBox.style.borderRadius = '4px';
    resultsBox.style.display = 'none';
    mapContainer.appendChild(resultsBox);

    let debounceTimer;
    searchInput.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      const query = this.value.trim();
      if (!query) {
        resultsBox.innerHTML = '';
        resultsBox.style.display = 'none';
        return;
      }

      debounceTimer = setTimeout(function () {
        const url = locationIqApiKey
          ? `https://us1.locationiq.com/v1/autocomplete.php?key=${encodeURIComponent(locationIqApiKey)}&q=${encodeURIComponent(query)}&format=json`
          : `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=5&q=${encodeURIComponent(query)}`;

        fetch(url)
          .then(res => res.json())
          .then(data => {
            resultsBox.innerHTML = '';
            if (!Array.isArray(data) || data.length === 0) {
              resultsBox.style.display = 'none';
              return;
            }

            data.forEach(function (item) {
              const row = document.createElement('div');
              row.style.padding = '8px 10px';
              row.style.cursor = 'pointer';
              row.style.borderBottom = '1px solid #f1f1f1';
              row.textContent = item.display_name || item.name || 'Unknown location';
              row.addEventListener('click', function () {
                const lat = parseFloat(item.lat);
                const lon = parseFloat(item.lon);
                if (!isNaN(lat) && !isNaN(lon)) {
                  map.setView([lat, lon], 15);
                  marker.setLatLng([lat, lon]);
                  updateFields(lat, lon);
                  searchInput.value = this.textContent;
                  resultsBox.innerHTML = '';
                  resultsBox.style.display = 'none';
                }
              });
              resultsBox.appendChild(row);
            });
            resultsBox.style.display = 'block';
          })
          .catch(function () {
            resultsBox.innerHTML = '';
            resultsBox.style.display = 'none';
          });
      }, 250);
    });

    document.addEventListener('click', function (e) {
      if (!mapContainer.contains(e.target)) {
        resultsBox.innerHTML = '';
        resultsBox.style.display = 'none';
      }
    });

    const initialLat = parseFloat(latInput.value);
    const initialLng = parseFloat(lngInput.value);
    if (!isNaN(initialLat) && !isNaN(initialLng)) {
      map.setView([initialLat, initialLng], zoom);
      marker.setLatLng([initialLat, initialLng]);
      reverseGeocode(initialLat, initialLng);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.location-map-widget').forEach(initLocationMap);
  });
})();
