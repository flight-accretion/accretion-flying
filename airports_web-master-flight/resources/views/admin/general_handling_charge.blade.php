@extends('layouts.admin_header')
@section('content')
  <section class="content city-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">General Handling Charge</h3>
          </div>            
          <form id="form-charge" role="form" method="POST" action="{{ url('/handling-charge') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <input type="hidden" name="city-id" value="{{ $obj_charges->city_id }}"> 
            <div class="box-body justify-content-center">
              <div class="row justify-content-center">
                <div class="form-group col-md-3">
                  <label for="charges">Handling Charge</label>
                  <input type="text" class="form-control" id="charges" placeholder="Handling Charge" name="charges" value="{{ $obj_charges->charges }}">
                  <span class="error-font text-danger">{{ $errors->first('charges')}}</span>
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