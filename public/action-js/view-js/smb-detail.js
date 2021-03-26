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
          // $('#total_traffic').html(data.total_traffic+' <small class="opacity-5 pl-1">This Month</small>');
          // $('#churn_rate').html(data.churn_rate+' <small class="opacity-5 pl-1">%</small>');
          // $('#active_user').html(data.active_user+' <small class="opacity-5 pl-1">Monthly</small>');
          // $('#sign_register_user').html(parseInt(data.active_user)+parseInt(data.new_user)+' <small class="opacity-5 pl-1">This Month</small>');
          // $('#total-revenue').html(formatRupiah(data.total_expense, 'Rp '));
          if(table == 'bonum'){
            $('#total_traffic').html(data.total_traffic);
            $('#churn_rate').html(data.churn_rate);
            $('#active_user').html(data.active_user);
            $('#sign_register_user').html(parseInt(data.active_user)+parseInt(data.new_user));
            $('#total-revenue').html(formatRupiah(data.total_expense, 'Rp '));
            $('#total-merchant').html(data.total_merchant);
            $('#total-merchant-ver').html(data.total_merchant_ver);
            $('#total-merchant-regis').html(data.total_merchant_regis);
            $('#total-merchant-aktif').html(data.total_merchant_aktif);
            $('#total-merchant-membeli').html('-');
            $('#total-teman').html(data.total_teman);
          }
      }
    }
  });
}

