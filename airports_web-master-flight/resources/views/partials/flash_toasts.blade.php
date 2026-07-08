<style>
  .flash-toast-wrapper {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    width: 320px;
    max-width: calc(100% - 40px);
  }

  .flash-toast-wrapper .alert {
    margin-bottom: 10px;
    box-shadow: 0 8px 22px rgba(0, 0, 0, 0.18);
    border-radius: 4px;
  }
</style>

<div class="flash-toast-wrapper">
  @if(Session::has('success'))
    <div class="alert alert-success alert-dismissable flash-toast">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      {{ Session::get('success') }}
    </div>
  @endif

  @if(Session::has('error'))
    <div class="alert alert-danger alert-dismissable flash-toast">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      {{ Session::get('error') }}
    </div>
  @endif

  @if(session('status'))
    <div class="alert alert-success alert-dismissable flash-toast">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      {{ session('status') }}
    </div>
  @endif

  @if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissable flash-toast">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      Please check the highlighted fields.
    </div>
  @endif
</div>

<script>
  $(function(){
    setTimeout(function(){
      $('.flash-toast').fadeOut(400, function(){
        $(this).remove();
      });
    }, 4000);
  });
</script>
