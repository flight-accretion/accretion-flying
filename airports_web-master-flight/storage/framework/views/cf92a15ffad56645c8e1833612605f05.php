<?php $__env->startSection('content'); ?>
  <section class="content plane-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Edit Machine</h3>
          </div>     
            
          <form id="form-add-plane" role="form" method="POST" action="<?php echo e(url('/plane/edit')); ?>">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"> 
            <div class="box-body">
              <div class="row">
                <div class="form-group col-md-3">
                  <label for="plane">Machine Name</label>
                  <input type="hidden" name="plane-id" value="<?php echo e($plane->id); ?>">
                  <input type="text" class="form-control" id="plane" placeholder="Enter Machine name" name="plane" value="<?php echo e($plane->name); ?>">
                  <span class="error-font text-danger"><?php echo e($errors->first('plane')); ?></span>
                </div>
                <div class="form-group col-md-3">
                  <label for="Call_Sign">Call Sign(VT)</label>
                  <input type="text" class="form-control" id="Call_Sign" placeholder="Enter Call Sign" name="Call_Sign" value="<?php echo e(old('Call_Sign', $plane->Call_Sign)); ?>" required>
                  <span class="error-font text-danger"><?php echo e($errors->first('Call_Sign')); ?></span>
                </div>
                 <div class="form-group col-md-3">
                  <label for="type">Type</label>
                  <select class="form-control select2" id="type" name="type">
                    <?php $__currentLoopData = $plane_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if($plane->type_id == $id): ?>
                        <option value="<?php echo e($id); ?>" selected><?php echo e($type); ?></option>
                      <?php else: ?>
                        <option value="<?php echo e($id); ?>"><?php echo e($type); ?></option>
                      <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                   <span class="error-font text-danger"><?php echo e($errors->first('type')); ?></span>
                </div>
                <div class="form-group col-md-3">
                  <label for="subtype">SubType</label>
                  <select class="form-control select2" id="subtype" name="subtype">
                    <option value="">Select</option>
                    <?php $__currentLoopData = $plane_subtypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $plane_subtype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($plane_subtype->id); ?>" <?php echo e($plane->subtype == $plane_subtype->id ? 'selected' : ''); ?>><?php echo e($plane_subtype->sub_type); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                   <span class="error-font text-danger"><?php echo e($errors->first('subtype')); ?></span>
                </div>
              </div>     
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="speed_coefficient">Take Off / Landing Speed Coefficient</label>
                  <input type="text" class="form-control" id="speed_coefficient" placeholder="Enter speed coefficient" name="speed_coefficient" value="<?php echo e($plane->speed_coefficient); ?>">
                  <span class="error-font text-danger"><?php echo e($errors->first('price')); ?></span>
                </div>
                <div class="form-group col-md-4">
                  <label for="price">Price Per Hour</label>
                  <input type="text" class="form-control" id="price" placeholder="Enter Machine price" name="price" value="<?php echo e($plane->price_per_hour); ?>">
                  <span class="error-font text-danger"><?php echo e($errors->first('price')); ?></span>
                </div>
                <div class="form-group col-md-4">
                  <label for="seats">Seats</label>
                  <input type="text" class="form-control" id="seats" placeholder="Enter number of seats" name="seats" value="<?php echo e($plane->seats); ?>">
                  <span class="error-font text-danger"><?php echo e($errors->first('seats')); ?></span>
                </div>
              </div> 
              <div class="row">
                <div class="form-group col-md-4">
                 <label for="speed">Speed Per Hour</label>
                  <input type="text" class="form-control" id="speed" placeholder="Enter Machine speed" name="speed" value="<?php echo e($plane->speed); ?>">
                  <span class="error-font text-danger"><?php echo e($errors->first('speed')); ?></span>
                </div>
                <div class="form-group col-md-4">
                  <label>Note</label>
                  <textarea name="note" class="form-control" rows="2" placeholder="Enter description in short"><?php echo e($plane->note); ?></textarea>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="lavatory">Lavatory</label>
                  <div class="radio">
                    <label class="radio-inline">
                        <input type="radio" name="lavatory" value="1"
                            <?php echo e(old('lavatory', $plane->lavatory) == 1 ? 'checked' : ''); ?>>
                        Yes
                    </label>
                  </div>
                  <div class="radio">
                    <label class="radio-inline">
                        <input type="radio" name="lavatory" value="0"
                            <?php echo e(old('lavatory', $plane->lavatory) == 0 ? 'checked' : ''); ?>>
                        No
                    </label>
                  </div>
                </div>
                <div class="form-group col-md-4 flower-shower">
                  <label for="flower-shower">Flower Shower ?</label><br/>
                  <input type="checkbox" id="flower-shower" name="flower-shower" value="1" <?php if($plane->flower_shower == 1){ echo 'checked';} ?>> 
                </div>
              </div> 
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h5 class="box-title">Owner Details</h5>
                    </div>
                    <div class="box-body">
                      <div class="row form-group">
                        <div class="form-group col-md-4">
                          <label for="owner">Owner</label>
                        
                          <input type="text" id="owner" name="owner" class="form-control" value="<?php if(isset($owners[$owner_id])) {echo $owners[$owner_id]->name;} ?>" tabindex="5" min="0" required>
                            <input type="hidden" id="owner-id" name="owner-id" value="<?php echo e($owner_id); ?>">
                           <span class="error-font text-danger"><?php echo e($errors->first('owner')); ?></span>
                        </div>
                        <div class="form-group col-md-4">
                          <label for="owner">Contact</label>
                           <input type="text" class="form-control" id="contact" name="contact" value="<?php if(isset($owners[$owner_id])) {echo $owners[$owner_id]->contact_number_1;} ?>">
                            <span class="error-font text-danger" style="color: #c41f45;"><?php echo e($errors->first('contact')); ?></span>
                        </div>
                        <div class="form-group col-md-4">
                          <label for="owner">Email</label>
                           <input type="text" class="form-control" id="email" name="email" value="<?php if(isset($owners[$owner_id])) {echo $owners[$owner_id]->email_1;} ?>">
                         <span class="error-font text-danger" style="color: #c41f45;"><?php echo e($errors->first('email')); ?></span>
                        </div>
                      </div> 
                      <div class="row form-group">
                        <div class="col-md-12">
                          <div class="box box-danger">
                            <div class="box-header with-border">
                              <h5 class="box-title">Secondary Contact Details</h5>
                            </div>
                          </div>
                          <div class="box-body secondary-contacts">
                              <div class="row form-group">
                                <div class="form-group col-md-4">
                                  <label for="owner">Name</label>
                                  <input type="text" class="form-control" id="name" name="name" value="<?php if(isset($owners[$owner_id])) {echo $owners[$owner_id]->sec_name;} ?>">
                                    <span class="error-font text-danger" style="color: #c41f45;"><?php echo e($errors->first('name')); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="number">Contact</label>
                                  <input type="text" class="form-control" id="number" name="number" value="<?php if(isset($owners[$owner_id])) {echo $owners[$owner_id]->contact;} ?>">
                                    <span class="error-font text-danger" style="color: #c41f45;"><?php echo e($errors->first('number')); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="email-id">Email</label>
                                  <input type="text" class="form-control" id="email-id" name="email-id" value="<?php if(isset($owners[$owner_id])) {echo $owners[$owner_id]->email;} ?>">
                                    <span class="error-font text-danger" style="color: #c41f45;"><?php echo e($errors->first('email')); ?></span>
                                </div>
                            </div> 
                          </div> 
                        </div> 
                      </div> 
                    </div> 
                  </div>
                </div>
              </div>              
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h5 class="box-title">Location Details</h5>
                    </div>
                    <div class="box-body">
                      <div class="row form-group">
                        <div class="form-group col-md-6">
                          <label for="city">City</label>
                          <select class="form-control select2" id="city" name="city">
														<option value="0">Please select</option>
                            <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php if($plane->city_id == $id): ?>
                                <option value="<?php echo e($id); ?>" selected><?php echo e($city); ?></option>
                              <?php else: ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($city); ?></option>
                              <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                          <span id="city-error" class="error-font text-danger"><?php echo $errors->first('city'); ?></span>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="city">Airport</label>
                          <select class="form-control" id="airport" name="airport">
														<option value="0"></option>
                            <?php $__currentLoopData = $airports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option data-id="<?php echo e($airport->id); ?>" value="<?php echo e($airport->id); ?>" <?php if(old('airport', $plane->airport_id) == $airport->id): ?> selected <?php endif; ?>><?php echo e($airport->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                        </div>
											</div>
                      <!-- <div class="row form-group">
                        <div class="col-md-6">
                          <label for="latitude">Latitude</label>
                          <input type="text" class="form-control" id="lat" name="lat" value="<?php echo e($plane->latitude); ?>" readOnly>
                        </div>               
                        <div class="col-md-6">
                          <label for="longitude">Longitude</label>
                          <input type="text" class="form-control" id="long" name="long" value="<?php echo e($plane->longitude); ?>" readOnly>
                        </div>               
                      </div>  
                      <div class="row form-group">
                        <div class="col-md-12 text-center">	
                            <input id="search-location" class="form-control mb-2" type="text" placeholder="Search Location">
                            <div id="map" style="height: 400px; margin: 10px 0; border: 1px solid #ccc;"></div>
                            <input id="latitude" name="latitude" type="hidden">
                            <input id="longitude" name="longitude" type="hidden">
                        </div> 
                      </div> -->

                          <?php echo $__env->make('partials.location_map', [
                'latInputId' => 'lat',
                'lngInputId' => 'long',
                'mapId' => 'map',
                'latInputName' => 'lat',
                'lngInputName' => 'long',
                'latInputValue' => old('lat', old('latitude', $plane->latitude)),
                'lngInputValue' => old('long', old('longitude', $plane->longitude))
              ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>               
                  </div>               
                </div> 
              </div>   <div class="row helicopter hide">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h5 class="box-title">Helicopter Temporary Location Details</h5>
                    </div>
                    <div class="box-body">
                      <div class="row form-group">
                        <div class="col-md-4 hide">
                          <label for="gt">Ground Time</label>
                          <input type="text" class="form-control" id="gt" name="gt" value="<?php echo e($plane->gt); ?>">
                        </div> 
                        <div class="col-md-6">
                          <label for="from-date">From Date *</label>
                          <div class="input-group date" id="fromdate">								
                            <input type="text" class="form-control from-date" id="from-date" name="from-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="<?php echo e(($plane->from_date != '0000-00-00 00:00:00' ? date('d-m-Y', strtotime($plane->from_date)) : '')); ?>">
                            <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                          </div>
                          <span class="error-font text-danger"><?php echo e($errors->first('from-date')); ?></span>
                        </div>  
                        <div class="col-md-6">
                          <label for="to-date">To Date *</label>
                          <div class="input-group date" id="todate">								
                            <input type="text" class="form-control to-date" id="to-date" name="to-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY" value="<?php echo e(($plane->to_date != '0000-00-00 00:00:00' ? date('d-m-Y', strtotime($plane->to_date)) : '')); ?>">
                            <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                          </div>
                          <span class="error-font text-danger"><?php echo e($errors->first('to-date')); ?></span>
                        </div> 
                      </div> 
                      <div class="row form-group">
                        <div class="col-md-6">
                          <label for="latitude-hel">Latitude</label>
                          <input type="text" class="form-control" id="lat-hel" name="lat-hel" value="<?php echo e($plane->temp_latitude); ?>">
                        </div>               
                        <div class="col-md-6">
                          <label for="longitude-hel">Longitude</label>
                          <input type="text" class="form-control" id="long-hel" name="long-hel" value="<?php echo e($plane->temp_longitude); ?>">
                        </div>                
                      </div>  
                      <div class="row form-group">
                        <div class="form-group col-md-6">
                          <label for="city-hel">City</label>
                          <select class="form-control select2" id="city-hel" style="width:100%" name="city-hel">
														<option value="0">Please select</option>
                            <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php if($plane->temporary_city_id == $id): ?>
                                <option value="<?php echo e($id); ?>" selected><?php echo e($city); ?></option>
                              <?php else: ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($city); ?></option>
                              <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                          <span id="city-hel-error" class="error-font text-danger"><?php echo $errors->first('city-hel'); ?></span>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="airport-hel">Airport</label>
                          <select class="form-control" id="airport-hel" name="airport-hel">
														<option value="0"></option>
                            <?php $__currentLoopData = $airports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option data-id="<?php echo e($airport->id); ?>" value="<?php echo e($airport->id); ?>" <?php if(old('airport-hel', $plane->temporary_airport_id) == $airport->id): ?> selected <?php endif; ?>><?php echo e($airport->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                        </div>
											</div>
                      <div class="row form-group">
                        <div class="col-md-12 text-center">	
                          <input id="search-location-hel" class="form-control mb-2" type="text" placeholder="Search Location">
                            <div id="map-hel" style="height: 400px; margin: 10px 0; border: 1px solid #ccc;"></div>
                          <input id="latitude-hel" name="latitude-hel" type="hidden">
                          <input id="longitude-hel" name="longitude-hel" type="hidden">
                        </div> 
                      </div>	             
                    </div>               
                  </div>               
                </div> 
              </div>               
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h3 class="box-title">Display Image &nbsp;<span class="text-danger">(Recommended size: 1171 x 662 px or same aspect ratio)</span></h3>
                    </div>
                    <div class="box-body">					
                      <div class="row">		
                        <div class="col-md-6 col-md-offset-3">							
                          <div class="col-md-12">								
                            <div class="form-group property-image-container text-center" id="display-image">
                              <label id="image-box-1" class="button image-box">
                                <span>
                                <?php 
                                $display_image = old('display-image', $plane->display_image);
                                if($display_image != ''){
                                  echo '<img class=""  src="/uploads/'. $display_image.'" height="100%" />';
                                }
                                else{
                                  echo'<i class="fa fa-picture-o fa-4x"></i>';
                                }
                                ?>
                                </span> 
                                <input class="fileupload" type="file" name="files[]" data-id="1" data-url="/plane/upload">
                                <input id="image-1" type="hidden" name="display-image" value="<?php echo e(old('display-image', $plane->display_image)); ?>">
                                <div id="progress-1">
                                  <div class="bar" style="width: 0%;"></div>
                                </div>
                            </label>
                              <div>
                                <span class="error-font text-danger"><?php echo e($errors->first('display-image')); ?></span>
                              </div>
                            </div>
                          </div>
                        </div> 
                      </div>	
                    </div>              
                  </div>               
                </div> 
              </div>             
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header with-border">
                      <h3 class="box-title">Machine Images &nbsp;<span class="text-danger">(Recommended size: 445 x 350 px or same aspect ratio)</span></h3>
                    </div>
                    <div class="box-body">	
                      <div class="row">
                         <?php $i=2;?>
                          <?php $__currentLoopData = $plane_images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image_id => $plane_image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-3 ">
                              <div class="form-group">
                                <label id="image-box-<?php echo e($i); ?>" class="button image-box" style="
                                  <?php if($plane_images !="") {
                                    echo "background-image: url('/uploads/".$plane_image ."'); background-color: #FFF;"; }
                                    ?>"><span class="dummy-image-<?php echo e($i); ?> <?php if($plane_image != "" ) {echo 'hide';}?>"><i class="fa fa-picture-o fa-4x fa-picture-set"></i></span>
                                  <?php					
                                  if($plane_image != "") {?>
                                    <a id="delete-image-modal-link-<?php echo e($i); ?>" class="delete-image" href="#delete-image-modal" data-toggle="modal" data-dismiss="modal"  data-target="#delete-image-modal" data-image-id="<?php echo $image_id ; ?>"></a>
                                  <?php } else { ?>									
                                    <a id="delete-image-modal-link-<?php echo e($i); ?>" class="delete-image hide" data-image-id="0" data-index="<?php echo e($i); ?>"></a>
                                  <?php } ?>																	
                                    <input class="fileupload" type="file" name="files[]" data-id="<?php echo e($i); ?>" data-url="/plane/upload">
                                    <input id="image-<?php echo e($i); ?>" type="hidden" name="images[]" value="<?php echo e(old('images.' . ($i - 2), $plane_image)); ?>">
                                  <div id="progress-<?php echo e($i); ?>">
                                    <div class="bar" style="width: 0%;"></div>
                                  </div>
                                  </label>
                              </div>
                            </div> 
                            <?php $i++;  ?>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          <?php for($j=$i; $j<6; $j++): ?>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label id="image-box-<?php echo e($j); ?>" class="button image-box"><span><i class="fa fa-picture-o fa-4x fa-picture-set"></i></span> 
                                  <input class="fileupload" type="file" name="files[]" data-id="<?php echo e($j); ?>" data-url="/plane/upload">
                                  <input id="image-<?php echo e($j); ?>" type="hidden" name="images[]" value="<?php echo e(old('images.' . ($j - 2))); ?>">
                                <div id="progress-<?php echo e($j); ?>">
                                  <div class="bar" style="width: 0%;"></div>
                                </div>
                                </label>
                              </div>
                            </div> 
                        <?php endfor; ?>
                      </div>																 
                    </div>             
                  </div>               
                </div> 
              </div>
            </div>              
            <div class="box-footer text-center">
              <button type="submit" class="btn btn-primary btn-fixed">Submit</button>
              <a href="/plane" type="submit" class="btn btn-primary btn-back-fixed">Back</a>
            </div>
          </form>  
        </div>
      </div>
    </div>
  </section>

  <!-- Leaflet CSS & JS -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.3/leaflet.css" />
  <link rel="stylesheet" href="https://maps.locationiq.com/v2/libs/leaflet-geocoder/1.9.6/leaflet-geocoder-locationiq.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.3/leaflet.js"></script>
  <script src="https://maps.locationiq.com/v2/libs/leaflet-geocoder/1.9.6/leaflet-geocoder-locationiq.min.js"></script>
  <script src="<?php echo e(asset('js/location-map.js')); ?>"></script>

  <!-- <script>
    $(function() {
      // $(".select2").select2();
      $("#from-date, #to-date").datepicker({ format: "dd-mm-yyyy", autoclose: true });

      // Variables
      var airports = <?php echo json_encode($airports) ?>;
      var plane_type = <?php echo $plane->type_id; ?>;

      // Initialize Maps
      var mainLat = $('#lat').val() || 20.5937;
      var mainLng = $('#long').val() || 78.9629;

      var helLat = $('#lat-hel').val() || mainLat;
      var helLng = $('#long-hel').val() || mainLng;

      var mainMap = L.map('map').setView([mainLat, mainLng], 13);
      var helMap  = L.map('map-hel').setView([helLat, helLng], 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(mainMap);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(helMap);

      // Initialize Markers
      var mainMarker = L.marker([$('#lat').val(), $('#long').val()], { draggable: true }).addTo(mainMap);
      var helMarker  = L.marker([$('#lat-hel').val(), $('#long-hel').val()], { draggable: true }).addTo(helMap);
      if (plane_type == 2) {
        $(".helicopter").removeClass("hide");

        setTimeout(function () {
          helMap.invalidateSize();
          helMap.setView(
            [$('#lat-hel').val(), $('#long-hel').val()],
            13
          );
        }, 300);
      }


      // Marker Drag → Update Inputs & Nearest Airport
      function enableMarkerDrag(marker, latInput, longInput, map, airportSelect, citySelect) {
        marker.on('dragend', function() {
          var pos = marker.getLatLng();
          $(latInput).val(pos.lat.toFixed(6));
          $(longInput).val(pos.lng.toFixed(6));
          map.setView(pos);

          // AJAX: Find nearest airport (like old Google Maps)
          $.ajax({
            url: '/plane/locationwise-airport',
            type: 'GET',
            data: { lat: pos.lat, long: pos.lng },
            success: function(data) {
              var result = (typeof data === 'string') ? JSON.parse(data) : data;
              if(result.distance <= 50){
                if(result.city_id != $(citySelect).val()){
                  $(citySelect).val(result.city_id).trigger('change');
                } else {
                  $(citySelect).val(result.city_id);
                }
                $(airportSelect).html('<option value="'+result.id+'">'+result.name+'</option>');
              } else {
                $(citySelect).val(0).trigger('change');
                $(airportSelect).html('');
                alert('City not found near this location. Please select a city.');
              }
            },
            error: function(){ alert("Error finding nearest airport."); }
          });
        });
      }

      enableMarkerDrag(mainMarker, '#lat', '#long', mainMap, '#airport', '#city');
      enableMarkerDrag(helMarker, '#lat-hel', '#long-hel', helMap, '#airport-hel', '#city-hel');

      // Manual Lat/Lng Change → Marker Moves
      $('#lat, #long').on('change', function() {
        mainMarker.setLatLng([$('#lat').val(), $('#long').val()]);
        mainMap.setView([$('#lat').val(), $('#long').val()], 13);
      });
      $('#lat-hel, #long-hel').on('change', function() {
        helMarker.setLatLng([$('#lat-hel').val(), $('#long-hel').val()]);
        helMap.setView([$('#lat-hel').val(), $('#long-hel').val()], 13);
      });

      // Airport Dropdown → Marker Moves
      $('#airport').change(function() {
        var airport_id = $(this).val();
        if(airport_id && airports[airport_id]){
          var lat = airports[airport_id].latitude;
          var lng = airports[airport_id].longitude;
          $('#lat').val(lat);
          $('#long').val(lng);
          mainMarker.setLatLng([lat,lng]);
          mainMap.setView([lat,lng],13);
        }
      });
      $('#airport-hel').change(function() {
        var airport_id = $(this).val();
        if(airport_id && airports[airport_id]){
          var lat = airports[airport_id].latitude;
          var lng = airports[airport_id].longitude;
          $('#lat-hel').val(lat);
          $('#long-hel').val(lng);
          helMarker.setLatLng([lat,lng]);
          helMap.setView([lat,lng],13);
        }
      });

      var selectedMainAirportId = <?php echo e(isset($plane->airport_id) ? $plane->airport_id : 'null'); ?>;
      var selectedHelAirportId  = <?php echo e(isset($plane->temporary_airport_id) ? $plane->temporary_airport_id : 'null'); ?>;

      // City Dropdown → Load Airports via AJAX
      function normalizeAirportResponse(data) {
        if (Array.isArray(data)) {
          return data;
        }

        if (data && Array.isArray(data.data)) {
          return data.data;
        }

        if (data && typeof data === 'object') {
          return Object.keys(data).map(function (key) {
            return data[key];
          });
        }

        return [];
      }

      function loadCityAirports(citySelector, airportSelector, selectedAirportId){
        $(citySelector).off('change').on('change', function() {
          var city_id = $(this).val();
          if(city_id != 0 && city_id != null){
            $.ajax({
              url: '/plane/citywise-airports',
              type: 'GET',
              data: { 'city-id': city_id },
              dataType: 'json',
              success: function(data){
                $(airportSelector).empty().append('<option value="0">Please select</option>');

                $.each(normalizeAirportResponse(data), function(i, airport){
                  let selected = Number(airport.id) === Number(selectedAirportId) ? 'selected' : '';
                  $(airportSelector).append(
                    '<option value="'+airport.id+'" '+selected+'>'+airport.name+'</option>'
                  );
                });

                if(selectedAirportId){
                  $(airportSelector).val(selectedAirportId).trigger('change');
                }
              },
              error: function(){ alert("Error loading airports"); }
            });
          }
        });
      }
      loadCityAirports('#city', '#airport', selectedMainAirportId);
      $('#city').trigger('change');

      loadCityAirports('#city-hel', '#airport-hel', selectedHelAirportId);
      $('#city-hel').trigger('change');


      // Helicopter Section Toggle
      if(plane_type == 2) $(".helicopter").removeClass("hide");

      $('#type').change(function() {
        plane_type = $(this).val();

        if (plane_type == 2) {
          $(".helicopter").removeClass("hide");
          setTimeout(function () {
            helMap.invalidateSize();
            helMap.setView(
              [$('#lat-hel').val(), $('#long-hel').val()],
              13
            );
          }, 300);

          $('#lat, #long').attr("readonly", false);
        } else {
          $(".helicopter").addClass("hide");
          $('#lat, #long').attr("readonly", true);
        }
      });

      function searchPickLocation(query, map, marker, latInput, longInput) {
  fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1`)
    .then(res => res.json())
    .then(data => {
      if (data && data.length > 0) {
        const lat = parseFloat(data[0].lat);
        const lng = parseFloat(data[0].lon);

        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 13);

        $(latInput).val(lat.toFixed(6));
        $(longInput).val(lng.toFixed(6));

        // Optional: Update nearest airport via AJAX
        $.ajax({
          url: '/plane/locationwise-airport',
          type: 'GET',
          data: { lat, long: lng },
          success: function(data){
            var result = (typeof data === 'string') ? JSON.parse(data) : data;
            if(result.distance <= 50){
              $('#city').val(result.city_id).trigger('change');
              $('#airport').html('<option value="'+result.id+'">'+result.name+'</option>');
            } else {
              $('#city').val(0).trigger('change');
              $('#airport').html('');
            }
          }
        });

      } else {
        alert("Location not found. Please refine your search.");
      }
    });
}

      // Bind Enter and Blur for plane search
      $('#search-location').on('keypress', function(e){
        if(e.key === "Enter"){
          e.preventDefault();
          searchPickLocation($(this).val(), mainMap, mainMarker, '#lat', '#long');
        }
      });
      $('#search-location').on('blur', function(){
        if($(this).val().length >= 3){
          searchPickLocation($(this).val(), mainMap, mainMarker, '#lat', '#long');
        }
      });

      // Similarly for helicopter
      $('#search-location-hel').on('keypress', function(e){
        if(e.key === "Enter"){
          e.preventDefault();
          searchPickLocation($(this).val(), helMap, helMarker, '#lat-hel', '#long-hel');
        }
      });
      $('#search-location-hel').on('blur', function(){
        if($(this).val().length >= 3){
          searchPickLocation($(this).val(), helMap, helMarker, '#lat-hel', '#long-hel');
        }
      });

    });
  </script> -->
  
  <!-- OLD SCRIPT -->
  
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\accretion-flying\airports_web-master-flight\resources\views/admin/edit_plane.blade.php ENDPATH**/ ?>