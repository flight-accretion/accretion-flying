@extends('layouts.admin_header')
@section('content')
  <section class="content airport-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Airports</h3>
            <div id="search-airport" class="pull-right"></div>
          </div>
          <div class="box-body">
            <table id="table-airport" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="5%" class="text-center">Sr. No.</th>
                  <th>Airport</th>
                  <!-- <th>City id</th> -->
                  <th>City Name</th>
                  <th>Ground Time (In minutes)</th>
                  <th>ICOA</th>
                  <th>IATA</th>
                  <th>Handling Charge</th>
                  <th width="15%" class="text-center">Status</th>
                  <th width="15%" class="text-center">Options</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $i = 1;
                  $general_charge = isset($charges[0]) ? (float) $charges[0] : 0;
                ?>
                @foreach($airports as $airport)
                  <?php
                    $airport_charge = isset($charges[$airport->id]) ? (float) $charges[$airport->id] : 0;
                    $handling_charge = max($airport_charge, $general_charge);
                  ?>
                  <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $airport->name }}</td>
                    <!-- <td>{{ (isset($cities[$airport->city_id]) ?  $cities[$airport->city_id]->name : '-') }} </td> -->
                     <td>{{ $airport->city_name ?? '-' }}</td>
                    <td>{{ $airport->gt ?? '-' }}</td>
                     <td>{{ $airport->icao ?? '-' }}</td>
                      <td>{{ $airport->iata ?? '-' }}</td>
                    <!-- <td>{{ $airport->country_name ?? '-' }}</td> -->
                    <td>{{ $handling_charge }}</td>
                    <td width="15%" class="text-center">
                        @if($airport->status != 0)
                      <button type="button" class="btn label label-success">   Active   </button>
                      @else
                      <button type="button" class="btn label label-danger">   Deactive   </button>
                      @endif
                    </td>
                    <td width="15%" class="text-center">
                      <a href="/airport/edit?airport-id={{ $airport->id }}" type="button" class="btn label label-primary">Edit</a>
                      <a href="/airport/view?airport-id={{ $airport->id }}" type="button" class="btn label label-info">View</a>
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
			$('#table-airport').dataTable( {				
				"bLengthChange": false,
				"bSort": true,
				"iDisplayLength": 6,
				"infoEmpty": "<center><div class='text-info'><br>No airport available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No airport available</div></center>",
          "sSearch": "",
          "oPaginate": {
            "sNext": '>',
            "sLast": '>|',
            "sFirst": '|<',
            "sPrevious": '<'
          }
        } 					 
			});
			$('.dataTables_filter input').attr("placeholder", "Search");
			$('.dataTables_filter input').removeClass("input-sm");
      $('.dataTables_filter input').addClass("form-control");
			$("#table-airport_info").detach().appendTo('#page-link-wrapper');
			$("#table-airport_paginate").detach().appendTo('#page-link-wrapper');
			$("#table-airport_filter").detach().appendTo('#search-airport');
      
      //Delete airport   
      $(document).on('click', '.delete-airport-link', function(ev) {
        ev.preventDefault();	
        var airport_id = $(this).data('airport-id'); 
        $('#airport-id').val(airport_id);
        $('#delete-airport-modal').modal('show');
      });   
		});
	</script>
@endsection
