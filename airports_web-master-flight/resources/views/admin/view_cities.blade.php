@extends('layouts.admin_header')
@section('content')
  <section class="content city-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Cities</h3>
            <div id="search-city" class="pull-right"></div>
          </div>
          <div class="box-body">
            <table id="table-city" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="5%" class="text-center">Sr. No.</th>
                  <th>City</th>
                  <th>State</th>
                  <th width="15%" class="text-center">Options</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; ?>
                @foreach($cities as $city)
                  <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $city->name }}</td>
                    <td>{{ isset($states[$city->state_id]) ? $states[$city->state_id] : '-' }} </td>
                    <td width="15%" class="text-center">
                      <a href="/city/edit?city-id={{ $city->id }}" type="button" class="btn label label-primary">Edit</a>
                      <a href="/city/view?city-id={{ $city->id }}" type="button" class="btn label label-info">View</a>
                      <a type="button" class="btn label label-danger delete-city-link" data-city-id="{{ $city->id }}">Delete</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <div class="row">
              <div id="page-link-wrapper" class="col-md-12 text-center"></div>	
            </div>
          </div>
        </div>
      </div> 
    </div>
    
    <!-- ========== DELETE MODEL START ========== -->  
    <div class="modal fade" id="delete-city-modal" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-md modal-teal">
        <div class="modal-content">
          <form id="form-delete-city" role="form" method="POST" action="{{ url('/city/delete') }}" novalidate>
            <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h2 class="modal-title text-center color-skyblue">City</h2>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="text-center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">               
                    <input type="hidden" id="city-id" name="city-id">
                    <h4>Are you sure to delete this city?</h4>
                  </div>
                </div>
              </div>      
            </div>
            <div class="modal-footer">
              <div class="row">
                <div class="col-md-8 col-md-offset-4">
                  <input type="submit" class="btn btn-primary" value="Yes">
                  <a href="/" class="btn btn-primary" data-dismiss="modal">No</a> 
                </div>
              </div>            
            </div>
          </form>
        </div>
      </div> 
    </div>
    <!-- ========== DELETE MODEL END ========== -->
    
    <!-- ========== DELETE WARNING MODEL START ========== -->  
    <div class="modal fade" id="delete-warning-city-modal" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-md modal-teal">
        <div class="modal-content">
          <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h2 class="modal-title text-center color-skyblue">City</h2>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-10 col-md-offset-1 text-center">
                <h4>This city can not be deleted as it is used either in airports or in planes.</h4>
              </div>
            </div>      
          </div>
          <div class="modal-footer">
            <div class="row">
              <div class="col-md-8 col-md-offset-4">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
              </div>
            </div>
          </div>
        </div>
      </div> 
    </div>
    <!-- ========== DELETE WARNING MODEL END ========== -->
  </section>
  <script type="text/javascript">
		$(function(){
      
      var error = <?php echo (Session::has('error')) ? 1 : 0 ?>; 
      if(error == 1) {        
        $('#delete-warning-city-modal').modal('show');
      }
      
			$('#table-city').dataTable( {				
				"bLengthChange": false,
				"iDisplayLength": 6,
				"infoEmpty": "<center><div class='text-info'><br>No city available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No city available</div></center>",
          "sSearch": "",
          "oPaginate": {
            "sNext": '>',
            "sLast": '>|',
            "sFirst": '|<',
            "sPrevious": '<'
          }
        },
        "bSort" : true  					 
			});
			$('.dataTables_filter input').attr("placeholder", "Search");
			$('.dataTables_filter input').removeClass("input-sm");
      $('.dataTables_filter input').addClass("form-control");
			$("#table-city_info").detach().appendTo('#page-link-wrapper');
			$("#table-city_paginate").detach().appendTo('#page-link-wrapper');
			$("#table-city_filter").detach().appendTo('#search-city');
      
      //Delete city   
      $(document).on('click', '.delete-city-link', function(ev) {
        ev.preventDefault();	
        var city_id = $(this).data('city-id'); 
        $('#city-id').val(city_id);
        $('#delete-city-modal').modal('show');
      });   
		});
	</script>
@endsection
