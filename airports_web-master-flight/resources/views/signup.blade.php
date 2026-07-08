@extends('layouts.login_header')
@section('content')

  <header>
    <div class="header-content">
      <div class="header-content-inner">
        <div class="row">
          <div class="col-md-12">
            <div class="plane-details">
              <!-- Tab panes -->
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="login">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                    <div class="col-md-8 col-md-offset-2">
                      <div class="panel panel-default" id="panel-signup">
                        <div class="panel-body text-center">
                          <h3>Sign Up</h3> 
                          <form id="form-sign-up" role="form" method="POST" action="{{ url('/user/sign-up') }}" novalidate>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"> <br>          
                            <div class="col-md-10 col-md-offset-1">
                              <div class="form-group col-md-12">
                                <input type="text" class="form-control search-planes-textbox" id="name" placeholder="Name" name="name" value="{{ old('name') }}">
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                              </div>
                              <div class="form-group col-md-12">
                                <input type="email" class="form-control search-planes-textbox" id="user-email" placeholder="Email Address" name="email" value="{{ old('email') }}">
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                              </div>
                              <div class="form-group col-md-12">
																<div class="row">
																	<div class="col-md-6">
																		<input type="text" class="form-control search-planes-textbox" id="phone" placeholder="Phone" name="phone" value="{{ old('phone') }}">
																		<span class="text-danger">{{ $errors->first('phone') }}</span>
																	</div>
																	<div class="col-md-6">
																		<select class="form-control search-planes-dropdown select2" name="city">
																			@foreach($cities as $city_id => $city)
																				<option value="{{ $city_id }}" {{ old('city') == $city_id ? 'selected' : '' }}>{{ $city }}</option>
																			@endforeach
																		</select>
																		<span class="text-danger">{{ $errors->first('city') }}</span>
																	</div>
																</div>
                              </div>
                              <div class="form-group col-md-12">
                                <input type="password" class="form-control search-planes-textbox" id="user-password" placeholder="Password" name="password" value="{{ old('password') }}">
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                              </div> 
                              <div class="form-group col-md-8 col-md-offset-2">
                                <button class="btn btn-login sign-up-button" type="submit">Sign Up</button>
                              </div>
                              <div class="clearfix"></div>
                              <div class="text-center"><span id="sign-in-loading" style="display:none;"><img src="images/loading.gif" alt="" />
                              </div>
															<div class="col-md-12">
																<h3><a href="/" >Login</a></h3>
															</div>
                            </div> 
                          </form>
                        </div>
                      </div>
                    </div> 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
	<script>
		$(function(){
			$('.select2').select2();
		})
	</script>
@endsection