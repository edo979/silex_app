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
// Custom example logic
var uploader = new plupload.Uploader({
  runtimes: 'html5,html4',
  browse_button: 'pickfiles', // you can pass in id...
  container: document.getElementById('container'), // ... or DOM Element itself
  url: '//webdev.dev/admin/photos/new',
  filters: {
    max_file_size: '5mb',
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
    height: 600,
    quality: 90,
    crop: false // crop to exact dimensions
  },
  // Rename files by clicking on their titles
  rename: true,
  // Views to activate
  views: {
    list: true,
    thumbs: true, // Show thumbs
    active: 'thumbs'
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
    Error: function(up, err) {
      document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
    }
  }
});

uploader.init();