// tinyMCE
$(function() {
  var addImage = {
    getWin: function() {
      return (!window.frameElement && window.dialogArguments) || opener || parent || top;
    }
  };
});
// Upload and resize image
// Custom example logic
$(function() {
  var uploader = new plupload.Uploader({
    runtimes: 'html5, gears,flash,silverlight,browserplus',
    browse_button: 'pickfiles',
    container: 'container',
    max_file_size: '3mb',
    url: '//webdev.dev/admin/photos/new',
    filters: [
      {title: "Image files", extensions: "jpg,gif,png"},
      {title: "Zip files", extensions: "zip"}
    ],
    resize: {width: 800, height: 600, quality: 90, crop: false}
  });
  uploader.bind('Init', function(up, params) {
    $('#filelist').html("<div>Current runtime: " + params.runtime + "</div>");
  });
  $('#uploadfiles').click(function(e) {
    uploader.start();
    e.preventDefault();
  });
  uploader.init();
  uploader.bind('FilesAdded', function(up, files) {
    $.each(files, function(i, file) {
      $('#filelist').append(
              '<div id="' + file.id + '">' +
              file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
              '</div>');
    });
    up.refresh(); // Reposition Flash/Silverlight
  });
  uploader.bind('UploadProgress', function(up, file) {
    $('#' + file.id + " b").html(file.percent + "%");
  });
  uploader.bind('Error', function(up, err) {
    $('#filelist').append("<div>Error: " + err.code +
            ", Message: " + err.message +
            (err.file ? ", File: " + err.file.name : "") +
            "</div>"
            );
    up.refresh(); // Reposition Flash/Silverlight
  });
  uploader.bind('FileUploaded', function(up, file, info) {
    $('#' + file.id + " b").html("100%");
    var data = $.parseJSON(info.response);
    // show picture
    ES.showPicture(data.id);
  });
});

var ES = {
  showPicture: function(id) {
    console.log(id);
  }
}; 