// parent.AdjustIframeHeight(1080);
// alert(thisIdProfile);
const INPUTEXT  = 1;
const TEXTAREA  = 2;
const SELECTOPT = 3;
const RADIONBTN = 4;

const TEXTS     = 'texts';
const NUMBERS   = 'numbers';


/* load profile dan palidasi */
var thisIdProfileArray = [thisIdProfile];
Object.defineProperty(Array.prototype, 'loadProfiled', {
    value: function(profileID) {

        var R            = [];

        let isObject     = {};
        isObject.ipoly   = profileID;
        
        $.ajax({
            type: 'POST',
            dataType: 'json',
            async: false,
            url: baseURL + '/jsondata/loadsetting',
            data: {
                iparam	    : cryptoEncrypt(PHRASE, isObject),
            },
            success: function(response){

                if(response.code == CODE_SUCCESS){

                    let result = cryptoDecrypt(PHRASE, response.data);

                    let data   = result.data[0];

                    /* load batas pengisian */
                    if(data.batas_isi == 1){
                        checkPengisian();
                    }

                    /* load masa aktif dan check tanggal kadaluarsa */
                    if(data.masa_aktif == 1){
                        let validDate = Date.now() > Date.parse(data.end_date);
                        if(validDate === true){                           
                            window.location = baseURL+'/magic/surveyexpired';
                        }
                    }

                    /* load aktif survey  */
                    if(data.publish == 3){
                        window.location = baseURL+'/magic/surveynotactive';
                    }

                    /* tampilkan judul */
                    $('#thisJudul').empty();
                    $('#thisJudul').html(data.judul);

                    /* tampilkan deskripsi */
                    $('#thisDeskripsi').empty();
                    $('#thisDeskripsi').html(data.deskripsi);

                    /* view header  */
                    if(data.header_view == 1){
                        $('#thisHeader').css('display','block');
                    }
                    
                    /* load color header */
                    $(".page-header").css("background", data.header_color);

                    /* load color latar belakang */
                    $(".content-wrapper").css("background", data.back_color);
                    
                    $(function() {
                        renderForm(data);

                        /* load ukuran layout */
                        if(data.layout_size == 1){
                            $('.contentx').removeClass('').addClass('container');
                        }else{
                            $('.contentx').removeClass('container').addClass('container-fluid');
                        }

                    });
                    
                }
                // R.push(result);
            },
            error: function(xhr){
                loaderPage(false);
                if(xhr.status != 200){
                    swal({
                        title: "Galat",
                        text:  xhr.status+"-"+xhr.statusText+" <br>Silahkan coba kembali :)",
                        confirmButtonColor: "#2196F3",
                        type: "error"
                    });                        
                }else{
                    swal({
                        title: "Galat",
                        text:  " Opps. hubungi orang terdekat, Silahkan coba kembali :)",
                        confirmButtonColor: "#2196F3",
                        type: "error"
                    });
                }
            }
        });
        return R;
    }
});
thisIdProfileArray.loadProfiled(thisIdProfile);

/* ------------------------------------------------------------------------------*
*  # Stepy wizard
* ---------------------------------------------------------------------------- */

