// Active tab in navbar
$(function() {
  var url = document.location.pathname,
      page = '', pageUp = '';
      
  if(url.length == 1){
    page = 'home'; pageUp = 'Home';
  }else {
    page = url.substr(url.lastIndexOf('/') + 1);
    pageUp = page.charAt(0).toUpperCase() + page.slice(1);
  }
  
  $("#" + page + " nav.navbar li a:contains('"+pageUp+"')")
    .first()
    .parent()
    .addClass('active');
});