@extends('layouts.admin_header')
@section('content')
  <section class="content setting-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">Settings</h3>
            <div id="search-setting" class="pull-right"></div>
          </div>
          <div class="box-body">
            <table id="table-setting" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="5%" class="text-center">Sr. No.</th>
                  <th>Type</th>
                  <th class="text-left">From Date</th>
                  <th class="text-left">To Date</th>
                  <th class="text-center">Amount</th>
                  <th class="text-center">GST (%)</th>
                  <th class="text-center">Status</th>
                  <th width="15%" class="text-center">Options</th>
                </tr>
              </thead>
              <tbody>
              <?php $i = 1; ?>
                @foreach($settings as $setting)
                  <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>@if($setting->setting_type == 0) GST @else Fixed medical team cost @endif</td>
                    <td>{{ date('d F,Y',strtotime( $setting->from_date)) }}</td>
                    <td>{{ date('d F,Y',strtotime( $setting->to_date)) }}</td>
                    <td class="text-center">{{ $setting->amount }}</td>
                    <td class="text-center">{{ $setting->gst }}</td>
                    <td class="text-center">@if($setting->status == 1) Active @else Inactive @endif</td>
                    <td width="15%" class="text-center">
                      <a href="/setting/edit?setting-id={{ $setting->id }}" type="button" class="btn label label-primary">Edit</a>
                      <a href="/setting/view?setting-id={{ $setting->id }}" type="button" class="btn label label-info">View</a>
                      <a type="button" class="btn label label-danger delete-setting-link" data-setting-id="{{ $setting->id }}">Delete</a>
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
    <div class="modal fade" id="delete-setting-modal" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-md modal-teal">
        <div class="modal-content">
          <form id="form-delete-setting" role="form" method="POST" action="{{ url('/setting/delete') }}" novalidate>
            <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h2 class="modal-title text-center color-skyblue">Setting</h2>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="text-center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">               
                    <input type="hidden" id="setting-id" name="setting-id">
                    <h4>Are you sure to delete this setting?</h4>
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
			$('#table-setting').dataTable( {				
				"bLengthChange": false,
				"iDisplayLength": 6,
				"infoEmpty": "<center><div class='text-info'><br>No setting available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No setting available</div></center>",
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
			$("#table-setting_info").detach().appendTo('#page-link-wrapper');
			$("#table-setting_paginate").detach().appendTo('#page-link-wrapper');
			$("#table-setting_filter").detach().appendTo('#search-setting');
      
      //Delete setting   
      $(document).on('click', '.delete-setting-link', function(ev) {
        ev.preventDefault();	
        var setting_id = $(this).data('setting-id'); 
        $('#setting-id').val(setting_id);
        $('#delete-setting-modal').modal('show');
      });   
		});
	</script>
@endsection