function stepWizard(){


    // Override defaults
    $.fn.stepy.defaults.legend      = false;
    $.fn.stepy.defaults.transition  = 'fade';
    $.fn.stepy.defaults.duration    = 150;
    $.fn.stepy.defaults.backLabel   = '<i class="icon-arrow-left13 position-left"></i> Back';
    $.fn.stepy.defaults.nextLabel   = 'Next <i class="icon-arrow-right14 position-right"></i>';

    // Wizard examples
    // ------------------------------

    // Basic wizard setup
    $(".stepy-basic").stepy();


    // Hide step description
    $(".stepy-no-description").stepy({
        description: false
    });


    // Clickable titles
    $(".stepy-clickable").stepy({
        titleClick: true
    });


    // Stepy callbacks
    $(".stepy-callbacks").stepy({
        validate: true,
        block: true,
        next: function(index) {
            // console.log(index);
            if (!$(".stepy-callbacks").validate(validate)) {
                return false
            }
            // if(index == 3){
            //     var spoly = $('#statusPoly').val();
            //     if(spoly == 'nok'){
            //         return false;
            //     }
            // }
        },
        back: function(index) {
           //alert('Returning to step: ' + index);
        },
        finish: function() {
            
            if (!$(".stepy-callbacks").validate(validate)) {
                return false
            }else{                
                InsertPolay();
            }
        }
    });


    //
    // Validation
    //

    // Initialize wizard
    $(".stepy-validation").stepy({
        validate: true,
        block: true,
        next: function(index) {
            if (!$(".stepy-validation").validate(validate)) {
                return false
            }
        }
    });


    // Initialize validation
    var validate = {
        ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
        errorClass: 'validation-error-label',
        successClass: 'validation-valid-label',
        highlight: function(element, errorClass) {
            $(element).removeClass(errorClass);
        },
        unhighlight: function(element, errorClass) {
            $(element).removeClass(errorClass);
        },

        // Different components require proper error label placement
        errorPlacement: function(error, element) {

            // Styled checkboxes, radios, bootstrap switch
            if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container') ) {
                if(element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                    error.appendTo( element.parent().parent().parent().parent() );
                }
                 else {
                    error.appendTo( element.parent().parent().parent().parent().parent() );
                }
            }

            // Unstyled checkboxes, radios
            else if (element.parents('div').hasClass('checkbox') || element.parents('div').hasClass('radio')) {
                error.appendTo( element.parent().parent().parent() );
            }

            // Input with icons and Select2
            else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                error.appendTo( element.parent() );
            }

            // Inline checkboxes, radios
            else if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                error.appendTo( element.parent().parent() );
            }

            // Input group, styled file input
            else if (element.parent().hasClass('uploader') || element.parents().hasClass('input-group')) {
                error.appendTo( element.parent().parent() );
            }

            else {
                error.insertAfter(element);
            }
        },
        rules: {
            email: {
                email: true
            }
        }
    }



    // Initialize plugins
    // ------------------------------

    // Apply "Back" and "Next" button styling
    $('.stepy-step').find('.button-next').addClass('btn btn-primary');
    $('.stepy-step').find('.button-back').addClass('btn btn-default');


    // Select2 selects
    $('.select').select2();


    // Simple select without search
    $('.select-simple').select2({
        minimumResultsForSearch: Infinity
    });


    // Styled checkboxes and radios
    $('.styled').uniform({
        radioClass: 'choice'
    });


    // Styled file input
    $('.file-styled').uniform({
        wrapperClass: 'bg-warning',
        fileButtonHtml: '<i class="icon-googleplus5"></i>'
    });
    
}

/* ------------------------------------------------------------------------------*
*  # End Stepy wizard
* ---------------------------------------------------------------------------- */

