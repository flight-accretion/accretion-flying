@extends('layouts.admin_header')
@section('content')
  
  <section class="content route-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Edit Route</h3>
          </div>            
          <form id="form-add-route" role="form" method="POST" action="{{ url('/route/edit') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-4">
                  <input type="hidden" name="route-id" value="{{ $route->id }}">
                  <label for="route">From Location</label>
                  <select class="form-control select2" id="location_1" name="location_1">
                    @foreach($airports as $id => $airport)
                      @if($route->location_1 == $id)
                        <option value="{{$id}}" selected>{{$airport}}</option>
                      @else
                        <option value="{{$id}}">{{$airport}}</option>
                      @endif
                    @endforeach
                  </select>
                  <span class="error-font text-danger">{{ $errors->first('location_1')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <input type="hidden" name="route-id" value="{{ $route->id }}">
                  <label for="route">To Location</label>
                  <select class="form-control select2" id="location_2" name="location_2">
                    @foreach($airports as $id => $airport)
                      @if($route->location_2 == $id)
                        <option value="{{$id}}" selected>{{$airport}}</option>
                      @else
                        <option value="{{$id}}">{{$airport}}</option>
                      @endif
                    @endforeach
                  </select>
                  <span class="error-font text-danger">{{ $errors->first('location_2')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="plane">Machine</label>
                  <select class="form-control select2" id="plane" name="plane">
                    @foreach($planes as $id => $plane)
                      @if($route->plane_id == $id)
                        <option value="{{$id}}" selected>{{$plane}}</option>
                      @else
                        <option value="{{$id}}">{{$plane}}</option>
                      @endif
                    @endforeach
                  </select>
                  <span class="error-font text-danger">{{ $errors->first('plane')}}</span>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="time">Time (In minutes)</label>
                  <input type="text" class="form-control" id="time" placeholder="Enter time" name="time" value="{{ $route->time }}">
                  <span class="error-font text-danger">{{ $errors->first('time')}}</span>
                </div>
                <!-- <div class="form-group col-md-4">
                  <label for="distance">Distance (In NM)</label>
                  <input type="text" class="form-control" id="distance" placeholder="Enter distance" name="distance" value="{{ $route->distance }}">
                  <span class="error-font text-danger">{{ $errors->first('distance')}}</span>
                </div> -->
              
              </div>             
            </div>             
            <div class="box-footer text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
              <a href="/route" type="submit" class="btn btn-primary">Back</a>
            </div>
          </form>  
        </div>
      </div>
    </div>
  </section>
  <script>
    $(function () {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
      });
    });
  </script>
@stop