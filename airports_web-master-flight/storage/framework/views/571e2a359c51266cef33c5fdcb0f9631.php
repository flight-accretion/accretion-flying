<?php $__env->startSection('content'); ?>

   <header>
    <div class="header-content text-center">
      <div class="header-content-inner"> 
				<div class="row">
					<div class="col-md-12 no-padding">
						<h1>Machine Details</h1>
					</div>
				</div>
			</div>
		</div>
    <div class="header-content hide">
      <div class="header-content-inner">
        <form id="search-planes" role="form" method="GET" action="<?php echo e(url('/plane/search')); ?>">
          <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"> 
          <div class="row">
            <div class="col-md-2 col-md-offset-1 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="trips" name="trips">
                  <option value="0" selected>Single Trip</option>
                  <option value="1">Round Trip</option>
                  <!--option value="2">Multi City Trip</option-->
                </select>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="planes" name="planes">
                  <option value="0" selected>Machine Type</option>
                  <?php $__currentLoopData = $plane_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $plane_type_name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($id == $plane_type): ?>
                      <option value="<?php echo e($id); ?>" data-id="<?php echo e($id); ?>" selected><?php echo e($plane_type_name); ?></option>
                    <?php else: ?>
                      <option value="<?php echo e($id); ?>" data-id="<?php echo e($id); ?>"><?php echo e($plane_type_name); ?></option>
                    <?php endif; ?>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-2 col-md-offset-1 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="departure" name="departure">
                  <option value="0" selected>Departure</option>
                  <?php $__currentLoopData = $airports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($airport->id == $departure): ?>
                      <option value="<?php echo e($airport->id); ?>" data-lat="<?php echo e($airport->latitude); ?>"  data-long="<?php echo e($airport->longitude); ?>"selected><?php echo e($airport->name); ?><?php if(isset($cities[$airport->city_id])): ?>,  <?php echo e($cities[$airport->city_id]->name); ?> <?php endif; ?></option>
                    <?php else: ?>
                      <option value="<?php echo e($airport->id); ?>" data-lat="<?php echo e($airport->latitude); ?>"  data-long="<?php echo e($airport->longitude); ?>" ><?php echo e($airport->name); ?><?php if(isset($cities[$airport->city_id])): ?>,  <?php echo e($cities[$airport->city_id]->name); ?> <?php endif; ?></option>
                    <?php endif; ?>                   
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <input type="hidden" id="dep-latitude" name="dep-latitude"  value="<?php echo e($latitude); ?>">
                  <input type="hidden" id="dep-longitude" name="dep-longitude"  value="<?php echo e($longitude); ?>">
                </select>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="arrival" name="arrival">
                  <option value="0" selected>Arrival</option>
                  <?php $__currentLoopData = $airports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($airport->id == $arrival): ?>
                      <option value="<?php echo e($airport->id); ?>" selected><?php echo e($airport->name); ?><?php if(isset($cities[$airport->city_id])): ?>,  <?php echo e($cities[$airport->city_id]->name); ?> <?php endif; ?></option>
                    <?php else: ?>
                      <option value="<?php echo e($airport->id); ?>" ><?php echo e($airport->name); ?><?php if(isset($cities[$airport->city_id])): ?>, <?php echo e($cities[$airport->city_id]->name); ?> <?php endif; ?></option>
                    <?php endif; ?> 
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <input type="number" class="form-control search-planes-element" id="adults" placeholder="Adults" name="adults" value="<?php echo e($adults); ?>" min=1 oninput="validity.valid||(value='');">
                <span class="error-font text-danger"><?php echo e($errors->first('adults')); ?></span>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="input-group date" id="from-date">								
                <input type="text" class="form-control date-time-picker search-planes-element" id="date" name="date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY"  value="<?php echo e($date); ?>" tabindex="2">
              </div>
              <span class="error-font text-danger"><?php echo e($errors->first('date')); ?></span>
            </div>
            <div class="col-md-2 no-padding" id="btn-search">
              <button type="submit" class="btn btn-search-plane">SEARCH</button>
            </div>
          </div>
          <div class="row hide" id="round-trip">
            <div class="col-md-2 col-md-offset-1 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="round-departure" name="round-departure">
                  <option value="0" selected>Departure</option>
                  <?php $__currentLoopData = $airports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($airport->id); ?>"><?php echo e($airport->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <select class="form-control search-planes-element select2" id="round-arrival" name="round-arrival">
                  <option value="0" selected>Arrival</option>
                  <?php $__currentLoopData = $airports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($airport->id); ?>"><?php echo e($airport->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="form-group">
                <input type="number" class="form-control search-planes-element" id="round-adults" placeholder="Adults" name="round-adults" value="<?php echo e($adults); ?>" min=1 oninput="validity.valid||(value='');">
                <span class="error-font text-danger"><?php echo e($errors->first('adults')); ?></span>
              </div>
            </div>
            <div class="col-md-2 no-padding">
              <div class="input-group date" id="from-date">								
                <input type="text" class="form-control date-picker search-planes-element" id="round-date" name="from-date" placeholder="dd-mm-yyyy" data-date-format="DD-MM-YYYY"  value="<?php echo e($date); ?>" tabindex="2">
                
              </div>
              <span class="error-font text-danger"><?php echo e($errors->first('from-date')); ?></span>
            </div>
            <div class="col-md-2 no-padding hide" id="btn-search-round">
              <button type="submit" class="btn btn-search-plane">SEARCH</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </header>
  
  <section id="contact">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h2 class="section-heading"><?php echo e($plane->name); ?></h2>
          <hr>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 text-center">
          <?php if($plane->display_image != ''): ?>
            <div class="col-md-8 col-md-offset-2">
                <div class="plane-image gallery-image" style="background-image:url('/uploads/<?php echo e($plane->display_image); ?>');">
                  <div class="plane-details-image"></div>
                </div>
            </div>
          <?php else: ?>
            <i class="fa fa-picture-o fa-4x"></i>
          <?php endif; ?>
        </div>
      </div>
      <div class="row">
        <div class="plane-details">
          <ul class="nav nav-tabs nav-justified" role="tablist">
            <li role="presentation" class="active"><a href="#flight-details" aria-controls="flight-details" role="tab" data-toggle="tab"><h4>Flight Details</h4></a></li>
            <li role="presentation"><a href="#cost" aria-controls="cost" role="tab" data-toggle="tab"><h4>Cost Estimate</h4></a></li>
            <li role="presentation"><a href="#owner-details" aria-controls="owner-details" role="tab" data-toggle="tab"><h4>Owner Details</h4></a></li>
          </ul>
          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="flight-details">
              <div class="row">
                <h4>Machine : <?php echo e($plane->name); ?> (<i class="fa fa-rupee"></i><?php echo e($plane->price_per_hour); ?> per hour)</h4>
                <table class="table table-hover table-bordered">
                  <thead>
                    <tr>
                      <th>Departure Time</th>
                      <th>Departure</th>
                      <th>Flight Time</th>
                      <th>Arrival</th>
                      <th>Arrival Time</th>
                      <th>Particular</th>
                    </tr>
                  </thead>
                  <tbody>
										<?php $__currentLoopData = $flights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                      <td><?php echo e(date('d F, Y H:i', strtotime($flight['departure_time']))); ?></td>
                      <td><?php echo e($flight['departure']); ?></td>
                      <td><?php echo e(( $flight['hours'] != 0 ? $flight['hours'].' hour ' : '').($flight['minutes'] != 0 ?$flight['minutes'].' minute':'')); ?></td>
                      <td><?php echo e($flight['arrival']); ?></td>
                      <td><?php echo e(date('d F, Y H:i', strtotime($flight['arrival_time']))); ?></td>
                      <td><?php echo e($flight['details']); ?></td>
                    </tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="cost">
              <div class="row">
                <h4>Machine : <?php echo e($plane->name); ?> (<i class="fa fa-rupee"></i><?php echo e($plane->price_per_hour); ?> per hour)</h4>
                
                 <table class="table table-hover table-bordered">
                  <thead>
                    <tr>
                      <th>Departure Time</th>
                      <th>Departure</th>
                      <th>Flight Time</th>
                      <th>Arrival</th>
                      <th>Arrival Time</th>
                      <th>Distance (In NM)</th>
                      <th>Cost (In Rs.)</th>
                      <th>Particular</th>
                    </tr>
                  </thead>
                  <tbody>
										<?php $__currentLoopData = $flights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                      <td><?php echo e(date('d F, Y H:i', strtotime($flight['departure_time']))); ?></td>
                      <td><?php echo e($flight['departure']); ?></td>
                      <td><?php echo e(( $flight['hours'] != 0 ? $flight['hours'].' hour ' : '').($flight['minutes'] != 0 ?$flight['minutes'].' minute':'')); ?></td>
                      <td><?php echo e($flight['arrival']); ?></td>
                      <td><?php echo e(date('d F, Y H:i', strtotime($flight['arrival_time']))); ?></td>
                      <td><?php echo e(round($flight['distance'],2)); ?></td>
                      <td><?php echo e(round($flight['cost'],2)); ?></td>
                      <td><?php echo e($flight['details']); ?></td>
                    </tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </tbody>
                </table>
              </div>
              <div class="row">
								<div class="col-md-6 col-md-offset-3">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th>Cost Details</th>
												<th class="text-right">Amount</th>
											</tr>
										</thead>
										<tbody>
                      <?php
                        $display_total_hours = $total_hours;
                        $display_total_mins = $total_mins;
                        $display_flight_time_hours = isset($flight_time_hours) ? $flight_time_hours : $total_hours;
                        $display_flight_time_mins = isset($flight_time_minutes) ? $flight_time_minutes : $total_mins;
                        $display_flower_shower_time = isset($flower_shower_time) ? (int) $flower_shower_time : 0;
                        $fuel_hault = 0;
                        $fuel_cost = 0;

                        if($display_flower_shower_time > 0){
                          $total_minutes_with_flower_shower = ((int)$total_hours * 60) + (int)$total_mins + $display_flower_shower_time;
                          $total_hours = floor($total_minutes_with_flower_shower / 60);
                          $total_mins = $total_minutes_with_flower_shower % 60;
                          $display_total_hours = $total_hours;
                          $display_total_mins = $total_mins;
                        }

                        if(isset($plane_type) && $plane_type == 2){
                          $flight_minutes_for_hault = ((int)$total_hours * 60) + (int)$total_mins;
                          $remaining_minutes_for_hault = $flight_minutes_for_hault;

                          while($remaining_minutes_for_hault > 90){
                            $fuel_hault += 30;
                            $remaining_minutes_for_hault -= 90;
                          }

                          $fuel_cost = ($fuel_hault/60) *  $plane->price_per_hour;
                          $final_total_minutes = $flight_minutes_for_hault + $fuel_hault;
                          $display_total_hours = floor($final_total_minutes / 60);
                          $display_total_mins = $final_total_minutes % 60;
                        }
                      ?>
                      <tr>
                        <td>Flight Time</td>
                        <td class="text-right"><?php echo e($display_flight_time_hours); ?> Hrs <?php echo e($display_flight_time_mins); ?> Mins</td>
                      </tr>
                      <?php if($display_flower_shower_time > 0): ?>
                        <tr>
                          <td>Flower Shower Time</td>
                          <td class="text-right"><?php echo e(floor($display_flower_shower_time / 60)); ?> Hrs <?php echo e($display_flower_shower_time % 60); ?> Mins</td>
                        </tr>
                      <?php endif; ?>
                       <tr>
                      <td>Fuel Hault</td>
                      <td class="text-right"><?php echo e((int)($fuel_hault/60)); ?> Hrs <?php echo e((int)$fuel_hault%60); ?> Mins</td>
                    </tr>
											<tr>
												<td>Total Flight Time</td>
												<td class="text-right"><?php echo e($display_total_hours); ?> Hrs <?php echo e($display_total_mins); ?> Mins</td>
											</tr>
                      <?php if(isset($plane_type) && $plane_type==2): ?>
                   
                   
                    <?php endif; ?>
 											<?php 
												$flying_hours = $display_total_hours;
												$flying_mins = $display_total_mins;
											?>
											<?php if(($stay_time_hours + $stay_time_minutes) != 0): ?>
                        <tr>
                          <td>Stay Time</td>
                          <td class="text-right"><?php echo e($stay_time_hours); ?> Hrs <?php echo e($stay_time_minutes); ?> Mins</td>
                        </tr>
                        <?php 
                          $total_billing_minutes = ($stay_time_hours + $display_total_hours)*60 + $display_total_mins + $stay_time_minutes ;
                          $total_hours = $billing_hours = floor($total_billing_minutes / 60);
                          $total_mins = $billing_minutes = ($total_billing_minutes % 60);
                        ?>
                        <tr>
                          <td>Total Billing Time</td>
                          <td class="text-right"><?php echo e($billing_hours); ?> Hrs <?php echo e($billing_minutes); ?> Mins</td>
                        </tr>
											<?php endif; ?>
											<tr>
												<td>Flying Cost </td>
												<?php 
													$total_flying_cost += $additional_cost; 
                          if(isset($plane_type) && $plane_type == 2){
                            $total_flying_cost += $fuel_cost;
                            }
													$flying_cost = $total_flying_cost; 
												?>
												<td class="text-right"><?php echo e(round($total_flying_cost)); ?></td>
											</tr>
											<tr>
												<td>Ground Handling</td>
												<td class="text-right"><?php echo e($ground_handling); ?></td>
											</tr>
											<tr>
												<td>Crew Handling</td>
												<td class="text-right"><?php echo e(round($crew_handling)); ?></td>
											</tr>
											<?php if($plane->type_id == 3 && $medical_cost > 0): ?>
												<tr>
													<td>Fixed Medical Team Cost</td>
													<td class="text-right"><?php echo e(round($medical_cost)); ?></td>
												</tr>
											<?php endif; ?>
											<tr>
												<td>Other Charges</td>
												<td class="text-right">As per actual</td>
											</tr>
											<tr>
												<td>Sub Total</td>  
												<td class="text-right"><?php echo e(round($total_flying_cost + $ground_handling + $crew_handling + $medical_cost)); ?></td>
											</tr>
											<?php if($plane->type_id != 3): ?>
												<tr>
													<td>GST @ 18%</td>
													<td class="text-right"><?php echo e(round((($total_flying_cost + $ground_handling + $crew_handling + $medical_cost ) * 18/100),2)); ?></td>
												</tr>
												<tr>
													<td>Grand Total</td>
													<?php $grand_total = round(($total_flying_cost + $ground_handling + $crew_handling + $medical_cost + (($total_flying_cost + $ground_handling + $crew_handling + $medical_cost ) * 18/100))); ?>
													<td class="text-right"><?php echo e(round(($total_flying_cost + $ground_handling + $crew_handling + $medical_cost + (($total_flying_cost + $ground_handling + $crew_handling + $medical_cost ) * 18/100)))); ?></td>
												</tr>
											<?php else: ?>
												<tr>
													<td>Grand Total</td>
													<?php $grand_total = round(($total_flying_cost + $ground_handling + $crew_handling + $medical_cost )); ?>
													<td class="text-right"><?php echo e(round(($total_flying_cost + $ground_handling + $crew_handling + $medical_cost ))); ?></td>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
              </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="owner-details">
              <div class="row">
                <h4>Machine : <?php echo e($plane->name); ?> (<i class="fa fa-rupee"></i><?php echo e($plane->price_per_hour); ?> per hour)</h4>
                  <table class="table table-hover table-bordered">
                  <thead>
                    <tr>
                      <th colspan="2" class="text-center" width="50%">Owner Details</th>
                      <th colspan="2" class="text-center" width="50%">Secondary Contact Details</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th>Name</th>
                      <td><?php echo e($owner_details['name']); ?></td>
                      <th>Name</th>
                      <td><?php echo e($owner_details['sec_name']); ?></td>
                    </tr>
                    <tr>
                      <th>Email</th>
                      <td><?php echo e($owner_details['email1']); ?></td>
                      <th>Email</th>
                      <td><?php echo e($owner_details['sec_email']); ?></td>
                    </tr>
                    <tr>
                      <th>Contact</th>
                      <td><?php echo e($owner_details['contact1']); ?></td>
                      <th>Contact</th>
                      <td><?php echo e($owner_details['sec_contact']); ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      <div class="row">
          
        <div class="col-md-12 text-left" style="display: flex; justify-content: center;">
          <form id="flight-details" role="form" method="POST" action="<?php echo e(url('/plane/machine-details-report')); ?>">
						<br/>
            <button type="submit" class="btn btn-export text-white" style="width: 200px;">Export Details</button>
             <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"> 
             <input id="plane-id" type="hidden" name="plane-id" value="<?php echo e($plane->id); ?>" />
             <input id="plane-name" type="hidden" name="plane-name" value="<?php echo e($plane->name); ?>" />
             <input id="total-hours" type="hidden" name="total-hours" value="<?php echo e($display_total_hours); ?>" />
             <input id="total-mins" type="hidden" name="total-mins" value="<?php echo e($display_total_mins); ?>" />
             <input id="total-flying-cost" type="hidden" name="total-flying-cost" value="<?php echo e($total_flying_cost); ?>" />
             <input id="ground-handling" type="hidden" name="ground-handling" value="<?php echo e($ground_handling); ?>" />
             <input id="crew-handling" type="hidden" name="crew-handling" value="<?php echo e($crew_handling); ?>" />
             <input id="medical-cost" type="hidden" name="medical-cost" value="<?php echo e($medical_cost); ?>" />
             <input id="flights" type="hidden" name="flights" value="<?php echo e(json_encode($flights)); ?>" />
          </form>
        </div>
      </div>
  </section>
  
  <section>
    <div class="container">
  </section>

  <!-- ========== IMAGE MODAL ========== -->
  <?php
    $carousel_images = [];
    foreach($plane_images as $image) {
      if(!empty($image->images)) {
        $carousel_images[] = $image->images;
      }
    }
    if(count($carousel_images) === 0 && !empty($plane->display_image)) {
      $carousel_images[] = $plane->display_image;
    }
  ?>