function checkPengisian(){
    
    let isObject     = {};
    isObject.ipoly   = thisIdProfile;

    $.ajax({
        type: 'POST',
        dataType: 'json',
        // async: false,
        url: baseURL + '/jsondata/checkuserisisurvey',
        data: {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        success: function(response){

            if(response.code == CODE_SUCCESS){
                window.location = baseURL+'/magic/surveylimitfilled';
            }

        }
    });
}

function paginate (array, page_size, page_number) {
  
    return array.slice(page_number * page_size, (page_number + 1) * page_size);

}


function controlRadio(){

   // Danger
   $(".control-danger").uniform({
       radioClass: 'choice',
       wrapperClass: 'border-danger-600 text-danger-800'
   });
   

}

function renderForm(thisData){
    console.log(thisData);
    let isObject            = {};
    isObject.ipoly          = thisIdProfile;
    isObject.loadtype       = 2;
    isObject.status_soal    = thisData.status_soal;
    isObject.tipe_bobot     = thisData.tipe_bobot;
    isObject.sort_data      = thisData.sort_data;
    isObject.materi_view    = thisData.materi_view;

    $('.renderForm').empty();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseURL + '/jsondata/generateform',
        data: {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        success: function(response){

            if(response.code == CODE_SUCCESS){

                let result = cryptoDecrypt(PHRASE, response.data);

                var res     = result.data;
                var obj     = {};
                obj.product = [];
               

                /* set papah */
                for(x in res){
                    if(res[x].has_parent == 0 && res[x].parent_id == null){
                        
                        if(isObject.status_soal == 1){ // jika profile hanya pilih soal  yg active

                            if(res[x].x_status == 1){
                                obj.product.push({ 
                                    property: res[x],
                                    option  : [], 
                                    children : [] 
                                });      
                            }

                        }else{

                            obj.product.push({ 
                                property: res[x],
                                option  : [], 
                                children : [] 
                            });  

                        }
                    }
                }
                
                /* papah cek tipe data jika select option */
                var optp = obj.product;
                
                for(p in optp){
                    for(x in res){
                        if(res[x].has_parent != 0 && res[x].parent_id == null){
                            
                            if(parseInt(res[x].object_type) == SELECTOPT || parseInt(res[x].object_type) == RADIONBTN){
                                
                                if(optp[p].property['paramid'] == res[x].has_parent){
                                    optp[p].option.push({ 
                                        property  : res[x]
                                    });      
                                } 

                            }

                        }

                    }
                }


                var counter          = 1;

                /* terbaru split-array-into-chunks-of-n-length */
                var thisProduct = [], size = thisData.limitx;
                while (obj.product.length > 0)
                thisProduct.push(obj.product.splice(0, size));


                var contentWizard    = '<form class="stepy-callbacks" action="#">';

                let isNumsPg = 0;
                for(z in  thisProduct){
                    var startRow    = '<fieldset title="'+counter+'">'+
                                        '<legend class="text-semibold">Page '+counter+' </legend>'+
                                            '<div class="row">'+
                                                '<div class="col-md-12">';
                                                var rowBody = '';
                                                for(s in thisProduct[z]){
                                                    // console.log(thisProduct[z][s]);
                                                    switch(parseInt(thisProduct[z][s].property['object_type'])) {

                                                        case INPUTEXT:
                                                            rowBody +='<div class="form-group">'+
                                                                '<label><h6 class="text-gray">'+capitalize(thisProduct[z][s].property.object_label)+'</h4></label>'+
                                                                '<input type="text" class="form-control " id="'+thisProduct[z][s].property.paramid+'"></input>'+
                                                            '</div>';
                                                        break;
                                                        
                                                        case TEXTAREA:
                                                            rowBody  +='<div class="form-group">'+
                                                                '<label><h6 class="text-gray">'+capitalize(thisProduct[z][s].property.object_label)+'</h4></label>'+
                                                                '<textarea type="textarea"  rows="5" class="form-control " id="'+thisProduct[z][s].property.paramid+'"></textarea>'+
                                                            '</div>';
                                                            break;
                                                        
                                                        case SELECTOPT:
                                                            
                                                            var $opt = '<option></option>';
                                                            for(i in thisProduct[z][s].option){
                                                                $opt += '<option value="'+thisProduct[z][s].option[i].property['object_value']+'">'+thisProduct[z][s].option[i].property['object_label']+'</option> ';
                                                            }
                                    
                                                            rowBody  += '<div class="form-group">'+
                                                                '<label><h6 class="text-gray">'+capitalize(thisProduct[z][s].property.object_label)+'</h4></label>'+
                                                                '<select class="form-control  " id="'+thisProduct[z][s].property.paramid+'">'+
                                                                    $opt+
                                                                '</select>'+
                                                            '</div>';
                                    
                                                        break;
                                                
                                                        case RADIONBTN:
                                                            var $opt = '';
                                                            for(i in thisProduct[z][s].option){
                                                                // console.log(thisProduct[z][s].option[i].property['object_value']);
                                                                $opt += '<div class="radio">'+
                                                                            '<label>'+
                                                                                '<input type="radio"  value="'+thisProduct[z][s].option[i].property['object_value']+'"  id="'+thisProduct[z][s].option[i].property.paramid+'" name="'+thisProduct[z][s].option[i].property.has_parent+'" class="control-danger" >'+
                                                                                thisProduct[z][s].option[i].property['object_label']+
                                                                            '</label>'+
                                                                        '</div>';
                                                            }

                                                            rowBody  += '<div class="form-group">'+
                                                                            '<h6 class="text-gray">'+capitalize(thisProduct[z][s].property.object_label)+'</h6>'+
                                                                            $opt+
                                                                        '</div>';
                                                        
                                                        break;   

                                                    }
                                                }
                                                

                    var endRow      =             '</div>'+                 
                                            '</div>'+                  
                                        '</fieldset>';
                    counter++;
                    contentWizard += startRow+rowBody+endRow;

                    
                    /* set json to value */
                    $("textarea#jsonArea").empty();
                    $("textarea#jsonArea").val(JSON.stringify(result));
                }

                contentWizard += '<span type="submit" class="btn btn btn-danger stepy-finish"  style="background-color: #ff004e; font-weight: 600; border-color: #ff004e;">Kirim <i class="icon-arrow-right14 position-right"></i></span>';
                contentWizard +=  '</form>';

                $('.wixard').append(contentWizard);

                
                $(function() {
                    stepWizard();
                    controlRadio();
                });
            }
           

        },
        error: function(xhr){
            if(xhr.status != 200){
                swal({
                    title: "Galat",
                    text:  xhr.status+"-"+xhr.statusText+" <br>Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });                        
            }else{
                swal({
                    title: "Galat",
                    text:  " Opps. hubungi orang terdekat, Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });
            }
        }
    });
} 

function InsertPolay(){
    loaderPage(true);

    var $string = $("textarea#jsonArea").val();
    var $obj    = JSON.parse($string);
    
    var $values        = {};
    $values.data       = [];
    
    for(n in $obj.data){
        
        if($obj.data[n].has_parent == null){
            // console.log($obj.data[n]);
            $values.data.push({
                property : [$obj.data[n]], 
                values   : $('#'+$obj.data[n].paramid).val()
            });

            if($obj.data[n].object_type == RADIONBTN){
                for(b in $values.data){
                    if($values.data[b].property[0].object_type == RADIONBTN){
                        var getRadioValue = $("input[name='"+$values.data[b].property[0].paramid+"']:checked").val();
                        if(getRadioValue){
                            $values.data[b].values = getRadioValue;
                        }else{
                            $values.data[b].values = '';
                        }
                    }
                   
                }                

            }            

        }

    }
    
    // console.log($values);
    var $csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseURL + '/jsondata/insertsurvey',
        data: {
            '_csrf'     : $csrfToken,
            datum       : $values,
            proid       : thisIdProfile,
        },
        success: function(result){
            loaderPage(false);
            if(result.code == 0){                
                swal({
                    title: "Success!",
                    text: "Terima kasih telah berpatisipasi :D",
                    confirmButtonColor: "#66BB6A",
                    type: "success",
                }, function() {
                    window.location = baseURL+'/magic/indexsurvey';
                });                   
            }else{                
                swal({
                    title: "Save Survey!",
                    text: "Gagal mengirim survey!!!",
                    confirmButtonColor: "#66BB6A",
                    type: "error"
                });
            }
        },
        error: function(xhr){
            loaderPage(false);
            if(xhr.status != 200){
                swal({
                    title: "Galat",
                    text:  xhr.status+"-"+xhr.statusText+" <br>Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });                        
            }else{
                swal({
                    title: "Galat",
                    text:  " Opps. hubungi orang terdekat, Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });
            }
        }
    });

    //console.log($values);

}