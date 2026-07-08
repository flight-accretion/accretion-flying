@extends('layouts.admin_header')
@section('content')
  <section class="content city-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Booking Points Settings</h3>
          </div>            
          <form id="form-add-points" role="form" method="POST" action="{{ url('/booking/points') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-2">
                  <label for="points">Points %</label>
                  <input type="text" class="form-control" id="points" name="points" value="{{ $points }}">
                  <span class="error-font text-danger">{{ $errors->first('points')}}</span>
                </div>
                <div class="form-group col-md-2">
                  <label>&nbsp;</label><br/>
                  <button type="submit" class="btn btn-success btn-md">Save</button>
                </div>
              </div>   
            </div>
          </form>  
        </div>
      </div>
    </div>
  </section>
@stop