@extends('layouts.plane_header')
@section('content')
   <header>
    <div class="header-content">
      <div class="header-content-inner">
        <form id="search-planes" role="form" method="GET" action="">
          <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
          <input id="old-lat" type="hidden" name="old-lat" value="{{ $lat }}" />
          <input id="old-long" type="hidden" name="old-long" value="{{ $long }}" />
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
    getPlaneList();
    
    //search-planes
    function getPlaneList()
    {
			$.ajax({
				url: '/plane/plane-list-by-flower-shower?lat='+ $('#old-lat').val() +'&long='+ $('#old-long').val()+'&filter-id='+ $('#price-filter').val(),
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
            list += '<h5><label class="fixed-width"><b>Base</b></label> : '+plane_list[i].city_name+'</h5>';
            list += '<h5><label class="fixed-width"><b>Owner</b></label> : '+owners[plane_list[i].owner_id].name+'</h5>';
            list += '<h5><label class="fixed-width"><b>Owner Contact</b></label> : '+owners[plane_list[i].owner_id].contact_number_1+'</h5>';
            list += '<h5><label class="fixed-width"><b>Owner Email</b></label> : '+owners[plane_list[i].owner_id].email_1+'</h5>';
            list += '<h5 class="grand-total" data-id="'+plane_list[i].id+'" data-total="'+plane_list[i].price+'"><label class="fixed-width"><b>Price (Per hour)</b></label> : <i class="fa fa-rupee"></i>'+plane_list[i].price+'</h5>';
            list += '<h5><label class="fixed-width"><b>Seats</b></label> : '+plane_list[i].seats+'</h5>';
            list += '<div class="col-md-12 text-right">';
            list += '</div>';
            list += '</div>';
            list += '</div>';
            list += '</div>';
          }
          $('#plane-list').html(list);
					//sortMachines();
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
      var distance = (google.maps.geometry.spherical.computeDistanceBetween(p1, p2) / 1000);
      //console.log("IN calc distance: ", distance);
      return distance;
    }
		
		$("#price-filter").change(function(){
			getPlaneList();
		});
  });
</script>
@endsection
