<?php $__env->startSection('content'); ?>

  <header>
    <div class="header-content">
      <div class="header-content-inner">
        <div class="row">
          <div class="col-md-12">
            <div class="plane-details">
              <!-- Tab panes -->
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="login">
                  <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"> 
                    <div class="col-md-8 col-md-offset-2">
                      <div class="panel panel-default" id="panel-login">
                        <div class="panel-body text-center">
                          <h3>LOGIN</h3> 
                          <form id="form-sign-in" role="form" method="POST" action="<?php echo e(url('/user/login')); ?>" novalidate>
                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"> <br>          
                            <div class="col-md-10 col-md-offset-1">
                              <div class="form-group col-md-12">
                                <input type="email" class="form-control search-planes-textbox" id="user-email" placeholder="Email Address" name="user-email" value="<?php echo e(old('email')); ?>">
                                <input id="login-from" type="hidden" name="login-from" value="header">
                                <span class="error-msgs text-center text-danger"></span>
                              </div>
                              <div class="form-group col-md-12">
                                <input type="password" class="form-control search-planes-textbox" id="user-password" placeholder="Password" name="user-password" value="<?php echo e(old('password')); ?>">
                                <span class="error-msgs text-center text-danger"></span>
                              </div> 
                              <div class="form-group col-md-8 col-md-offset-2">
                                <button class="btn btn-login sign-in-button" type="submit">Login</button>
                              </div>
                              <div class="clearfix"></div>
                              <div class="text-center"><span id="sign-in-loading" style="display:none;"><img src="images/loading.gif" alt="" />
                              </div>
															<div class="col-md-12">
																<h3><a href="/password/email" >Forgot password?</a> | <a href="/sign-up">Sign Up</a></h3>
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.login_header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\accretion-flying\airports_web-master-flight\resources\views/welcome.blade.php ENDPATH**/ ?>