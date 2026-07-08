@extends('layouts.admin_header')
@section('content')
  <section class="content mail-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Mail Content</h3>
            <div id="search-mail" class="pull-right"></div>
          </div>
          <div class="box-body">
            <table id="table-mail" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="9%" class="text-center">Sr. No.</th>
                  <th>Name</th>
                  <th width="15%" class="text-center">Options</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; ?>
                @foreach($mail_contents as $id => $mail_content)
                  <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $mail_content }}</td>
                    <td width="15%" class="text-center">
                      <a href="/mail-content/edit?id={{ $id }}" type="button" class="btn label label-primary">Edit</a>
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
  </section>
  <script type="text/javascript">
		$(function(){
      $('#table-mail').dataTable( {				
				"bLengthChange": false,
				"iDisplayLength": 6,
				"infoEmpty": "<center><div class='text-info'><br>No records available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No records available</div></center>",
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
			$("#table-mail_info").detach().appendTo('#page-link-wrapper');
			$("#table-mail_paginate").detach().appendTo('#page-link-wrapper');
			$("#table-mail_filter").detach().appendTo('#search-mail');  
		});
	</script>
@endsection
