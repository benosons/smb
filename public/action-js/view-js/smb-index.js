console.log('versi Jquery adalah '+$.fn.jquery);
$(document).ready(function(){
  $('li#li-dashboard', parent.document).removeClass('mm-active');
  $('li#li-dashboard > a', parent.document).removeClass('mm-active');

  $('li#li-smb', parent.document).addClass('mm-active');
  $('li#li-smb > a', parent.document).addClass('mm-active');
  loadtable('');
});

function loadtable(table){
    let isObject   = {};
    isObject.table = table;
    $('#for-content').empty();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: baseURL + '/jsondata/loadtable',
		data :{
			iparam	    : cryptoEncrypt(PHRASE, isObject),
		},
		success: function(response){

      if(response.code == CODE_SUCCESS){
          let result = cryptoDecrypt(PHRASE, response.data);
          let data = result.data;
          let name = [];
          for (var i = 0; i < data.length; i++) {
              let tbl = data[i].table_name;
              switch (tbl) {
                case 'dma_digibiz_mytds_dxb_digihotel_dashboard_v':
                    name.push("Digi Hotel");
                  break;
                case 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v':
                    name.push("Digi Clinic");
                  break;
                case 'dma_digibiz_mytds_dxb_digierp_dashboard_v':
                    name.push("Digi ERP");
                  break;
                case 'dma_digibiz_bonum_user':
                    name.push("Bonum");
                  break;
                case 'dma_digibiz_sakoo_user':
                    name.push("Sakoo");
                  break;
                default:

              }
            // let tbl = data[i].table_name.split('dma_digibiz_').pop().split('_')[0];
            // name[tbl] = data[i].table_name;
          }
          console.log(name);
          // let names = Object.assign({}, name);
          let content = '';
          $.each(name, function(index, value) {
              content += `<div class="col-sm-12 col-md-3 col-xl-3">
                  <div class="card-shadow-primary card-border mb-3 profile-responsive card">
                    <div class="dropdown-menu-header">
                        <div class="dropdown-menu-header-inner bg-primary">
                            <div class="menu-header-content">
                                <div class="avatar-icon-wrapper mb-3 avatar-icon-xl">
                                    <div class="avatar-icon">
                                        <img src="/assets/images/avatars/`+value+`.jpg" alt="Avatar 5">
                                    </div>
                                </div>
                                <div>
                                    <h5 class="menu-header-title">`+value.toUpperCase()+`</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                      <ul class="list-group list-group-flush">
                          <li class="p-0 list-group-item">
                              <div class="widget-content">
                                  <div class="text-center">
                                      <h5 class="widget-heading mb-0 opacity-4">Aenean commodo ligula eget dolor.
                                          Aenean massa. Cum sociis natoque.</h5>
                                  </div>
                              </div>
                          </li>
                          <li class="p-0 list-group-item">
                              <div class="grid-menu grid-menu-2col">
                                  <div class="no-gutters row">
                                      <div class="col-sm-6">
                                          <div class="p-1">
                                              <button onclick="parent.openDetailVisit('`+value.toLowerCase()+`')" class="btn-icon-vertical btn-transition-text btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-warning"><i
                                                      class="lnr-chart-bars text-warning opacity-7 btn-icon-wrapper mb-2"></i>Visit
                                              </button>
                                          </div>
                                      </div>
                                      <div class="col-sm-6">
                                          <div class="p-1">
                                              <button class="btn-icon-vertical btn-transition-text btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-warning"><i
                                                      class="lnr-license text-warning opacity-7 btn-icon-wrapper mb-2"></i>Profile
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </li>
                      </ul>
                  </div>
              </div>`;
          });

          $('#for-content').append(content);

      }
    }
  });
}
