@extends('layouts.admin_header')
@section('content')
  <section class="content owner-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Add Owner</h3>
          </div>            
          <form id="form-add-owner" role="form" method="POST" action="{{ url('/owner/add') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="owner">Owner Name *</label>
                  <input type="text" class="form-control" id="owner" placeholder="Enter owner name" name="owner" value="{{ old('owner') }}">
                  <span class="error-font text-danger">{{ $errors->first('owner')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="contact-1">Contact Number *</label>
                  <input type="text" class="form-control" id="contact-1" placeholder="Enter contact number" name="contact-1" value="{{ old('contact-1') }}">
                  <span class="error-font text-danger">{{ $errors->first('contact-1')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="email-1">Contact Email *</label>
                  <input type="email" class="form-control" id="email-1" placeholder="Enter email" name="email-1" value="{{ old('email-1') }}">
                  <span class="error-font text-danger">{{ $errors->first('email-1')}}</span>
                </div>
              </div>      
            
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h5 class="box-title">Secondary Contact Details</h5>
                    </div>
                    <div class="box-body">
                      <div class="row form-group">
                        @for($i=2; $i<7; $i++)
                          <div class="form-group col-md-4">
                            <label for="name-{{$i}}">Name</label>
                            <input type="text" class="form-control" id="name-{{$i}}" placeholder="Enter name" name="names[]" value="">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="contact-{{$i}}">Contact</label>
                            <input type="number" class="form-control" id="contact-{{$i}}" placeholder="Enter contact" name="contacts[]" value="">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="email-{{$i}}">Email</label>
                            <input type="email" class="form-control" id="email-{{$i}}" placeholder="Enter email" name="emails[]" value="">
                          </div>
                        @endfor
                      </div>
                    </div>
                  </div>           
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
@stop