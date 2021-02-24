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

  loadexp();
});

function loadexp(){
    let isObject   = {};
    isObject.table = '';

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: baseURL + '/jsondata/loadexp',
		data :{
			iparam	    : cryptoEncrypt(PHRASE, isObject),
		},
		success: function(response){

      if(response.code == CODE_SUCCESS){
          let result = cryptoDecrypt(PHRASE, response.data);
          let contet = '';
          let i = 1;
          $.each(result.data, function(key, value) {
            console.log(result.data[key]);
            contet += `<tr>
                <td class="text-center text-muted" style="width: 80px;">#`+i+++`</td>
                <td class="text-center" style="width: 80px;">
                    <img width="40" class="rounded-circle" src="`+result.data[key]['image']+`" alt="">
                </td>
                <td class="text-center"><a href="#" onclick="parent.openDetailVisit('`+result.data[key]['name'].toLowerCase()+`')">`+result.data[key]['name']+`</a></td>

                <td class="text-center">
                    <span class="pr-2 opacity-6">
                        <i class="fa fa-business-time"></i>
                    </span>
                    `+result.data[key]['date']+`
                </td>
                <td class="text-center" style="width: 200px;">
                    <div class="widget-content p-0">
                        <div class="widget-content-outer">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left pr-2">
                                    <div class="widget-numbers fsize-1 text-danger">`+result.data[key]['total']+`</div>
                                </div>
                                <div class="widget-content-right w-100">
                                    <div class="progress-bar-xs progress">
                                        <div class="progress-bar bg-danger" role="progressbar"
                                            aria-valuenow="71" aria-valuemin="0" aria-valuemax="100" style="width: 71%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>`;

          });

          $('#body-expense').html(contet);
        }
      }
    })
  }
