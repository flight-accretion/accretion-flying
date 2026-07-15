<?php $__env->startSection('content'); ?>
   <header>
    <div class="header-content">
      <div class="header-content-inner">
        <form id="search-planes" role="form" method="GET" action="">
          <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"> 
          <input id="old-lat" type="hidden" name="old-lat" value="<?php echo e($lat); ?>" />
          <input id="old-long" type="hidden" name="old-long" value="<?php echo e($long); ?>" />
          <input id="old-location" type="hidden" name="old-location" value="<?php echo e($location_name); ?>" />
          <input id="old-flower-shower-time" type="hidden" name="flower-shower-time" value="<?php echo e(isset($flower_shower_time) ? $flower_shower_time : 0); ?>" />
        </form>
				<div class="row">
					<div class="col-md-12 no-padding">
						<h1>Available Machines</h1>
					</div>
				</div>
      </div>
    </div>
  </header>
  
  <section id="contact">
    <div class="container">
      <div class="row">
				<div class="col-md-2 col-md-offset-10">
          <div class="form-group">
            <label for="price-filter">Distance</label>
            <select class="form-control" id="price-filter">
              <option id="price-asc" value="0">Ascending</option>
              <option id="price-desc" value="1">Descending</option>
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

<script>
  window.initialize = window.initialize || function () {};
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzltLLaZzQXcGVsgfBHNa30FkSLIPqpaA&libraries=geometry,places&callback=initialize" async defer></script>
<script>
  $(function () {
   var plane_types = <?php echo json_encode($plane_types) ?>; 
   var owners = <?php echo json_encode($owners) ?>; 
   var selectedLocation = $('#old-location').val() || 'Selected Location';
    getPlaneList();

    function numberValue(value, fallback) {
      var parsed = parseFloat(value);
      return isNaN(parsed) ? (fallback || 0) : parsed;
    }

    function flowerShowerMinutes() {
      return Math.max(0, Math.round(numberValue($('#old-flower-shower-time').val())));
    }

    function formatDuration(totalMinutes) {
      totalMinutes = Math.max(0, Math.round(numberValue(totalMinutes)));
      return Math.floor(totalMinutes / 60) + ' Hrs ' + (totalMinutes % 60) + ' min.';
    }

    function fuelHaultMinutes(totalMinutes, plane) {
      var remainingMinutes = Math.max(0, Math.round(numberValue(totalMinutes)));
      var fuelMinutes = 0;

      if(String(plane.type_id) !== '2') {
        return 0;
      }

      while(remainingMinutes > 90) {
        fuelMinutes += 30;
        remainingMinutes -= 90;
      }

      return fuelMinutes;
    }

    function normalizeGstRate(value) {
      var rate = parseFloat(value);
      return !isNaN(rate) && rate > 0 ? rate : 18;
    }

    function gstRateLabel(value) {
      var rate = normalizeGstRate(value);
      return rate % 1 === 0 ? rate.toFixed(0) : rate.toFixed(2);
    }

    function formatMoney(value) {
      return numberValue(value).toFixed();
    }

    function toRadians(value) {
      return value * Math.PI / 180;
    }

    function calcDistance(p1, p2) {
      var earthRadiusKm = 6371;
      var lat1 = toRadians(numberValue(p1.lat));
      var lat2 = toRadians(numberValue(p2.lat));
      var deltaLat = toRadians(numberValue(p2.lat) - numberValue(p1.lat));
      var deltaLng = toRadians(numberValue(p2.lng) - numberValue(p1.lng));
      var a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
        Math.cos(lat1) * Math.cos(lat2) *
        Math.sin(deltaLng / 2) * Math.sin(deltaLng / 2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return earthRadiusKm * c;
    }

  function flightMinutes(
    distanceNm,
    speed,
    speedCoefficient
) {
    distanceNm = numberValue(distanceNm);
    speed = numberValue(speed);
    speedCoefficient =
        numberValue(speedCoefficient, 1);

    if(distanceNm <= 0 || speed <= 0) {
        return 0;
    }

    if(speedCoefficient <= 0) {
        speedCoefficient = 1;
    }

    var reducedSpeedPerMinute =
        (speed * speedCoefficient) / 60;

    var normalSpeedPerMinute =
        speed / 60;

    if(distanceNm > 200) {
        return (
            200 / reducedSpeedPerMinute
        ) + (
            (distanceNm - 200) /
            normalSpeedPerMinute
        );
    }

    return distanceNm /
        reducedSpeedPerMinute;
}

    function flowerShowerTotals(plane) {
      var basePoint = {
        lat: numberValue(plane.avail_planes_lat),
        lng: numberValue(plane.avail_planes_lng)
      };
      var servicePoint = {
        lat: numberValue(plane.selected_lat || $('#old-lat').val()),
        lng: numberValue(plane.selected_lng || $('#old-long').val())
      };
      var speedCoefficient = numberValue(plane.speed_coefficient, 1);
      var pricePerHour = numberValue(plane.price_per_hour || plane.price);
      var oneWayDistanceNm = 0.539957 * calcDistance(basePoint, servicePoint);
      var outMinutes = flightMinutes(oneWayDistanceNm, plane.speed, speedCoefficient);
      var returnMinutes = flightMinutes(oneWayDistanceNm, plane.speed, speedCoefficient);
      var rawMinutes = outMinutes + returnMinutes;
      var additionalMinutes = rawMinutes > 0 && rawMinutes < 120 ? 120 - rawMinutes : 0;
      var flightChargeMinutes = rawMinutes + additionalMinutes;
      var flowerMinutes = flowerShowerMinutes();
      var chargeMinutes = flightChargeMinutes + flowerMinutes;
      var fuelMinutes = fuelHaultMinutes(chargeMinutes, plane);
      var totalChargeMinutes = chargeMinutes + fuelMinutes;
      var handlingCharges = (typeof plane.handling_charges !== 'undefined')
        ? numberValue(plane.handling_charges)
        : 0;

      var flightCost = (totalChargeMinutes / 60) * pricePerHour;
      var subTotal = flightCost + handlingCharges;
      var tax = normalizeGstRate(plane.tax);
      var taxAmount = (tax / 100) * subTotal;
      var grandTotal = subTotal + taxAmount;
      var roundedMinutes = Math.round(totalChargeMinutes);

      return {
        distance: oneWayDistanceNm,
        flightMinutes: flightChargeMinutes,
        flowerShowerMinutes: flowerMinutes,
        fuelHaultMinutes: fuelMinutes,
        hours: Math.floor(roundedMinutes / 60),
        minutes: roundedMinutes % 60,
        flightCost: flightCost,
        handlingCharges: handlingCharges,
        subTotal: subTotal,
        tax: tax,
        taxAmount: taxAmount,
        grandTotal: grandTotal
      };
    }
    
    //search-planes
    function getPlaneList()
    {
			$.ajax({
				url: '/plane/plane-list-by-flower-shower?lat='+ $('#old-lat').val() +'&long='+ $('#old-long').val()+'&filter-id='+ $('#price-filter').val()+'&location='+encodeURIComponent(selectedLocation)+'&flower-shower-time='+encodeURIComponent(flowerShowerMinutes()),
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
              list += '<div class="col-md-6">';
              list += '<i class="fa fa-picture-o fa-4x"></i>';
              list += '</div>';
            }
            list += ' <div class="col-md-6">';
            list += ' <div class="col-md-6 text-left no-padding">';
            list += '<h4 class="plane-name">'+plane_list[i].name+(plane_list[i].Call_Sign ? ' {'+plane_list[i].Call_Sign+'}' : '')+'</h4>';
            list += '</div>';
            list += ' <div class="col-md-6 text-right no-padding">'; 
            list += '<h5 class="plane-type">'+plane_types[plane_list[i].type_id]+'</h5>';
            list += '</div>';
            list += '<div class="clearfix"></div>';
            var owner = owners[plane_list[i].owner_id] || {};
            var totals = flowerShowerTotals(plane_list[i]);
            list += '<h5><label class="fixed-width"><b>Base</b></label> : '+plane_list[i].city_name+'</h5>';
            list += '<h5><label class="fixed-width"><b>Route</b></label> : '+(plane_list[i].path || (plane_list[i].city_name+' > '+selectedLocation+' > '+plane_list[i].city_name))+'</h5>';
            list += '<h5><label class="fixed-width"><b>Flying Cost</b></label> : <i class="fa fa-rupee"></i>'+formatMoney(totals.flightCost)+' (For '+totals.hours+' Hrs '+totals.minutes+' min.)</h5>';
            list += '<h5><label class="fixed-width"><b>Flight Time</b></label> : '+formatDuration(totals.flightMinutes)+'</h5>';
            list += '<h5><label class="fixed-width"><b>Flower Shower Time</b></label> : '+totals.flowerShowerMinutes+' min.</h5>';
            if(totals.fuelHaultMinutes > 0) {
              list += '<h5><label class="fixed-width"><b>Fuel Hault</b></label> : '+formatDuration(totals.fuelHaultMinutes)+'</h5>';
            }
            list += '<h5><label class="fixed-width"><b>Distance</b></label> : '+totals.distance.toFixed(2)+' NM</h5>';
            list += '<h5><label class="fixed-width"><b>Airport Handling Charges</b></label> : <i class="fa fa-rupee"></i>'+formatMoney(totals.handlingCharges)+'</h5>';
            list += '<h5><label class="fixed-width"><b>Sub Total</b></label> : <i class="fa fa-rupee"></i>'+formatMoney(totals.subTotal)+'</h5>';
            list += '<h5><label class="fixed-width"><b>GST ('+gstRateLabel(totals.tax)+'%)</b></label> : <i class="fa fa-rupee"></i>'+formatMoney(totals.taxAmount)+'</h5>';
            list += '<h5 class="grand-total" data-id="'+plane_list[i].id+'" data-total="'+formatMoney(totals.grandTotal)+'"><label class="fixed-width"><b>Grand Total</b></label> : <i class="fa fa-rupee"></i>'+formatMoney(totals.grandTotal)+'</h5>';
            // list += '<h5><label class="fixed-width"><b>Owner</b></label> : '+(owner.name || '-')+'</h5>';
            // list += '<h5><label class="fixed-width"><b>Owner Contact</b></label> : '+(owner.contact_number_1 || '-')+'</h5>';
            // list += '<h5><label class="fixed-width"><b>Owner Email</b></label> : '+(owner.email_1 || '-')+'</h5>';
            // list += '<h5><label class="fixed-width"><b>Price (Per hour)</b></label> : <i class="fa fa-rupee"></i>'+formatMoney(plane_list[i].price_per_hour || plane_list[i].price)+'</h5>';
            // list += '<h5><label class="fixed-width"><b>Seats</b></label> : '+plane_list[i].seats+'</h5>';
            var detailsUrl = '/plane/plane?flower-shower=1'
              + '&plane-id='+plane_list[i].id
              + '&selected-lat='+encodeURIComponent(plane_list[i].selected_lat || $('#old-lat').val())
              + '&selected-long='+encodeURIComponent(plane_list[i].selected_lng || $('#old-long').val())
              + '&selected-location='+encodeURIComponent(selectedLocation)
              + '&flower-shower-time='+encodeURIComponent(totals.flowerShowerMinutes)
              + '&speed_coefficient='+encodeURIComponent(plane_list[i].speed_coefficient || 1);

            list += '<div class="col-md-12 text-right">';
            list += '<a href="'+detailsUrl+'" class="btn btn-view">View Details</a>';
            list += '</div>';
            list += '<div class="col-md-12 text-right">';
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
		
		function sortMachines(){
			var div_json = new Array();
			$.each($('.grand-total'), function(index, valxue){ 	
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
		
		$("#price-filter").change(function(){
			getPlaneList();
		});
  });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.plane_header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\accretion-flying\airports_web-master-flight\resources\views/plane_list_by_flower_shower.blade.php ENDPATH**/ ?>