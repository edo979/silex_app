// tinyMCE
$(function() {
  $('textarea.tinymce').tinymce({
    script_url: '/js/tinymce/tinymce.min.js',
    theme: "modern",
    height: "350",
    selector: "textarea",
    language: "bs",
    menu: 'false',
    content_css: '/css/bootstrap.min.css',
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
          $('#addPhoto').modal();
        }
      });
    }
  });
});

// Upload and resize image
// Custom example logic
$(function() {
  var uploader = new plupload.Uploader({
    runtimes: 'html5, gears,flash,silverlight,browserplus',
    browse_button: 'pickfiles',
    container: 'container',
    multi_selection: false,
    max_file_size: '3mb',
    url: '//webdev.dev/admin/images/new',
    filters: [
      {title: "Image files", extensions: "jpg,gif,png"},
      {title: "Zip files", extensions: "zip"}
    ],
    resize: {width: 800, height: 600, quality: 90, crop: false}
  });
  uploader.bind('Init', function(up, params) {
    $('#filelist').html("");
  });
  $('#uploadfiles').click(function(e) {
    uploader.start();
    e.preventDefault();
  });
  uploader.init();
  uploader.bind('FilesAdded', function(up, files) {
    $.each(files, function(i, file) {
      if (uploader.files.length != 1) {
        console.log(files);
        up.splice(0, 1);
      }
      $('#filelist').empty();
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
    up.splice();
    up.refresh();
    $('#filelist').empty();
    $('#addPhoto').modal('hide');
    // get image id from response
    var data = $.parseJSON(info.response);
    // show picture
    ESarticle.showPicture(data.imageId);
  });

  $('#addPhoto').on('hidden.bs.modal', function() {
    uploader.splice();
    uploader.refresh();
    $('#filelist').empty();
  });
});

// My Listeners
$(function() {
  $('#savePublishedArticle').on('click', function() {
    ESarticle.saveArticle();
  });

  $(document).bind("DOMNodeRemoved", function(e) {
    var images = [];
    if (e.target.innerHTML == 'img') {
      $('#body_ifr').contents().find('img').each(function() {
        images.push($(this).attr('src'));
      });
      console.log(images);
    }
  });
});

// My triggers
$(function() {
  $('button span.glyphicon-arrow-up').parent().on('click', function() {
    ESarticle.insertImageFromPanel($(this));
  });
});

// Object for menage ajax call
var ESarticle = {
  // id of article returned from server
  articleId: 0,
  showPicture: function(id) {
    // show image in editor
    tinymce.EditorManager
      .activeEditor
      .insertContent("<img width='200px' src='//webdev.dev/image/" + id + "'>");
    // save article
    this.saveArticle();
    this.showNewImage(id);
  },
  saveArticle: function() {
    var self = this,
      title = $(document).find('#title').val(),
      publishDate = $(document).find('#datepicker.date').val(),
      content = tinymce.activeEditor.getContent();
    // Check for article ID from url for new or edit method
    if (this.getArticleId() == 0) {
      // Post to new
      $.post("//webdev.dev/admin/articles/new", {
        id: self.articleId,
        publishDate: publishDate,
        title: title,
        body: content,
        publish: self.getPublishState()
      })
        .done(function(data) {
          // set article id
          self.articleId = data.articleId;
          var result = true;
          self.serverInfo(result, data)
        }, 'json')
        .fail(function(data) {
          var result = false;
          self.serverInfo(result, data)
        }, 'json');
    } else {
      // Post to edit
      $.post(
        "//webdev.dev/admin/article/" + this.articleId, {
          id: self.articleId,
          publishDate: publishDate,
          title: title,
          body: content,
          publish: self.getPublishState()
        })
        .done(function(data) {
          var result = true;
          self.serverInfo(result, data)
        }, 'json')
        .fail(function(data) {
          var result = false;
          self.serverInfo(result, data)
        }, 'json');
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
  },
  getPublishState: function() {
    var selectValue = $('input:radio:checked').val();
    if (selectValue && selectValue == 'publish') {
      return 1;
    }
    return 0;
  },
  serverInfo: function(serverStatus, data) {
    var panel = $(document).find('#panelServerInfo'),
      label = panel.find('.panel-heading').find('div').hide();
    if (serverStatus) {
      // Set class and text
      panel.removeClass()
        .addClass('panel panel-success')
        .find('.panel-body').find('span').text(data.modified);
      // add label and animate
      label.empty().stop().append("<span class='label label-success'>Spremljeno</span>")
        .fadeToggle(400, function() {
          var $this = $(this);
          // Wait 2s and remove label
          setTimeout(
            function()
            {
              $this.fadeToggle(400);
            }, 2000);
        });
    } else {
      $('#panelServerInfo').removeClass()
        .addClass('panel panel-danger');
    }
  },
  showNewImage: function(id) {
    var section = $('section.manage-images');
    // remove last
    if (section.find('div.row div').length > 2) {
      section.find('div.row div:last').remove();
    }
    // Clone and manipulate id of image
    section.find('.row > div:first')
      .clone().find('img').attr('src', '/image/' + id)
      .end()
      .prependTo(section.find('div.row'));
  },
  insertImageFromPanel: function(button) {
    // grab image src
    var imageSrc = button.parents('div#manage-image-icon').find('img').attr('src'),
      id;
    // get id
    id = imageSrc.substring(imageSrc.lastIndexOf('/') + 1);
    // insert image into editor
    tinymce.EditorManager
      .activeEditor
      .insertContent("<img width='200px' src='//webdev.dev/image/" + id + "'>");
  }
};

// Date picker
$(function() {
  // set bs language
  $.fn.datepicker.dates['bs'] = {
    days: ["Nedjelja", "Ponedjeljak", "Utorak", "Srijeda", "Četvrtak", "Petak", "Subota", "Nedjelja"],
    daysShort: ["Ned", "Pon", "Uto", "Sri", "Čet", "Pet", "Sub", "Ned"],
    daysMin: ["Ne", "Po", "Ut", "Sr", "Če", "Pe", "Su", "Ne"],
    months: ["Januar", "Februar", "Mart", "April", "Maj", "Juni", "Juli", "Avgust", "Septembar", "Oktobar", "Novembar", "Decembar"],
    monthsShort: ["Jan", "Feb", "Mar", "Apr", "Maj", "Jun", "Jul", "Avg", "Sep", "Okt", "Nov", "Dec"],
    today: "Danas",
    clear: "Očisti"
  };
  // Call datepicker
  $('#datepicker.date').datepicker({
    format: "yyyy-mm-dd",
    weekStart: 1,
    autoclose: true,
    language: 'bs',
    todayHighlight: true
  });
});