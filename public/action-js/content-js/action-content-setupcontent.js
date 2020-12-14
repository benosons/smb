// parent.AdjustIframeHeight(1646);

// alert(thisIdProfile);
const INPUTEXT  = 1;
const TEXTAREA  = 2;
const SELECTOPT = 3;
const RADIONBTN = 4;
const TEXTS     = 'texts';
const NUMBERS   = 'numbers';

var m           = 0;
var myArray     = new Array();
var objHeader   = [];

var obj         = {};
obj.statusd     = 0;
obj.product     = [];
obj.value       = [];
var child       = [];

let maxcounter = [];

//objek let
let obj2       = {};
obj2.product   = [];

window.Bobot   = 0;

var GenRandom  =  {

    Stored: [],

    Job: function(){
        var newId = Date.now().toString().substr(6); // or use any method that you want to achieve this string

        if( this.Check(newId) ){
            this.Job();
        }

        this.Stored.push(newId);
        return newId; // or store it in sql database or whatever you want

    },

    Check: function(id){
        for( var i = 0; i < this.Stored.length; i++ ){
            if( this.Stored[i] == id ) return true;
        }
        return false;
    }

};

/* set tab ke local storage webs */
$(document).ready(function(){

    $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });

    let activeTab = localStorage.getItem('activeTab');
    
    if(activeTab == '#sub2-justified-tab1' || activeTab == '#sub2-justified-tab2' ){

        let getIDTabs = $('a[href="#left-tab3"]').attr('id');
    
        $("#"+getIDTabs).click();
    
        $('a[href="#left-tab3"]').tab('show');

    }else{
        let getIDTabs = $('a[href="' + activeTab + '"]').attr('id');
    
        $("#"+getIDTabs).click();
    
        $('a[href="' + activeTab + '"]').tab('show');

    }
    
    setIFrameSize();
    

});

function uniqId() {
    return Math.round(new Date().getTime() + (Math.random() * 100));
}
  

 /* refrash page */
$('#refreshX').on('click', function(){
    location.reload();
});

$('#bottom-justified-dataX').on('click', function(){
    loadtakslist();
});

loadPengaturan();
$('#bottom-justified-settingX').on('click', function(){
    setIFrameSize();
});

$('#bottom-justified-matheri').on('click', function(){
    loadMateri();
});

$('#sub2-justified-tab2x').on('click', function(){
    loadMappingMateri();
});

var checkBtnClick3 = 0;
$('#bottom-justified-eleX').on('click', function(){
    checkBtnClick3++;
    loadElements(checkBtnClick3);
    loadLevelRules();    
    setIFrameSize();
});

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

                    $(function() {
                        // renderForm(data);
                    });

                    R.push(data);
                    
                }

            },
            error: function(){
                loaderPage(false);
                swal({
                    title: "Galat",
                    text:  " Opps. hubungi orang terdekat, Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });
            }
        });
        return R;
    }
});

var isSetting = thisIdProfileArray.loadProfiled(thisIdProfile);
// console.log(isSetting);
var checkBtnClick = 0;
$('#asTemplate').on('click', function(){

    checkBtnClick++;
    if(checkBtnClick == 1){ // jika click nya cuman 1x load data
        renderForm(isSetting[0]);
    }

});

