@extends('layouts.admin_header')
@section('content')
  <section class="content plane-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Edit Machine</h3>
          </div>            
          <form id="form-add-plane" role="form" method="POST" action="{{ url('/subtype/edit') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                  <input type="hidden" name="subtype-id" value="{{ $plane->id }}">
                 <div class="form-group col-md-4">
                  <label for="type">Type</label>
                  <select class="form-control select2" id="type" name="plane_type">
                    @foreach($plane_types as $id => $type)
                      @if($plane->plane_type == $id)
                        <option value="{{$id}}" selected>{{$type}}</option>
                      @else
                        <option value="{{$id}}">{{$type}}</option>
                      @endif
                    @endforeach
                  </select>
                   <span class="error-font text-danger">{{ $errors->first('type')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="sub_type">Plane SubType</label>
                  <input type="text" class="form-control" id="plane" placeholder="Enter Plane SubType" name="sub_type" value="{{ $plane->sub_type }}">
                  <span class="error-font text-danger">{{ $errors->first('plane')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="lavatory">Status</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="status" value="1" <?php if($plane->status == 1){ echo 'checked';} ?>> &nbsp;Active
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="status" value="0" <?php if($plane->status == 0){ echo 'checked';} ?>> &nbsp;&nbsp;Deactive
                    </label>
                  </div>
                </div>
              </div> 
            </div>              
            <div class="box-footer text-center">
              <button type="submit" class="btn btn-primary btn-fixed">Submit</button>
              <a href="/subtype" type="submit" class="btn btn-primary btn-back-fixed">Back</a>
            </div>
          </form>  
        </div>
      </div>
    </div>
  </section>
  
  <script>
    $(function(){
      $('input').iCheck({
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
      });
    });
  </script>
@stop