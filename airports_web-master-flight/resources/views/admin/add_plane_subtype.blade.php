@extends('layouts.admin_header')
@section('content')
  <section class="content plane-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Add Plane SubType</h3>
          </div>            
          <form id="form-add-plane" role="form" method="POST" action="{{ url('/subtype/add') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="type">Type</label>
                   <select id="type" class="form-control select2" name="type">
                      @foreach($plane_types as $plane_type)
                        <option value="{{ $plane_type->id }}" <?php if(old('type') == $plane_type->id ) {echo "selected";}?>>{{ $plane_type->name }}</option>
                      @endforeach
                   </select>
                   <span class="error-font text-danger">{{ $errors->first('type')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="sub_type">Plane SubType</label>
                  <input type="text" class="form-control" id="sub_type" placeholder="Enter Plane Subtype name" name="sub_type" value="{{ old('sub_type') }}">
                  <span class="error-font text-danger">{{ $errors->first('sub_type')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="lavatory">Status</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="status" value="1" checked> &nbsp;&nbsp;Active
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="status" value="0"> &nbsp;&nbsp;Deactive
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
@stop