function loadsalesdetail(table){
    let isObject   = {};
    isObject.table = table;

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: baseURL + '/jsondata/load-sales-detail',
		data :{
			iparam	    : cryptoEncrypt(PHRASE, isObject),
		},
		success: function(response){
      if(response.code == CODE_SUCCESS){
          let result = cryptoDecrypt(PHRASE, response.data);
          let data = result.data;
          // $('#total_traffic').html(data.total_traffic+' <small class="opacity-5 pl-1">This Month</small>');
          // $('#churn_rate').html(data.churn_rate+' <small class="opacity-5 pl-1">%</small>');
          // $('#active_user').html(data.active_user+' <small class="opacity-5 pl-1">Monthly</small>');
          // $('#sign_register_user').html(parseInt(data.active_user)+parseInt(data.new_user)+' <small class="opacity-5 pl-1">This Month</small>');
          // $('#total-revenue').html(formatRupiah(data.total_expense, 'Rp '));
          if(table == 'bonum'){
            console.log(data);

              let data_merch_regis           = data.detail_merchant_regis;
              let periode_merch_regis        = [];
              let total_merch_regis          = [];

              let data_new_user              = data.detail_new_user;
              let periode_new_user           = [];
              let total_new_user             = [];

              let data_active_user           = data.detail_active_user;
              let periode_active_user        = [];
              let total_active_user          = [];

              let data_merch_ver             = data.detail_merchant_ver;
              let periode_merch_ver          = [];
              let total_merch_ver            = [];

              let data_merch_active          = data.detail_merchant_active;
              let periode_merch_active       = [];
              let total_merch_active         = [];

              let data_detail_transaksi      = data.detail_transaksi;
              let for_detail_transaksi  = [];

              for (var i = 0; i < data_merch_regis.length; i++) {
                periode_merch_regis.push(data_merch_regis[i].periode);
                total_merch_regis.push(data_merch_regis[i].total);
              }

              for (var i = 0; i < data_new_user.length; i++) {
                periode_new_user.push(data_new_user[i].periode);
                total_new_user.push(data_new_user[i].total);
              }

              for (var i = 0; i < data_active_user.length; i++) {
                periode_active_user.push(data_active_user[i].periode);
                total_active_user.push(data_active_user[i].total);
              }

              for (var i = 0; i < data_merch_ver.length; i++) {
                periode_merch_ver.push(data_merch_ver[i].periode);
                total_merch_ver.push(data_merch_ver[i].total);
              }

              for (var i = 0; i < data_merch_active.length; i++) {
                periode_merch_active.push(data_merch_active[i].periode);
                total_merch_active.push(data_merch_active[i].total);
              }

              for (var i = 0; i < data_detail_transaksi.length; i++) {
                let dat = {
                              x: data_detail_transaksi[i].province ? data_detail_transaksi[i].province : 'None' ,
                              y: data_detail_transaksi[i].total
                            }

                for_detail_transaksi.push(dat);
              }

              var options_merch_regis = {
              series: [{
                name: 'Merchan Registrasi',
                data: total_merch_regis
              }],
              chart: {
                height: 350,
                type: 'bar',
              },
              plotOptions: {
                bar: {
                  // distributed: true,
                  borderRadius: 10,
                  dataLabels: {
                    position: 'top', // top, center, bottom
                  },
                }
              },
              dataLabels: {
                enabled: true,
                formatter: function (val) {
                  return val + "";
                },
                offsetY: -20,
                style: {
                  fontSize: '12px',
                  colors: ["#304758"]
                }
              },

              xaxis: {
                categories: periode_merch_regis,
                position: 'top',
                axisBorder: {
                  show: false
                },
                axisTicks: {
                  show: false
                },
                crosshairs: {
                  fill: {
                    type: 'gradient',
                    gradient: {
                      colorFrom: '#D8E3F0',
                      colorTo: '#BED1E6',
                      stops: [0, 100],
                      opacityFrom: 0.4,
                      opacityTo: 0.5,
                    }
                  }
                },
                tooltip: {
                  enabled: true,
                }
              },
              yaxis: {
                axisBorder: {
                  show: false
                },
                axisTicks: {
                  show: false,
                },
                labels: {
                  show: false,
                  formatter: function (val) {
                    return val + "";
                  }
                }

              },
              title: {
                text: 'Merchant melakukan Registrasi berdasarkan periode',
                floating: true,
                offsetY: 330,
                align: 'center',
                style: {
                  color: '#444'
                }
              }
              };

              var options_data_user = {
                  series: [
                    {
                    name: "New User",
                    data: total_new_user
                    },
                    {
                    name: "Active User",
                    data: total_active_user
                    },
                ],
                  chart: {
                  height: 330,
                  type: 'line',
                  zoom: {
                    enabled: false
                  }
                },
                dataLabels: {
                  enabled: false
                },
                stroke: {
                  curve: 'straight'
                },
                title: {
                  text: 'User berdasarkan periode',
                  align: 'center'
                },
                grid: {
                  row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                  },
                },
                xaxis: {
                  categories: periode_new_user,
                }
                };

              var options_merch_ver = {
                series: [{
                  name: 'Merchan Registrasi',
                  data: total_merch_ver
                }],
                chart: {
                  height: 350,
                  type: 'bar',
                },
                plotOptions: {
                  bar: {
                    borderRadius: 10,
                    dataLabels: {
                      position: 'top', // top, center, bottom
                    },
                  }
                },
                dataLabels: {
                  enabled: true,
                  formatter: function (val) {
                    return val + "";
                  },
                  offsetY: -20,
                  style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                  }
                },

                xaxis: {
                  categories: periode_merch_ver,
                  position: 'top',
                  axisBorder: {
                    show: false
                  },
                  axisTicks: {
                    show: false
                  },
                  crosshairs: {
                    fill: {
                      type: 'gradient',
                      gradient: {
                        colorFrom: '#D8E3F0',
                        colorTo: '#BED1E6',
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5,
                      }
                    }
                  },
                  tooltip: {
                    enabled: true,
                  }
                },
                yaxis: {
                  axisBorder: {
                    show: false
                  },
                  axisTicks: {
                    show: false,
                  },
                  labels: {
                    show: false,
                    formatter: function (val) {
                      return val + "";
                    }
                  }

                },
                title: {
                  text: 'Merchant melakukan Verifikasi berdasarkan periode',
                  floating: true,
                  offsetY: 330,
                  align: 'center',
                  style: {
                    color: '#444'
                  }
                }
                };

              var options_data_user_active = {
                    series: [
                      {
                      name: "Active User",
                      data: total_active_user
                      },
                  ],
                    chart: {
                    height: 330,
                    type: 'line',
                    zoom: {
                      enabled: false
                    }
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    curve: 'straight'
                  },
                  title: {
                    text: 'User Aktif berdasarkan periode',
                    align: 'center'
                  },
                  grid: {
                    row: {
                      colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                      opacity: 0.5
                    },
                  },
                  xaxis: {
                    categories: periode_active_user,
                  }
                  };

              var options_merch_active = {
                  series: [{
                    name: 'Merchan Registrasi',
                    data: total_merch_active
                  }],
                  chart: {
                    height: 350,
                    type: 'bar',
                  },
                  plotOptions: {
                    bar: {
                      borderRadius: 10,
                      dataLabels: {
                        position: 'top', // top, center, bottom
                      },
                    }
                  },
                  dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                      return val + "";
                    },
                    offsetY: -20,
                    style: {
                      fontSize: '12px',
                      colors: ["#304758"]
                    }
                  },

                  xaxis: {
                    categories: periode_merch_active,
                    position: 'top',
                    axisBorder: {
                      show: false
                    },
                    axisTicks: {
                      show: false
                    },
                    crosshairs: {
                      fill: {
                        type: 'gradient',
                        gradient: {
                          colorFrom: '#D8E3F0',
                          colorTo: '#BED1E6',
                          stops: [0, 100],
                          opacityFrom: 0.4,
                          opacityTo: 0.5,
                        }
                      }
                    },
                    tooltip: {
                      enabled: true,
                    }
                  },
                  yaxis: {
                    axisBorder: {
                      show: false
                    },
                    axisTicks: {
                      show: false,
                    },
                    labels: {
                      show: false,
                      formatter: function (val) {
                        return val + "";
                      }
                    }

                  },
                  title: {
                    text: 'Merchant Aktif menggunakan berdasarkan periode',
                    floating: true,
                    offsetY: 330,
                    align: 'center',
                    style: {
                      color: '#444'
                    }
                  }
                  };

              var options_detail_transaksi = {
                series: [
                {
                  data: for_detail_transaksi
                }
              ],
                legend: {
                show: true
              },
              chart: {
                height: 330,
                type: 'treemap'
              },
              title: {
                text: 'Total Transaksi Berdasarkan Provinsi',
                align: 'center'
              },
              colors: [
                '#3B93A5',
                '#F7B844',
                '#ADD8C7',
                '#EC3C65',
                '#CDD7B6',
                '#C1F666',
                '#D43F97',
                '#1E5D8C',
                '#421243',
                '#7F94B0',
                '#EF6537',
                '#C0ADDB'
              ],
              plotOptions: {
                treemap: {
                  distributed: true,
                  enableShades: false
                }
              }
              };

              var chart_merch_regis = new ApexCharts(document.querySelector("#chart-apex-regis"), options_merch_regis);
              chart_merch_regis.render();

              var chart_data_user = new ApexCharts(document.querySelector("#chart-apex-data-user"), options_data_user);
              chart_data_user.render();

              var chart_merch_ver = new ApexCharts(document.querySelector("#chart-apex-ver"), options_merch_ver);
              chart_merch_ver.render();

              var chart_data_user_active = new ApexCharts(document.querySelector("#chart-apex-data-user-active"), options_data_user_active);
              chart_data_user_active.render();

              var chart_merch_active = new ApexCharts(document.querySelector("#chart-apex-active"), options_merch_active);
              chart_merch_active.render();

              var chart_detail_transaksi = new ApexCharts(document.querySelector("#chart-apex-data-transaksi"), options_detail_transaksi);
              chart_detail_transaksi.render();
          }
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

    loadsalesdetail(vendor);
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
