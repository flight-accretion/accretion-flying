@extends('layouts.admin_header')
@section('content')
  <section class="content route-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Routes</h3>
            <div id="search-route" class="pull-right"></div>
          </div>
          <div class="box-body">
            <table id="table-route" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="5%" class="text-center">Sr. No.</th>
                  <th>From Location</th>
                  <th>To Location</th>
                  <th>Plane</th>
                  <th>Time(In minutes)</th>
                  <!--th>Price</th-->
                  <th>Distance(In NM)</th>
                  <th width="15%" class="text-center">Options</th>
                </tr>
              </thead>
              <tbody>
              <?php $i = 1; ?>
                @foreach($routes as $route)
                  <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $airports[$route->location_1] }}</td>
                    <td>{{ $airports[$route->location_2] }}</td>
                    @if($route->plane_id != 0)
                      <td>{{ (isset($planes[$route->plane_id])) }}</td>
                    @else
                      <td>-</td>
                    @endif
                    <td>{{ $route->time }}</td>
                    <td>{{ $route->distance }}</td>
                    <!--td>{{ $route->price }}</td-->
                    <td width="15%" class="text-center">
                      <a href="/route/edit?route-id={{ $route->id }}" type="button" class="btn label label-primary">Edit</a>
                      <a href="/route/view?route-id={{ $route->id }}" type="button" class="btn label label-info">View</a>
                      <a type="button" class="btn label label-danger delete-route-link" data-route-id="{{ $route->id }}">Delete</a>
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
    <div class="modal fade" id="delete-route-modal" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-md modal-teal">
        <div class="modal-content">
          <form id="form-delete-route" role="form" method="POST" action="{{ url('/route/delete') }}" novalidate>
            <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h2 class="modal-title text-center color-skyblue">Route</h2>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="text-center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">               
                    <input type="hidden" id="route-id" name="route-id">
                    <h4>Are you sure to delete this route?</h4>
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
  </section>
  <script type="text/javascript">
		$(function(){
			$('#table-route').dataTable( {				
				"bLengthChange": false,
				"iDisplayLength": 6,
				"infoEmpty": "<center><div class='text-info'><br>No route available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No route available</div></center>",
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
			$("#table-route_info").detach().appendTo('#page-link-wrapper');
			$("#table-route_paginate").detach().appendTo('#page-link-wrapper');
			$("#table-route_filter").detach().appendTo('#search-route');
      
      //Delete route   
      $(document).on('click', '.delete-route-link', function(ev) {
        ev.preventDefault();	
        var route_id = $(this).data('route-id'); 
        $('#route-id').val(route_id);
        $('#delete-route-modal').modal('show');
      });   
		});
	</script>
@endsection