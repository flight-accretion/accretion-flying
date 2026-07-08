@extends('layouts.admin_header')
@section('content')
  <section class="content plane-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Machines</h3>
            <div id="search-plane" class="pull-right"></div>
          </div>
          <div class="box-body">
            <table id="table-plane" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="5%" class="text-center">Sr. No.</th>
                  <th>Name</th>
                  <th>Call Sign(VT)</th>
                  <th>Type</th>
                  <th>SubType</th>
                  <th>Owner</th>
                  <th>Owner Contact</th>
                  <th class="text-center">Price/Hour ( <i class="fa fa-rupee"></i> )</th>
                  <th class="text-center">Speed ( nm/hr )</th>
                  <th>City</th>
                  <th width="15%" class="text-center">Options</th>
                </tr>
              </thead>
              <tbody>
              <?php $i = 1; ?>
                @foreach($planes as $plane)
                  <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $plane->name }}</td>
                    <td>{{ $plane->Call_Sign }}</td>
                    <td>{{ $types[$plane->type_id]  }} </td>
                    @if(isset($plane_subtypes[$plane->subtype]))
                    <td>{{ $plane_subtypes[$plane->subtype]  }} </td>
                    @else
                      <td>Null</td>
                    @endif
                    @if($plane->owner_id != 0)
                      <td>{{ $owners[$plane->owner_id] }}</td>
                    @else
                      <td></td>
                    @endif
                    @if($plane->owner_id != 0)
                      <td>{{ $owner_contact[$plane->owner_id] }}</td>
                    @else
                      <td></td>
                    @endif
                    <td class="text-center">{{ $plane->price_per_hour }}</td>
                    <td class="text-center">{{ $plane->speed }}</td>
                    <td>{{ $cities[$plane->city_id] }}</td>
                    <td width="15%" class="text-center">
                      <a href="/plane/edit?plane-id={{ $plane->id }}" type="button" class="btn label label-primary">Edit</a>
                      <a href="/plane/view?plane-id={{ $plane->id }}" type="button" class="btn label label-info">View</a>
                      <a type="button" class="btn label label-danger delete-plane-link" data-plane-id="{{ $plane->id }}">Delete</a>
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
    <div class="modal fade" id="delete-plane-modal" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-md modal-teal">
        <div class="modal-content">
          <form id="form-delete-plane" role="form" method="POST" action="{{ url('/plane/delete') }}" novalidate>
            <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h2 class="modal-title text-center color-skyblue">Plane</h2>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="text-center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">               
                    <input type="hidden" id="plane-id" name="plane-id">
                    <h4>Are you sure to delete this plane?</h4>
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
			$('#table-plane').dataTable( {				
				"bLengthChange": false,
				"iDisplayLength": 6,
				"infoEmpty": "<center><div class='text-info'><br>No plane available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No plane available</div></center>",
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
			$("#table-plane_info").detach().appendTo('#page-link-wrapper');
			$("#table-plane_paginate").detach().appendTo('#page-link-wrapper');
			$("#table-plane_filter").detach().appendTo('#search-plane');
      
      //Delete plane   
      $(document).on('click', '.delete-plane-link', function(ev) {
        ev.preventDefault();	
        var plane_id = $(this).data('plane-id'); 
        $('#plane-id').val(plane_id);
        $('#delete-plane-modal').modal('show');
      });   
		});
	</script>
@endsection