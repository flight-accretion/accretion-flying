@extends('layouts.admin_header')
@section('content')
  <section class="content user-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Users</h3>
            
            <div id="search-user" class="pull-right"></div>
          </div>
          <div class="box-body">
          <a type="button" class="btn btn-primary pull-right" href="/user/export">&nbsp;&nbsp;Export List&nbsp;&nbsp;</a>&nbsp;&nbsp;
            <table id="table-user" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="5%" class="text-center">Sr. No.</th>
                  <th><b>Name</b></th>
                  <th><b>City</b></th>
                  <th><b>Email</b></th>
                  <th><b>Phone</b></th>
                  <th><b>Points</b></th>
                  <th><b>Status</b></th>
                  <th width="15%" class="text-center">Options</th>
                </tr>
              </thead>
              <tbody>
              <?php $i = 1; ?>
                @foreach($users as $user)
                  <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->city }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->contact_number }}</td>
                    <td>{{ $user->points }}</td>
                    <td>{{ $user->status == 1 ? 'Active' : 'Deactive' }}</td>
                    <td width="15%" class="text-center">
                      <a href="/user/edit?id={{ $user->id }}" type="button" class="btn label label-primary">Edit</a>
											@if($user->status == 1)
												<a type="button" class="btn label label-danger deactivate-link" data-id="{{ $user->id }}">Deactivate</a>
											@else
												<a type="button" class="btn label label-success activate-link" data-id="{{ $user->id }}">&nbsp;&nbsp;Activate&nbsp;&nbsp;</a>
											@endif
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
    
		<div class="modal fade" id="confirm" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
						<h4 class="modal-title text-center">Confirmation</h4>
					</div>
					<div class="modal-body">
						Are you sure?
					</div>
					<div class="modal-footer">
						<button type="button" data-dismiss="modal" class="btn btn-primary" id="yes">Yes</button>
						<button type="button" data-dismiss="modal" class="btn">NO</button>
					</div>
				</div>
			</div>
		</div>
  </section>
  <script type="text/javascript">
		$(function(){
			$('#table-user').dataTable( {				
				"bLengthChange": false,
				"iDisplayLength": 6,
				"infoEmpty": "<center><div class='text-info'><br>No user available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No user available</div></center>",
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
			$("#table-user_info").detach().appendTo('#page-link-wrapper');
			$("#table-user_paginate").detach().appendTo('#page-link-wrapper');
			$("#table-user_filter").detach().appendTo('#search-user');
      
      
			
			$(document).on('click', '.deactivate-link', function(e){
				e.preventDefault();
				var id = $(this).data('id');
				$('.modal-body').text('');
				$('.modal-body').text('Are you sure you want to deactivate this user? ');
				$('#confirm').modal()
					.one('click', '#yes', function() {
						location.href= '/user/deactivate?id='+id;
					});
			});
			
			$(document).on('click', '.activate-link', function(e){
				e.preventDefault();
				var id = $(this).data('id');
				$('.modal-body').text('');
				$('.modal-body').text('Are you sure you want to activate this user? ');
				$('#confirm').modal()
					.one('click', '#yes', function() {
						location.href= '/user/activate?id='+id;
					});
			});
		});
	</script>
@endsection