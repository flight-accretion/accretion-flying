<div class="location-map-widget"
     data-lat-input-id="<?php echo e($latInputId ?? 'latitude'); ?>"
     data-lng-input-id="<?php echo e($lngInputId ?? 'longitude'); ?>"
     data-map-id="<?php echo e($mapId ?? 'map'); ?>"
     data-default-lat="<?php echo e($defaultLat ?? 19.0549990); ?>"
     data-default-lng="<?php echo e($defaultLng ?? 72.8692035); ?>"
     data-zoom="<?php echo e($zoom ?? 13); ?>"
     data-locationiq-key="<?php echo e(env('LOCATIONIQ_API_KEY', '')); ?>">
  <div class="row">
    <div class="form-group col-md-6">
      <label for="address">Latitude *</label>
      <input type="text" class="form-control" id="<?php echo e($latInputId ?? 'latitude'); ?>" name="<?php echo e($latInputName ?? 'latitude'); ?>" value="<?php echo e($latInputValue ?? old($latInputName ?? 'latitude') ?? ($defaultLat ?? '')); ?>">
      <span class="error-font text-danger"><?php echo e($errors->first($latInputName ?? 'latitude')); ?></span>
    </div>
    <div class="form-group col-md-6">
      <label for="address">Longitude *</label>
      <input type="text" class="form-control" id="<?php echo e($lngInputId ?? 'longitude'); ?>" name="<?php echo e($lngInputName ?? 'longitude'); ?>" value="<?php echo e($lngInputValue ?? old($lngInputName ?? 'longitude') ?? ($defaultLng ?? '')); ?>">
      <span class="error-font text-danger"><?php echo e($errors->first($lngInputName ?? 'longitude')); ?></span>
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-12">
      <div style="position: relative; height: 400px;">
        <div id="<?php echo e($mapId ?? 'map'); ?>" style="height: 100%; width: 100%;"></div>
      </div>
    </div>
  </div>
</div>
<?php /**PATH C:\xampp\htdocs\accretion-flying\airports_web-master-flight\resources\views/partials/location_map.blade.php ENDPATH**/ ?>