function renderForm(thisData){

    loaderPage(true);

    let isObject     = {};
    isObject.ipoly   = thisIdProfile;
   
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

                let result  = cryptoDecrypt(PHRASE, response.data);

                var res     = result.data;
                var  x      = counter-1;//Initial input field is set to 1

                /* set papah */
                for(x in res){
                    if(res[x].has_parent == 0 && res[x].parent_id == null){
                        obj2.product.push({ 
                            property: res[x],
                            option  : [], 
                            children : [] 
                        });      
                    }
                }
                /* papah cek tipe data jika select option */
                var optp = obj2.product;

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

                /* cek adik adik */
                for(p in optp){
                    for(x in res){
                        if(res[x].has_parent == 0 && res[x].parent_id != null){
                            
                            if(parseInt(res[x].object_type) == SELECTOPT || parseInt(res[x].object_type) == RADIONBTN){

                                if(optp[p].property['paramid'] == res[x].parent_id){
                                    optp[p].children.push({ 
                                        property  : res[x],
                                        option    : []
                                    });      
                                } 
                            }else if(parseInt(res[x].object_type) == INPUTEXT || parseInt(res[x].object_type) == TEXTAREA){
                                if(optp[p].property['paramid'] == res[x].parent_id){
                                    optp[p].children.push({ 
                                        property  : res[x],
                                    });      
                                }
                            }
                        }
                    }
                }

                /* cek adik adik jika ada select option atau radio */
                for(c in optp){
                    /* jika mempunyai adik2 */
                    if(optp[c].children){
                        
                        for(x in res){
                            if(res[x].has_parent != 0 && res[x].parent_id != null){
                                
                                if(parseInt(res[x].object_type) == SELECTOPT || parseInt(res[x].object_type) == RADIONBTN){
                                    
                                    for(g in optp[c].children){                                    
                                        if(optp[c].children[g].property['paramid'] == res[x].has_parent){
                                            optp[c].children[g].option.push({ 
                                                property  : res[x]
                                            });      
                                        } 
                                    }
                                }
                            }
                        }
                    }
                }

                /* ok selanjutnya generate formnya  */
                /* mun pakai limit jadi arrayna harus dipecah pecah(bongkars) */
                Object.defineProperty(Array.prototype, 'chunk', {
                    value: function(chunkSize) {
                        var R = [];
                        for (var i = 0; i < this.length; i += chunkSize)
                        R.push(this.slice(i, i + chunkSize));
                        return R;
                    }
                });

                var counter             = 0;
                var thisProduct         = obj2.product.chunk(thisData.limitx);
                var $contentClass       = 'typeInput';

                var contentWizard   = '';
                // console.log(obj2.product);
                for(z in  thisProduct){
                    // console.log(thisProduct);
                    var rowBody = '';
                                    
                    for(s in thisProduct[z]){
                        // var datStr = JSON.stringify(thisProduct[z][0].property['paramid']);
                        

                        obj.product.push({
                            headerid        : counter,
                            header          : 'selectHeader'+counter,
                            deleted         : 0,
                            option          : [], 
                            children        : [],
                            paramid         : thisProduct[z][s].property['paramid'],
                            position        : thisProduct[z][s].property['sortingx'],
                            x_status        : thisProduct[z][s].property['x_status'],
                        });

                        switch(parseInt(thisProduct[z][s].property['object_type'])) {

                            case INPUTEXT:
                            var inputan = "";
                            rowBody +='<div class="headrGroup'+counter+'"> <div class="form-group">'+
                                        '<div class="col-lg-12">'+
                                            '<div class="row">'+
                                                '<div class="col-md-3">'+
                                                    '<select onchange="changeType('+counter+')" class="form-control selectHeader'+counter+' " id="selectHeader'+counter+'" name="sel" data-width="100%" tabindex="-98">'+
                                                        '<option value="AK">Select type</option>'+
                                                        '<option value="'+INPUTEXT+'"selected>Input text</option>'+
                                                        '<option value="'+TEXTAREA+'">Textarea</option>'+
                                                        '<option value="'+SELECTOPT+'">Select option</option>'+
                                                        '<option value="'+RADIONBTN+'">Radio button</option>'+
                                                    '</select>'+
                                                '</div>'+
                                                '<div class="col-md-8">'+
                                                    '<span class="typeInput'+counter+'" >'+
                                                        '<div class="col-md-6">'+
                                                            '<input type="text" class="form-control labelparent'+counter+'" name="typeInput'+counter+'[]" placeholder="Label name . . ." value="'+thisProduct[z][s].property.object_label+'"></input>'+
                                                        '</div>'+
                                                        '<div class="col-md-6">'+
                                                            '<select class="form-control typedataparent'+counter+'" name="typeInputData'+counter+'[]" data-width="100%" tabindex="-98">'+
                                                                '<option value="AK">- Type Data -</option>'+
                                                                '<option value="'+TEXTS+'">Text/Varchar</option>'+
                                                                '<option value="'+NUMBERS+'">Number/Integer<option>'+
                                                            '</select>'+
                                                        '</div>'+
                                                    '</span>'+
                                                '</div>'+
                                                // '<div class="col-md-1">'+
                                                //     '<a type="button" onclick="addChild('+x+');return false;" class="ibtnAddChild btn border-warning-100 text-warning-100 btn-flat btn-xs btn-icon btn-rounded legitRipple btn-warning" style="color: #fb0e0e;"><i class="icon icon-cross"></i> Add Child</a>'+
                                                // '</div>'+
                                                '<div class="col-md-1">'+
                                                    '<a type="button" onclick="removeHeader('+counter+',\''+thisProduct[z][s].property.paramid+'\');return false;" class="ibtnDel btn bg-slate  btn-icon pull-right btn-xs" style="width:100%;font-weight: 500;"><i class="icon icon-trash"></i> Hapus</a>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div></div>';
                            break;
                                
                            case TEXTAREA:
                                rowBody +='<div class="headrGroup'+counter+'"> <div class="form-group">'+
                                            '<div class="col-lg-12">'+
                                                '<div class="row">'+
                                                    '<div class="col-md-3">'+
                                                        '<select onchange="changeType('+counter+')" class="form-control selectHeader'+counter+' " id="selectHeader'+counter+'" name="sel" data-width="100%" tabindex="-98">'+
                                                            '<option value="AK">Select type</option>'+
                                                            '<option value="'+INPUTEXT+'">Input text</option>'+
                                                            '<option value="'+TEXTAREA+'"selected>Textarea</option>'+
                                                            '<option value="'+SELECTOPT+'">Select option</option>'+
                                                            '<option value="'+RADIONBTN+'">Radio button</option>'+
                                                        '</select>'+
                                                    '</div>'+
                                                    '<div class="col-md-8">'+
                                                        '<span class="typeInput'+counter+'" >'+
                                                            '<input type="text"  class="form-control labelparent'+counter+'" placeholder="Label name . . ." value="'+thisProduct[z][s].property.object_label+'"></input>'+
                                                        '</span>'+
                                                    '</div>'+
                                                    // '<div class="col-md-1">'+
                                                    //     '<a type="button" onclick="addChild('+x+');return false;" class="ibtnAddChild btn border-warning-100 text-warning-100 btn-flat btn-xs btn-icon btn-rounded legitRipple btn-warning" style="color: #fb0e0e;"><i class="icon icon-cross"></i> Add Child</a>'+
                                                    // '</div>'+
                                                    '<div class="col-md-1">'+
                                                        '<a type="button" onclick="removeHeader('+counter+',\''+thisProduct[z][s].property.paramid+'\');return false;" class="ibtnDel btn bg-slate  btn-icon pull-right btn-xs" style="width:100%;font-weight: 500;"><i class="icon icon-trash"></i> Hapus</a>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div></div>';
                                break;
                                
                                case SELECTOPT:  
                                    var $opt = '';
                                    var ank  = [];

                                    '<div class="headrGroup'+counter+'"> <div class="form-group">';
                                    for(i in thisProduct[z][s].option){
                                        m = uniqId();
                                        var paramadk = thisProduct[z][s].option[i].property['paramid']; 
                                        ank.push(paramadk);

                                        $opt += '<div class="sel" style=""><div class="fx">'+
                                                    '<div class="col-lg-12" style="margin-top: 10px;">'+
                                                        '<div class="row">'+
                                                            '<div class="col-md-5">'+
                                                                '<input type="text" class="form-control optionlabel'+m+'" name="optionlabel'+m+'" placeholder="Label name . . ." value="'+thisProduct[z][s].option[i].property['object_label']+'"></input>'+
                                                            '</div>'+
                                                            '<div class="col-md-5">'+
                                                                '<input type="text" class="form-control optionvalue'+m+'" name="optionvalue'+m+'" placeholder="Value option . . ." value="'+thisProduct[z][s].option[i].property['object_value']+'"></input>'+
                                                            '</div>'+
                                                            '<div class="col-md-2">'+
                                                                '<a type="button" onclick="removeHeaderAnk('+m+',\''+thisProduct[z][s].option[i].property['paramid']+'\');return false;" class="ibtnDel delete'+m+' btn bg-slate  btn-icon pull-right btn-xs" style="width:100%;font-weight: 500;"><i class="icon icon-trash"></i> Hapus</a>'+
                                                            '</div>'+   
                                                            '</div>'+
                                                        '</div><br>'+
                                                    '</div>'+
                                                '</div>';      
                                                
                                        if('selectHeader'+counter){   
                                            for(i in obj.product){
                                            /* check jika dia anak anak atau adik adik */
                                                if(obj.product[i].header == 'selectHeader'+counter){
                                                    obj.product[i].option.push({
                                                        paramsend   : paramadk.toString(),
                                                        optionlabel : 'optionlabel'+m,
                                                        optionvalue : 'optionvalue'+m,
                                                        deleted     : 0
                                                    });
                                                }       
                                            }
                                        }
                                    }
                                    
                                    rowBody +='<div class="headrGroup'+counter+'"> <div class="form-group">'+
                                                '<div class="col-lg-12">'+
                                                        '<div class="row">'+
                                                            '<div class="col-md-3">'+
                                                                '<select onchange="changeType('+counter+')" class="form-control selectHeader'+counter+' " id="selectHeader'+counter+'" name="sel" data-width="100%" tabindex="-98">'+
                                                                    '<option value="AK">Select type</option>'+
                                                                    '<option value="'+INPUTEXT+'">Input text</option>'+
                                                                    '<option value="'+TEXTAREA+'">Textarea</option>'+
                                                                    '<option value="'+SELECTOPT+'"selected>Select option</option>'+
                                                                    '<option value="'+RADIONBTN+'">Radio button</option>'+
                                                                '</select>'+
                                                            '</div>'+
                                                            '<div class="col-md-8">'+
                                                                '<span class="typeInput'+counter+'" >'+
                                                                    '<div class="col-md-10">'+
                                                                        '<input type="text" class="form-control labelparent'+counter+'" placeholder="Label name . . ." value="'+thisProduct[z][s].property.object_label+'"></input>'+
                                                                    '</div>'+
                                                                    '<div class="col-md-2">'+
                                                                        '<span id="" onclick="addSelect('+counter+',\''+$contentClass+'\')" class="pull-right form-group btn btn-xs addmoreHeader btn-info add_field_butto col-xs-12 col-lg-12" ><i class="icon-plus-circle2"></i><b> Add Option</b></span>'+
                                                                    '</div>'+
                                                                    $opt+
                                                                '</span>'+
                                                            '</div>'+
                                                            // '<div class="col-md-1">'+
                                                            //     '<a type="button" onclick="addChild('+x+');return false;" class="ibtnAddChild btn border-warning-100 text-warning-100 btn-flat btn-xs btn-icon btn-rounded legitRipple btn-warning" style="color: #fb0e0e;"><i class="icon icon-cross"></i> Add Child</a>'+
                                                            // '</div>'+
                                                            '<div class="col-md-1">'+
                                                                '<a type="button" onclick="removeHeader('+counter+',\''+thisProduct[z][s].property.paramid+'\',\''+ank+'\');return false;" class="ibtnDel btn bg-slate  btn-icon pull-right btn-xs" style="width:100%;font-weight: 500;"><i class="icon icon-trash"></i> Hapus</a>'+
                                                            '</div>'+
                                                        '</div>'+
                                                    '</div>'+
                                                '</div></div>';
                                break;
                        
                                case RADIONBTN:

                                    var $opt = '';
                                    var ank  = [];

                                    '<div class="headrGroup'+counter+'"> <div class="form-group">';
                                    for(i in thisProduct[z][s].option){
                                        m = uniqId();
                                        var paramadk = thisProduct[z][s].option[i].property['paramid']; 
                                        ank.push(paramadk);
                                        
                                        $opt += '<div class="sel" style=""><div class="fx">'+
                                                    '<div class="col-lg-12" style="margin-top: 10px;">'+
                                                        '<div class="row">'+
                                                            '<div class="col-md-5">'+
                                                                '<input type="text" class="form-control optionlabel'+m+'" name="optionlabel'+m+'" placeholder="Label name . . ." value="'+thisProduct[z][s].option[i].property['object_label']+'"></input>'+
                                                            '</div>'+
                                                            '<div class="col-md-5">'+
                                                                '<input type="text" class="form-control optionvalue'+m+'" name="optionvalue'+m+'" placeholder="Value option . . ." value="'+thisProduct[z][s].option[i].property['object_value']+'"></input>'+
                                                            '</div>'+
                                                            '<div class="col-md-2">'+
                                                                '<a type="button" onclick="removeHeaderAnk('+m+',\''+thisProduct[z][s].option[i].property['paramid']+'\');return false;" class="ibtnDel delete'+m+' btn bg-slate  btn-icon pull-right btn-xs" style="width:100%;font-weight: 500;"><i class="icon icon-trash"></i> Hapus</a>'+
                                                            '</div>'+ 
                                                            '</div>'+
                                                        '</div><br>'+
                                                    '</div>'+
                                                '</div>';      
                                                
                                        if('selectHeader'+counter){   
                                            for(i in obj.product){
                                            /* check jika dia anak anak atau adik adik */
                                                if(obj.product[i].header == 'selectHeader'+counter){
                                                    obj.product[i].option.push({
                                                        paramsend   : paramadk.toString(),
                                                        optionlabel : 'optionlabel'+m,
                                                        optionvalue : 'optionvalue'+m,
                                                        deleted     : 0
                                                    });
                                                }       
                                            }
                                        }
                                    }
                                    rowBody +='<div class="headrGroup'+counter+'"> <div class="form-group">'+
                                                '<div class="col-lg-12">'+
                                                    '<div class="row">'+
                                                        '<div class="col-md-3">'+
                                                            '<select onchange="changeType('+counter+')" class="form-control selectHeader'+counter+' " id="selectHeader'+counter+'" name="sel" data-width="100%" tabindex="-98">'+
                                                                '<option value="AK">Select type</option>'+
                                                                '<option value="'+INPUTEXT+'">Input text</option>'+
                                                                '<option value="'+TEXTAREA+'">Textarea</option>'+
                                                                '<option value="'+SELECTOPT+'">Select option</option>'+
                                                                '<option value="'+RADIONBTN+'"selected>Radio button</option>'+
                                                            '</select>'+
                                                        '</div>'+
                                                        '<div class="col-md-8">'+
                                                            '<span class="typeInput'+counter+'" >'+
                                                                '<div class="col-md-10">'+
                                                                    '<input type="text" class="form-control labelparent'+counter+'" placeholder="Label name . . ." value="'+thisProduct[z][s].property.object_label+'"></input>'+
                                                                '</div>'+
                                                                '<div class="col-md-2">'+
                                                                    '<span id="" onclick="addSelect('+counter+',\''+$contentClass+'\')" class="pull-right form-group btn btn-xs addmoreHeader btn-info add_field_butto col-xs-12 col-lg-12" ><i class="icon-plus-circle2"></i><b></b></span>'+
                                                                '</div>'+
                                                                $opt+
                                                            '</span>'+
                                                        '</div>'+
                                                        // '<div class="col-md-1">'+
                                                        //     '<a type="button" onclick="addChild('+x+');return false;" class="ibtnAddChild btn border-warning-100 text-warning-100 btn-flat btn-xs btn-icon btn-rounded legitRipple btn-warning" style="color: #fb0e0e;"><i class="icon icon-cross"></i> Add Child</a>'+
                                                        // '</div>'+
                                                        '<div class="col-md-1">'+
                                                            '<a type="button" onclick="removeHeader('+counter+',\''+thisProduct[z][s].property.paramid+','+ank+'\');return false;" class="ibtnDel btn bg-slate  btn-icon pull-right btn-xs" style="width:100%;font-weight: 500;"><i class="icon icon-trash"></i> Hapus</a>'+
                                                        '</div>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div></div>';
                                
                                break;   

                                
                        }
                            counter++;
                        }

                        contentWizard += rowBody;

                        /* set json to value */
                        $("textarea#jsonArea").empty();
                        $("textarea#jsonArea").val(JSON.stringify(result));
                    }

                maxcounter.push(counter);

                $('.headerObject').append(contentWizard);
                
                $(function() {
                    // stepWizard();
                    controlRadio();
                });

            }

            loaderPage(false);
           
            
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
}  

$(document).ready(function() {
    var max_fields  = 99; //Maximum allowed input fields 
    var wrapper     = $(".headerObject"); //Input fields wrapper
    var add_button  = $(".addmoreHeader"); //Add button class or ID
    var x           = maxcounter;//Initial input field is set to 1

    let myOpt       = ``;
    if(isSetting[0].type_content == 2){ // if type is quiz then
        myOpt += '<option value="'+RADIONBTN+'" selected>Radio button</option>';
    }else{
        myOpt +=    '<option value="AK">Select type</option>'+
                    '<option value="'+INPUTEXT+'">Input text</option>'+
                    '<option value="'+TEXTAREA+'">Textarea</option>'+
                    '<option value="'+SELECTOPT+'">Select option</option>'+
                    '<option value="'+RADIONBTN+'">Radio button</option>'
    }

    //When user click on add input button
    $(add_button).click(function(e){
        e.preventDefault();
        //Check maximum allowed input fields
        if(x < max_fields){ 
            x++; //input field increment
            //add input field
            $(wrapper).append('<div class="headrGroup'+x+'"> <div class="form-group">'+
                '<div class="col-lg-12">'+
                    '<div class="row">'+
                        '<div class="col-md-2">'+
                            '<select onchange="changeType('+x+')" class="form-control selectHeader'+x+' " id="selectHeader'+x+'" name="sel" data-width="100%" tabindex="-98">'+
                                myOpt+
                            '</select>'+
                        '</div>'+
                        '<div class="col-md-8">'+
                            '<span class="typeInput'+x+'" ></span>'+
                        '</div>'+
                        // '<div class="col-md-1">'+
                        //     '<a type="button" onclick="addChild('+x+');return false;" class="ibtnAddChild btn border-warning-100 text-warning-100 btn-flat btn-xs btn-icon btn-rounded legitRipple btn-warning" style="color: #fb0e0e;"><i class="icon icon-cross"></i> Add Child</a>'+
                        // '</div>'+
                        '<div class="col-md-2">'+
                            '<a type="button" onclick="removeHeader('+x+');return false;" class="ibtnDel btn bg-slate  btn-icon pull-right btn-xs" style="width:100%;font-weight: 500;"><i class="icon icon-trash"></i> Hapus</a>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div></div>'); 

            obj.product.push({ headerid: x, header: 'selectHeader'+x, deleted : 0, option : [], children : [], paramid:[], position : 99, x_status: 1});
            // console.log(obj.product);  
            
            if(isSetting[0].type_content == 2){ // if type is quiz then
                changeType(x);
            }
        }

    });
});


