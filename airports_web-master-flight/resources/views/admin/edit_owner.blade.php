@extends('layouts.admin_header')
@section('content')
  <section class="content-header">
    <h1>
      Owner
    </h1>
  </section>
  
  <section class="content owner-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Edit Owner</h3>
          </div>            
          <form id="form-add-owner" role="form" method="POST" action="{{ url('/owner/edit') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-4">
                  <input type="hidden" name="owner-id" value="{{ $owner->id }}">
                  <label for="owner">Owner Name</label>
                  <input type="text" class="form-control" id="owner" placeholder="Enter owner name" name="owner" value="{{ $owner->name }}">
                  <span class="error-font text-danger">{{ $errors->first('owner')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="contact-1">Contact Number 1 *</label>
                  <input type="text" class="form-control" id="contact-1" placeholder="Enter contact number" name="contact-1" value="{{ $owner->contact_number_1 }}">
                  <span class="error-font text-danger">{{ $errors->first('contact-1')}}</span>
                </div>
                <div class="form-group col-md-4">
                  <label for="email-1">Contact Email 1 *</label>
                  <input type="email" class="form-control" id="email-1" placeholder="Enter email" name="email-1" value="{{ $owner->email_1 }}">
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
                      <?php $i=2;?>
                        @foreach($secondary_contacts as $contact)
                          <div class="form-group col-md-4">
                            <label for="name-{{$i}}">Name</label>
                            <input type="text" class="form-control" id="name-{{$i}}" placeholder="Enter name" name="names[]" value="{{ $contact->name }}">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="contact-{{$i}}">Contact</label>
                            <input type="number" class="form-control" id="contact-{{$i}}" placeholder="Enter contact" name="contacts[]" value="{{ $contact->contact }}">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="email-{{$i}}">Email</label>
                            <input type="email" class="form-control" id="email-{{$i}}" placeholder="Enter email" name="emails[]" value="{{ $contact->email }}">
                          </div>
                          <?php $i++;  ?>
                        @endforeach
                        <?php $count=0; ?>
                        @for($i=2+count($secondary_contacts); $i<7; $i++)
                          <div class="form-group col-md-4">
                            <label for="name-{{$i}}">Name</label>
                            <input type="text" class="form-control" id="name-{{$i}}" placeholder="Enter name" name="names[]">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="contact-{{$i}}">Contact</label>
                            <input type="number" class="form-control" id="contact-{{$i}}" placeholder="Enter contact" name="contacts[]">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="email-{{$i}}">Email</label>
                            <input type="email" class="form-control" id="email-{{$i}}" placeholder="Enter email" name="emails[]">
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
              <a href="/owner" type="submit" class="btn btn-primary">Back</a>
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
