@extends('layouts.admin_header')
@section('content')
  <section class="content route-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Add Route</h3>
          </div>            
          <form id="form-add-route" role="form" method="POST" action="{{ url('/route/add') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="location_1">From Location</label>
                   <select id="location_1" class="form-control select2" name="location_1">
                      @foreach($airports as $airport)
                        <option value="{{ $airport->id }}" <?php if(old('location_1') == $airport->id ) {echo "selected";}?>>{{ $airport->name }}</option>
                      @endforeach
                   </select>
                  <span class="error-font text-danger">{{ $errors->first('location_1')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="location_2">To Location</label>
                   <select id="location_2" class="form-control select2" name="location_2">
                      @foreach($airports as $airport)
                        <option value="{{ $airport->id }}" <?php if(old('location_2') == $airport->id ) {echo "selected";}?>>{{ $airport->name }}</option>
                      @endforeach
                   </select>
                  <span class="error-font text-danger">{{ $errors->first('location_2')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="plane">Machine</label>
                   <select id="plane" class="form-control select2" name="plane">
                      @foreach($planes as $plane)
                        <option value="{{ $plane->id }}" <?php if(old('plane') == $plane->id ) {echo "selected";}?>>{{ $plane->name }}</option>
                      @endforeach
                   </select>
                  <span class="error-font text-danger">{{ $errors->first('plane')}}</span>
                </div>
              </div> 
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="time">Time (In minutes)</label>
                  <input type="text" class="form-control" id="time" placeholder="Enter time" name="time" value="{{ old('time') }}">
                  <span class="error-font text-danger">{{ $errors->first('time')}}</span>
                </div>
                <!-- <div class="form-group col-md-4">
                  <label for="distance">Distance (In NM)</label>
                  <input type="text" class="form-control" id="distance" placeholder="Enter distance" name="distance" value="{{ old('distance') }}">
                  <span class="error-font text-danger">{{ $errors->first('distance')}}</span>
                </div> -->
            
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