function changeType(paramid, haschild){
    var $type = $('.selectHeader'+paramid).val();
    $('.typeInput'+paramid).empty();
    var $contentClass = 'typeInput';
    // console.log($contentClass);
    switch(parseInt($type)) {

        case INPUTEXT:
            $('.typeInput'+paramid).append('<div class="col-md-6">'+
                '<input type="text" class="form-control labelparent'+paramid+'" name="typeInput'+paramid+'[]" placeholder="Label name . . ."></input>'+
            '</div>'+
            '<div class="col-md-6">'+
                '<select class="form-control typedataparent'+paramid+'" name="typeInputData'+paramid+'[]"data-width="100%" tabindex="-98">'+
                    '<option value="AK">- Type Data -</option>'+
                    '<option value="'+TEXTS+'">Text/Varchar</option>'+
                    '<option value="'+NUMBERS+'">Number/Integer<option>'+
                '</select>'+
            '</div>');
        break;
        
        case TEXTAREA:
            $('.typeInput'+paramid).append('<input type="text"  class="form-control labelparent'+paramid+'" placeholder="Label name . . ."></input>');
        break;
        
        case SELECTOPT:
            $('.typeInput'+paramid).append('<div class="col-md-10">'+
                '<input type="text" class="form-control labelparent'+paramid+'" placeholder="Label name . . ."></input>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<span id="" onclick="addSelect('+paramid+',\''+$contentClass+'\','+haschild+')" class="pull-right form-group btn btn-xs addmoreHeader btn-info add_field_butto col-xs-12 col-lg-12" ><i class="icon-plus-circle2"></i><b> Add Option</b></span>'+
            '</div>');
        break;

        case RADIONBTN:
            $('.typeInput'+paramid).append('<div class="col-md-10">'+
                '<input type="text" class="form-control labelparent'+paramid+'" placeholder="Label name . . ."></input>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<span id="" onclick="addSelect('+paramid+',\''+$contentClass+'\','+haschild+')" class="pull-right form-group btn btn-xs addmoreHeader btn-info add_field_butto col-xs-12 col-lg-12" ><i class="icon-plus-circle2"></i><b> </b></span>'+
            '</div>');
        break;
        
        default:
            $('.typeInput'+paramid).append('<p><b>Type data belum dipilih</b></p>');
    }

    myArray = jQuery.grep(myArray, function(value) {
        return value['type'] !=  'typeInput'+paramid;
    });  
   
}

function addSelect(paramid, contentid, haschild){
    //var j = GenRandom.Job();
    var j           = uniqId();    
    
    $('.'+contentid+''+paramid).append('<div class="sel delete'+j+'" style=""><div class="fx">'+
        '<div class="col-lg-12" style="margin-top: 10px;">'+
            '<div class="row">'+
                '<div class="col-md-5">'+
                    '<input type="text" class="form-control optionlabel'+j+'" name="optionlabel'+j+'" placeholder="Label name . . ."></input>'+
                '</div>'+
                '<div class="col-md-5">'+
                    '<input type="text" class="form-control optionvalue'+j+'" name="optionvalue'+j+'" placeholder="Value option . . ."></input>'+
                '</div>'+
                '<div class="col-md-2">'+
                    '<a type="button" onclick="removeHeaderAnk('+j+');return false;" class="ibtnDel button'+j+' btn bg-slate btn-icon pull-right btn-xs" style="width:100%;font-weight: 500;"><i class="icon icon-trash"></i> Hapus</a>'+
                '</div>'+
            '</div>'+
        '</div>'+
    '</div></div><br>');

    if(obj.product){
        var i;
        // console.log(haschild);
        for(i in obj.product){

            /* check jika dia anak anak atau adik adik */
            if(haschild){
                for(c in obj.product[i].children){
                    if(obj.product[i].children[c].childid == paramid){
                        obj.product[i].children[c].option.push({
                            optionlabel : 'optionlabel'+j,
                            optionvalue : 'optionvalue'+j,
                            deleted     : 0
                        });
                    }
                }
            }else{
                if(obj.product[i].header == 'selectHeader'+paramid){
                    obj.product[i].option.push({
                        paramsendank    : "",
                        optionlabel     : 'optionlabel'+j,
                        optionvalue     : 'optionvalue'+j,
                        deleted         : 0
                    });
                }
            }           
        }
    }
    // console.log(obj.product);
}

function removeHeaderS(content, paramid1, paramid2){
    $('.headrGroup'+paramid).remove();
}

function removeHeader(paramid, paramiddb, paramank){
    
    let rem  = 0;
    let rem2 = 0;

    for(rem in obj.product){
        if(obj.product[rem].headerid == paramid){           
            obj.product[rem].deleted = 1;
        }
    }
    
    obj.statusd = 1; // jika delete
    
    
    if(paramank == '' || paramank != undefined){
        var paramsenddb = (paramiddb.concat(",",paramank));
    }else{
        
        var paramsenddb = paramiddb;

        if(paramiddb){
            let lastParam   = paramiddb.substr(paramiddb.length - 1); // get coma jika last string na coma
    
            if(lastParam == ','){
                paramsenddb = paramiddb.replace(',','');
            }
        }
    }

    if(paramsenddb){

        /* hapus data */
        
        let isObject     = {};
        isObject.paramid = paramsenddb;

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseURL + '/jsondata/deleteparameter',
            data: {
                iparam	    : cryptoEncrypt(PHRASE, isObject),
            },
            success: function(result){
                if(result.code == 0){  
                    
                    new PNotify({
                        title: 'Notice',
                        text: 'Failed delete.',
                        icon: 'icon-warning22'
                    });

                    for(rem in obj.product){
                        if(obj.product[rem].headerid == paramid){           
                            obj.product[rem].deleted = 0;
                        }
                    }

                    new PNotify({
                        title: 'Notice',
                        text: 'Success delete.',
                        icon: 'icon-warning22'
                    });

                    $('.headrGroup'+paramid).remove();
                    // loadsetting();
                }else{
                    // loaduser();
                    new PNotify({
                        title: 'Notice',
                        text: 'Failed delete.',
                        icon: 'icon-warning22'
                    });
                }
            }
        });
    }else{
        $('.headrGroup'+paramid).remove();
    }  
}

function removeHeaderAnk(paramid, paramiddb){

    let rem  = 0;
    let rem2 = 0;

    for(rem in obj.product){
        for (rem2 in obj.product[rem].option) {
            if(obj.product[rem].option[rem2].optionlabel == 'optionlabel'+paramid){        
                obj.product[rem].option[rem2].deleted = 1;
            }
        }        
    }


    if(paramiddb){

        /* hapus data */
        let isObject     = {};
        isObject.paramid = paramiddb;

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseURL + '/jsondata/deleteparameter',
            data: {
                iparam	    : cryptoEncrypt(PHRASE, isObject),
            },
            success: function(result){
                if(result.code == CODE_SUCCESS){    

                    $('.optionvalue'+paramid).remove();
                    $('.optionlabel'+paramid).remove();
                    $('.delete'+paramid).remove();
                    $('.button'+paramid).remove();
                    
                    for(rem in obj.product){
                        for (rem2 in obj.product[rem].option) {
                            if(obj.product[rem].option[rem2].optionlabel == 'optionlabel'+paramid){        
                                obj.product[rem].option[rem2].deleted = 2;
                            }
                        }        
                    }

                    new PNotify({
                        title: 'Notice',
                        text: 'Success delete.',
                        icon: 'icon-warning22'
                    });

                }else{
                    new PNotify({
                        title: 'Notice',
                        text: 'Failed delete.',
                        icon: 'icon-warning22'
                    });
                }
            }
        });
    }else{              
        $('.optionvalue'+paramid).remove();
        $('.optionlabel'+paramid).remove();
        $('.delete'+paramid).remove();
        $('.button'+paramid).remove();
    }
}

function addChild(paramid){
   //var j          = GenRandom.Job();
    var j          = uniqId();
    $('.headerObject > .headrGroup'+paramid).append('<div class="headrGroup'+paramid+' childGroup'+j+'"><div class="form-group">'+
        '<div class="col-lg-12">'+
            '<div class="row">'+
                '<div class="col-md-1">'+
                    '<label>Child</label>'+
                '</div>'+
                '<div class="col-md-2">'+
                    '<select onchange="changeType('+j+', 1)" class="form-control selectChild'+j+' selectHeader'+j+' " name="sel[]" data-width="100%" tabindex="-98">'+
                        '<option value="AK">Select type</option>'+
                        '<option value="'+INPUTEXT+'">Input text</option>'+
                        '<option value="'+TEXTAREA+'">Textarea</option>'+
                        '<option value="'+SELECTOPT+'">Select option</option>'+
                        '<option value="'+RADIONBTN+'">Radio button</option>'+
                    '</select>'+
                '</div>'+
                '<div class="col-md-8">'+
                    '<span class="typeInput'+j+'" ></span>'+
                '</div>'+
                '<div class="col-md-1">'+
                    '<a type="button" onclick="removeChilds('+j+');return false;" class="btn border-warning-100 text-warning-100 btn-flat btn-xs btn-icon btn-rounded legitRipple btn-warning" style="color: #fb0e0e;"><i class="icon icon-cross"></i> Hapus</a>'+
                '</div>'+
            '</div>'+
        '</div>'+
    '</div></div>');

    if(obj.product){
        var i;
        for(i in obj.product){
            if(obj.product[i].header == 'selectHeader'+paramid){
                obj.product[i].children.push({
                    childid: j, 
                    parentid: paramid, 
                    option : [],
                });
            }           
        }
    }
}

function removeChilds(paramid){
    $('.childGroup'+paramid).remove();
} 

