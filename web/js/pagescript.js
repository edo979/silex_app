// Active tab in navbar
//$(function() {
//  var url = document.location.pathname,
//      page = '', pageUp = '';
//      
//  if(url.length == 1){
//    // Active class for home meny
//    page = 'home'; pageUp = 'Home';
//  } else if(url.indexOf("/articles/new") != -1 || url.indexOf("/article/") != -1) {
//    // Active class for articles meny
//    page = 'article'; pageUp = 'Articles';
//  } else {
//    // Active class for all other
//    page = url.substr(url.lastIndexOf('/') + 1);
//    pageUp = page.charAt(0).toUpperCase() + page.slice(1);
//  }
//  
//  $("#" + page + " nav.navbar li a:contains('"+pageUp+"')")
//    .first()
//    .parent()
//    .addClass('active');
//});

$(function() {
  var page = $('body').attr('id'),
    pageNavName = page.charAt(0).toUpperCase() + page.slice(1);
    
    $("#" + page + " nav.navbar li a:contains('"+pageNavName+"')")
      .first()
      .parent()
      .addClass('active'); 
});