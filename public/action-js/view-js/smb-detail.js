console.log('versi Jquery adalah '+$.fn.jquery);
$(document).ready(function(){
  $('li#li-dashboard', parent.document).removeClass('mm-active');
  $('li#li-dashboard > a', parent.document).removeClass('mm-active');

  $('li#li-smb', parent.document).addClass('mm-active');
  $('li#li-smb > a', parent.document).addClass('mm-active');
});
