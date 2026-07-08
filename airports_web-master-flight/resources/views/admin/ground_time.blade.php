@extends('layouts.admin_header')
@section('content')
  
  <section class="content owner-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Set Ground Time</h3>
          </div>            
          <form id="form-add-owner" role="form" method="POST" action="{{ url('/set_ground_time') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <input type="hidden" name="airport-id" id="airport-id">
                  <label for="airport">Airport</label>
                  <input type="text" class="form-control" id="airport" name="airport" tabindex="1" min="0" required>
                  <span class="error-font text-danger">{{ $errors->first('airport')}}</span>
                </div>
                <div class="form-group col-md-6">
                  <label for="gt">Ground Time (In minutes)</label>
                  <input type="number" class="form-control" id="gt" placeholder="Enter ground time" name="gt">
                  <span class="error-font text-danger">{{ $errors->first('gt')}}</span>
                </div>
              </div>                
            </div>             
            <div class="box-footer text-center">
              <button type="submit" class="btn btn-primary">Update</button>
              <a href="admin/dashboard" type="submit" class="btn btn-primary">Back</a>
            </div>
          </form>  
        </div>
      </div>
    </div>
  </section>
  <script>
    $(function () {
    
     var airports = <?php echo json_encode($airports) ?>;
     console.log(airports);
     
     $('#airport').change(function(){ 
       var airport_id = $("#airport-id").val(); 
        $("#gt").val(airports[airport_id].gt);
      });
      
      //Autocomplete textbox
      $('#airport').autocomplete({
        source: function( request, response ) { 
          $.ajax({
            url: '/home/all-airports',
            type: 'GET',
            data: {
               name_start_with: request.term
            },
            success: function( data ) { 
              var tmp = obj = [];
              $("#airport").val('');
              $.each(data , function( index, value ) { 
               obj.push({ 
                    'value': value,
                    'id': index
                });
              });
              response( $.map( obj, function( item ) {  
                return { 
                  label: item.value,
                  value: item.id
                }
              })); 
              if(data.length == 0){ 
                $("#airport-id").val('');
                $("#gt").val('');
              }
            }            
          });
        },
        autoFocus: true,
        selectFirst: true,
        minLength: 0,
        select: function (event, ui) { 
          var value = ui.item.value;  
          var name = ui.item.label;  
          data: 'airport='+name
          $("#airport").val(ui.item.label);
          $("#airport-id").val(ui.item.value);
          $("#airport").change();
          return false;
        }
      });
    });
  </script>
@stop