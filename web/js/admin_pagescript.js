// tinyMCE
$(function() {
  $('textarea.tinymce').tinymce({
    script_url: '/js/tinymce/tinymce.min.js',
    theme: "modern",
    height: "350",
    selector: "textarea",
    language: "bs",
    menu: 'false',
    plugins: "wordcount save",
    toolbar: " save | undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent |  removeformat image",
    save_enablewhendirty: true,
    save_onsavecallback: function() {
      ESarticle.saveArticle();
    },
    theme_advanced_buttons1: 'image',
    setup: function(ed) {
      // Register example button
      ed.addButton('image', {
        title: 'add image',
        onclick: function() {
          //ed.windowManager.alert('Hello world!! Selection: ' + ed.selection.getContent({format: 'text'}));
          $('#addPhoto').modal();
        }
      });
    }
  });

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
    $('#filelist').html("Add photo fot upload");
  });
  $('#uploadfiles').click(function(e) {
    //uploader.start();
    console.log('uploading');
    e.preventDefault();
  });
  uploader.init();
  uploader.bind('FilesAdded', function(up, files) {
    $.each(files, function(i, file) {
      $('#filelist').append(
        '<div id="' + file.id + '">' +
        file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
        '</div>');
      console.log(file);
      document.EStest = file;
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
    ESarticle.showPicture(data.id);
  });
});

// Object for menage ajax call

var ESarticle = {
  // id of article returned from server
  articleId: 0,
  showPicture: function(id) {
    console.log(id);
  },
  saveArticle: function() {
    var self = this,
      title = $(document).find('#title').val(),
      content = tinymce.activeEditor.getContent();
    // Check for article ID from url for new or edit method
    if (this.getArticleId() == 0) {
      // Post to new
      $.post("//webdev.dev/admin/articles/new", {id: this.articleId, title: title, body: content})
        .done(function(data) {
          // set article id
          self.articleId = data.id;
        }, "json");
    } else {
      // Post to edit
      $.post(
        "//webdev.dev/admin/article/" + this.articleId,
        {id: this.articleId, title: title, body: content}
      );
    }
  },
  getArticleId: function() {
    if (this.articleId != 0) {
      return this.articleId;
    }

    var url = document.location.pathname,
      id = url.substr(url.lastIndexOf('/') + 1);

    if (!parseInt(id))
    {
      return this.articleId = 0;
    }
    return this.articleId = id;
  }
};