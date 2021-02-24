console.log('versi Jquery adalah '+$.fn.jquery);
$(document).ready(function(){
  $('li#li-dashboard', parent.document).removeClass('mm-active');
  $('li#li-dashboard > a', parent.document).removeClass('mm-active');

  $('li#li-smb', parent.document).removeClass('mm-active');
  $('li#li-smb > a', parent.document).removeClass('mm-active');

  $('li#li-users', parent.document).addClass('mm-active');
  $('li#li-users > a', parent.document).addClass('mm-active');
  loadusers('');

  $('#add-user').on('click', function(){
    let isObject   = {};
    isObject.name = $('#is_name').val();
    isObject.username = $('#is_uname').val();
    isObject.role = $('#is_role').val();
    isObject.password = '12345';
    adduser(isObject);
  });
});

function loadusers(table){
    let isObject   = {};
    isObject.table = table;

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: baseURL + '/jsondata/load-all-user',
		data :{
			iparam	    : cryptoEncrypt(PHRASE, isObject),
		},
		success: function(response){

      if(response.code == CODE_SUCCESS){
          let result = cryptoDecrypt(PHRASE, response.data);
          let data = result.data;

          $('#list-users').DataTable({
                    aaData: data,
                    lengthChange: false,
                    pageLength: 10,
                    aoColumns: [
                        { 'mDataProp': 'iduser'},
                        { 'mDataProp': 'username', 'sClass':'text-center'},
                        { 'mDataProp': 'name', 'sClass':'text-center'},
                        { 'mDataProp': 'role', 'sClass':'text-center'},
                        { 'mDataProp': 'status', 'sClass':'text-center'},
                        { 'mDataProp': 'create_dtm', 'sClass':'text-center'},
                        { 'mDataProp': 'iduser', sClass: 'text-center'},

                    ],
                    order: [[0, 'ASC']],
                    aoColumnDefs:[
                      {
                          mRender: function ( data, type, row ) {
                            var el = ``;
                            switch (row.role) {
                              case '10':
                                  el = "Super Admin";
                                break;
                              case '20':
                                  el = "Admin";
                                break;
                              case '30':
                                  el = "User";
                                break;
                              default:

                            }

                              return el;
                          },
                          aTargets: [ 3 ]
                      },
                      {
                          mRender: function ( data, type, row ) {
                            var el = ``;
                            switch (row.status) {
                              case '10':
                                  el = '<div class="badge badge-pill badge-success">Active</div>';
                                break;
                              case '20':
                                  el = '<div class="badge badge-pill badge-info">Not Active</div>';
                                break;
                              case '30':
                                  el = '<div class="badge badge-pill badge-danger">Block</div>';
                                break;
                              default:

                            }

                              return el;
                          },
                          aTargets: [ 4 ]
                      },
                      {
                          mRender: function ( data, type, row ) {
                            var el = `<div role="group" class="btn-group-sm btn-group btn-group-toggle">
                                <button type="button" class="btn btn-warning" onclick="action('edit',`+row.iduser+`,'`+row.iduser+`')"><i class="fa fa-edit" aria-hidden="true" title="Copy to use edit"></i></button>
                                <button type="button" class="btn btn-danger" onclick="action('hapus',`+row.iduser+`,'`+row.iduser+`')"><i class="fa fa-trash" aria-hidden="true" title="Copy to use edit"></i></button>
                            </div>`;

                              return el;
                          },
                          aTargets: [ 6 ]
                      },
                    ],
                    fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
                        var index = iDisplayIndexFull + 1;
                        $('td:eq(0)', nRow).html('#'+index);
                        return  index;
                    },
                    fnInitComplete: function () {
                        var that = this;
                        var td ;
                        var tr ;
                        this.$('td').click( function () {
                            td = this;
                        });
                        this.$('tr').click( function () {
                            tr = this;
                        });

                    }
                });

      }
    }
  });
}

function adduser(obj){
	$.ajax({
    bServerSide: true,
    bDestroy: true,
		type: 'POST',
		dataType: 'json',
		url: baseURL + '/jsondata/add-user',
		data :{
			iparam	    : cryptoEncrypt(PHRASE, obj),
		},
		success: function(response){

      // if(response.code == CODE_SUCCESS){
      //     let result = cryptoDecrypt(PHRASE, response);
      //     let data = result.data;
      //   }
      var iframe = parent.document.getElementById('users-frame');
      iframe.src = iframe.src;
        $('.btn-secondary').trigger('click');
      }
    })
  }
