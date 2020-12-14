function lRadioX(){
    loadCountMaps();
}

$('#orderBY').on('change', function(){
    loadCountMaps();
});

$('#sortx').on('change', function(){
    loadCountMaps();
});

$('#cariJudul').on('keyup', function(){
    loadCountMaps();
});

/*--------------------------------------------------------------
---------------------- LOAD DATA & PAGENATION-------------------
---------------------------------------------------------------*/
$('#filterPages').on('change', function(){
    loadCountMaps();
});

function PageSize(){
    let pageSize = $('#filterPages').val(); // limit na gans
    return pageSize;
}

function currentPageIndex(){
    let currentPageIndex = 0; // depaultna
    return currentPageIndex;
}

/* counting data gazzz */
loadCountMaps();
function loadCountMaps(){
    
    let isObject   = {};
    isObject.param = 'count';
    
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: baseURL + '/jsondata/loadprofilepartial',
		data :{
			iparam	    : cryptoEncrypt(PHRASE, isObject),
		},
		success: function(response){

            if(response.code == CODE_SUCCESS){
               
                let result = cryptoDecrypt(PHRASE, response.data);
                
                $('#tempCurent').val(result.data);
                paginationGenerate(currentPageIndex(), PageSize(), result.data);

            }else{
                alert('not found');
            }
		}
	});
}

$('#gView').on('click', function(){
    $.cookie("gridSys", ["1"]);
    loadCountMaps();
});

$('#tView').on('click', function(){
    $.cookie("gridSys", ["2"]);
    loadCountMaps();
});

$('#lView').on('click', function(){
    $.cookie("gridSys", ["3"]);
    loadCountMaps();
});

