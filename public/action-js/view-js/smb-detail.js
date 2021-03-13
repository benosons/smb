console.log('versi Jquery adalah '+$.fn.jquery);

$(document).ready(function(){
  $('li#li-dashboard', parent.document).removeClass('mm-active');
  $('li#li-dashboard > a', parent.document).removeClass('mm-active');

  $('li#li-users', parent.document).removeClass('mm-active');
  $('li#li-users > a', parent.document).removeClass('mm-active');

  $('li#li-smb', parent.document).addClass('mm-active');
  $('li#li-smb > a', parent.document).addClass('mm-active');

  $('#smb-product').on('click', function(){
      resize();
  });

  let ven = vendor.replace(/%20/g, " ");
  $('#vendor-name').html(ven);
  $('#vendor-name-1').html(ven);
  $('#vendor-logo-').attr('src', '/assets/images/logo/'+ven+'.png');
  $('#vendor-name-2').html(ven.toUpperCase());
  loadsales(vendor);

});

function loadsales(table){
    let isObject   = {};
    isObject.table = table;

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: baseURL + '/jsondata/loadsales',
		data :{
			iparam	    : cryptoEncrypt(PHRASE, isObject),
		},
		success: function(response){
      if(response.code == CODE_SUCCESS){
          let result = cryptoDecrypt(PHRASE, response.data);
          let data = result.data;
          console.log(data)
          // $('#total_traffic').html(data.total_traffic+' <small class="opacity-5 pl-1">This Month</small>');
          // $('#churn_rate').html(data.churn_rate+' <small class="opacity-5 pl-1">%</small>');
          // $('#active_user').html(data.active_user+' <small class="opacity-5 pl-1">Monthly</small>');
          // $('#sign_register_user').html(parseInt(data.active_user)+parseInt(data.new_user)+' <small class="opacity-5 pl-1">This Month</small>');
          // $('#total-revenue').html(formatRupiah(data.total_expense, 'Rp '));
          $('#total_traffic').html(data.total_traffic);
          $('#churn_rate').html(data.churn_rate);
          $('#active_user').html(data.active_user);
          $('#sign_register_user').html(parseInt(data.active_user)+parseInt(data.new_user));
          $('#total-revenue').html(formatRupiah(data.total_expense, 'Rp '));
      }
    }
  });
}

function goback(){
  parent.$('#li-smb > a').trigger('click');
}

function detail(param, show){
  if(show === true){
    $('#btn-detail-graphic').hide();
    $('#btn-back-summary').show();

    $('#sales-performance').hide();
    $('#sales-performance-detail').show();

    resize();
  }else{
    $('#btn-detail-graphic').show();
    $('#btn-back-summary').hide();

    $('#sales-performance').show();
    $('#sales-performance-detail').hide();

    resize();
  }

}

function resize(){
  setTimeout(function(){
    var iframe = window.parent.document.getElementById(vendor+'-frame');
    parent.resizeIframe( iframe );
  }, 2000);
}

function formatRupiah(angka, prefix){
  var number_string = angka.replace(/[^,\d]/g, '').toString(),
  split   		= number_string.split(','),
  sisa     		= split[0].length % 3,
  rupiah     		= split[0].substr(0, sisa),
  ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

  // tambahkan titik jika yang di input sudah menjadi angka ribuan
  if(ribuan){
    separator = sisa ? '.' : '';
    rupiah += separator + ribuan.join('.');
  }

  rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
  return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}
