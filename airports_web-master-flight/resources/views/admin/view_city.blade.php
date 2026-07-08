@extends('layouts.admin_header')
@section('content')
  <section class="content city-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">View City</h3>
          </div>            
          <form id="form-add-city" role="form" method="POST" action="{{ url('/city/edit') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="state">State</label>
                  <select class="form-control select2" id="state" name="state" disabled>
                    @foreach($states as $id => $state)
                      @if($city->state_id == $id)
                        <option value="{{$id}}" selected>{{$state}}</option>
                      @else
                        <option value="{{$id}}">{{$state}}</option>
                      @endif
                    @endforeach
                  </select>
                  <span class="error-font text-danger">{{ $errors->first('city') }}</span>
                </div>
                <div class="form-group col-md-6">
                  <label for="city">City Name</label>
                  <input type="hidden" name="city-id" value="{{ $city->id }}">
                  <input type="text" class="form-control" id="city" placeholder="Enter City name" name="city" value="{{ $city->name }}" disabled>
                  <span class="error-font text-danger">{{ $errors->first('city')}}</span>
                </div>
              </div>           
            </div>              
            <div class="box-footer text-center">
              <a href="/city" type="submit" class="btn btn-primary">Back</a>
            </div>
          </form>  
        </div>
      </div>
    </div>
  </section>
  
  <script>
    $(function(){
      //Initialize Select2 Elements
      $(".select2").select2();
      
      var cities = <?php echo json_encode($cities) ?>; 
      
      $('#state').change(function(){
        var state_id = $(this).val(); 
        
      });
      
    });
  </script>
@stop