$('#saved').click(function(e){
    obj.value = [];
    var headerType;

    // console.log(obj.product);

    if(obj.product){
        var i;
        var k;
        var f;
        for(i in obj.product){
            /* jika status delete/1 jangan dibawa */
            if(obj.product[i].deleted == 0){

                headerType      = $('.'+obj.product[i].header).val();
                paramsend       = obj.product[i].paramid;
                var q           = paramsend.toString();
                
                switch(parseInt(headerType)) {
                    
                    case INPUTEXT:
                        obj.value.push({ 
                            parenttype      : headerType, 
                            parentlabel     : $('.'+'labelparent'+obj.product[i].headerid).val(), 
                            parenttyped     : $('.'+'typedataparent'+obj.product[i].headerid).val(), 
                            paramsend       : q,
                            option          : [],
                            children        : [],
                            position        : obj.product[i].position,
                            x_status        : obj.product[i].x_status,
                            
                        });
                    break;
                    
                    case TEXTAREA:
                        obj.value.push({ 
                            parenttype      : headerType, 
                            parentlabel     : $('.'+'labelparent'+obj.product[i].headerid).val(), 
                            parenttyped     : $('.'+'typedataparent'+obj.product[i].headerid).val(),
                            paramsend       : q,
                            option          : [],
                            children        : [] ,
                            position        : obj.product[i].position,
                            x_status        : obj.product[i].x_status,
                        });
                    break;
                    
                    case SELECTOPT:
                        obj.value.push({ 
                            parenttype      : headerType, 
                            parentlabel     : $('.'+'labelparent'+obj.product[i].headerid).val(), 
                            parenttyped     : $('.'+'typedataparent'+obj.product[i].headerid).val(),
                            paramsend       : q,
                            option          : [],
                            children        : [],
                            position        : obj.product[i].position, 
                            x_status        : obj.product[i].x_status,
                        });
                        
                        /* get value option header */                               
                        if(obj.product[i].option){
                            for(f in obj.product[i].option){

                                if(obj.statusd == 1){ // jika ada field yang di delete
                                    var ba = f;
                                }else{
                                    var ba = i;
                                }
                                
                                if(obj.product[i].option[f].paramsend == undefined){
                                    var anak = ""; //jika add option nya baru buat ngisi paramid
                                }else{
                                    var anak = obj.product[i].option[f].paramsend; //jika add option nya mau di updte paramid nya
                                }
                                
                                if(obj.product[i].option[f].deleted == 0){
                                    obj.value[ba].option.push({ 
                                        optionlabel      : $('.'+obj.product[i].option[f].optionlabel).val(),
                                        optionvalue      : $('.'+obj.product[i].option[f].optionvalue).val(),
                                        paramsendank     : anak, //paramid dari pilihan option
                                    });
                                }
                            }
                        }
                        
                    break;
            
                    case RADIONBTN:
                        obj.value.push({ 
                            parenttype      : headerType, 
                            parentlabel     : $('.'+'labelparent'+obj.product[i].headerid).val(), 
                            parenttyped     : $('.'+'typedataparent'+obj.product[i].headerid).val(), 
                            option          : [],
                            paramsend       : q,
                            children        : [],
                            position        : obj.product[i].position, 
                            x_status        : obj.product[i].x_status,
                        });

                        /* get value option header */                               
                        if(obj.product[i].option){
                            for(f in obj.product[i].option){

                                if(obj.statusd == 1){ // jika ada field yang di delete
                                    var ba = f;
                                }else{
                                    var ba = i;
                                }

                                if(obj.product[i].option[f].paramsend == undefined){
                                    var anak = ""; //jika add option nya baru buat ngisi paramid
                                }else{
                                    var anak = obj.product[i].option[f].paramsend; //jika add option nya mau di updte paramid nya
                                }
                                if(obj.product[i].option[f].deleted == 0){
                                    obj.value[ba].option.push({ 
                                        optionlabel      : $('.'+obj.product[i].option[f].optionlabel).val(),
                                        optionvalue      : $('.'+obj.product[i].option[f].optionvalue).val(),
                                        paramsendank     : anak,
                                    });
                                }
                            }
                        }
                    break;
                    
                    default:
                        $('.typeInput').append('<p><b>Type data belum dipilih</b></p>');
                }     

            }
            
            /* get value child */
            if(obj.product[i].children){
                
                for(k in obj.product[i].children){
                        
                    childID     = obj.product[i].children[k].childid;
                    parentID    = obj.product[i].children[k].parentid;
                    childType   = $('.selectChild'+childID).val();
                    
                    if(obj.product[i].header == 'selectHeader'+parentID){

                        switch(parseInt(childType)) {
                            case INPUTEXT:
                                obj.value[i].children.push({
                                    childtype       : childType,
                                    childlabel      : $('.'+'labelparent'+childID).val(), 
                                    childtyped      : $('.'+'typedataparent'+childID).val(), 
                                    childoption     : [],
                                });
                            break;
                            
                            case TEXTAREA:
                                obj.value[i].children.push({
                                    childtype       : childType,
                                    childlabel      : $('.'+'labelparent'+childID).val(), 
                                    childtyped      : $('.'+'typedataparent'+childID).val(), 
                                    childoption     : [],
                                });
                            break;
                            
                            case SELECTOPT:
                                obj.value[i].children.push({
                                    childtype       : childType,
                                    childlabel      : $('.'+'labelparent'+childID).val(), 
                                    childtyped      : $('.'+'typedataparent'+childID).val(), 
                                    childoption     : [],
                                });
                                // console.log(obj);
                                // console.log(obj.product[i].children[k].option);
                                /* get value option child */                        
                                if(obj.product[i].children[k].option){
                                    for(f in obj.product[i].children[k].option){
                                        
                                        obj.value[i].children[k].childoption.push({ 
                                            optionlabel      : $('.'+obj.product[i].children[k].option[f].optionlabel).val(),
                                            optionvalue      : $('.'+obj.product[i].children[k].option[f].optionvalue).val(),
                                            
                                        });
                                        
                                    }
                                }

                            break;
                    
                            case RADIONBTN:
                                obj.value[i].children.push({
                                    childtype       : childType,
                                    childlabel      : $('.'+'labelparent'+childID).val(), 
                                    childtyped      : $('.'+'typedataparent'+childID).val(), 
                                    childoption     : [],
                                });

                                /* get value option child */                               
                                if(obj.product[i].children[k].option){
                                    for(f in obj.product[i].children[k].option){
                                        
                                        obj.value[i].children[k].childoption.push({ 
                                            optionlabel      : $('.'+obj.product[i].children[k].option[f].optionlabel).val(),
                                            optionvalue      : $('.'+obj.product[i].children[k].option[f].optionvalue).val(),
                                        });

                                    }
                                }
                                
                            break;
                            
                            default:
                                $('.typeInput'+paramid).append('<p><b>Type data belum dipilih</b></p>');
                        }
                    }
                }
            }
        }
    }

    /* save data */
    let isObject     = {};
    isObject.idata   = obj.value;
    isObject.ipoly   = thisIdProfile;


    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseURL + '/jsondata/insertmanagementparam',
        data: {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        success: function(result){
            if(result.code == 0){                   
                swal({
                    title: "Sukses",
                    text: "Formulir berhasil diupdate ",
                    confirmButtonColor: "#66BB6A",
                    type: "success"
                }, function(){ 
                    location.reload();
                });
                
            }else{
                // loaduser();
                swal({
                    title: "Galat",
                    text:  result.info+" Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });
            }
        }
    });
});

$('.pratinjauX').on('click', function(){
    window.open(baseURL+'/magic/charsurvey?get='+thisIdProfile, '_blank');
});


function loadtakslist(my = false){
//     var no_ticket    = $('#cari_no_tiket').val();
    
//     var status_create = $("input[name='status_create']:checked").val();
//     if(status_create){
//         var picker_start = $("input[name='daterangepicker_start']").val();
//         var picker_end   = $("input[name='daterangepicker_end']").val();
//     }else{
//         var picker_start = null;
//         var picker_end   = null;
//     }

//     var type_laporan = [];
//     $("input[name='input_type_laporan']:checked").each(function() {
//         type_laporan.push(this.value);
//     });

//     var status_laporan = [];
//     $("input[name='input_status_laporan']:checked").each(function() {
//         status_laporan.push(this.value);
//     });

//     var subject_laporan = [];
//     $("input[name='input_subject_laporan']:checked").each(function() {
//         subject_laporan.push(this.value);
//     });

//     var subject_laporan_2 = [];
//     $("input[name='input_subject_laporan_2']:checked").each(function() {
//         subject_laporan_2.push(this.value);
//     });

//     var $projectIist    = $('#check_user').select2("val");
//     if($projectIist){
//         var $toString	= $projectIist.toString();
//     }else{
//         var $toString   = null;
//     }
    // 
    
    var $csrfToken = $('meta[name="csrf-token"]').attr("content");

    var dtpr = $('#data-list').DataTable({
        serverSide	: false,
        destroy		: true,
        bFilter		: true,
        responsive	: true,
        searching   : true,
        pagingType  : 'full',
        lengthMenu  : [[10, 25, 50, -1], [10, 25, 50, "All"]],
        pageLength  : 25,
        //sDom: '<"top"l>rt<"bottom"p><"clear">',  //search hide atau show
        ajax		: {
            url: baseURL + '/jsondata/loadsurveydata',
            type: 'POST',
            data : function (d) {
                return $.extend( {}, d, {
                    role			        : 'a',
                    proid		            :  thisIdProfile,
                    '_csrf'                 : $csrfToken,
                    // searchx			    : searchx, // search dari inputan manual/ advan search
                    // search			        : d.search['value'],
                } );
            },
        },	
        columns: [
            { 'data': 'detail_id', 'sClass':'table-inbox-checkbox rowlink-skip','sWidth':'10px'},
            { 'data': 'nama'},
            { 'data': 'pertanyaan'},
            { 'data': 'jawaban'}, // 3
            { 'data': 'create_date'}, // 4
        ],
        // order: [[ 4, 'desc' ]],
        buttons: {            
            buttons: [
                {
                    extend: 'copyHtml5',
                    title: 'Data export',
                    className: 'btn btn-default',
                    text: '<i class="icon-copy3 position-left"></i> Copy',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excelHtml5',
                    title: 'Data export',
                    className: 'btn btn-default',
                    text: '<i class="icon-file-excel position-left"></i> Excel',
                    exportOptions: {
                        columns: function(idx, data, node) {
                            if ($(node).hasClass('noVis')) {
                                return false;
                            }
                            return $('#data-list').DataTable().column(idx).visible();
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Data export',
                    className: 'btn btn-default',
                    text: '<i class="icon-file-pdf position-left"></i> Pdf',
                    exportOptions: {
                        columns: [':visible']
                    }
                },
                {
                    extend: 'colvis',
                    className: 'btn btn-default',
                    // extend: 'colvis',
                    // text: '<i class="icon-three-bars"></i> <span class="caret"></span>',
                    // className: 'btn bg-blue btn-icon'
                }
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
                visible: true,
                targets: 1
            },
            {
                visible: true,
                targets: 2
            },
            {
                visible: true,
                targets: 3
            },
            {
                visible: true,
                targets: 4
            },    
        ],
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){ 


            var index = iDisplayIndexFull + 1; 
            $('td:eq(0)', nRow).html('#'+index); 
            return  index;
        },
        drawCallback: function (settings) {
            
            var api = this.api();
            var rows = api.rows({page:'current'}).nodes();
            var last=null;
            
            // console.log(api.rows());
            // Grouod rows
            // api.column(1, {page:'current'}).data().each(function (group, i) {
            //     if (last !== group) {
            //         $(rows).eq(i).before(
            //             '<tr class="active border-double"><td colspan="10" class="text-semibold">'+group+'</td></tr>'
            //         );

            //         last = group;
            //     }
            // });

            // Reverse last 3 dropdowns orientation
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');

            
        },
        fnInitComplete: function (oSettings, json) {
            loaderPage(false);
            var that = this;
            var td ;
            var tr ;

            this.$('td').click( function () {
                td = this;
            });
            this.$('tr').click( function () {
                tr = this;
            });
            
            


            $('#data-list input').bind('keyup', function (e) {
                return this.value;
            });

            /* buat nampilin individual search kari dibuka 
            ---------------------------------------------------*/
            // $("#tasks-list thead").append($("#tasks-list thead tr:first ").clone());
            // $('#tasks-list thead tr:eq(1) th').each( function (index) {
            //     var that = this;                    
            //     loadIndividualSearch(this, index, dtpr);                    
            // });

            // /* redraw kolom na gans */
            // $('#tasks-list').on( 'column-visibility.dt', function ( e, settings, column, state ) {
            //     $("#tasks-list thead tr:eq(1)").remove();

            //     $("#tasks-list thead").append($("#tasks-list thead tr:first ").clone());
            //     $('#tasks-list thead tr:eq(1) th').each( function (index) {
            //         var that = this;
            //         loadIndividualSearch(this, index, dtpr);
            //         $('#tasks-list thead tr:eq(1) th').removeClass('table-inbox-checkbox rowlink-skip sorting');
            //     });
            //     // console.log(
            //     //     'Column '+ column +' has changed to '+ (state ? 'visible' : 'hidden')
            //     // );
            // });

            /* buat nampilin individual search kari dibuka 
            ---------------------------------------------------*/

            // var availableHeight = $(window).height() + $('#tasks-list').height() + $('#hheadr').height() + 200;
            // parent.AdjustIframeHeight(availableHeight);

        }
    });
        
}

// setting 

function controlRadio(){

     // Primary
    $(".control-primary").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-primary-600 text-primary-800'
    });
    
    // Danger
    $(".control-danger").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-danger-600 text-danger-800'
    });
    
    // Success
    $(".control-success").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-success-600 text-success-800'
    });
    
    // Warning
    $(".control-warning").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-warning-600 text-warning-800'
    });
    
    // Info
    $(".control-info").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-info-600 text-info-800'
    });
    
    // Custom color
    $(".control-custom").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-indigo-600 text-indigo-800'
    });

    $(".switch").bootstrapSwitch();

}

