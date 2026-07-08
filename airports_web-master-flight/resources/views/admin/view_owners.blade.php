@extends('layouts.admin_header')
@section('content')
  <section class="content owner-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Owners</h3>
            <div id="search-owner" class="pull-right"></div>
          </div>
          <div class="box-body">
            <table id="table-owner" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="5%" class="text-center">Sr. No.</th>
                  <th>Name</th>
                  <th>Contact</th>
                  <th>Email</th>
                  <th width="15%" class="text-center">Options</th>
                </tr>
              </thead>
              <tbody>
              <?php $i = 1; ?>
                @foreach($owners as $owner)
                  <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $owner->name }}</td>
                    <td>{{ $owner->contact_number_1 }}</td>
                    <td>{{ $owner->email_1 }}</td>
                    <td width="15%" class="text-center">
                      <a href="/owner/edit?owner-id={{ $owner->id }}" type="button" class="btn label label-primary">Edit</a>
                      <a href="/owner/view?owner-id={{ $owner->id }}" type="button" class="btn label label-info">View</a>
                      <a type="button" class="btn label label-danger delete-owner-link" data-owner-id="{{ $owner->id }}">Delete</a>
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
    <div class="modal fade" id="delete-owner-modal" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-md modal-teal">
        <div class="modal-content">
          <form id="form-delete-owner" role="form" method="POST" action="{{ url('/owner/delete') }}" novalidate>
            <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h2 class="modal-title text-center color-skyblue">Owner</h2>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="text-center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">               
                    <input type="hidden" id="owner-id" name="owner-id">
                    <h4>Are you sure to delete this owner?</h4>
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
			$('#table-owner').dataTable( {				
				"bLengthChange": false,
				"iDisplayLength": 6,
				"infoEmpty": "<center><div class='text-info'><br>No owner available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No owner available</div></center>",
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
			$("#table-owner_info").detach().appendTo('#page-link-wrapper');
			$("#table-owner_paginate").detach().appendTo('#page-link-wrapper');
			$("#table-owner_filter").detach().appendTo('#search-owner');
      
      //Delete owner   
      $(document).on('click', '.delete-owner-link', function(ev) {
        ev.preventDefault();	
        var owner_id = $(this).data('owner-id'); 
        $('#owner-id').val(owner_id);
        $('#delete-owner-modal').modal('show');
      });   
		});
	</script>
@endsection
