// tinyMCE
$(function() {
  var addImage = {
    getWin: function() {
      return (!window.frameElement && window.dialogArguments) || opener || parent || top;
    }
  };
});

// Upload and resize image
function log() {
  var str = "";

  plupload.each(arguments, function(arg) {
    var row = "";

    if (typeof(arg) != "string") {
      plupload.each(arg, function(value, key) {
        // Convert items in File objects to human readable form
        if (arg instanceof plupload.File) {
          // Convert status to human readable
          switch (value) {
            case plupload.QUEUED:
              value = 'QUEUED';
              break;

            case plupload.UPLOADING:
              value = 'UPLOADING';
              break;

            case plupload.FAILED:
              value = 'FAILED';
              break;

            case plupload.DONE:
              value = 'DONE';
              break;
          }
        }

        if (typeof(value) != "function") {
          row += (row ? ', ' : '') + key + '=' + value;
        }
      });

      str += row + " ";
    } else {
      str += arg + " ";
    }
  });

  console.log(str + "\n");
}
// Custom example logic
var uploader = new plupload.Uploader({
  runtimes: 'html5,html4',
  browse_button: 'pickfiles', // you can pass in id...
  container: document.getElementById('container'), // ... or DOM Element itself
  url: '//webdev.dev/admin/photos/new',
  filters: {
    max_file_size: '3mb',
    mime_types: [
      {title: "Image files", extensions: "jpg,gif,png"},
      {title: "Zip files", extensions: "zip"}
    ]
  },
  // User can upload no more then 20 files in one go (sets multiple_queues to false)
  max_file_count: 5,
  chunk_size: '1mb',
  // Resize images on clientside if we can
  resize: {
    width: 800,
    height: 800,
    quality: 90,
    crop: false // crop to exact dimensions
  },
  init: {
    PostInit: function() {
      document.getElementById('filelist').innerHTML = '';

      document.getElementById('uploadfiles').onclick = function() {
        uploader.start();
        return false;
      };
    },
    FilesAdded: function(up, files) {
      plupload.each(files, function(file) {
        document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
      });
    },
    UploadProgress: function(up, file) {
      document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
    },
    FileUploaded: function(up, file, info) {
      // Called when a file has finished uploading
      var data = $.parseJSON(info.response);
      console.log(data.id);
    },
    Error: function(up, err) {
      document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
    }
  }
});

uploader.init();