console.log('versi Jquery adalah '+$.fn.jquery);
$(document).ready(function(){
  $('li#li-smb', parent.document).removeClass('mm-active');
  $('li#li-smb > a', parent.document).removeClass('mm-active');

  $('li#li-dashboard', parent.document).addClass('mm-active');
  $('li#li-dashboard > a', parent.document).addClass('mm-active');

  // setTimeout(function(){
  //   var iframe = window.parent.document.getElementById('dashboard-frame');
  //   parent.resizeIframe( iframe );
  // }, 2000);

  $('#custom-inp-top').on('change', function(){
    switch (this.value) {
      case 'Last Week':
          $("#new-account").html(7);
          $("#total-expenses").html(1);
          $("#company-value").html(8);
          $("#new-employees").html(2);
        break;
      case 'Last Month':
          $("#new-account").html(29);
          $("#total-expenses").html(7);
          $("#company-value").html(32);
          $("#new-employees").html(11);
        break;
      case 'Last Year':
          $("#new-account").html(348);
          $("#total-expenses").html(91);
          $("#company-value").html(145);
          $("#new-employees").html(80);
        break;
      default:

    }
  });

});
