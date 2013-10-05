$(function(){
  // Active tab in navbar
  // TODO write loop for adding class to meny
  $("#home nav.navbar li a:contains('Home')").parent().addClass('active');
  $("#gallery nav.navbar li a:contains('Gallery')").parent().addClass('active');
  $("#about nav.navbar ul li a:contains('About')").first().parent().addClass('active');
  $("#example nav.navbar ul li a:contains('Example')").first().parent().addClass('active');
  $("#contact nav.navbar ul li a:contains('Contact')").first().parent().addClass('active');
});