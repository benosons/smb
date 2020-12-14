

const player = new Plyr('#player', {
    // controls: ['play-large', 'play', 'progress', 'current-time', 'captions', 'fullscreen', 'settings'],
    title: 'Example Title',
});


$(document).ready(function(){
    setIFrameSize();
});

$('#btn-add-add').on('click', function(){
    parent.AdjustIframeHeight(905);
    $('#contentAdd').css('display', 'block');
    $('#contentList').css('display', 'none');

    $('#btn-add-add').css('display', 'none');
    $('#btn-backs').css('display', 'block');
});

$('#btn-backs').on('click', function(){
    $('#contentAdd').css('display', 'none');
    $('#contentList').css('display', 'block');

    $('#btn-add-add').css('display', 'block');
    $('#btn-backs').css('display', 'none');
});

var editor = CKEDITOR.replace( 'editor-full', {
    height: '200px',
    extraPlugins: 'forms'
});

/*  save materi */
$('#simpanMateri').on('click', function(){
    saveTheoryOfDespair();
});



function saveTheoryOfDespair(){

    loaderPage(true);

    const fileupload = $('#fileupload').prop('files')[0];

    let titleMater   = $('#titleMater').val();

    let ex_char      = editor.getData();

    if(titleMater  ==''){
        loaderPage(false);
        swal({
            title: "Ops!",
            text:" Title tidak boleh kosong. Silahkan coba kembali :)",
            confirmButtonColor: "#2196F3",
            type: "warning"
        });
        return false;
    }
    let formData = new FormData();

    formData.append('fileupload', fileupload); // set file ke tipe data binary
    formData.append('titleMater', titleMater); 
    formData.append('ex_char', ex_char); 

    $.ajax({
        type        : 'POST',
        url         : baseURL + '/jsondata/savemateri',
        data        : formData,
        cache       : false,
        processData : false,
        contentType : false,
        success: function (response) {

            if(response.code == CODE_SUCCESS){

                document.getElementById("form-data").reset();

                swal({
                    title: "Alert",
                    text: "Materi baru telah ditambahkan ",
                    confirmButtonColor: "#66BB6A",
                    type: "success"
                },function(){ 
                    loadElements();
                    $('#contentAdd').css('display', 'none');
                    $('#contentList').css('display', 'block');
                    $('#btn-add-add').css('display', 'block');
                    $('#btn-backs').css('display', 'none');
                });

            }else{
                swal({
                    title: "Alert",
                    text:  response.info+ " Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });  
            };

            loaderPage(false);
            
        },
        error: function () {
            loaderPage(false);
            if(xhr.status != 200){
                swal({
                    title: "Alert",
                    text:  xhr.status+"-"+xhr.statusText+" Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });                        
            }
        }
    });

}

loadElements();
function loadElements(clicksc){
    
    let isObject     = {}; 
    isObject.ipoly   = 1;

    var dtpr = $('#data-list2').DataTable({
        serverSide	: true,
        destroy		: true,
        bFilter		: true,
        responsive	: true,
        searching   : true,
        pagingType  : 'full',
        lengthMenu  : [[1, 25, 50, -1], [1, 25, 50, "All"]],
        pageLength  : 25,
        ajax		: {
            url: baseURL + '/jsondata/loadpartialmateri',
            type: 'POST',
            // async: false,
            data : function (d) {         
                return $.extend( {}, d, {
                    iparam	    : cryptoEncrypt(PHRASE, isObject),
                });
            },
            dataSrc: function(response) {
                
                if(response.code == CODE_SUCCESS){
                    response = cryptoDecrypt(PHRASE, response.data);

                    return response.data.data;
                }else{
                    return response;
                }
               
            }
        },	
        columns: [
            { 'data': 'idmateri', 'sClass':'','sWidth':'10px'},
            { 'data': 'file_name', 'sClass':''},
            { 'data': 'titles', 'sClass':''},
            { 'data': 'file_size', 'sClass':''},
            { 'data': 'create_date', 'sClass':''},
            { 'data': 'idmateri', 'sClass':'text-center','sWidth':'10px'},
        ],
        buttons: {            
            buttons: [
                {
                    extend: 'colvis',
                    className: 'btn btn-default'
                },
            ]
        },
        processing: true,
        stateSave: false,
        autoWidth: false,
        dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
        language: {
            search: '<span>Filter:</span> _INPUT_',
            lengthMenu: '<span>Show:</span> _MENU_',
            processing: "<div class='table-loading'><img src='../../img/loadingtwo.gif' /></div>",

            paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
        },
        columnDefs: [
            {
                width: '20px',
                targets: 0,
                visible: true
            },
            {   


                render: function (data, type, row){
                    // var $rowData = '<img src="/distro/assets/images/placeholder.jpg" alt="" class="img-rounded img-preview showAtt" style="cursor: pointer;">';
                    let isIcon = ' icon-file-download2';
                    if(row.file_ext == 'mkv' || row.file_ext == 'mp4'){
                        isIcon = 'icon-play4';
                    }else if(row.file_ext == 'pdf'){
                        isIcon = 'icon-file-pdf';
                    }

                    var $rowData = '<div class="thumb img-rounded img-preview showAtt" style="width: 70px;">'+
                        '<img src="/distro/assets/images/placeholder.jpg" alt="" class="img-rounded img-preview showAtt">'+
                        '<div class="caption-overflow">'+
                            '<span>'+
                                '<p  class="btn showAtt bg-success-400 btn-icon btn-lg" style="font-variant: all-petite-caps;background-color: #0e0e0e33 !important;border-color: #25252591 !important;">'+
                                    '<i class="'+isIcon+'"></i> '
                                '</p>'+
                            '</span>'+
                        '</div>'+
                    '</div>';

                    return $rowData;
                },
                visible: true,
                targets: 1,
            },  
            {   
                render: function (data, type, row){
                    // console.log(row);
                    var $rowData = `
                        <ul class="list-condensed list-unstyled no-margin">					                        		
                            <li><span class="text-semibold">Size:</span> `+row.file_size+`</li>
                            <li><span class="text-semibold">Format:</span> .`+row.file_ext+`</li>
                        </ul>
                    `;

                    return $rowData;
                },
                visible: true,
                targets: 3,
            }, 
            {   
                render: function (data, type, row){
                    // console.log(row);
                    var $rowData = `
                        <ul class="icons-list">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="icon-menu9"></i>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="#"><i class="icon-pencil7"></i> Edit materi</a></li>
                                </ul>
                            </li>
                        </ul>
                    `;

                    return $rowData;
                },
                visible: true,
                targets: 5,
                className: 'dt-center'
            }, 
        ],
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){ 
            var index = iDisplayIndexFull + 1; 
            $('td:eq(0)', nRow).html('#'+index); 
            return  index;
        },
        drawCallback: function (settings) {
            // console.log(settings.json);
            
            var api  = this.api();
            var rows = api.rows({page:'current'}).nodes();
            var last = null;

            // console.log(rows)
            // Reverse last 3 dropdowns orientation
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');

            var that = this;
            var td ;
            var tr ;

            this.$('td').click( function () {
                td = this;
            });
            this.$('tr').click( function () {
                tr = this;
            });

            this.$('.showAtt').on('click', function(){

                var tr      = $(this).parents('tr')
                var aData   = dtpr.row( tr ).data();

                loadAttachments(aData);

            });

            
        },
        fnInitComplete: function (oSettings, json) {
            var that = this;
            var td ;
            var tr ;

            this.$('td').click( function () {
                td = this;
            });
            this.$('tr').click( function () {
                tr = this;
            });

            

            $('#data-list2 input').bind('keyup', function (e) {
                return this.value;
            });

            setIFrameSize();

        }
    });

}

function loadAttachments(aData){
    
    $('.isv').empty();
    if(aData.file_ext == 'mkv' || aData.file_ext == 'mp4'){
        
        $('#modal-video').modal("show");

        $('.isv').append(` <video src="`+aData.file_dir+``+aData.file_name+`" id="player" controls data-plyr-config='{ "title": "Example Title" }' style="width: 100%;"></video>`);

    }else if(aData.file_ext == 'pdf'){

        $('#modal-video').modal("show");

        $('.isv').append(`<embed src="`+aData.file_dir+``+aData.file_name+`" type="application/pdf"   height="700px" width="500">`);

    }else{ // download
        window.open(aData.file_dir+``+aData.file_name, '_blank');
    }

}

/* stop video jika modal close */
$('#modal-video').on('hidden.bs.modal', function (e) {
    $('.isv').empty();
})