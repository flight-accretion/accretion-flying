@extends('layouts.admin_header')
@section('content')
  <section class="content setting-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Add Setting</h3>
          </div>            
          <form id="form-add-setting" role="form" method="POST" action="{{ url('/setting/add') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row form-group">        
                <div class="col-md-6 col-md-offset-3"> 
                  <label for="status">Type *</label><br>
                  <label class="radio-inline">
                    <input id="type-1" type="radio" class="minimal status" name="type" value="0"<?php if(old('type') == "0") {echo "checked";}?> checked>&nbsp;GST
                  </label>
                  <label class="radio-inline">
                    <input id="type-2" type="radio" class="minimal status" name="type" value="1"<?php if(old('type') == "1") {echo "checked";}?>>&nbsp;Fixed medical team cost 
                  </label>
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-3 col-md-offset-3">
                  <label for="description">From Date *</label>
									<div class="input-group date" id="from-date">								
										<input type="text" class="form-control from-date" id="from-date" name="from-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="{{ date('d-m-Y')}}" tabindex="2">
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
									<span class="error-font text-danger">{{ $errors->first('from-date') }}</span>
								</div>  
                <div class="col-md-3">
                  <label for="description">To Date *</label>
									<div class="input-group date" id="to-date">								
										<input type="text" class="form-control to-date" id="to-date" name="to-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="{{ date('d-m-Y', strtotime('+1 year'))}}" tabindex="2">
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
									<span class="error-font text-danger">{{ $errors->first('to-date') }}</span>
								</div>               
              </div>
              <div id="gst-div" class="<?php if(old('type') == "1") {echo "hide";}?>">
                <div class="row form-group gst-rate <?php if(old('type') == "1") {echo "hide";}?>">
                  <div class="col-md-3 col-md-offset-3">
                    <label for="gst-rate">GST(%) *</label>					
                      <input type="text" class="form-control" name="gst-rate" id="gst-rate" placeholder="GST (%)" onfocusout="splitRate()" value="{{ old('gst-rate') }}">
                    <span class="error-font text-danger">{{ $errors->first('gst-rate') }}</span>
                  </div> 
                </div>
                <!-- <div class="row form-group gst <?php if(old('type') == "1") {echo "hide";}?>">
                  <div class="col-md-3 col-md-offset-3">
                    <label for="description">CGST(%) *</label>					
                      <input type="text" class="form-control" name="cgst" id="cgst" placeholder="CGST (%)" readonly>
                    <span class="error-font text-danger">{{ $errors->first('cgst') }}</span>
                  </div>  
                  <div class="col-md-3">
                    <label for="description">SGST(%) *</label>					
                      <input type="text" class="form-control" name="sgst" id="sgst" placeholder="SGST (%)" readonly>
                    <span class="error-font text-danger">{{ $errors->first('sgst') }}</span>
                  </div>  
                </div>
                <div class="row form-group igst <?php if(old('type') == "1") {echo "hide";}?>">
                  <div class="col-md-3 col-md-offset-3 <?php if(old('type') == "1") {echo "hide";}?>">
                    <input type="checkbox" id="igst-check" class="inveoice-status" name="igst-check" value=""> &nbsp; &nbsp; &nbsp; &nbsp;
                    <label for="">IGST</label>
                  </div> 
                  <div class="col-md-3">		
                    <input type="text" class="form-control hide" id="igst-rate" name="igst-rate" placeholder="IGST (%)">
                    <span class="error-font text-danger">{{ $errors->first('igst-rate') }}</span>
                  </div>
                </div> -->
              </div>
              <div class="row form-group fixed-cost <?php if(old('type')){if(old('type') == "0") {echo "hide";}} else {echo "hide"; }?>">
                <div class="col-md-3 col-md-offset-3">
                  <label for="description">Amount *</label>					
									<input type="text" class="form-control" name="amount" placeholder="Fixed medical team cost">
									<span class="error-font text-danger">{{ $errors->first('amount') }}</span>
								</div>   
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
    $(function () {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
      });
      $("#to-date").datepicker({
        format: "dd-mm-yyyy",
        autoclose: true,
      });
      $("#from-date").datepicker({
        format: "dd-mm-yyyy",
        autoclose: true,
      });
      
    });
  </script>
  <script type="text/javascript">
    $(document).ready(function(){
    $('input[name=type]').on('ifClicked', function (event) { 
      var type = this.value;
      if(type == 1) {
        // medical cost
        $(".fixed-cost").removeClass("hide");
        $("#gst-div").addClass("hide");
      } else {
        // gst
        $(".fixed-cost").addClass("hide");
        $("#gst-div").removeClass("hide");
      }
    });
    
    $("#igst-check").on('ifChanged', function(event) {
      if($("#igst-check").is(':checked')){ 
        $(".igst").removeClass();
        $("#igst-rate").removeClass();
        $(".gst").addClass("hide");
        $(".gst-rate").addClass("hide");
      } else {
      // gst
      $(".fixed-cost").addClass("hide");
      $(".gst").removeClass("hide");
      $(".gst-rate").removeClass("hide");
      $(".igst").addClass("hide");
      $("#igst-rate").addClass("hide");
    }
  });
    
  $("#gst-rate").focusout(function(){
    $(this).css("background-color", "#FFFFFF");
  });  
    
    $("#is-percent").on('ifChanged', function(event) {
      if($("#is-percent").is(':checked')){ 
        $("#value-percentage").removeClass();
        $("#value-fixed").addClass("hide");
        $("#value-percentage").addClass("input-group-addon");
      }
      else{
        $("#value-percentage").addClass("hide");
        $("#value-fixed").removeClass();
        $("#value-fixed").addClass("input-group-addon");
      }
    });
    });
  </script>
  
  <script>
    function splitRate() {
    var gst = $("#gst-rate").val();
    var gst_half= gst / 2;
    $('#cgst').val(gst_half);
    $('#sgst').val(gst_half);
    console.log(gst_half);
  }
  </script>
@stop