(function() {
  'use strict';

  var SERVICE_LABELS = {
    'air-ambulance': 'Air Ambulance',
    'private-jet': 'Private Jet',
    'private-charter-helicopter': 'Private Charter Helicopter'
  };

  var SERVICE_ALIASES = {
    'air ambulance': 'air-ambulance',
    'air-ambulance': 'air-ambulance',
    'air_ambulance': 'air-ambulance',
    'private jet': 'private-jet',
    'private-jet': 'private-jet',
    'private_jat': 'private-jet',
    'private jat': 'private-jet',
    'private charter helicopter': 'private-charter-helicopter',
    'private-charter-helicopter': 'private-charter-helicopter',
    'private helicopter': 'private-charter-helicopter',
    'helicopter': 'private-charter-helicopter'
  };

  function normalizeService(value) {
    var raw = String(value || '').toLowerCase().replace(/_/g, '-').trim();

    if(SERVICE_ALIASES[raw]) {
      return SERVICE_ALIASES[raw];
    }

    raw = raw.replace(/-/g, ' ');
    return SERVICE_ALIASES[raw] || 'air-ambulance';
  }

  function inferService(root) {
    var text = [
      root.getAttribute('data-service') || '',
      document.body ? document.body.getAttribute('data-service') || '' : '',
      window.location.pathname || '',
      document.title || ''
    ].join(' ').toLowerCase();

    if(text.indexOf('private') !== -1 && text.indexOf('helicopter') !== -1) {
      return 'private-charter-helicopter';
    }

    if(text.indexOf('private') !== -1 && (text.indexOf('jet') !== -1 || text.indexOf('jat') !== -1)) {
      return 'private-jet';
    }

    if(text.indexOf('air') !== -1 && text.indexOf('ambulance') !== -1) {
      return 'air-ambulance';
    }

    return normalizeService(root.getAttribute('data-service'));
  }

  function currentScriptOrigin() {
    var script = document.currentScript;

    if(script && script.src) {
      var anchor = document.createElement('a');
      anchor.href = script.src;
      return anchor.protocol + '//' + anchor.host;
    }

    return window.location.origin;
  }

  function escapeHtml(value) {
    return String(value === null || value === undefined ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function money(value) {
    var number = Number(value || 0);

    if(!isFinite(number)) {
      number = 0;
    }

    return String.fromCharCode(8377) + Math.round(number).toLocaleString('en-IN');
  }

  function numberText(value, suffix) {
    var number = Number(value || 0);

    if(!isFinite(number)) {
      number = 0;
    }

    return number.toLocaleString('en-IN', {
      minimumFractionDigits: Number.isInteger(number) ? 0 : 2,
      maximumFractionDigits: 2
    }) + (suffix || '');
  }

  function airportLabel(airport) {
    var parts = [];

    if(airport.city_name) {
      parts.push(airport.city_name);
    }

    if(airport.name && airport.name !== airport.city_name) {
      parts.push(airport.name);
    }

    if(airport.iata) {
      parts.push(airport.iata);
    } else if(airport.icao) {
      parts.push(airport.icao);
    }

    return parts.join(' - ') || airport.name || ('Airport #' + airport.id);
  }

    function normalizeAirportText(value) {
    return String(value || '')
      .toLowerCase()
      .normalize('NFKD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9]+/g, ' ')
      .trim();
  }

  function airportSearchText(airport) {
    return normalizeAirportText([
      airportLabel(airport),
      airport.name,
      airport.iata,
      airport.icao,
      airport.city_name,
      airport.state_name,
      airport.country_name
    ].filter(Boolean).join(' '));
  }

  function airportPickerHtml(name, label, listId) {
    return '' +
      '<div class="aa-filter-field aa-airport-field">' +
        '<label>' + escapeHtml(label) + '</label>' +
        '<input type="hidden" name="' + escapeHtml(name) + '">' +
        '<div class="aa-airport-control">' +
          '<input type="search" class="aa-airport-search" data-airport-input="' + escapeHtml(name) + '" list="' + escapeHtml(listId) + '" placeholder="' + escapeHtml(label) + ' airport / city / IATA">' +
          '<button type="button" class="aa-map-button" data-aa-map-picker="' + escapeHtml(name) + '">Select from Map</button>' +
        '</div>' +
        '<datalist id="' + escapeHtml(listId) + '"></datalist>' +
      '</div>';
  }

  function fillAirportDatalist(input, airports) {
    var list = document.getElementById(input.getAttribute('list'));
    if(!list) return;

    list.innerHTML = '';
    airports.forEach(function(airport) {
      var option = document.createElement('option');
      option.value = airportLabel(airport);
      list.appendChild(option);
    });
  }

  function airportById(widget, id) {
    id = String(id || '');
    return widget.airportById && widget.airportById[id] ? widget.airportById[id] : null;
  }

  function matchAirport(widget, value) {
    var needle = normalizeAirportText(value);
    if(!needle) return null;

    var exact = null;
    var contains = null;

    widget.airports.some(function(airport) {
      var label = normalizeAirportText(airportLabel(airport));
      var name = normalizeAirportText(airport.name);
      var iata = normalizeAirportText(airport.iata);
      var icao = normalizeAirportText(airport.icao);
      var searchable = airportSearchText(airport);

      if(label === needle || name === needle || iata === needle || icao === needle) {
        exact = airport;
        return true;
      }

      if(!contains && searchable.indexOf(needle) !== -1) {
        contains = airport;
      }

      return false;
    });

    return exact || contains;
  }

  function setAirportPickerValue(widget, name, airport) {
    var hidden = widget.form.querySelector('[name="' + name + '"]');
    var input = widget.form.querySelector('[data-airport-input="' + name + '"]');

    if(!hidden || !input || !airport) return;

    hidden.value = airport.id || '';
    input.value = airportLabel(airport);
    input.setAttribute('data-selected-airport-id', airport.id || '');
  }

  function syncAirportInput(widget, input) {
    var name = input.getAttribute('data-airport-input');
    var hidden = widget.form.querySelector('[name="' + name + '"]');
    if(!hidden) return;

    var airport = matchAirport(widget, input.value);
    hidden.value = airport ? airport.id : '';
    input.setAttribute('data-selected-airport-id', airport ? airport.id : '');
  }

  function syncAirportInputs(widget) {
    widget.form.querySelectorAll('[data-airport-input]').forEach(function(input) {
      syncAirportInput(widget, input);
    });
  }

  function syncAirportDisplays(widget) {
    widget.form.querySelectorAll('[data-airport-input]').forEach(function(input) {
      var name = input.getAttribute('data-airport-input');
      var hidden = widget.form.querySelector('[name="' + name + '"]');
      var airport = hidden ? airportById(widget, hidden.value) : null;
      if(airport) input.value = airportLabel(airport);
    });
  }

  function bindAirportPickers(widget, scope) {
    scope.querySelectorAll('[data-airport-input]').forEach(function(input) {
      if(input.getAttribute('data-aa-bound') === '1') return;
      input.setAttribute('data-aa-bound', '1');
      fillAirportDatalist(input, widget.airports);
      input.addEventListener('change', function() { syncAirportInput(widget, input); });
      input.addEventListener('blur', function() { syncAirportInput(widget, input); });
    });

    scope.querySelectorAll('[data-aa-map-picker]').forEach(function(button) {
      if(button.getAttribute('data-aa-bound') === '1') return;
      button.setAttribute('data-aa-bound', '1');
      button.addEventListener('click', function() {
        openAirportMap(widget, button.getAttribute('data-aa-map-picker'));
      });
    });
  }

  function apiDate(value) {
    if(!value) {
      return '';
    }

    var date = new Date(value);

    if(isNaN(date.getTime())) {
      return value;
    }

    var dd = String(date.getDate()).padStart(2, '0');
    var mm = String(date.getMonth() + 1).padStart(2, '0');
    var yyyy = date.getFullYear();
    var hh = String(date.getHours()).padStart(2, '0');
    var min = String(date.getMinutes()).padStart(2, '0');

    return dd + '-' + mm + '-' + yyyy + ' ' + hh + ':' + min;
  }

  function defaultDateTimeValue() {
    var date = new Date();
    date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
    return date.toISOString().slice(0, 16);
  }

  function fetchJson(url, options) {
    return fetch(url, options).then(function(response) {
      if(!response.ok) {
        throw new Error('Request failed with status ' + response.status);
      }

      return response.json();
    });
  }

  function setStatus(widget, message, isError) {
    widget.status.classList.remove('is-loading');
    widget.status.textContent = message || '';
    widget.status.classList.toggle('is-error', !!isError);
  }

  function setLoading(widget) {
    widget.status.classList.remove('is-error');
    widget.status.classList.add('is-loading');
    widget.status.innerHTML = '<span class="aa-loading-spinner" aria-label="Loading"></span>';
  }

  function buildSelect(name, label, options) {
    var field = document.createElement('div');
    field.className = 'aa-filter-field';
    field.innerHTML = '<label>' + escapeHtml(label) + '</label>';

    var select = document.createElement('select');
    select.name = name;

    var placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = options && options.placeholder ? options.placeholder : label;
    select.appendChild(placeholder);

    field.appendChild(select);
    return field;
  }

  function fillAirportSelect(select, airports) {
    var current = select.value;
    var placeholder = select.options[0] ? select.options[0].textContent : 'Select Airport';
    select.innerHTML = '';

    var blank = document.createElement('option');
    blank.value = '';
    blank.textContent = placeholder;
    select.appendChild(blank);

    airports.forEach(function(airport) {
      var option = document.createElement('option');
      option.value = airport.id;
      option.textContent = airportLabel(airport);
      option.setAttribute('data-latitude', airport.latitude || '');
      option.setAttribute('data-longitude', airport.longitude || '');
      option.setAttribute('data-name', airport.name || '');
      select.appendChild(option);
    });

    if(current) {
      select.value = current;
    }
  }

  function clearMachineQueryParams(params) {
    [
      'aa_search',
      'service',
      'trip_type',
      'sort',
      'departure_airport_id',
      'arrival_airport_id',
      'adults',
      'date',
      'multi_departure[]',
      'multi_arrival[]',
      'multi_adults[]',
      'multi_date[]',
      'multi_departure',
      'multi_arrival',
      'multi_adults',
      'multi_date'
    ].forEach(function(key) {
      params.delete(key);
    });
  }

  function isSearchUrl() {
    return new URLSearchParams(window.location.search).get('aa_search') === '1';
  }

  function getAllParams(params, key) {
    var values = params.getAll(key);
    if(!values.length && key.indexOf('[]') !== -1) {
      values = params.getAll(key.replace('[]', ''));
    }
    return values;
  }

  function buildSearchUrl(widget) {
    var form = widget.form;
    syncAirportInputs(widget);
    var tripType = form.querySelector('[name="trip_type"]').value || 'single';
    var url = new URL(window.location.href);
    var params = url.searchParams;

    clearMachineQueryParams(params);
    params.set('aa_search', '1');
    params.set('service', widget.service);
    params.set('trip_type', tripType);
    params.set('sort', form.querySelector('[name="sort"]').value || 'price_asc');

    if(tripType === 'multi') {
      form.querySelectorAll('.aa-multi-leg').forEach(function(row) {
        params.append('multi_departure[]', row.querySelector('[name="multi_departure[]"]').value);
        params.append('multi_arrival[]', row.querySelector('[name="multi_arrival[]"]').value);
        params.append('multi_adults[]', row.querySelector('[name="multi_adults[]"]').value || 1);
        params.append('multi_date[]', row.querySelector('[name="multi_date[]"]').value);
      });
    } else {
      params.set('departure_airport_id', form.querySelector('[name="departure_airport_id"]').value);
      params.set('arrival_airport_id', form.querySelector('[name="arrival_airport_id"]').value);
      params.set('adults', form.querySelector('[name="adults"]').value || 1);
      params.set('date', form.querySelector('[name="date"]').value);
    }

    return url.toString();
  }

  function openSearchUrl(widget) {
    window.location.href = buildSearchUrl(widget);
  }

  function openBaseUrl() {
    var url = new URL(window.location.href);
    clearMachineQueryParams(url.searchParams);
    window.location.href = url.toString();
  }

  function applyUrlParams(widget) {
    if(!widget.searchUrlMode || !isSearchUrl()) {
      return false;
    }

    var params = new URLSearchParams(window.location.search);
    var form = widget.form;
    var tripType = params.get('trip_type') || 'single';

    form.querySelector('[name="trip_type"]').value = tripType;
    form.classList.toggle('is-multi', tripType === 'multi');
    form.querySelector('[name="sort"]').value = params.get('sort') || 'price_asc';

    if(tripType === 'multi') {
      var departures = getAllParams(params, 'multi_departure[]');
      var arrivals = getAllParams(params, 'multi_arrival[]');
      var adults = getAllParams(params, 'multi_adults[]');
      var dates = getAllParams(params, 'multi_date[]');
      var rowsNeeded = Math.max(departures.length, arrivals.length, adults.length, dates.length, 1);

      widget.multiContainer.innerHTML = '';
      widget.addLegButton = null;
      for(var i = 0; i < rowsNeeded; i++) {
        addMultiLeg(widget);
      }

      form.querySelectorAll('.aa-multi-leg').forEach(function(row, index) {
        row.querySelector('[name="multi_departure[]"]').value = departures[index] || '';
        row.querySelector('[name="multi_arrival[]"]').value = arrivals[index] || '';
        row.querySelector('[name="multi_adults[]"]').value = adults[index] || 1;
        row.querySelector('[name="multi_date[]"]').value = dates[index] || defaultDateTimeValue();
      });
    } else {
      form.querySelector('[name="departure_airport_id"]').value = params.get('departure_airport_id') || '';
      form.querySelector('[name="arrival_airport_id"]').value = params.get('arrival_airport_id') || '';
      form.querySelector('[name="adults"]').value = params.get('adults') || 1;
      form.querySelector('[name="date"]').value = params.get('date') || defaultDateTimeValue();
    }
   syncAirportDisplays(widget);
    return true;
  }

   function nearestAirportUrl(widget, lat, lng) {
    var url = widget.apiBase + '/api/v1/airports?nearest=1&limit=1&latitude=' + encodeURIComponent(lat) + '&longitude=' + encodeURIComponent(lng);
    if(widget.apiKey) {
      url += '&api_key=' + encodeURIComponent(widget.apiKey);
    }
    return url;
  }

  function openAirportMap(widget, fieldName) {
    if(typeof L === 'undefined') {
      alert('Map library is not loaded.');
      return;
    }

    var modal = getAirportMapModal(widget);
    modal.classList.add('is-open');
    modal.setAttribute('data-target-field', fieldName);
    modal.querySelector('.aa-map-status').textContent = 'Click on the map, then press Select Airport.';

    setTimeout(function() {
      if(!widget.airportMap) {
        widget.airportMap = L.map(modal.querySelector('.aa-airport-map')).setView([20.5937, 78.9629], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(widget.airportMap);

        widget.airportMap.on('click', function(event) {
          var lat = event.latlng.lat;
          var lng = event.latlng.lng;
          modal.setAttribute('data-latitude', lat);
          modal.setAttribute('data-longitude', lng);
          modal.querySelector('.aa-map-status').textContent = 'Selected: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);

          if(!widget.airportMarker) {
            widget.airportMarker = L.marker([lat, lng]).addTo(widget.airportMap);
          } else {
            widget.airportMarker.setLatLng([lat, lng]);
          }
        });
      }

      widget.airportMap.invalidateSize();
    }, 150);
  }

  function getAirportMapModal(widget) {
    if(widget.airportMapModal) return widget.airportMapModal;

    var modal = document.createElement('div');
    modal.className = 'aa-modal-backdrop aa-airport-map-modal';
    modal.innerHTML =
      '<div class="aa-modal aa-airport-modal" role="dialog" aria-modal="true">' +
        '<div class="aa-modal-header">' +
          '<h3>Select Airport From Map</h3>' +
          '<button class="aa-close" type="button" aria-label="Close">&times;</button>' +
        '</div>' +
        '<div class="aa-airport-modal-body">' +
          '<div class="aa-map-status">Click on the map, then press Select Airport.</div>' +
          '<div class="aa-airport-map"></div>' +
          '<div class="aa-filter-actions aa-map-actions">' +
            '<button class="aa-button aa-map-select" type="button">Select Airport</button>' +
            '<button class="aa-button aa-button-secondary aa-map-cancel" type="button">Cancel</button>' +
          '</div>' +
        '</div>' +
      '</div>';

    modal.querySelector('.aa-close').addEventListener('click', function() {
      modal.classList.remove('is-open');
    });
    modal.querySelector('.aa-map-cancel').addEventListener('click', function() {
      modal.classList.remove('is-open');
    });
    modal.addEventListener('click', function(event) {
      if(event.target === modal) modal.classList.remove('is-open');
    });
    modal.querySelector('.aa-map-select').addEventListener('click', function() {
      var lat = modal.getAttribute('data-latitude');
      var lng = modal.getAttribute('data-longitude');
      var fieldName = modal.getAttribute('data-target-field');

      if(!lat || !lng) {
        modal.querySelector('.aa-map-status').textContent = 'Please click a location on the map first.';
        return;
      }

      modal.querySelector('.aa-map-status').textContent = 'Finding nearest airport...';
      fetchJson(nearestAirportUrl(widget, lat, lng)).then(function(response) {
        var airport = response.data && response.data.length ? response.data[0] : null;
        if(!airport) {
          modal.querySelector('.aa-map-status').textContent = 'No airport found near this point.';
          return;
        }

        if(!airportById(widget, airport.id)) {
          widget.airports.push(airport);
        }
        widget.airportById[String(airport.id)] = airport;
        setAirportPickerValue(widget, fieldName, airport);
        modal.classList.remove('is-open');
      }).catch(function(error) {
        modal.querySelector('.aa-map-status').textContent = error.message || 'Unable to find nearest airport.';
      });
    });

    document.body.appendChild(modal);
    widget.airportMapModal = modal;
    return modal;
  }

  function createFilter(widget) {
    var form = document.createElement('form');
    form.className = 'aa-machine-filter';
    form.innerHTML =
      '<div class="aa-filter-field">' +
        '<label>Trip Type</label>' +
        '<select name="trip_type">' +
          '<option value="single">Single Trip</option>' +
          '<option value="round">Round Trip</option>' +
          '<option value="multi">Multi Trip</option>' +
        '</select>' +
      '</div>' +
      airportPickerHtml('departure_airport_id', 'Departure', 'aa-airports-departure') +
      airportPickerHtml('arrival_airport_id', 'Arrival', 'aa-airports-arrival') +
      '<div class="aa-filter-field aa-single-fields">' +
        '<label>Passengers</label>' +
        '<input type="number" name="adults" min="1" value="1">' +
      '</div>' +
      '<div class="aa-filter-field aa-single-fields">' +
        '<label>Date & Time</label>' +
        '<input type="datetime-local" name="date" value="' + defaultDateTimeValue() + '">' +
      '</div>' +
      '<div class="aa-filter-field">' +
        '<label>Price</label>' +
        '<select name="sort">' +
          '<option value="price_asc">Ascending</option>' +
          '<option value="price_desc">Descending</option>' +
        '</select>' +
      '</div>' +
      '<div class="aa-multi-legs"></div>' +
      '<div class="aa-filter-actions">' +
        '<button class="aa-button" type="submit">Search</button>' +
        '<button class="aa-button aa-button-secondary aa-reset" type="button">Reset</button>' +
      '</div>';

    widget.root.appendChild(form);
    widget.form = form;
    widget.multiContainer = form.querySelector('.aa-multi-legs');
    bindAirportPickers(widget, form);

    addMultiLeg(widget);

    form.querySelector('[name="trip_type"]').addEventListener('change', function() {
      var isMulti = this.value === 'multi';
      form.classList.toggle('is-multi', isMulti);
    });

    form.querySelector('.aa-reset').addEventListener('click', function() {
      form.reset();
      form.classList.remove('is-multi');
      widget.multiContainer.innerHTML = '';
      addMultiLeg(widget);
      widget.hasSearched = false;
      clearValidation(form);
      widget.root.dispatchEvent(new CustomEvent('aa:machineFilterReset', { bubbles: true }));
      if(widget.searchUrlMode && isSearchUrl()) {
        openBaseUrl();
        return;
      }
      loadMachines(widget, false);
    });

    form.querySelector('[name="sort"]').addEventListener('change', function() {
      if(widget.hasSearched && validateFilter(widget)) {
        if(widget.searchUrlMode) {
          openSearchUrl(widget);
          return;
        }
        loadMachines(widget, true);
      }
    });

    form.addEventListener('input', function() {
      clearValidation(form);
    });

    form.addEventListener('change', function(event) {
      if(event.target && event.target.name !== 'sort') {
        clearValidation(form);
      }
    });

    form.addEventListener('submit', function(event) {
      event.preventDefault();
      if(!validateFilter(widget)) {
        return;
      }
      widget.hasSearched = true;
      if(widget.searchUrlMode) {
        openSearchUrl(widget);
        return;
      }
      widget.root.dispatchEvent(new CustomEvent('aa:machineFilterSearch', { bubbles: true }));
      loadMachines(widget, true);
    });
  }

  function clearValidation(form) {
    if(!form) {
      return;
    }

    form.querySelectorAll('.aa-validation-error').forEach(function(error) {
      error.remove();
    });

    form.querySelectorAll('.aa-has-error').forEach(function(field) {
      field.classList.remove('aa-has-error');
    });
  }

  function fieldValue(field) {
    return field ? String(field.value || '').trim() : '';
  }

  function addValidationError(field, message, state) {
    if(!field) {
      return;
    }

    state.valid = false;
    if(!state.firstField) {
      state.firstField = field;
    }

    var group = field.closest('.aa-filter-field') || field.parentNode;
    if(!group) {
      return;
    }

    group.classList.add('aa-has-error');
    if(!group.querySelector('.aa-validation-error')) {
      var error = document.createElement('span');
      error.className = 'aa-validation-error';
      error.textContent = message;
      error.style.display = 'block';
      error.style.marginTop = '4px';
      error.style.color = '#dc3545';
      error.style.fontSize = '13px';
      error.style.lineHeight = '1.3';
      group.appendChild(error);
    }
  }

  function validateFilter(widget) {
    var form = widget.form;
    var state = { valid: true, firstField: null };
    var tripType = form.querySelector('[name="trip_type"]').value || 'single';

    clearValidation(form);

    if(tripType === 'multi') {
      var rows = form.querySelectorAll('.aa-multi-leg');

      if(!rows.length) {
        addValidationError(widget.addLegButton, 'Please add at least one trip.', state);
      }

      rows.forEach(function(row) {
        var departure = row.querySelector('[name="multi_departure[]"]');
        var arrival = row.querySelector('[name="multi_arrival[]"]');
        var adults = row.querySelector('[name="multi_adults[]"]');
        var date = row.querySelector('[name="multi_date[]"]');

        if(!fieldValue(departure)) {
        addValidationError(row.querySelector('[data-airport-input="multi_departure[]"]') || departure, 'Please select departure airport.', state);
      }

      if(!fieldValue(arrival)) {
        addValidationError(row.querySelector('[data-airport-input="multi_arrival[]"]') || arrival, 'Please select arrival airport.', state);
      }

        if(!fieldValue(adults) || Number(fieldValue(adults)) < 1) {
          addValidationError(adults, 'Please enter valid no. of passengers.', state);
        }

        if(!fieldValue(date)) {
          addValidationError(date, 'Please select departure date.', state);
        }
      });
    } else {
      var departure = form.querySelector('[name="departure_airport_id"]');
      var arrival = form.querySelector('[name="arrival_airport_id"]');
      var adults = form.querySelector('[name="adults"]');
      var date = form.querySelector('[name="date"]');

      if(!fieldValue(departure)) {
        addValidationError(form.querySelector('[data-airport-input="departure_airport_id"]') || departure, 'Please select departure airport.', state);
      }

      if(!fieldValue(arrival)) {
        addValidationError(form.querySelector('[data-airport-input="arrival_airport_id"]') || arrival, 'Please select arrival airport.', state);
      }

      if(!fieldValue(adults) || Number(fieldValue(adults)) < 1) {
        addValidationError(adults, 'Please enter valid no. of passengers.', state);
      }

      if(!fieldValue(date)) {
        addValidationError(date, 'Please select departure date.', state);
      }
    }

    if(!state.valid && state.firstField) {
      state.firstField.focus({ preventScroll: true });
      var target = state.firstField.closest('.aa-filter-field') || state.firstField;
      if(target && target.scrollIntoView) {
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }

    return state.valid;
  }
  function addMultiLeg(widget) {
    widget.multiLegIndex = (widget.multiLegIndex || 0) + 1;
var depListId = 'aa-airports-multi-departure-' + widget.multiLegIndex;
var arrListId = 'aa-airports-multi-arrival-' + widget.multiLegIndex;
    var row = document.createElement('div');
    row.className = 'aa-multi-leg';
    row.innerHTML =
    airportPickerHtml('multi_departure[]', 'Departure', depListId) +
    airportPickerHtml('multi_arrival[]', 'Arrival', arrListId) +
      '<div class="aa-filter-field">' +
        '<label>Passengers</label>' +
        '<input type="number" name="multi_adults[]" min="1" value="1">' +
      '</div>' +
      '<div class="aa-filter-field">' +
        '<label>Date & Time</label>' +
        '<input type="datetime-local" name="multi_date[]" value="' + defaultDateTimeValue() + '">' +
      '</div>' +
      '<button class="aa-button aa-remove-leg" type="button" title="Remove leg">-</button>';

    row.querySelector('.aa-remove-leg').addEventListener('click', function() {
      if(widget.multiContainer.querySelectorAll('.aa-multi-leg').length > 1) {
        row.remove();
      }
    });

    widget.multiContainer.appendChild(row);
    bindAirportPickers(widget, row);


    if(!widget.addLegButton) {
      widget.addLegButton = document.createElement('button');
      widget.addLegButton.type = 'button';
      widget.addLegButton.className = 'aa-button aa-button-secondary aa-add-leg';
      widget.addLegButton.textContent = 'Add Leg';
      widget.addLegButton.addEventListener('click', function() {
        addMultiLeg(widget);
      });
      widget.multiContainer.appendChild(widget.addLegButton);
    } else {
      widget.multiContainer.appendChild(widget.addLegButton);
    }

    // if(widget.airports.length) {
    //   fillAirportSelect(row.querySelector('[name="multi_departure[]"]'), widget.airports);
    //   fillAirportSelect(row.querySelector('[name="multi_arrival[]"]'), widget.airports);
    // }
  }

  function createParams(widget, fromFilter) {
    var params = new URLSearchParams();
    var form = widget.form;
    syncAirportInputs(widget);
    var tripType = form.querySelector('[name="trip_type"]').value || 'single';
    var sort = form.querySelector('[name="sort"]').value || 'price_asc';

    params.set('service', widget.service);
    params.set('trip_type', tripType);
    params.set('sort', sort);

    if(widget.apiKey) {
      params.set('api_key', widget.apiKey);
    }

    if(!fromFilter) {
      return params;
    }

    if(tripType === 'multi') {
      form.querySelectorAll('.aa-multi-leg').forEach(function(row) {
        var dep = row.querySelector('[name="multi_departure[]"]').value;
        var arr = row.querySelector('[name="multi_arrival[]"]').value;
        var adults = row.querySelector('[name="multi_adults[]"]').value || 1;
        var date = row.querySelector('[name="multi_date[]"]').value;

        params.append('multi_departure[]', dep);
        params.append('multi_arrival[]', arr);
        params.append('multi_adults[]', adults);
        params.append('multi_date[]', apiDate(date));
      });

      return params;
    }

    var departure = form.querySelector('[name="departure_airport_id"]').value;
    var arrival = form.querySelector('[name="arrival_airport_id"]').value;

    if(departure && arrival) {
      params.set('departure_airport_id', departure);
      params.set('arrival_airport_id', arrival);
      params.set('adults', form.querySelector('[name="adults"]').value || 1);
      params.set('date', apiDate(form.querySelector('[name="date"]').value));
    }

    return params;
  }

  function loadAirports(widget) {
  var url = widget.apiBase + '/api/v1/airports?limit=1000';

  if(widget.apiKey) {
    url += '&api_key=' + encodeURIComponent(widget.apiKey);
  }

  return fetchJson(url).then(function(response) {
    widget.airports = response.data || [];
    widget.airportById = {};

    widget.airports.forEach(function(airport) {
      widget.airportById[String(airport.id)] = airport;
    });

    widget.form.querySelectorAll('[data-airport-input]').forEach(function(input) {
      fillAirportDatalist(input, widget.airports);
    });

    syncAirportDisplays(widget);
  });
}

  function loadMachines(widget, fromFilter) {
    var params = createParams(widget, fromFilter);
    var url = widget.apiBase + '/api/v1/machines/search?' + params.toString();

    setLoading(widget);
    widget.list.innerHTML = '';

    return fetchJson(url).then(function(response) {
      var machines = response.data || [];
      renderMachines(widget, machines, response.meta || {});
      setStatus(widget, '', false);
    }).catch(function(error) {
      setStatus(widget, error.message || 'Unable to load machines.', true);
    });
  }

  function renderMachines(widget, machines, meta) {
    if(!machines.length) {
      widget.list.innerHTML = '<div class="aa-machine-card"><div>No machines found for this service.</div></div>';
      return;
    }

    widget.list.innerHTML = machines.map(function(machine) {
      var quote = machine.quote || null;
      var image = machine.image_url || widget.placeholderImage;
      var title = machine.name || 'Machine';
      var type = machine.type || SERVICE_LABELS[widget.service];
      var quoteHtml = quote ? quoteRows(machine, quote) : summaryRows(machine);

      return '' +
        '<article class="aa-machine-card" data-machine-id="' + escapeHtml(machine.id) + '">' +
          '<img class="aa-machine-image" src="' + escapeHtml(image) + '" alt="' + escapeHtml(title) + '" loading="lazy" decoding="async">' +
          '<div class="aa-machine-content">' +
            '<div class="aa-machine-heading">' +
              '<h3 class="aa-machine-title">' + escapeHtml(title) + '</h3>' +
              '<div class="aa-machine-kicker">' +
              '<span>' + escapeHtml(type) + '</span>' +
              (machine.subtype ? '<span>' + escapeHtml(machine.subtype) + '</span>' : '') +
              '</div>' +
            '</div>' +
            quoteHtml +
            '<div class="aa-card-actions">' +
              '<button class="aa-button aa-book-now" type="button" data-machine-id="' + escapeHtml(machine.id) + '">Book Now</button>' +
            '</div>' +
          '</div>' +
        '</article>';
    }).join('');

    widget.machineMap = {};
    machines.forEach(function(machine) {
      widget.machineMap[String(machine.id)] = machine;
    });

    widget.list.querySelectorAll('.aa-book-now').forEach(function(button) {
      button.addEventListener('click', function() {
        openBooking(widget, widget.machineMap[String(this.getAttribute('data-machine-id'))]);
      });
    });
  }

  function summaryRows(machine) {
    return '<div class="aa-machine-details">' +
      row('Base', machine.base && machine.base.name ? machine.base.name : '') +
      row('Airport', machine.base && machine.base.airport_name ? machine.base.airport_name : '') +
      row('Seats', machine.seats || '') +
      row('Speed', machine.speed ? numberText(machine.speed, ' NM/Hr') : '') +
      row('Price Per Hour', money(machine.price_per_hour)) +
      row('Lavatory', machine.lavatory ? 'Yes' : 'No') +
    '</div>';
  }

  function formatMoneyDisplay(label, value) {
  var text = String(value === null || value === undefined ? '' : value);
  var isMoneyRow = /cost|price|charges|sub total|grand total|gst|medical/i.test(String(label || ''));

  if(!isMoneyRow) {
    return text;
  }

  return text.replace(/₹\s*([0-9,]+(?:\.\d+)?)|(^|[^0-9.])([0-9,]{4,}(?:\.\d+)?)(?![0-9.])/g, function(match, rupeeAmount, prefix, bareAmount) {
    var amount = rupeeAmount || bareAmount;
    var number = Number(String(amount).replace(/,/g, ''));

    if(!isFinite(number)) {
      return match;
    }

    return (rupeeAmount ? '' : (prefix || '')) + String.fromCharCode(8377) + Math.round(number).toLocaleString('en-IN');
  });
}

  function quoteRows(machine, quote) {
if(Array.isArray(quote.display_rows) && quote.display_rows.length) {
  return '<div class="aa-machine-details">' + quote.display_rows.map(function(item) {
    return row(item.label || '', formatMoneyDisplay(item.label || '', item.value));
  }).join('') + '</div>';
}

    var gstLabel = 'GST' + (quote.gst_rate !== undefined && quote.gst_rate !== null ? ' (' + Number(quote.gst_rate).toLocaleString('en-IN') + '%)' : '');
    var isAirAmbulance = Number(machine.type_id || 0) === 3;
    var html = '<div class="aa-machine-details">' +
      row('Base', machine.base && machine.base.name ? machine.base.name : '') +
      row('Route', quote.route) +
      row('Flying Cost', money(quote.flying_cost) + ' (For ' + (quote.flight_time || '-') + '.)') +
      row('Distance', numberText(quote.distance_nm, ' NM')) +
      row('Airport Handling Charges', money(quote.handling_charges));

    if(Number(quote.crew_handling || 0) > 0) {
      html += row('Crew Handling Charges', money(quote.crew_handling));
    }

    html += row('Sub Total', money(quote.sub_total));

    if(isAirAmbulance && Number(quote.medical_cost || 0) > 0) {
      html += row('Fixed Medical Team Cost', money(quote.medical_cost));
    } else {
      html += row(gstLabel, money(quote.gst_amount));
    }

    html += row('Grand Total', money(quote.grand_total)) + '</div>';

    return html;
  }

  function row(label, value) {
    if(value === null || value === undefined || value === '') {
      value = '-';
    }

    return '<div class="aa-machine-row"><b>' + escapeHtml(label) + '</b><span>: ' + escapeHtml(value) + '</span></div>';
  }

  function openBooking(widget, machine) {
    if(!machine) {
      return;
    }

    document.dispatchEvent(new CustomEvent('aa:machineBookNow', {
      detail: {
        service: widget.service,
        service_label: SERVICE_LABELS[widget.service],
        machine: machine
      }
    }));

    if(widget.formSelector && fillExistingForm(widget, machine)) {
      return;
    }

    openFallbackModal(widget, machine);
  }

  function fillExistingForm(widget, machine) {
    var form = document.querySelector(widget.formSelector);

    if(!form) {
      return false;
    }

    var message = buildBookingMessage(widget, machine);

    setFormValue(form, ['service', 'service_type', 'package', 'enquiry_for'], SERVICE_LABELS[widget.service]);
    setFormValue(form, ['machine', 'machine_name', 'plane', 'plane_name'], machine.name || '');
    setFormValue(form, ['machine_id', 'plane_id'], machine.id || '');
    setFormValue(form, ['message', 'requirement', 'description'], message);

    form.dispatchEvent(new CustomEvent('aa:machineFormFilled', {
      detail: {
        service: widget.service,
        machine: machine
      }
    }));

    form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return true;
  }

  function setFormValue(form, names, value) {
    names.some(function(name) {
      var field = form.querySelector('[name="' + name + '"]');

      if(!field) {
        return false;
      }

      field.value = value;
      field.dispatchEvent(new Event('input', { bubbles: true }));
      field.dispatchEvent(new Event('change', { bubbles: true }));
      return true;
    });
  }

  function buildBookingMessage(widget, machine) {
    var quote = machine.quote || {};
    var lines = [
      'Service: ' + SERVICE_LABELS[widget.service],
      'Machine: ' + (machine.name || ''),
      'Type: ' + (machine.type || ''),
      'Subtype: ' + (machine.subtype || '')
    ];

    if(quote.route) {
      lines.push('Route: ' + quote.route);
      lines.push('Flight Time: ' + quote.flight_time);
      lines.push('Grand Total: ' + money(quote.grand_total));
    }

    return lines.join('\n');
  }

  function openFallbackModal(widget, machine) {
    var modal = getModal(widget);
    var form = modal.querySelector('form');
    var title = modal.querySelector('.aa-booking-machine');

    title.textContent = machine.name || 'Machine';
    form.querySelector('[name="service"]').value = SERVICE_LABELS[widget.service];
    form.querySelector('[name="machine_id"]').value = machine.id || '';
    form.querySelector('[name="machine_name"]').value = machine.name || '';
    form.querySelector('[name="message"]').value = buildBookingMessage(widget, machine);

    modal.classList.add('is-open');
  }

  function getModal(widget) {
    if(widget.modal) {
      return widget.modal;
    }

    var modal = document.createElement('div');
    modal.className = 'aa-modal-backdrop';
    modal.innerHTML =
      '<div class="aa-modal" role="dialog" aria-modal="true">' +
        '<div class="aa-modal-header">' +
          '<h3>Book Now: <span class="aa-booking-machine"></span></h3>' +
          '<button class="aa-close" type="button" aria-label="Close">&times;</button>' +
        '</div>' +
        '<form class="aa-booking-form" method="post">' +
          '<input type="hidden" name="service">' +
          '<input type="hidden" name="machine_id">' +
          '<input type="hidden" name="machine_name">' +
          '<div class="aa-filter-field"><label>Name</label><input name="name" required></div>' +
          '<div class="aa-filter-field"><label>Mobile No</label><input name="mobile" required></div>' +
          '<div class="aa-filter-field"><label>Date</label><input type="date" name="date"></div>' +
          '<div class="aa-filter-field"><label>Time</label><input type="time" name="time"></div>' +
          '<div class="aa-filter-field"><label>Passengers</label><input type="number" name="adults" min="1" value="1"></div>' +
          '<div class="aa-filter-field aa-full"><label>Message</label><textarea name="message" rows="5"></textarea></div>' +
          '<div class="aa-full"><button class="aa-button" type="submit">Enquiry Now</button></div>' +
        '</form>' +
      '</div>';

    modal.querySelector('.aa-close').addEventListener('click', function() {
      modal.classList.remove('is-open');
    });

    modal.addEventListener('click', function(event) {
      if(event.target === modal) {
        modal.classList.remove('is-open');
      }
    });

    var form = modal.querySelector('form');

    if(widget.enquiryAction) {
      form.action = widget.enquiryAction;
    } else {
      form.addEventListener('submit', function(event) {
        event.preventDefault();
        document.dispatchEvent(new CustomEvent('aa:machineEnquirySubmit', {
          detail: {
            form: form,
            values: Object.fromEntries(new FormData(form).entries())
          }
        }));
        alert('Please connect this form with the Accretion Aviation enquiry endpoint.');
      });
    }

    document.body.appendChild(modal);
    widget.modal = modal;
    return modal;
  }

  function init(root) {
    var apiBase = (root.getAttribute('data-api-base') || currentScriptOrigin()).replace(/\/$/, '');
    var widget = {
      root: root,
      service: inferService(root),
      apiBase: apiBase,
      apiKey: root.getAttribute('data-api-key') || '',
      formSelector: root.getAttribute('data-form-selector') || '',
      enquiryAction: root.getAttribute('data-enquiry-action') || '',
      placeholderImage: root.getAttribute('data-placeholder-image') || (apiBase + '/img/plane-2image.jpg'),
      airports: [],
      airportById: {},
      airportMap: null,
      airportMarker: null,
      airportMapModal: null,
      machineMap: {},
      hasSearched: false,
      searchUrlMode: root.getAttribute('data-search-url-mode') === '1'
    };

    widget.root.classList.add('aa-machine-widget');
    widget.root.innerHTML = '';

    createFilter(widget);

    widget.status = document.createElement('div');
    widget.status.className = 'aa-machine-status';
    widget.root.appendChild(widget.status);

    widget.list = document.createElement('div');
    widget.list.className = 'aa-machine-list';
    widget.root.appendChild(widget.list);

    setLoading(widget);

    loadAirports(widget)
      .catch(function(error) {
        setStatus(widget, 'Airport filter failed: ' + error.message, true);
      })
      .finally(function() {
        if(applyUrlParams(widget) && validateFilter(widget)) {
          widget.hasSearched = true;
          widget.root.dispatchEvent(new CustomEvent('aa:machineFilterSearch', { bubbles: true }));
          loadMachines(widget, true);
          return;
        }
        loadMachines(widget, false);
      });
  }

  function boot() {
    document.querySelectorAll('[data-machine-api-widget]').forEach(init);
  }

  if(document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