/* load profile */
function loadmaps(param, start, limit){
    
    loaderPage(true);
    
    let isObject     = {};
    isObject.judul   = $('#cariJudul').val();
    isObject.publish = $("input[name='radio-styled-colorX']:checked").val();
    isObject.order   = $('#orderBY').val(); 
    isObject.sort    = $('#sortx').val(); 
    isObject.start   = start;
    isObject.limit   = limit;

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseURL + '/jsondata/loadprofilepartial',
        data: {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        success: function(response){
            
            loaderPage(false);
            
            if(response.code == CODE_SUCCESS){
               
                let result = cryptoDecrypt(PHRASE, response.data);
                
                $('#loadForm').empty();
                $('.tabelOfContent > tbody').empty();
                $('.contentList').empty();

                let $res = result.data;
                
                let $isB = '../../img/ss/adms2.jpg';
                let $isT = 'Quiz';
                
                /*  cookies view */   
                if(!$.cookie("gridSys")){
                    $.cookie("gridSys", ["1"]);
                }

                if($.cookie("gridSys") == 1){
                    $('#loadForm').css('display', 'block');
                    $('.isTabelContentHead').css('display', 'none');
                    $('.isListContentHead').css('display', 'none');
                }else if($.cookie("gridSys") == 2){
                    $('#loadForm').css('display', 'none');
                    $('.isTabelContentHead').css('display', 'block');
                    $('.isListContentHead').css('display', 'none');
                }else{
                    $('#loadForm').css('display', 'none');
                    $('.isTabelContentHead').css('display', 'none');
                    $('.isListContentHead').css('display', 'block');
                }

                let isNums = 0;
                for($x in $res){

                    isNums++;

                    if($res[$x].type_content == 1){ // survey atau kuisoner
                        $isB = '../../img/ss/adms4.jpg';
                        $isT = 'Survey atau Kuisoner';
                    }

                    if($.cookie("gridSys") == 1){
                    
                        $('#loadForm').append('<div class="col-lg-3 col-md-4 col-sm-4">'+
                            '<div class="thumbnail no-padding">'+
                                '<div class="thumb">'+
                                    '<img src="'+$isB+'" alt="" class="img-responsive">'+
                                    '<div class="caption-overflow">'+
                                        '<span>'+
                                            '<a onClick="loadProfileDetail('+$res[$x].id_profile+')" class="btn bg-success-400 btn-icon btn-lg" style="font-variant: all-petite-caps;background-color: #0e0e0e33 !important;border-color: #25252591 !important;">'+
                                                '<i class="icon-quill4"></i> '+$res[$x].judul+
                                            '</a>'+
                                        '</span>'+
                                    '</div>'+
                                '</div>'+
                            
                                '<div class="caption text-center">'+
                                    '<a onClick="loadProfileDetail('+$res[$x].id_profile+')"><h6 class="text-semibold no-margin">'+$res[$x].judul.substr(0,15)+'...<small class="display-block"></small></h6></a>'+
                                '</div>'+
                            '</div>'+
                        '</div>');

                    }else if($.cookie("gridSys") == 2){

                        $('.tabelOfContent > tbody').append(`
                            <tr>
                                <th scope="row">`+isNums+`</th>
                                <td>`+$res[$x].judul+`</td>
                                <td>`+$res[$x].deskripsi+`</td>
                                <td>`+$isT+`</td>
                                <td>`+$res[$x].create_date+`</td>
                            </tr>
                        `);

                    }else{
                        $('.contentList').append(`
                        <ul class="media-list" style="background:white;padding:20px;margin-top: 10px;border: 1px solid #ddd;">
                            <li class="media stack-media-on-mobile">
                                <div class="media-left">
                                    <div class="thumb">
                                        <a href="#">
                                            <img src="`+$isB+`" class="img-responsive img-rounded media-preview" alt="">
                                            <span class="zoom-image"><i class="icon-play3"></i></span>
                                        </a>
                                    </div>
                                </div>

                                <div class="media-body">
                                    <h6 class="media-heading"><a href="#">`+$res[$x].judul+`</a></h6>
                                    <ul class="list-inline list-inline-separate text-muted mb-5">
                                        <li><i class="icon-book-play position-left"></i> `+$isT+`</li>
                                    </ul>

                                    `+$res[$x].deskripsi+`
                                </div>
                            </li>
                        </ul>`);
                    }
                    
                }

                setIFrameSize();
                
            }else{
                $('#loadForm').empty();
            }

        },
        error: function(xhr) {
            loaderPage(false);
            if(xhr.status != 200){
                swal({
                    title: "Galat",
                    text:  xhr.status+"-"+xhr.statusText+" <br>Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });                        
            }
        }
    });
}

/*--------------------------------------------------------------
---------------------- PAGENATION GAN---------------------------
---------------------------------------------------------------*/

function paginationGenerate(page, pageCounts) {

	if(length){
		var length = parseFloat(length);  // ntr ini dari total db.. rudet
	}else{
		var length = parseFloat($('#tempCurent').val());  // ntr ini dari total db.. rudet
	}
	
	//console.log(page);
	if(page == 0){
		var page = 1;
	}else{
		var page = page;
	}

    let pageSize = PageSize();

	pageSizex = pageSize * page;
    pageCount = 0;
	if (length > pageSize) { 
		if (length / pageSize > parseInt(length / pageSize)) {
			pageCount = parseInt(length / pageSize) + 1;
		} else {
			pageCount = length / pageSize; 
		}
	}
	
	var start  = currentPageIndex() + pageSizex;
	
	var startItemIndex = page * pageSize;
	var remainingItems = length - startItemIndex; 
	itemsToDisplay = pageSize + startItemIndex; 

	if(remainingItems < pageSize) { 
		itemsToDisplay = remainingItems + startItemIndex;
	}

	//console.log(remainingItems);
	if(page < 1){
		var curentPages = 0;
	}else{		
		var curentPages = pageSize * (page - 1) ;
		
	}
	//console.log(length);
	
	loadmaps(null, curentPages, pageSize);
    var pagination = "<ul class=\"pagination\"><li><span style='cursor: pointer;' onclick=\"paginationGenerate(checkPrevious(" + page + ")," + pageCount + ") \">&laquo;</span></li>";

    /* Page Range Calculation */
    var range = pageRange(page, pageCount);
    var start = range.start;
    var end = range.end;

	//console.log(pageSizex);

    for (var page_id = start; page_id <= end; page_id++) {
        if (page_id != page) pagination += "<li><span style='cursor: pointer;' onclick=\"paginationGenerate(" + page_id + "," + pageCount + ")\">" + page_id + "</span></li>";
        else pagination += "<li class='active'><span>" + page_id + "</span></li>";
    }
    pagination += "<li><span style='cursor: pointer;' onclick=\"paginationGenerate(checkNext(" + page + "," + pageCount + ")," + pageCount + ")\">&raquo;</span></li></ul>";

    /* Appending data Pagination na gans */
    $('.pagination').remove();
    $('.contentPagination').append(pagination);
}

/*--------------------------------------------------------------
---------------------- END PAGENATION GAN-----------------------
---------------------------------------------------------------*/


/* Pagination Navigation gazzzz */
function checkPrevious(id) {
	//console.log('checkPrevious: '+id);
    if (id > 1) {
        return (id - 1);
    }
    return 1;
}

/* Pagination Navigation gazzzz */
function checkNext(id, pageCount) {
    if (id < pageCount) {
        return (id + 1);
    }
    return id;
}

/* Page Range calculation Method for Pagination gazzzzzzzzzz*/
function pageRange(page, pageCount) {

    var start = page - 2,
        end = page + 2;

    if (end > pageCount) {
        start -= (end - pageCount);
        end = pageCount;
    }
    if (start <= 0) {
        end += ((start - 1) * (-1));
        start = 1;
    }

    end = end > pageCount ? pageCount : end;

    return {
        start: start,
        end: end
    };
}

/* load profile detail */
function loadProfileDetail(thisID){
    var id_li       = 'detailForm';
    var id_content  = 'infoui_tabx';
    var label_tab   = 'Setup Running Text';
    var src         = '/content/setupcontent?'+thisID;
    parent.addtab(id_li,id_content,label_tab,src);
}



$('#btn-add-add').click(function(){
    
    if( $('input[name="typeFormulir"]').is(':checked') ) {

        $('label.boxForm div').find('span.checked').removeClass('checked'); // rest checkbox

    } 
   
	document.getElementById("formmen").reset(); // reset form

    $('#modal-add-visit').modal("show");
});






/* save profile */
function saveForm(){
    
    let isObject     = {};
    isObject.judul   = $('#judul').val();
    isObject.desk    = $('#keterangan').val();
    isObject.tpForm  = $("input[name='typeFormulir']:checked").val();
   
    if(isObject.tpForm == undefined){
        loaderPage(false);
        swal({
            title: "Ops!",
            text:" Tipe formulir tidak boleh kosong. Silahkan coba kembali :)",
            confirmButtonColor: "#2196F3",
            type: "warning"
        });
        return false;
    }

    if(isObject.judul ==''){
        loaderPage(false);
        swal({
            title: "Ops!",
            text:" Judul tidak boleh kosong. Silahkan coba kembali :)",
            confirmButtonColor: "#2196F3",
            type: "warning"
        });
        return false;
    }

    if(isObject.desk  ==''){
        loaderPage(false);
        swal({
            title: "Ops!",
            text:" Deskripsi tidak boleh kosong. Silahkan coba kembali :)",
            confirmButtonColor: "#2196F3",
            type: "warning"
        });
        return false;
    }


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseURL + '/jsondata/saveprofile',
        data: {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        success: function(result){
            if(result.code == 0){     
                $('#modal-add-visit').modal('hide');              
                swal({
                    title: "Sukses",
                    text: "Profile baru telah ditambahkan ",
                    confirmButtonColor: "#66BB6A",
                    type: "success"
                });
                loadCountMaps();
            }else{
                // loaduser();
                swal({
                    title: "Galat",
                    text:  result.info+" Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });
            }

            loaderPage(false);

        },
        error: function(xhr) {
            loaderPage(false);
            if(xhr.status != 200){
                swal({
                    title: "Galat",
                    text:  xhr.status+"-"+xhr.statusText+" <br>Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });                        
            }
        }
    });
}

