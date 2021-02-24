console.log('versi Jquery adalah '+$.fn.jquery);
$(document).ready(function(){
  $('li#li-dashboard', parent.document).removeClass('mm-active');
  $('li#li-dashboard > a', parent.document).removeClass('mm-active');

  $('li#li-users', parent.document).removeClass('mm-active');
  $('li#li-users > a', parent.document).removeClass('mm-active');

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
            let desc = '';
            switch (value) {
              case 'Digi Hotel':
                desc      = "Solusi Hotel & Reservasi. Semua layanan kamar hotel dapat diakses melalui aplikasi seluler yang memungkinkan para tamu untuk meminta layanan pengiriman makanan dan minuman, binatu, perlengkapan mandi, bahkan layanan wakeup call";
                break;
              case 'Digi Clinic':
                desc      = "Aplikasi sistem informasi aplikasi manajemen sistem informasi berbasis web service untuk pelayanan dokter pribadi dan klinik serta dapat diakses kapan saja dimana saja melalui HP dan Komputer";
                break;
              case 'Digi ERP':
                desc      = "Aplikasi Bisnis Berbasis Cloud untuk Menunjang Usaha Anda dalam Mengelola Laporan Keuangan, Akuntansi, Penjualan, Pembelian, Inventory, Aset dan Manajemen Sumber Daya Manusia";
                break;
              case 'Bonum':
                desc      = "Bonum adalah sebuah aplikasi Point Of Sales (POS) dari Telkom Indonesia. Saat ini, Bonum telah dipakai oleh banyak UKM dan pemilik bisnis di Indonesia";
                break;
              case 'Sakoo':
                desc      = "Sakoo (Satu Toko Online) merupakan aplikasi berbasis web yang menyediakan dan mengintegrasikan channel penjualan offline dan online sehingga dapat membantu pemilik bisnis untuk meningkatkan efektivitas dan efisiensi dalam berjualan";
                break;
              default:

            }
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
                          <li class="p-0 list-group-item" style="height: 210px;">
                              <div class="widget-content">
                                  <div class="text-center">
                                      <h6 class="widget-heading mb-0 opacity-4" style="text-align: justify;">`+desc+`.</h6>
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
                                              <button onclick="parent.openProfile('`+value.toLowerCase()+`')" class="btn-icon-vertical btn-transition-text btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-warning"><i
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
