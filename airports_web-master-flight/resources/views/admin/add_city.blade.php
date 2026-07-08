@extends('layouts.admin_header')
@section('content')
  <section class="content city-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Add city</h3>
          </div>            
          <form id="form-add-city" role="form" method="POST" action="{{ url('/city/add') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="city">State</label>
                   <select id="state" class="select2 form-control" name="state_id">
                      @foreach($states as $id => $state)
                          <option value="{{ $id }}" {{ old('state_id') == $id ? 'selected' : '' }}>{{ $state }}</option>
                      @endforeach
                  </select>
                  <span class="error-font text-danger">{{ $errors->first('state_id') }}</span>
                </div>
                <div class="form-group col-md-6">
                  <label for="city">City Name *</label>
                  <input type="text" class="form-control" id="city" placeholder="Enter city name" name="city" value="{{ old('city') }}">
                  <span class="error-font text-danger">{{ $errors->first('city')}}</span>
                </div>
              </div>   
            </div>           
            <div class="box-footer text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
              <a href="/" type="submit" class="btn btn-primary">Back</a>
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
    });
  </script>
@stop
