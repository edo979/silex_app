$(function() {
// Active tab in navbar
// TODO write loop for adding class to meny
  $("#home nav.navbar li a:contains('Home')").parent().addClass('active');
  $("#gallery nav.navbar li a:contains('Gallery')").parent().addClass('active');
  $("#about nav.navbar ul li a:contains('About')").first().parent().addClass('active');
  $("#example nav.navbar ul li a:contains('Example')").first().parent().addClass('active');
  $("#contact nav.navbar ul li a:contains('Contact')").first().parent().addClass('active');
  $("#login nav.navbar ul li a:contains('Admin')").first().parent().addClass('active');
});
// tinyMCE
$(function() {
  var addImage = {
    getWin: function() {
      return (!window.frameElement && window.dialogArguments) || opener || parent || top;
    }
  };
});
// Upload and resize image
/*jslint unparam: true, regexp: true */
/*global window, $ */
$(function() {
  'use strict';
  // Change this to the location of your server-side upload handler:
  var url = window.location.hostname === 'webdev.dev' ?
      '//webdev.dev/' : 'server/php/',
      uploadButton = $('<button/>')
      .addClass('btn btn-primary')
      .prop('disabled', true)
      .text('Processing...')
      .on('click', function() {
    var $this = $(this),
        data = $this.data();
    $this
        .off('click')
        .text('Abort')
        .on('click', function() {
      $this.remove();
      data.abort();
    });
    data.submit().always(function() {
      $this.remove();
    });
  });
  $('#fileupload').fileupload({
    url: url,
    dataType: 'json',
    autoUpload: false,
    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
    maxFileSize: 5000000, // 5 MB
    // Enable image resizing, except for Android and Opera,
    // which actually support image resizing, but fail to
    // send Blob objects via XHR requests:
    disableImageResize: /Android(?!.*Chrome)|Opera/
        .test(window.navigator && navigator.userAgent),
    imageMaxWidth: 800,
    imageMaxHeight: 800,
    imageCrop: true, // Force cropped images
    previewMaxWidth: 100,
    previewMaxHeight: 100,
    previewCrop: true
  }).on('fileuploadadd', function(e, data) {
    data.context = $('<div/>').appendTo('#files');
    $.each(data.files, function(index, file) {
      var node = $('<p/>')
          .append($('<span/>').text(file.name));
      if (!index) {
        node
            .append('<br>')
            .append(uploadButton.clone(true).data(data));
      }
      node.appendTo(data.context);
    });
  }).on('fileuploadprocessalways', function(e, data) {
    var index = data.index,
        file = data.files[index],
        node = $(data.context.children()[index]);
    if (file.preview) {
      node
          .prepend('<br>')
          .prepend(file.preview);
    }
    if (file.error) {
      node
          .append('<br>')
          .append($('<span class="text-danger"/>').text(file.error));
    }
    if (index + 1 === data.files.length) {
      data.context.find('button')
          .text('Upload')
          .prop('disabled', !!data.files.error);
    }
  }).on('fileuploadprogressall', function(e, data) {
    var progress = parseInt(data.loaded / data.total * 100, 10);
    $('#progress .progress-bar').css(
        'width',
        progress + '%'
        );
  }).on('fileuploaddone', function(e, data) {
    $.each(data.result.files, function(index, file) {
      if (file.url) {
        var link = $('<a>')
            .attr('target', '_blank')
            .prop('href', file.url);
        $(data.context.children()[index])
            .wrap(link);
      } else if (file.error) {
        var error = $('<span class="text-danger"/>').text(file.error);
        $(data.context.children()[index])
            .append('<br>')
            .append(error);
      }
    });
  }).on('fileuploadfail', function(e, data) {
    $.each(data.files, function(index, file) {
      var error = $('<span class="text-danger"/>').text('File upload failed.');
      $(data.context.children()[index])
          .append('<br>')
          .append(error);
    });
  }).prop('disabled', !$.support.fileInput)
      .parent().addClass($.support.fileInput ? undefined : 'disabled');
});