<div role="dialog" id="image-modal" class="modal fade">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <?php if(count($carousel_images) > 0): ?>
        <div id="carousel-example-generic" class="carousel slide" <?php if(count($carousel_images) > 1): ?> data-ride="carousel" <?php endif; ?>>
          <!-- Indicators -->
          <?php if(count($carousel_images) > 1): ?>
          <ol class="carousel-indicators">
            <?php for($n=0; $n<count($carousel_images); $n++): ?>
              <li data-target="#carousel-example-generic" data-slide-to="<?php echo e($n); ?>" class="<?php echo e($n == 0 ? 'active' : ''); ?>"></li>
            <?php endfor; ?>
          </ol>
          <?php endif; ?>
               

          <!-- Wrapper for slides -->
          <div class="carousel-inner" role="listbox">
            <?php $__currentLoopData = $carousel_images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="item <?php echo e($key == 0 ? 'active' : ''); ?>">
                <img src="/uploads/<?php echo e($image); ?>" alt="<?php echo e($plane->name); ?>">
                <div class="carousel-caption">
                  <h3><?php echo e($plane->name); ?></h3>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
               

          <!-- Controls -->
          <?php if(count($carousel_images) > 1): ?>
          <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
          <?php endif; ?>
        </div>
        <?php else: ?>
          <div class="text-center"><i class="fa fa-picture-o fa-4x"></i></div>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- =================================================== -->

<script>
	$(function () {
      $("#date").datepicker({
        format: "dd-mm-yyyy",
        autoclose: true,
      });
      
      $(document).on("click", ".gallery-image", function(){
		  $("#image-modal").modal('show');
		});
    
    //Initialize Select2 Elements
    $(".select2").select2();   
      
	});
   
	$('.btn').on('click', function(){
      $('.btn').removeClass('selected');
      $(this).addClass('selected');
  });
  
</script>

<?php if(config('app.debug')): ?>
<script>
  // console.group('Flight calculation debug');
  // console.log('Flights from backend', <?php echo json_encode($flights, 15, 512) ?>);
  // console.log('Route match debug', <?php echo json_encode($route_debug ?? [], 15, 512) ?>);
  console.groupEnd();
</script>
<?php endif; ?>

<script>
  $(document).ready(function(){
    $("select.country").change(function(){
        var selectedCountry = $(".country option:selected").val();
        alert("You have selected the country - " + selectedCountry);
    });
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.plane_header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\accretion-flying\airports_web-master-flight\resources\views/plane_details.blade.php ENDPATH**/ ?>