function switchery(){
    if (Array.prototype.forEach) {
       var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
       elems.forEach(function(html) {
           var switchery = new Switchery(html);
       });
    }
    else {
        var elems = document.querySelectorAll('.switchery');
    
        for (var i = 0; i < elems.length; i++) {
            var switchery = new Switchery(elems[i]);
        }
    }

    var warning = document.querySelector('.switchery-warning');
    var switchery = new Switchery(warning, { color: '#00BCD4' });

    var warning = document.querySelector('.switchery-warning2');
    var switchery = new Switchery(warning, { color: '#00BCD4' });


}

// Basic initialization
$('.daterange-single').daterangepicker({ 
    singleDatePicker: true,
    locale: {
        format: 'YYYY-MM-DD'
    }
});

 // Checkboxes, radios
 $(".styled, .multiselect-container input").uniform({
    radioClass: 'choice'
});

function loadPengaturan(){
    
    let isObject     = {};
    isObject.ipoly   = thisIdProfile;
    
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseURL + '/jsondata/loadsetting',
        data: {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        success: function(response){

            if(response.code == CODE_SUCCESS){         

                let result  = cryptoDecrypt(PHRASE, response.data);
               
                let res     = result.data[0];

                /* load judul */
                $('#valueJudul').val(res.judul);
                /* load deskripsi */
                $('#valueDeskripsi').val(res.deskripsi);

                /* load setting publish */     
                var radioPublish = 'checked="checked"';
                let pub          = 0;
                let pob          = ['Public', 'Private', 'Non Aktif'];
                for(pub in pob){
                    var pab = parseInt(pub) + 1; 
                    if(pab == res.publish){
                        $('#publishRadio').append('<div class="radio">'+
                            '<label>'+
                                '<input type="radio" onClick="lRadio('+pab+')" value="'+pab+'" name="radioPublishName" class="control-info" '+radioPublish+' checked>'+
                                pob[pub]+
                            '</label>'+
                        '</div>');
                    }else{
                        $('#publishRadio').append('<div class="radio">'+
                            '<label>'+
                                '<input type="radio" onClick="lRadio('+pab+')" value="'+pab+'"  name="radioPublishName" class="control-info">'+
                                pob[pub]+
                            '</label>'+
                        '</div>');
                    }
                }

                /* opsi jika private form */
                if(res.publish == 2){
                    $('#linkTautanPrivate').css('display','block');
                }
                
                $('#linkRef').append('<input type="text" value="'+baseURL+'/content/nikanschsweish/'+thisEncyIDProfile+'" class="form-control"/>');

                /* end load setting publish */

                /* load masa aktif */
                if(res.masa_aktif == 1){
                    var checkeds = 'checked';    
                    $('.masaaktifField').css('display','block');
                }else{
                    var checkeds = '';
                }
                
                $('.daterange-single').val(res.end_date);
                
                $('#masaAktif').append('<div class=checkbox checkbox-switchery switchery-double  switchery-lg" >'+
                    '<label>'+
                        'Disabled &nbsp;&nbsp;<input type="checkbox" id="swhit" onclick="checkFluency()"  name="status_create" class="switchery-warning" '+checkeds+'>&nbsp;&nbsp; Enable'+
                    '</label>'+
                '</div>');
                /* end load masa aktif */

                /* load batas pengisian */
                var radioBatasIsi = 'checked="checked"';
                let rub           = 0;
                let rob           = ['Sekali saja  (1x pengisian)', 'Beberapa kali (tidak terbatas)'];
                for(rub in rob){
                    var rab = parseInt(rub) + 1; 
                    if(rab == res.batas_isi){
                        $('#radioBatasIsi').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+rab+'" name="radioBatasIsiName" class="control-info" '+radioBatasIsi+' checked>'+
                                rob[rub]+
                            '</label>'+
                        '</div>');
                    }else{
                        $('#radioBatasIsi').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+rab+'"  name="radioBatasIsiName" class="control-info">'+
                                rob[rub]+
                            '</label>'+
                        '</div>');
                    }
                }
                /* end load batas pengisian */

                /******** SETELAN QUIZ *********************/
                

                /* load tipe kuis */
                let radioTipeQuizTemp  = 'checked="checked"';
                let radioTipeQuizArr1  = [1,2,3];
                let radioTipeQuizArr2  = ['Ujian / Test', 'Try Out', 'Latihan'];

                for(eex in radioTipeQuizArr1){

                    if(radioTipeQuizArr1[eex] == res.tipe_kuiz){
                        $('#radioTipeQuizTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+radioTipeQuizArr1[eex]+'" name="radioTipeQuizTempName" class="control-info" '+radioTipeQuizTemp+' checked>'+
                                radioTipeQuizArr2[eex]+
                            '</label>'+
                        '</div>');
                    }else{
                        $('#radioTipeQuizTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+radioTipeQuizArr1[eex]+'" name="radioTipeQuizTempName" class="control-info">'+
                                radioTipeQuizArr2[eex]+
                            '</label>'+
                        '</div>');
                    }

                }

                /* load radio tipe bobot */
                let radioTipeBobotTemp  = 'checked="checked"';
                let radioTipeBobotArr1  = [1,2];
                let radioTipeBobotArr2  = ['General', 'Score'];

                for(eex in radioTipeBobotArr1){

                    if(radioTipeBobotArr1[eex] == res.tipe_bobot){
                        $('#radioTipeBobotTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+radioTipeBobotArr1[eex]+'" name="radioTipeBobotTempName" class="control-info" '+radioTipeBobotTemp+' checked>'+
                                radioTipeBobotArr2[eex]+
                            '</label>'+
                        '</div>');
                    }else{
                        $('#radioTipeBobotTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+radioTipeBobotArr1[eex]+'" name="radioTipeBobotTempName" class="control-info">'+
                                radioTipeBobotArr2[eex]+
                            '</label>'+
                        '</div>');
                    }

                }

                /* load radio status soal  */
                let radioStatusSoalTemp  = 'checked="checked"';
                let radioStatusSoalArr1  = [1,2];
                let radioStatusSoalArr2  = ['YA (Soal hanya menampilkan yang <code>aktif</code> saja)', 'Tidak (akan menampilkan <code>semua</code> soal aktif/tidak )'];

                for(eex in radioStatusSoalArr1){

                    if(radioStatusSoalArr1[eex] == res.status_soal){
                        $('#radioStatusSoalTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+radioStatusSoalArr1[eex]+'" name="radioStatusSoalTempName" class="control-info" '+radioStatusSoalTemp+' checked>'+
                                radioStatusSoalArr2[eex]+
                            '</label>'+
                        '</div>');
                    }else{
                        $('#radioStatusSoalTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+radioStatusSoalArr1[eex]+'" name="radioStatusSoalTempName" class="control-info">'+
                                radioStatusSoalArr2[eex]+
                            '</label>'+
                        '</div>');
                    }

                }


                /* load radio sort data */
                let radioSortingSoalTem   = 'checked="checked"';
                let radioSortingSoalArr1  = [1,2];
                let radioSortingSoalArr2  = ['Random (Sortir <code>acak</code>)', 'Manual (Sesuai keinginan/prosedur)'];

                for(eex in radioSortingSoalArr1){

                    if(radioSortingSoalArr1[eex] == res.sort_data){
                        $('#radioSortingSoalTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+radioSortingSoalArr1[eex]+'" name="radioSortingSoalTemName" class="control-info" '+radioSortingSoalTem+' checked>'+
                                radioSortingSoalArr2[eex]+
                            '</label>'+
                        '</div>');
                    }else{
                        $('#radioSortingSoalTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+radioSortingSoalArr1[eex]+'" name="radioSortingSoalTemName" class="control-info">'+
                                radioSortingSoalArr2[eex]+
                            '</label>'+
                        '</div>');
                    }

                }



                /* load radio view materi  */
                let radioViewMateriTemp  = 'checked="checked"';
                let radioViewMateriArr1  = [1,2];
                let radioViewMateriArr2  = ['YEAHHH...', 'No Way...'];

                for(eex in radioViewMateriArr1){

                    if(radioViewMateriArr1[eex] == res.materi_view){
                        $('#radioViewMateriTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+radioViewMateriArr1[eex]+'" name="radioViewMateriTempName" class="control-info" '+radioViewMateriTemp+' checked>'+
                                radioViewMateriArr2[eex]+
                            '</label>'+
                        '</div>');
                    }else{
                        $('#radioViewMateriTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+radioViewMateriArr1[eex]+'" name="radioViewMateriTempName" class="control-info">'+
                                radioViewMateriArr2[eex]+
                            '</label>'+
                        '</div>');
                    }

                }
                



                /******** END SETELAN QUIZ *****************/

                /* load limit jumlah pertanyaan perhalaman */

                if(parseInt(res.limitx) > 0 && parseInt(res.limitx) <= 5){
                    $('.sampeu').removeClass('ui-slider-info ui-slider-warning ui-slider-danger').addClass('ui-slider-success');
                }else if(parseInt(res.limitx) > 5 && parseInt(res.limitx) <= 10){
                    $('.sampeu').removeClass('ui-slider-success ui-slider-warning ui-slider-danger').addClass('ui-slider-info');
                }else if(parseInt(res.limitx)> 10 && parseInt(res.limitx)<= 15){
                    $('.sampeu').removeClass('ui-slider-success  ui-slider-info ui-slider-danger').addClass('ui-slider-warning');
                }else{
                    $('.sampeu').removeClass('ui-slider-success  ui-slider-info ui-slider-warning').addClass('ui-slider-danger');
                }

                $('#limitx').val(res.limitx);
                $(".ui-slider-labels").slider({
                    max: 20,
                    min:0,
                    range: 'min',
                    value: res.limitx,
                    animate: true,
                    slide: function( event, ui ) {

                        if(ui.value > 0 && ui.value <= 5){
                            $('.sampeu').removeClass('ui-slider-info ui-slider-warning ui-slider-danger').addClass('ui-slider-success');
                        }else if(ui.value > 5 && ui.value <= 10){
                            $('.sampeu').removeClass('ui-slider-success ui-slider-warning ui-slider-danger').addClass('ui-slider-info');
                        }else if(ui.value > 10 && ui.value <= 15){
                            $('.sampeu').removeClass('ui-slider-success  ui-slider-info ui-slider-danger').addClass('ui-slider-warning');
                        }else{
                            $('.sampeu').removeClass('ui-slider-success  ui-slider-info ui-slider-warning').addClass('ui-slider-danger');
                        }
                        $('#limitx').val(ui.value);
                    }
                });
                $(".ui-slider-labels").slider("pips" , {
                    rest: "label"
                });
                /* end load limit jumlah pertanyaan perhalaman */

                /* load privasi */
                var radioPrivasi = 'checked="checked"';
                let cub          = 0;
                let cob          = ['Public', 'Private'];
                for(cub in cob){
                    var cab = parseInt(cub) + 1; 
                    if(cab == res.privasix){
                        $('#privasiRadio').append('<div class="radio">'+
                            '<label>'+
                                '<input type="radio"  value="'+cab+'" name="radioPrivasiName" class="control-info" '+radioPrivasi+' checked>'+
                                cob[cub]+
                            '</label>'+
                        '</div>');
                    }else{
                        $('#privasiRadio').append('<div class="radio">'+
                            '<label>'+
                                '<input type="radio" value="'+cab+'"  name="radioPrivasiName" class="control-info">'+
                                cob[cub]+
                            '</label>'+
                        '</div>');
                    }
                }
                /* end load privasi */

                /* load view header */
                var radioVieHeade = 'checked="checked"';
                let dub           = 0;
                let dob           = ['Show', 'Hide'];
                for(dub in dob){
                    var dab = parseInt(dub) + 1; 
                    if(dab == res.header_view){
                        $('#radioBatasViewHeaderTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+dab+'" name="radioBatasViewHeader" class="control-info" '+radioVieHeade+' checked>'+
                                dob[dub]+
                            '</label>'+
                        '</div>');
                    }else{
                        $('#radioBatasViewHeaderTemp').append('<div class="radio-inline">'+
                            '<label>'+
                                '<input type="radio" value="'+dab+'"  name="radioBatasViewHeader" class="control-info">'+
                                dob[dub]+
                            '</label>'+
                        '</div>');
                    }
                }
                /* end load view header */

                /* load warna header */
                $('#headerColor').append('<input type="text" name="name_name[]" id="headerColorValue" class="form-control colorpicker-basic" data-cancel-text="Cancel" data-choose-text="Ok"  data-preferred-format="rgb"  value="'+res.header_color+'">');
                /* end load warna header */

                /* load warna latar belakang */
                $('#backColor').append('<input type="text" name="name_name[]" id="backColorValue" class="form-control colorpicker-basic" data-cancel-text="Cancel" data-choose-text="Ok"  data-preferred-format="rgb"  value="'+res.back_color+'">');
                /* end load warna latar belakang  */

                /* load huruf judul */
                if(res.judul_view == 1){
                    var checkedJ = 'checked';  
                }else{
                    var checkedJ = '';
                }
                $('#viewJudulTemp').append('<div class=checkbox checkbox-switchery switchery-double  switchery-lg" style="margin-left: -30px;">'+
                    '<label>'+
                        'Hide &nbsp;&nbsp;<input type="checkbox" id="view_judul"   name="view_judul" class="switchery-warning2" '+checkedJ+'>&nbsp;&nbsp; Show'+
                    '</label>'+
                '</div>');
                /* end load huruf judul */

                /* load huruf deskripsi */
                if(res.deskripsi_view == 1){
                    var checkedD = 'checked';  
                }else{
                    var checkedD = '';
                }
                $('#viewDeskripsiTemp').append('<div class=checkbox checkbox-switchery switchery-double  switchery-lg" style="margin-left: -30px;">'+
                    '<label>'+
                        'Hide &nbsp;&nbsp;<input type="checkbox" id="view_deskripsi"   name="view_deskripsi" class="switchery" '+checkedD+'>&nbsp;&nbsp; Show'+
                    '</label>'+
                '</div>');
                /* end load huruf deskripsi */

                /* load ukuran layout */
                var radioLayout  = 'checked="checked"';
                let eub          = 0;
                let eob          = ['Medium', 'Full Size'];
                for(eub in eob){
                    var eab = parseInt(eub) + 1; 
                    if(eab == res.layout_size){
                        $('#radioLayoutTemp').append('<div class="radio">'+
                            '<label>'+
                                '<input type="radio"  value="'+eab+'" name="radioradioLayout" class="control-info" '+radioLayout+' checked>'+
                                eob[eub]+
                            '</label>'+
                        '</div>');
                    }else{
                        $('#radioLayoutTemp').append('<div class="radio">'+
                            '<label>'+
                                '<input type="radio" value="'+eab+'"  name="radioradioLayout" class="control-info">'+
                                eob[eub]+
                            '</label>'+
                        '</div>');
                    }
                }
                /* end load ukuran layout  */

                $(".colorpicker-basic").spectrum();
                

                $(function() {
                    switchery();
                    controlRadio();
                });

                
                setIFrameSize();

            }else{
                // loaduser();
                swal({
                    title: "Galat",
                    text:  result.info+" Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
                    type: "error"
                });
            }
        }
    });
}

