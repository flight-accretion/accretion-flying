@extends('layouts.header')
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
                          <h3>Change Password</h3> 
                          <form id="form-sign-up" role="form" method="POST" action="{{ url('/user/change-password') }}" novalidate>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"> <br>          
                            <div class="col-md-10 col-md-offset-1">
                              <div class="form-group col-md-12">
                                <input type="password" class="form-control search-planes-textbox" placeholder="Current Password" name="current-password">
                                <span class="text-danger">{{ $errors->first('current-password') }}</span>
                              </div> 
                              <div class="form-group col-md-12">
                                <input type="password" class="form-control search-planes-textbox" placeholder="New Password" name="new-password">
                                <span class="text-danger">{{ $errors->first('new-password') }}</span>
                              </div> 
                              <div class="form-group col-md-8 col-md-offset-2">
                                <button class="btn btn-login sign-up-button" type="submit">Update</button>
                              </div>
                              <div class="clearfix"></div>
                              <div class="text-center"><span id="sign-in-loading" style="display:none;"><img src="images/loading.gif" alt="" />
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
