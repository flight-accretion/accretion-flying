<?php $__env->startSection('content'); ?>
  <section class="content plane-container">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Machines</h3>
            <div id="search-plane" class="pull-right"></div>
          </div>
          <div class="box-body">
            <table id="table-plane" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="5%" class="text-center">Sr. No.</th>
                  <th>Name</th>
                  <th>Call Sign(VT)</th>
                  <th>Type</th>
                  <th>SubType</th>
                  <th>Owner</th>
                  <th>Owner Contact</th>
                  <th class="text-center">Price/Hour ( <i class="fa fa-rupee"></i> )</th>
                  <th class="text-center">Speed ( nm/hr )</th>
                  <th>City</th>
                  <th width="15%" class="text-center">Options</th>
                </tr>
              </thead>
              <tbody>
              <?php $i = 1; ?>
                <?php $__currentLoopData = $planes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plane): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td class="text-center"><?php echo e($i++); ?></td>
                    <td><?php echo e($plane->name); ?></td>
                    <td><?php echo e($plane->Call_Sign); ?></td>
                    <td><?php echo e($types[$plane->type_id]); ?> </td>
                    <?php if(isset($plane_subtypes[$plane->subtype])): ?>
                    <td><?php echo e($plane_subtypes[$plane->subtype]); ?> </td>
                    <?php else: ?>
                      <td>Null</td>
                    <?php endif; ?>
                    <?php if($plane->owner_id != 0): ?>
                      <td><?php echo e($owners[$plane->owner_id]); ?></td>
                    <?php else: ?>
                      <td></td>
                    <?php endif; ?>
                    <?php if($plane->owner_id != 0): ?>
                      <td><?php echo e($owner_contact[$plane->owner_id]); ?></td>
                    <?php else: ?>
                      <td></td>
                    <?php endif; ?>
                    <td class="text-center"><?php echo e($plane->price_per_hour); ?></td>
                    <td class="text-center"><?php echo e($plane->speed); ?></td>
                    <td><?php echo e($cities[$plane->city_id]); ?></td>
                    <td width="15%" class="text-center">
                      <a href="/plane/edit?plane-id=<?php echo e($plane->id); ?>" type="button" class="btn label label-primary">Edit</a>
                      <a href="/plane/view?plane-id=<?php echo e($plane->id); ?>" type="button" class="btn label label-info">View</a>
                      <a type="button" class="btn label label-danger delete-plane-link" data-plane-id="<?php echo e($plane->id); ?>">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
            <div class="row">
              <div id="page-link-wrapper" class="col-md-12 text-center"></div>	
            </div>
          </div>
        </div>
      </div> 
    </div>
    
    <!-- ========== DELETE MODEL START ========== -->  
    <div class="modal fade" id="delete-plane-modal" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-md modal-teal">
        <div class="modal-content">
          <form id="form-delete-plane" role="form" method="POST" action="<?php echo e(url('/plane/delete')); ?>" novalidate>
            <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h2 class="modal-title text-center color-skyblue">Plane</h2>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="text-center">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">               
                    <input type="hidden" id="plane-id" name="plane-id">
                    <h4>Are you sure to delete this plane?</h4>
                  </div>
                </div>
              </div>      
            </div>
            <div class="modal-footer">
              <div class="row">
                <div class="col-md-8 col-md-offset-4">
                  <input type="submit" class="btn btn-primary" value="Yes">
                  <a href="/" class="btn btn-primary" data-dismiss="modal">No</a> 
                </div>
              </div>            
            </div>
          </form>
        </div>
      </div> 
    </div>
    <!-- ========== DELETE MODEL END ========== -->
  </section>
  <script type="text/javascript">
		$(function(){
			$('#table-plane').dataTable( {				
				"bLengthChange": false,
				"iDisplayLength": 6,
				"infoEmpty": "<center><div class='text-info'><br>No plane available</div></center>",
				"oLanguage": {
          "sEmptyTable":"<center><div class='text-info'><br>No plane available</div></center>",
          "sSearch": "",
          "oPaginate": {
            "sNext": '>',
            "sLast": '>|',
            "sFirst": '|<',
            "sPrevious": '<'
          }
        },
        "bSort" : true  					 
			});
			$('.dataTables_filter input').attr("placeholder", "Search");
			$('.dataTables_filter input').removeClass("input-sm");
      $('.dataTables_filter input').addClass("form-control");
			$("#table-plane_info").detach().appendTo('#page-link-wrapper');
			$("#table-plane_paginate").detach().appendTo('#page-link-wrapper');
			$("#table-plane_filter").detach().appendTo('#search-plane');
      
      //Delete plane   
      $(document).on('click', '.delete-plane-link', function(ev) {
        ev.preventDefault();	
        var plane_id = $(this).data('plane-id'); 
        $('#plane-id').val(plane_id);
        $('#delete-plane-modal').modal('show');
      });   
		});
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\accretion-flying\airports_web-master-flight\resources\views/admin/view_planes.blade.php ENDPATH**/ ?>