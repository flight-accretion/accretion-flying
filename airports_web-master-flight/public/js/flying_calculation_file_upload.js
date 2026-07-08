$(function () {
  'use strict';

  var activeUploads = 0;
  var maxFileSize = 5 * 1024 * 1024;
  var acceptedFileTypes = /(\.|\/)(jpe?g|png)$/i;

  function setUploadingState() {
    var isUploading = activeUploads > 0;
    $('.profile-img-loader').toggleClass('hide', !isUploading);
    $('form').has('.fileupload').find('button[type="submit"]').prop('disabled', isUploading);
  }

  function showUploadError($input, message) {
    var $group = $input.closest('.form-group');
    $group.find('.upload-error').remove();
    $group.append('<div class="upload-error text-danger text-center">' + message + '</div>');
  }

  function clearUploadError($input) {
    $input.closest('.form-group').find('.upload-error').remove();
  }

  function placeholderIcon() {
    return '<i class="fa fa-picture-o fa-4x fa-picture-set"></i>';
  }

  function getCsrfFormData() {
    var token = $('input[name="_token"]').first().val();
    return token ? [{ name: '_token', value: token }] : [];
  }

  function parseUploadResult(result) {
    if (typeof result === 'string') {
      try {
        return JSON.parse(result);
      } catch (e) {
        return null;
      }
    }

    return result;
  }

  function getUploadedFile(result) {
    if (!result) {
      return null;
    }

    if (result.files) {
      if (Array.isArray(result.files)) {
        return result.files.length ? result.files[0] : null;
      }

      return result.files;
    }

    if (result.file) {
      return result.file;
    }

    return result.name ? result : null;
  }

  $('.fileupload').fileupload({
    dataType: 'json',
    formData: getCsrfFormData,
    add: function (e, data) {
      var $input = $(this);
      var file = data.originalFiles && data.originalFiles.length ? data.originalFiles[0] : null;
      var fileType = file ? (file.type || file.name || '') : '';
      var uploadErrors = [];

      clearUploadError($input);

      if (!file) {
        uploadErrors.push('Please select an image.');
      } else {
        if (!acceptedFileTypes.test(fileType)) {
          uploadErrors.push('Only JPG, JPEG and PNG images are allowed.');
        }

        if (file.size > maxFileSize) {
          uploadErrors.push('Image size should be within 5MB.');
        }
      }

      if (uploadErrors.length > 0) {
        showUploadError($input, uploadErrors.join('<br>'));
        return;
      }

      activeUploads++;
      setUploadingState();

      var imageBox = '#image-box-' + $input.data('id');
      $(imageBox + ' span').addClass('hide');
      data.submit();
    },
    done: function (e, data) {
      var $input = $(this);
      var results = parseUploadResult(data.result);
      var file = getUploadedFile(results);

      if (!file || !file.name || file.error) {
        showUploadError($input, file && file.error ? file.error : 'Image upload failed.');
        return;
      }

      var imageName = file.name;
      var imageUrl = '/uploads/' + encodeURIComponent(imageName);
      var id = $input.data('id');
      var image = '#image-' + id;
      var imgDelete = '#delete-' + id;
      var imageBox = '#image-box-' + id;
      var progressBar = '#progress-' + id;

      if (!$(image).length) {
        showUploadError($input, 'Image field not found. Please refresh and try again.');
        return;
      }

      $(image).val(imageName).attr('value', imageName);
      $(imgDelete).val(imageName);
      $(imageBox + ' span')
        .removeClass('hide')
        .html('<img src="' + imageUrl + '" class="upload-preview-image" alt="Uploaded image">');
      $(imageBox).css('background-image', 'url(' + imageUrl + ')');
      $(imageBox).css('background-size', 'contain');
      $(imageBox).css('background-repeat', 'no-repeat');
      $(imageBox).css('background-position', 'center');
      $(imageBox + '.button').css('background-color', '#FFFFFF');
      $(progressBar + ' .bar').css('width', '0%');
      clearUploadError($input);
    },
    fail: function (e, data) {
      var $input = $(this);
      var message = 'Image upload failed.';

      if (data.jqXHR && data.jqXHR.responseJSON && data.jqXHR.responseJSON.message) {
        message = data.jqXHR.responseJSON.message;
      } else if (data.errorThrown) {
        message = data.errorThrown;
      } else if (data.textStatus) {
        message = data.textStatus;
      }

      showUploadError($input, message);
    },
    always: function () {
      activeUploads = Math.max(activeUploads - 1, 0);
      setUploadingState();
    },
    progress: function (e, data) {
      var progress = parseInt(data.loaded / data.total * 100, 10);
      var progressBar = '#progress-' + $(this).data('id');
      $(progressBar + ' .bar').css('width', progress + '%');
    }
  });

  $('form').has('.fileupload').on('submit', function (e) {
    if (activeUploads > 0) {
      e.preventDefault();
      alert('Please wait, image upload is still in progress.');
      return;
    }

    $(this).find('.fileupload').prop('disabled', true);
  });

  $('.delete-uploaded-image').click(function() {
    var id = this.id;
    var name = $('#' + id).val();
    var dataId = $(this).data('id');

    $.ajax({
      url: '/plane/upload?file=' + name,
      type: 'DELETE',
      data: '',
      success: function()
      {
        var imageBox = '#image-box-' + dataId;
        $(imageBox).css('background-image', '');
        $(imageBox + ' span').removeClass('hide').html(placeholderIcon());
        $(imageBox + '.button').css('background-color', '');
        var image = '#image-' + dataId;
        $(image).val('');
        $('#' + id).val('');
      },
      error: function(jqXHR)
      {
        console.log(jqXHR);
      }
    });
  });
});