function lRadio(intp){
    if(intp == 2){
        $('#linkTautanPrivate').css('display','block');
    }else{
        $('#linkTautanPrivate').css('display','none');
    }
}

function checkFluency(){
    if ($('#swhit').is(":checked")){
        $('.masaaktifField').css('display','block');
    }else{
        $('.masaaktifField').css('display','none');
    }
}


/* simpan pengaturan form */
$('#simpanPengaturan').on('click', function(){
    
    loaderPage(true);

    var juduls   = $('#valueJudul').val();
    if(juduls ==''){
        loaderPage(false);
        swal({
            title: "Ops!",
            text:" Judul tidak boleh kosong. Silahkan coba kembali :)",
            confirmButtonColor: "#2196F3",
            type: "warning"
        });
        return false;
    }

    var deskripsid   = $('#valueDeskripsi').val();
    if(deskripsid ==''){
        loaderPage(false);
        swal({
            title: "Ops!",
            text:" Deskripsi tidak boleh kosong. Silahkan coba kembali :)",
            confirmButtonColor: "#2196F3",
            type: "warning"
        });
        return false;
    }

    let publish = $("input[name='radioPublishName']:checked").val();

    if ($('#swhit').is(":checked")){
        var masaAktif = 1;
    }else{
        var masaAktif = 2;
    }

    var dateAktif   = $('.daterange-single').val();

    let batasIsi    = $("input[name='radioBatasIsiName']:checked").val();

    let bLimitx     = $('#limitx').val();

    if(bLimitx ==''){
        loaderPage(false);
        swal({
            title: "Ops!",
            text: "Batas halaman tidak boleh kosong. Silahkan coba kembali :)",
            confirmButtonColor: "#2196F3",
            type: "warning"
        });
        return false;
    }

    let privasi     = $("input[name='radioPrivasiName']:checked").val();

    let viewheader  = $("input[name='radioBatasViewHeader']:checked").val();

    var warnaHead   = $('#headerColorValue').val();

    var warnaBack   = $('#backColorValue').val();

    if ($('#view_judul').is(":checked")){
        var view_judul = 1;
    }else{
        var view_judul = 2;
    }

    if ($('#view_deskripsi').is(":checked")){
        var view_deskripsi = 1;
    }else{
        var view_deskripsi = 2;
    }

    let layoutSize = $("input[name='radioradioLayout']:checked").val();


    let tipe_bobot     = $("input[name='radioTipeBobotTempName']:checked").val();
    let status_soal    = $("input[name='radioStatusSoalTempName']:checked").val();
    let sort_data      = $("input[name='radioSortingSoalTemName']:checked").val();
    let materi_view    = $("input[name='radioViewMateriTempName']:checked").val();
    let tipe_kuiz      = $("input[name='radioTipeQuizTempName']:checked").val();
    
    let isObject            = {};
    isObject.ipoly          = thisIdProfile;
    isObject.juduls         = juduls;
    isObject.deskripsid     = deskripsid;
    isObject.publish        = publish;
    isObject.masaAktif      = masaAktif;
    isObject.dateAktif      = dateAktif;
    isObject.batasIsi       = batasIsi;
    isObject.bLimitx        = bLimitx;
    isObject.privasi        = privasi;
    isObject.viewheader     = viewheader;
    isObject.warnaHead      = warnaHead;
    isObject.warnaBack      = warnaBack;
    isObject.view_judul     = view_judul;
    isObject.view_deskripsi = view_deskripsi;
    isObject.layoutSize     = layoutSize;
    isObject.tipe_bobot     = tipe_bobot;
    isObject.status_soal    = status_soal;
    isObject.sort_data      = sort_data;
    isObject.materi_view    = materi_view;
    isObject.tipe_kuiz      = tipe_kuiz;
    
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseURL + '/jsondata/updateprofilesetting',
        data: {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        success: function(result){
            
            loaderPage(false);

            if(result.code == 0){                   
                swal({
                    title: "Sukses",
                    text: "Pengaturan berhasil diterapkan. ",
                    confirmButtonColor: "#66BB6A",
                    type: "success"
                });
                // loaduser();
            }else{
                // loaduser();
                swal({
                    title: "Galat",
                    text:  result.info+" Silahkan coba kembali :)",
                    confirmButtonColor: "#2196F3",
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

   
});    




function loadElements(clicksc){
    
    loaderPage(false);
    
    let isObject     = {}; 
    isObject.ipoly   = thisIdProfile;
    isObject.ilvl    = $('.seleLevel').val();

    $("#data-list2 thead tr:eq(1)").remove();

    var dtpr = $('#data-list2').DataTable({
        serverSide	: false,
        destroy		: true,
        bFilter		: true,
        responsive	: true,
        searching   : true,
        pagingType  : 'full',
        lengthMenu  : [[10, 25, 50, -1], [10, 25, 50, "All"]],
        pageLength  : 50,
        ajax		: {
            url: baseURL + '/jsondata/loadeleque',
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
                    return response.data;
                }else{
                    return response;
                }
               
            }
        },	
        columns: [
            { 'data': 'sortingx', 'sClass':'','sWidth':'10px'},
            { 'data': 'x_status', 'sClass':'table-inbox-checkbox rowlink-skip','sWidth':'10px'},
            { 'data': 'questions', 'sClass':''},
            { 'data': 'object_string', 'sClass':''},
            { 'data': 'object_value', 'sClass':''},
            { 'data': 'is_opt', 'sClass':'text-center'},
            { 'data': 'x_difficult', 'sClass':'text-center'},
        ],
        buttons: {            
            buttons: [
                {
                    extend: 'colvis',
                    className: 'btn btn-default',
                    text: '<i class="icon-three-bars"></i>  Visibility',
                },
                {
                    title: 'Simpan',
                    className: 'btn bg-slate',
                    text: '<i class=" icon-database-check position-left"></i> Simpan',
                    action: function ( e, dt, node, config ) {
                        saveRulesSetup();
                    }
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
                // checkboxes: {
                //     selectRow: true
                // },
                render: function (data, type, row){
                    let $rowData = '';

                    if(row.x_status == 1){ // is active
                        $rowData += `<input type="checkbox" class="styled" value="`+row.paramid+`" name="setActiveSoal" checked=checked> ` ;
                    }else{
                        $rowData += `<input type="checkbox" class="styled" value="`+row.paramid+`" name="setActiveSoal"> ` ;
                    }
                     
                    return $rowData;
                },
                width: '20px',
                targets: 1,
                visible: true
            },
            {
                targets: 2,
                visible: true
            },
            {
                targets: 3,
                visible: true
            },
            {
                targets: 4,
                visible: true
            },
            {
                
				render: function (data, type, row){

                    let selOpt   = '';

                    let splitOpt = row.is_opt.split(",");
                    let splitVal = row.is_values.split(",");

                    if(!row.x_answer){
                        selOpt += '<option value="" disabled selected hidden>Choose answer...</option>';
                    }
                    
                    if(splitOpt.length > 0){
                        for(so in splitOpt){     
                            if(row.x_answer == splitVal[so].trim()){
                                selOpt += '<option value="'+splitVal[so].trim()+'~'+row.paramid+' " selected="selected">'+splitOpt[so]+'</option>';
                            }else{                   
                                selOpt += '<option value="'+splitVal[so].trim()+'~'+row.paramid+'">'+splitOpt[so]+'</option>';
                            }  
                        }
                    }

                    // let toggle = `<select id="sel_answer_val`+row.paramid+`" class="form-control"  name="sel_answer_val_name" /* onchange="getRulesOnSelect('sel_answer_val',`+row.paramid+`,'x_answer')" */ style="background-color:#fff0;" placeholder="jawaban?">
                    let toggle = `<select id="sel_answer_val`+row.paramid+`" class="form-control"  name="sel_answer_val_name"  style="background-color:#fff0;" placeholder="jawaban?">
                        `+selOpt+`
                    </select>`;

					return toggle;
				},
				targets: 5,
				visible: true
            },
            {
				render: function (data, type, row){
                   
                    let selDif   = '';

                    if(!row.x_difficult){
                        selDif += '<option value="" disabled selected hidden>Choose level...</option>';
                    }

                    let dif2    = isPointLevel[0];
                    
                    for(dif1 in dif2){
                        if(dif2[dif1].level_code == row.x_difficult){

                            selDif += '<option value="'+dif2[dif1].level_code+'~'+row.paramid+'" selected>'+dif2[dif1].level_name+'</option>';

                        }else{                            
                            selDif += '<option value="'+dif2[dif1].level_code+'~'+row.paramid+'" >'+dif2[dif1].level_name+'</option>';
                        }
                    }

                    let toggle = `<select class="form-control" name="nameLevelValue" style="background-color:#fff0;" placeholder="jawaban?">
                        `+selDif+`
                    </select>`;

					return toggle;
				},
				targets: 6,
				visible: true
			},
        ],
        // select: {
        //     style:    'os',
        //     selector: 'td:first-child'
        // },
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){ 
            // var index = iDisplayIndexFull + 1; 
            // $('td:eq(0)', nRow).html('#'+index); 
            // return  index;
        },
        drawCallback: function (settings) {
            // console.log(settings.json);
            
            var api  = this.api();
            var rows = api.rows({page:'current'}).nodes();
            var last = null;

            // console.log(rows)
            // Reverse last 3 dropdowns orientation
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');

            
        },
        fnInitComplete: function (oSettings, json) {
            loaderPage(false);
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
            
            /* buat nampilin individual search kari dibuka 
            ---------------------------------------------------*/
            if(clicksc == 1){
                $("#data-list2 thead").append($("#data-list2 thead tr:first ").clone());
                $('#data-list2 thead tr:eq(1) th').each( function (index) {
                    var that = this;                    
                    loadIndividualSearch(this, index, dtpr);                    
                });
    
                /* redraw kolom na gans */
                $('#data-list2').on( 'column-visibility.dt', function ( e, settings, column, state ) {
                    $("#data-list2 thead tr:eq(1)").remove();
    
                    $("#data-list2 thead").append($("#data-list2 thead tr:first ").clone());
                    $('#data-list2 thead tr:eq(1) th').each( function (index) {
                        var that = this;
                        loadIndividualSearch(this, index, dtpr);
                        $('#data-list2 thead tr:eq(1) th').removeClass('table-inbox-checkbox rowlink-skip sorting');
                    });
                    // console.log(
                    //     'Column '+ column +' has changed to '+ (state ? 'visible' : 'hidden')
                    // );
                });
    
                /* buat nampilin individual search kari dibuka 
                ---------------------------------------------------*/
            }

        }
    });

}

function loadIndividualSearch(that, idx, dtpr){
    
    var th          = $("#data-list2 thead tr:eq(1) th:eq(" + idx + ")").eq($(this).index());
    var headerName  = th.text();
    
    var indexTarget = 0;
    
    /* set target index */
    if(headerName == '#'){
        indexTarget = 0;
    }else if(headerName == ' '){
        indexTarget = 2;
    }else if(headerName == 'Question label'){
        indexTarget = 2;
    }else if(headerName == 'Text option'){
        indexTarget = 3;
    }else if(headerName == 'Value option'){
        indexTarget = 4;
    }else if(headerName == 'Correct answer'){
        indexTarget = 5;
    }else if(headerName == 'Level'){
        indexTarget = 6;
    }

    // var headerName =  dtpr.column(idx).header().innerHTML;
    // console.log(idx );

    var title      = $('#data-list2 tfoot th').eq( $(this).index() ).text();
    var table      = $('#data-list2').DataTable();

    if(headerName != '#No' && headerName != ' ' && headerName != 'Level' && headerName != 'Correct answer'){

        $(that).html('<input type="text" class="form-control input-sm" placeholder="Search...'+title+'" style="border: 1px solid #9E9E9E;border-top: 1px solid #9E9E9E;"/>');
        
        var searchControl = $("#data-list2 thead tr:eq(1) th:eq(" + idx + ") input");

        table.columns(indexTarget).every( function () {
           
            var that = this;
            
            searchControl.on('keyup change', function () {

                that.search(this.value).draw();

            });

        });

    }else if(headerName == 'Level' || headerName == 'Segment' || headerName == 'Regional'){
        loadRuleSelectTable(that, idx, dtpr, headerName, indexTarget);
    }else{
        $(that).html('');
    }

    $('#data-list2 thead tr:eq(1) th').removeClass('table-inbox-checkbox rowlink-skip sorting');
}

function loadRuleSelectTable(that, idx, dtpr, jenis, indexTarget){
    // console.log(dtpr);
    var empTable = $('#data-list2').DataTable();
    var officeDropDown = $('<select/>').addClass('form-control seleLevel');
    officeDropDown.append($('<option/>').attr('value','').text('Filter  '+jenis+''));
    var office = [];
    
    if(jenis  == 'Level'){

        let dif2    = isPointLevel[0];
        
        officeDropDown.append($('<option/>').attr('value', 0).text('ALL LEVEL'));
        for(ar in dif2){
            officeDropDown.append($('<option/>').attr('value', dif2[ar].level_code).text(dif2[ar].level_name));
        }

        
    }else if(jenis  == 'Kemitraan'){
        officeDropDown.append($('<option/>').attr('value', 'Infrastruktur').text('Infrastruktur'));
        officeDropDown.append($('<option/>').attr('value', 'Marketing').text('Marketing'));
        officeDropDown.append($('<option/>').attr('value', 'Non Partnership').text('Non Partnership'));
        officeDropDown.append($('<option/>').attr('value', 'Belum Teridentifikasi').text('Belum Teridentifikasi'));

    }else{
        // var i;
        // for (i = 1; i <= 7; i++) {
        //     officeDropDown.append($('<option/>').attr('value', 'TREG-'+i).text('TREG-'+i));
        // }
    }

    $(that).replaceWith('<th>' + $(officeDropDown).prop('outerHTML') + '</th>');
    // var searchControl = $("#data-list2 thead tr:eq(1) th:eq(" + idx + ") select");
    // searchControl.on('change', function () {
    //     console.log($(this).val());
    //     // console.log(searchControl.val()+'$');
    //     // dtpr.column(indexTarget).search('^' + $(this).val() + '$', true, false).draw();

    //     // dtpr.column(indexTarget).search(searchControl.val() == "" ? "" :  '^' + searchControl.val()+'$',true,false).draw();
    // }); 


    
    $('.seleLevel').on('change', function(){
        loadElements(2);
    });
}

function saveRulesSetup(){

    let isObject        = {};
    isObject.actSoalArr = [];
    isObject.ansSoalArr = [];
    isObject.levSoalArr = [];

    $('input:checkbox[name="setActiveSoal"]').each(function() {

        loaderPage(true);

        if ($(this).is(':checked')) {
            isObject.actSoalArr.push({ 
                'paramid'  : this.value, 
                'status'   : 1,
            });             
        }else{
            isObject.actSoalArr.push({ 
                'paramid'  : this.value, 
                'status'   : 2,
            });
        }
       
    });

    $('select[name="sel_answer_val_name"]').each(function() {
        if(this.value != ''){
            let spliDataAer = this.value.split('~');
            isObject.ansSoalArr.push({ 
                'has_parent'    : spliDataAer[0], 
                'paramid'       : spliDataAer[1],
            });
        }       
    });

    $('select[name="nameLevelValue"]').each(function() {
        if(this.value != ''){
            let spliDataAer = this.value.split('~');
            isObject.levSoalArr.push({ 
                'levelcode'  : spliDataAer[0], 
                'paramid'    : spliDataAer[1],
            });
        }       
    });

    $.ajax({
        type        : 'POST',
        dataType    : 'json',
        data        : {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        url         : baseURL + '/jsondata/saverulesetup',
        success: function(response){

            if(response.code == CODE_SUCCESS){    

                swal({
                    title: "Sukses",
                    text: "Rule berhasil diupdate ",
                    confirmButtonColor: "#66BB6A",
                    type: "success"
                }, function(){ 
                    location.reload();
                });
                
            }else{
                swal({
                    title: "Perhatian!",
                    text:  response['info'],
                    confirmButtonColor: "#66BB6A",
                    type: "error"
                });
            }

            loaderPage(false);
            
        },
        error: function(xhr) {
            loaderPage(false);
            if(xhr.status != 200){
                bootbox.dialog({
                    message: "<span class='bigger-110'>"+xhr.status+"-"+xhr.statusText+"<br>Silahkan coba kembali</span>",
                    buttons:
                    {
                        "OK" :
                         {
                            "label" : "<i class='icon-ok'></i> OK ",
                            "className" : "btn-sm btn-danger",
                            callback : function(){
                                       
                            }
                        }
                    }
                });
                
            }
        }
    });

}

function selectall(source) {
    checkboxes = document.getElementsByName('setActiveSoal');
    for(var i=0, n=checkboxes.length;i<n;i++) {
      checkboxes[i].checked = source.checked;
    }
}


function getRulesOnSelect(contentID, paramID, columnDB){

    let getVal = $('#'+contentID+''+paramID).val();

    let isObject = {};
    isObject.paramid = paramID;
    isObject.isvalue = getVal;
    isObject.columns = columnDB;

    $.ajax({
        type        : 'POST',
        dataType    : 'json',
        data        : {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        url         : baseURL + '/jsondata/saverulesopt',
        success: function(response){

            if(response.code == CODE_SUCCESS){ 
                new PNotify({
                    title: 'Notice',
                    text: 'Success update.',
                    icon: 'icon-warning22'
                });
                
            }else{
                new PNotify({
                    title: 'Notice',
                    text: 'Failed update.',
                    icon: 'icon-warning22'
                });

            }

            
        },
        error: function(xhr) {

            if(xhr.status != 200){
                bootbox.dialog({
                    message: "<span class='bigger-110'>"+xhr.status+"-"+xhr.statusText+"<br>Silahkan coba kembali</span>",
                    buttons:
                    {
                        "OK" :
                         {
                            "label" : "<i class='icon-ok'></i> OK ",
                            "className" : "btn-sm btn-danger",
                            callback : function(){
                                       
                            }
                        }
                    }
                });
                
            }
        }
    });
    

}

/* sorting pertanyaan */
$('#bottom-justified-sort').on('click', function(){
    loadActorLevel();
    setIFrameSize();
});

// sort functionality
$("#sortable-list-basic").sortable();
$("#sortable-list-basic").disableSelection();

function loadActorLevel(){

    loaderPage(true);

    $('ul#sortable-list-basic').empty();
    $('.numSorting').empty();

    let isObject   = {};
    isObject.ipoly = thisIdProfile;

    $.ajax({
        type        : 'POST',
        dataType    : 'json',
        data        : {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        url         : baseURL + '/jsondata/loadquelevel',
        success: function(response){

            if(response.code == CODE_SUCCESS){         

                let result  = cryptoDecrypt(PHRASE, response.data);
               
                let res     = result.data;
                console.log(res);
                let IsnumS  = 1;
                for(x in res){
                    
                    let numv  = IsnumS++;
                    
                    if(res[x].x_status == 2){// jika status nya tidak aktif

                        $('.numSorting').append(`<div class="col-sm-12" style="padding: 4px;text-align: center;background: #c3c3c3;font-size: 15px;    margin-top: 10px;">#`+numv+`</div>`);
                        
                        $('ul#sortable-list-basic').append('<li class="ui-sortable-handle" value="'+res[x]['paramid']+'" style="font-size: 17px;background: #c3c3c3;">'+res[x]['object_label']+'</li>');   
                         
                    }else{

                        $('.numSorting').append(`<div class="col-sm-12" style="padding: 4px;text-align: center; background: #fcfcfc;font-size: 15px;    margin-top: 10px;">#`+numv+`</div>`);
                        
                        $('ul#sortable-list-basic').append('<li class="ui-sortable-handle" value="'+res[x]['paramid']+'" style="font-size: 17px;">'+res[x]['object_label']+'</li>');      
    
                    }
                }
                
            }else{
                swal({
                    title: "Perhatian!",
                    text:  response['info'],
                    confirmButtonColor: "#66BB6A",
                    type: "error"
                });
            }

            loaderPage(false);
            
        },
        error: function(xhr) {
            loaderPage(false);
            if(xhr.status != 200){
                bootbox.dialog({
                    message: "<span class='bigger-110'>"+xhr.status+"-"+xhr.statusText+"<br>Silahkan coba kembali</span>",
                    buttons:
                    {
                        "OK" :
                         {
                            "label" : "<i class='icon-ok'></i> OK ",
                            "className" : "btn-sm btn-danger",
                            callback : function(){
                                       
                            }
                        }
                    }
                });
                
            }
        }
    });
}

/* save sorting category */
window.getValues = function() {
    loaderPage(true);
    
    var ecahArr = [];
    var i       = 0;

    $.each($('#sortable-list-basic').find('li'), function() {
        ecahArr.push({ 'position': i, 'idcategory': $(this).val() }); 
        i++;   
    });
    
    let isObject     = {};
    isObject.idata   = ecahArr;

    $.ajax({
        type        : 'POST',
        dataType    : 'json',
        data        : {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        url         : baseURL + '/jsondata/savesortinglevel',
        success: function(result){

            if(result.code == CODE_SUCCESS){

                swal({
                    title: "Success!",
                    text:  'Sorting data berhasil',
                    confirmButtonColor: "#66BB6A",
                    type: "success"
                }, function(){ 
                    location.reload();
                });

            }else{

                swal({
                    title: "Perhatian!",
                    text:  result['info'],
                    confirmButtonColor: "#66BB6A",
                    type: "error"
                });

            }

            loaderPage(false);
        },
        error: function(xhr) {
        loaderPage(false);
        //alert(xhr.status+'-'+xhr.statusText);
            if(xhr.status != 200){
                bootbox.dialog({
                    message: "<span class='bigger-110'>"+xhr.status+"-"+xhr.statusText+"<br>Silahkan coba kembali</span>",
                    buttons:
                    {
                        "OK" :
                         {
                            "label" : "<i class='icon-ok'></i> OK ",
                            "className" : "btn-sm btn-danger",
                            callback : function(){
                                       
                            }
                        }
                    }
                });
                
            }
        }
    });

}

var isPointLevel = [];
function loadLevelRules(){

    loaderPage(true);

    $('.thisLevelBo').empty();
    $('.thisLevelBo2').empty();

    let isObject   = {};
    isObject.ipoly = thisIdProfile;

    $.ajax({
        type        : 'POST',
        dataType    : 'json',
        async       : false,
        data        : {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        url         : baseURL + '/jsondata/loadlevel',
        success: function(response){

            if(response.code == CODE_SUCCESS){         

                let result   = cryptoDecrypt(PHRASE, response.data);
               
                let res      = result.data;
                
                isPointLevel.push(res);

                for(x in res){
                    $('.thisLevelBo').append(`
                        
                        <div class="input-group input-group-xs" style="width: -webkit-fill-available;">
                            <span class="input-group-addon" style="width: 11%;text-align: left;">`+res[x].level_name+`:</span>
                            <input type="number" name="nameBobotLevel" id="jparam~`+res[x].levelid+`" value="`+res[x].level_value+`" class="form-control" placeholder="Masukan nilai bobot..." style="background-color: aliceblue;border-bottom: 1px solid #dddddd;">
                        </div>
                    `);
                }

                $('.thisLevelBo2').append(`                        
                    <div class="input-group input-group-xs" style="width: -webkit-fill-available;">
                        <span class="input-group-addon" style="width: 11%;text-align: left;">DEFAULT:</span>
                        <input type="number" name="nameBobotLevel2" value="`+isSetting[0].bobot_value+`" class="form-control" placeholder="Masukan nilai bobot..." style="background-color: aliceblue;border-bottom: 1px solid #dddddd;">
                    </div>
                `);
                
            }else{
                swal({
                    title: "Perhatian!",
                    text:  response['info'],
                    confirmButtonColor: "#66BB6A",
                    type: "error"
                });
            }

            loaderPage(false);
            
        },
        error: function(xhr) {
            loaderPage(false);
            if(xhr.status != 200){
                bootbox.dialog({
                    message: "<span class='bigger-110'>"+xhr.status+"-"+xhr.statusText+"<br>Silahkan coba kembali</span>",
                    buttons:
                    {
                        "OK" :
                         {
                            "label" : "<i class='icon-ok'></i> OK ",
                            "className" : "btn-sm btn-danger",
                            callback : function(){
                                       
                            }
                        }
                    }
                });
                
            }
        }
    });
}

$('#saveBobots').on('click', function(){
    savePoinBobot();
})


function savePoinBobot(){

    loaderPage(true);

    let isObject          = {}
    isObject.actPoinLevel = [];

    $('input[name="nameBobotLevel"]').each(function() {

        let getLvId = this.id.split('~');

        isObject.actPoinLevel.push({
            'level_id'  : getLvId[1],
            'this_val'  : this.value,
        });
             
    });

    isObject.ProfileID  = thisIdProfile;
    isObject.DefaultVal = $('input[name="nameBobotLevel2"]').val();

    $.ajax({
        type        : 'POST',
        dataType    : 'json',
        data        : {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        url         : baseURL + '/jsondata/savepointbobots',
        success: function(response){

            if(response.code == CODE_SUCCESS){         

                swal({
                    title: "Sukses",
                    text: "Poin bobot berhasil diterapkan. ",
                    confirmButtonColor: "#66BB6A",
                    type: "success"
                });
                
            }else{
                swal({
                    title: "Perhatian!",
                    text:  response['info'],
                    confirmButtonColor: "#66BB6A",
                    type: "error"
                });
            }

            loaderPage(false);
            
        },
        error: function(xhr) {
            loaderPage(false);
            if(xhr.status != 200){
                bootbox.dialog({
                    message: "<span class='bigger-110'>"+xhr.status+"-"+xhr.statusText+"<br>Silahkan coba kembali</span>",
                    buttons:
                    {
                        "OK" :
                         {
                            "label" : "<i class='icon-ok'></i> OK ",
                            "className" : "btn-sm btn-danger",
                            callback : function(){
                                       
                            }
                        }
                    }
                });
                
            }
        }
    });
}


function loadMappingMateri(){

    let isObject   = {};
    isObject.ipoly = thisIdProfile;

    var dtpr = $('#data-list4').DataTable({
        serverSide	: false,
        destroy		: true,
        bFilter		: true,
        responsive	: true,
        searching   : true,
        pagingType  : 'full',
        lengthMenu  : [[1, 25, 50, -1], [1, 25, 50, "All"]],
        pageLength  : 25,
        ajax		: {
            url: baseURL + '/jsondata/loadmappingmateri',
            type: 'POST',
            data : function (d) {         
                return $.extend( {}, d, {
                    iparam	    : cryptoEncrypt(PHRASE, isObject),
                });
            },
            dataSrc: function(response) {
                
                if(response.code == CODE_SUCCESS){
                    response = cryptoDecrypt(PHRASE, response.data);

                    return response.data;
                }else{
                    return response;
                }
               
            }
        },	
        columns: [
            { 'data': 'idmateri', 'sClass':'','sWidth':'10px'},
            { 'data': 'idmateri', 'sClass':'','sWidth':'10px'},
            { 'data': 'titles', 'sClass':''},
            { 'data': 'file_name', 'sClass':''},
            { 'data': 'create_date', 'sClass':''},
            { 'data': 'idmateri', 'sClass':'','sWidth':'10px'},
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

                    var $rowData = '<div class="thumb img-rounded img-preview showAtt2" style="width: 70px;">'+
                        '<img src="/distro/assets/images/placeholder.jpg" alt="" class="img-rounded img-preview showAtt2">'+
                        '<div class="caption-overflow">'+
                            '<span>'+
                                '<a  class="btn showAtt bg-success-400 btn-icon btn-lg" style="font-variant: all-petite-caps;background-color: #0e0e0e33 !important;border-color: #25252591 !important;">'+
                                    '<i class="'+isIcon+'"></i> '
                                '</a>'+
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
                    var $rowData = `<span class="btn btn-xs btn-default removeIsmateri"><i class="icon-cross3"></i> Remove</span>`;

                    return $rowData;
                },
                visible: true,
                targets: 5,
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

            this.$('.showAtt2').on('click', function(){

                var tr      = $(this).parents('tr')
                var aData   = dtpr.row( tr ).data();

                loadAttachments(aData);
                
            });

            this.$('.removeIsmateri').on('click', function(){

                var tr      = $(this).parents('tr')
                var aData   = dtpr.row( tr ).data();

                saveActiveMateri(aData, 2);

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

            

            $('#data-list4 input').bind('keyup', function (e) {
                return this.value;
            });

            setIFrameSize();

        }
    });

}

function loadMateri(){
    
    let isObject     = {}; 
    isObject.ipoly   = 1;

    var dtpr = $('#data-list3').DataTable({
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
            { 'data': 'idmateri', 'sClass':'','sWidth':'10px'},
            { 'data': 'titles', 'sClass':''},
            { 'data': 'file_name', 'sClass':''},
            { 'data': 'create_date', 'sClass':''},
            { 'data': 'idmateri', 'sClass':'','sWidth':'10px'},
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
                                '<a  class="btn showAtt bg-success-400 btn-icon btn-lg" style="font-variant: all-petite-caps;background-color: #0e0e0e33 !important;border-color: #25252591 !important;">'+
                                    '<i class="'+isIcon+'"></i> '
                                '</a>'+
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
                    var $rowData = `<span class="btn btn-xs btn-default mappingIsMateri"><i class=" icon-enter"></i> Mapping</span>`;

                    return $rowData;
                },
                visible: true,
                targets: 5,
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

            this.$('.mappingIsMateri').on('click', function(){

                var tr      = $(this).parents('tr')
                var aData   = dtpr.row( tr ).data();

                saveActiveMateri(aData, 1);

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

            

            $('#data-list3 input').bind('keyup', function (e) {
                return this.value;
            });

            setIFrameSize();

        }
    });

}

const player = new Plyr('#player', {
    // controls: ['play-large', 'play', 'progress', 'current-time', 'captions', 'fullscreen', 'settings'],
    title: 'Example Title',
});


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

/* save active materi */
function saveActiveMateri(aData, sStatus){

    loaderPage(true);

    /* sStatus 
        jika sStatus == 1 (dia insert)
        jika sStatus == 2 (dia remove)
    */ 

    let isObject       = {};
    isObject.idmateri  = aData.idmateri;
    isObject.profileid = thisIdProfile;
    isObject.istatus   = sStatus;

    $.ajax({
        type        : 'POST',
        dataType    : 'json',
        data        : {
            iparam	    : cryptoEncrypt(PHRASE, isObject),
        },
        url         : baseURL + '/jsondata/savemapmateri',
        success: function(response){

            if(response.code == CODE_SUCCESS){    

                swal({
                    title: "Sukses",
                    text: "Materi berhasil diupdate. ",
                    confirmButtonColor: "#66BB6A",
                    type: "success"
                }, function(){ 

                    if(sStatus == 2){
                        loadMappingMateri();
                    }
                    
                });
                
            }else{
                swal({
                    title: "Perhatian!",
                    text:  response['info'],
                    confirmButtonColor: "#66BB6A",
                    type: "error"
                });
            }

            loaderPage(false);
            
        },
        error: function(xhr) {
            loaderPage(false);
            if(xhr.status != 200){
                bootbox.dialog({
                    message: "<span class='bigger-110'>"+xhr.status+"-"+xhr.statusText+"<br>Silahkan coba kembali</span>",
                    buttons:
                    {
                        "OK" :
                         {
                            "label" : "<i class='icon-ok'></i> OK ",
                            "className" : "btn-sm btn-danger",
                            callback : function(){
                                       
                            }
                        }
                    }
                });
                
            }
        }
    });
}
