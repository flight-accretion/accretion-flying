@extends('layouts.admin_header')
@section('content')
  <section class="content route-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Add User</h3>
          </div>            
          <form id="form-add-route" role="form" method="POST" action="{{ url('/user/add') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-4">
                  <label>Name</label>
									<input name="name" class="form-control" value="{{ old('name') }}" />
                  <span class="error-font text-danger">{{ $errors->first('name')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label>Email</label>
									<input type="email" name="email" class="form-control" value="{{ old('email') }}" />
                  <span class="error-font text-danger">{{ $errors->first('email')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label>Phone</label>
									<input name="phone" class="form-control" value="{{ old('phone') }}" />
                  <span class="error-font text-danger">{{ $errors->first('phone')}}</span>
                </div>
							</div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label>Password</label>
									<input type="password" name="password" class="form-control" value="{{ old('password') }}" />
                  <span class="error-font text-danger">{{ $errors->first('password')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="city">City</label>
                   <select id="city" class="form-control select2" name="city">
                      @foreach($cities as $city_id => $city)
                        <option value="{{ $city_id }}" <?php if(old('city') == $city_id ) {echo "selected";}?>>{{ $city }}</option>
                      @endforeach
                   </select>
                  <span class="error-font text-danger">{{ $errors->first('city')}}</span>
                </div>
              </div>  
            </div>           
            <div class="box-footer text-center">
              <button type="submit" class="btn btn-primary">Save</button>
              <a href="/user" type="submit" class="btn btn-primary">Back</a>
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