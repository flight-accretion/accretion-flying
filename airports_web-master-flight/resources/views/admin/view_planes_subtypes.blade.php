@extends('layouts.admin_header')
@section('content')
  <section class="content plane-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Planes SubType</h3>
            <div id="search-plane" class="pull-right"></div>
          </div>
          <div class="box-body">
            <table id="table-plane" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="5%" class="text-center">Sr. No.</th>
                  <th>Plane Type</th>
                  <th>Plane SubType</th>
                  <th>Status</th>
                  <th width="25%" class="text-center">Options</th>
                </tr>
              </thead>
              <tbody>
              <?php $i = 1; ?>
                @foreach($subtypes as $plane)
                  <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $types[$plane->plane_type] }}</td>
                    <td>{{ $plane->sub_type }}</td>
                    <td class="text-center">  
                      @if($plane->status != 0)
                      <button type="button" class="btn label label-success">   Active   </button>
                      @else
                      <button type="button" class="btn label label-danger">   Deactive   </button>
                      @endif
                    </td>
                    <td width="25%" class="text-center">
                      <a href="/subtype/edit?subtype-id={{ $plane->id }}" type="button" class="btn label label-primary">Edit</a>
                      <a type="button" class="btn label label-danger delete-subtype-link" data-plane-id="{{ $plane->id }}">Delete</a>
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
          <form id="form-delete-plane" role="form" method="POST" action="{{ url('/subtype/delete') }}" novalidate>
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
                    <h4>Are you sure to delete this plane subtype?</h4>
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
				"infoEmpty": "<center><div class='text-info'><br>No plane subtype available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No plane subtype available</div></center>",
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
      $(document).on('click', '.delete-subtype-link', function(ev) {
        ev.preventDefault();	
        var plane_id = $(this).data('plane-id'); 
        $('#plane-id').val(plane_id);
        $('#delete-plane-modal').modal('show');
      });   
		});
	</script>
@endsection