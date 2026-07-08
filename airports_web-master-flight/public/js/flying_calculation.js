$(function () {
    
  //Handle modal scroll issue
  $('#sign-up-modal').on("shown.bs.modal", function() {
    $("body").addClass("modal-open");
  });
  
  $('#sign-up-modal').on("hide.bs.modal", function() {
    $("body").addClass("remove-padding");
  }); 
  
  $('#sign-in-modal').on("shown.bs.modal", function() {
    $("body").addClass("modal-open");
  });
  
  $('#sign-in-modal').on("hide.bs.modal", function() {
    $("body").addClass("remove-padding");
  }); 
  
  $('#forgot-password-modal').on("shown.bs.modal", function() {
    $("body").addClass("modal-open");
  });
  
  $('#forgot-password-modal').on("hide.bs.modal", function() {
    $("body").addClass("remove-padding");
  }); 
	
  
  //User SignUp
	/* $("#form-sign-up").submit(function(ev){
    ev.preventDefault();	
    $(this).find(".text-danger").remove();
		$(this).find(".has-error").removeClass("has-error");
    $("#sign-up-loading").show();
    $('.sign-up-button').attr('disabled', true);
    var formURL = $(this).attr("action");
    var postData = $(this).serializeArray(); 
    $.ajax({
      url: formURL,
      type: 'POST',
      data: postData,
      success: function(data, textStatus, jqXHR){
				$('#sign-up-modal').modal('hide');	
        $("#sign-up-loading").hide();
        //location.reload();  
				window.location.href = data;            
      },
      error: function(jqXHR, textStatus, errorThrown){
        $('.sign-up-button').attr('disabled', false);
        $("#sign-up-loading").hide();
        var errResponse = JSON.parse(jqXHR.responseText);
				if (errResponse.error) {
					$.each(errResponse.error, function(index, value)
					{ 	
						if (value.length != 0)
            {
              var $inpElm = $("#" + index);
              $inpElm.closest('.form-group').addClass('has-error');
              $inpElm.closest('.form-group').append('<span class="text-danger">' + value + '</span>');
            }
					});
				}
      },
    });
  }); */
  
  //User SignIn
	$("#form-sign-in").submit(function(ev){
    ev.preventDefault();	
    $(this).find(".text-danger").remove();
		$(this).find(".has-error").removeClass("has-error");
    $("#sign-in-loading").show();
    $('.sign-in-button').attr('disabled', true);
    var formURL = $(this).attr("action");
    var postData = $(this).serializeArray(); 
    $.ajax({
      url: formURL,
      type: 'POST',
      data: postData,
      success: function(data, textStatus, jqXHR){
				$('#sign-in-modal').modal('hide');   
        $("#sign-in-loading").hide();        
        //location.reload();
				window.location.href = data;        
      },
      error: function(jqXHR, textStatus, errorThrown){
        $('.sign-in-button').attr('disabled', false);
        $("#sign-in-loading").hide();  
        var errResponse = JSON.parse(jqXHR.responseText);
				if (errResponse.error) {
					$.each(errResponse.error, function(index, value)
					{ 	
						if (value.length != 0)
            {
              var $inpElm = $("#" + index);
              $inpElm.closest('.form-group').addClass('has-error');
              $inpElm.closest('.form-group').append('<span class="text-danger">' + value + '</span>');
            }
					});
				}
      },
    });
  });
	
	
})