@extends('layouts.admin_header')
@section('content')
  <section class="content mail-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Edit Mail Content for {{ $mail_content->name }}</h3>
            <div id="search-mail" class="pull-right"></div>
          </div>
          <div class="box-body">
						@if( $mail_content->name == 'New User')
							<div class="row">
								<div class="col-md-12">
									<ul>
										<li>Use <b>---name---</b> for user's name.</li>
										<li>Use <b>---email---</b> for user's email.</li>
										<li>Use <b>---password---</b> for user's password.</li>
										<li>Use <b>---url---</b> for site's url.</li>
									</ul>
								</div>
							</div>
						@elseif( $mail_content->name == 'Booking')
							<div class="row">
								<div class="col-md-12">
									<ul>
										<li>Use <b>---name---</b> for user's name.</li>
										<li>Use <b>---plane---</b> for plane's name.</li>
										<li>Use <b>---bookings---</b> for booking details.</li>
									</ul>
								</div>
							</div>
						@elseif( $mail_content->name == 'Points Summary')
							<div class="row">
								<div class="col-md-12">
									<ul>
										<li>Use <b>---name---</b> for user's name.</li>
										<li>Use <b>---points_earned---</b> for points earned.</li>
										<li>Use <b>---points_redeemed---</b> for points redeemed.</li>
										<li>Use <b>---points_available---</b> for points available.</li>
										<li>Use <b>---total_bookings---</b> for total bookings.</li>
									</ul>
								</div>
							</div>
						@endif
						<form id="form-edit-content" action="/mail-content/edit" method="POST">
							<div class="row">
								<div class="col-md-12 form-group">
									<label>Subject</label>
									<input class="form-control" id="subject" name="subject" value="{{ $mail_content->subject }}" />
								</div>
							</div>
							<div class="row form-group">
								<input name="_token" value="{{ csrf_token() }}" type="hidden" />
								<input name="id" value="{{ $mail_content->id }}" type="hidden" />
								<div class="col-md-12">  
									<div id="txt-editor"></div> 
									<input class="form-control" name="content" type="hidden" id="content" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 text-center">
									<button class="btn btn-success">Save</button>
								</div>
							</div>
						</form>
          </div>
        </div>
      </div> 
    </div>
  </section>
  <script>
		$(function(){
			
			$("#txt-editor").Editor({
        'insert_table':false,
        //'insert_img':false,
        'print':false,
        //'fonts':false,
        //'styles':false,
        //'font_size':false,
        //'l_align':false,
        //'r_align':false,
        //'c_align':false,
        //'justify':false,
        //'indent':false,
        'outdent':false,
        //'block_quote':false,
        'strikeout':false,
        //'hr_line':false,
        'splchars':false,
        //'source':false,
      });   
			
      $("#txt-editor").Editor("setText", '<?php echo trim(preg_replace('/\s\s+/', '\n', $mail_content->content));?>');
      $("#form-edit-content").submit(function(){
        $("#content").val($("#txt-editor").Editor("getText"));
      });
			
    });
  </script>
@endsection