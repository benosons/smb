console.log('versi Jquery adalah '+$.fn.jquery);
$(document).ready(function(){
  $('li#li-smb', parent.document).removeClass('mm-active');
  $('li#li-smb > a', parent.document).removeClass('mm-active');

  $('li#li-dashboard', parent.document).addClass('mm-active');
  $('li#li-dashboard > a', parent.document).addClass('mm-active');


  setTimeout(function(){
    var iframe = window.parent.document.getElementById('dashboard-frame');
    parent.resizeIframe( iframe );
  }, 2000);
});
