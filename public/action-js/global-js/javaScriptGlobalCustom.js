/**
*
** file js ini hanya berisi fungsi-fungsi yang bersifat global/public
*
**/

//-----------------------------------------------------------------------------------
//		ADD TAB
//-----------------------------------------------------------------------------------

function resizeIframe(obj) {
    if(obj.contentWindow.document.body.scrollHeight<720){
        obj.style.height = 646  + 'px';
    }else{
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';

    }
}

function addtab(li,content,label,src){
    var x = content;
	$('#thisFrame').empty();
	$('#thisFrame').append('<iframe data-id="'+li+'" id="'+li+'-frame" class="row frame-container" src="'+src+'" onload="resizeIframe(this)" frameborder="0"></iframe>');
	$('#thisFrameMahasiswa').empty();
    $('#thisFrameMahasiswa').append('<iframe id="'+li+'-frame" class="row frame-container" src="'+src+'" onload="resizeIframe(this)" frameborder="0"></iframe>');

    var elems = document.querySelectorAll(".active");

    [].forEach.call(elems, function(el) {
        el.classList.remove("active");
    });


    var li = document.getElementById(li)

    // li.classList.add("active");
}

function resizeIFrameToFitContent( iFrame ) {
    iFrame.width  = iFrame.contentWindow.document.body.scrollWidth;
    iFrame.height = iFrame.contentWindow.document.body.scrollHeight;
}


function addTabKoordinator(li, content, label, src){
    var x = content;
    $('#thisFrameKoordinator').empty();
    $('#thisFrameKoordinator').append('<iframe id="dashboard-frame" class="row frame-container" src="'+src+'" onload="resizeIframe(this)" frameborder="0"></iframe>');
}

function addTabPembimbing(li, content, label, src){
    var x = content;
    $('#thisFramePembimbing').empty();
    $('#thisFramePembimbing').append('<iframe id="preview-frame" class="row frame-container" src="'+src+'" onload="resizeIframe(this)" frameborder="0"></iframe>');
}

$('#thisFrame').append('<iframe id="dashboard-frame" class="row frame-container" src="/view/dashboard-index" frameborder="0" scrolling="no" onload="resizeIframe(this)"></iframe>');

function AdjustIframeHeight(i) {
	window.parent.document.getElementById("preview-frame").style.height = parseInt(i) + "px";
}

$('#settingjadwal').on('click', function(){
    alert('anggi')
})
function setIFrameSize() {
    setTimeout(function() {

        let iframed =  document.getElementsByClassName("page-container")[0].offsetHeight;

        resizeFrameCustom('preview-frame', iframed);

        window.parent.$("body").css("overflow", "hidden !important");

    }, 100 + Math.floor(Math.random() * 11) * 6);

}

//-----------------------------------------------------------------------------------
//		END ADD TAB
//-----------------------------------------------------------------------------------

// document.onkeydown = function(e) {
//     if(e.keyCode == 123) {
//         alert('Bangsat kau!');
//         return false;
//     }
//     if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
//         alert('Bangsat!');
//         return false;
//     }
//     if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
//         alert('Bangsat!');
//         return false;
//     }
//     if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
//         alert('Bangsat!');
//         return false;
//     }
//     if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
//         alert('Bangsat!');
//         return false;
//     }
//   }

// document.onmousedown=disableclick;

// status  = "Right Click Disabled";

// function disableclick(event){

//     if(event.button==2){

//         alert('Bangsat!');
//         return false;

//     }
// }

/* send token csrf */
$.ajaxSetup({
    headers : {
        'Csrf-Token': $('meta[name="csrf-token"]').attr('content')
    }
});

/* Cryptograpi sat */
var CryptoJSAesJson = {
    stringify: function (cipherParams) {

        let j = {
            ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64)
        };

        if (cipherParams.iv) j.iv  = cipherParams.iv.toString();

        if (cipherParams.salt) j.s = cipherParams.salt.toString();

        return JSON.stringify(j);

    },
    parse: function (jsonStr) {

        let j = JSON.parse(jsonStr); // jika data string pake ini gan
        // let j = jsonStr; // jika sudah object pake ini eumh

        let cipherParams = CryptoJS.lib.CipherParams.create({
            ciphertext: CryptoJS.enc.Base64.parse(j.ct)
        });

        if (j.iv) cipherParams.iv    = CryptoJS.enc.Hex.parse(j.iv)

        if (j.s) cipherParams.salt   = CryptoJS.enc.Hex.parse(j.s)

        return cipherParams;
    }
}


function cryptoEncrypt(passphrase, isValue){

    return CryptoJS.AES.encrypt(JSON.stringify(isValue), passphrase, {format: CryptoJSAesJson}).toString();

}

function cryptoDecrypt(passphrase, isValue){

    let resp =   JSON.parse(CryptoJS.AES.decrypt(isValue, passphrase, {format: CryptoJSAesJson}).toString(CryptoJS.enc.Utf8));

    return JSON.parse(resp);

}


function CryptoJSAesDecrypt(passphrase, encrypted_json_string){ // mun ek make 256 bytes

    let obj_json  = JSON.parse(encrypted_json_string);

    let encrypted = obj_json.ciphertext;
    let salt      = CryptoJS.enc.Hex.parse(obj_json.salt);
    let iv        = CryptoJS.enc.Hex.parse(obj_json.iv);

    let key       = CryptoJS.PBKDF2(passphrase, salt, { hasher: CryptoJS.algo.SHA512, keySize: 64/8, iterations: 999});


    let decrypted = CryptoJS.AES.decrypt(encrypted, key, { iv: iv});

    return decrypted.toString(CryptoJS.enc.Utf8);

}

/* kueh / cookies */
var cookieList = function(cookieName) {
    //When the cookie is saved the items will be a comma seperated string
    //So we will split the cookie by comma to get the original array
    var cookie = $.cookie(cookieName);
    //Load the items or a new array if null.
    var items = cookie ? cookie.split(/,/) : new Array();

    //Return a object that we can use to access the array.
    //while hiding direct access to the declared items array
    //this is called closures see http://www.jibbering.com/faq/faq_notes/closures.html
    return {
        "add": function(val) {
            //Add to the items.
            items.push(val);
            //Save the items to a cookie.
            //EDIT: Modified from linked answer by Nick see
            //      http://stackoverflow.com/questions/3387251/how-to-store-array-in-jquery-cookie
            $.cookie(cookieName, items.join(','));
        },
        "remove": function (val) {
            //EDIT: Thx to Assef and luke for remove.
            indx = items.indexOf(val);
            if(indx!=-1) items.splice(indx, 1);
            $.cookie(cookieName, items.join(','));        },
        "clear": function() {
            items = null;
            //clear the cookie.
            $.cookie(cookieName, null);
        },
        "items": function() {
            //Get all the items.
            return items;
        }
      }
    }

    /* cara pake */
    // var list = new cookieList("MyItems");
    // // list.add("foo");
    // // console.log(list.items());
    // list.clear();


$(function(){

});


// Light reload
function lightReload(idBtn){
	$('#'+idBtn).on('click', function() {
		var light_4 = $(this).parent();
		$(light_4).block({
			message: '<i class="icon-spinner4 spinner"></i>',
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: 0.8,
				cursor: 'wait'
			},
			css: {
				border: 0,
				padding: 0,
				backgroundColor: 'none'
			}
		});
		window.setTimeout(function () {
			$(light_4).unblock();
		}, 2000);
	});
}


/* loading */
function loaderPage(stat){
	// parent.AdjustIframeHeight($("#ko").scrollHeight);
    if(stat == true){
        $('.page_loader').show();
    }else{
        $('.page_loader').hide();
    }
}

function capitalize(str) {
    strVal = '';
    str = str.split(' ');
    for (var chr = 0; chr < str.length; chr++) {
      strVal += str[chr].substring(0, 1).toUpperCase() + str[chr].substring(1, str[chr].length) + ' '
    }
    return strVal
}

function effent1(idcontent, anima){
    if(anima){
        var animation = anima;
    }else{
        var animation = 'fadeInRight';
    }
    //alert(anima);
    $(idcontent).addClass("animated " + animation).one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
        $(this).removeClass("animated " + animation);
    });
}

function effent2(idcontent, anima){
    if(anima){
        var animation = anima;
    }else{
        var animation = 'fadeInLeft';
    }

    $(idcontent).addClass("animated " + animation).one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
        $(this).removeClass("animated " + animation);
    });
}


// Custom notif   position
var stack_top_left = {"dir1": "down", "dir2": "right", "push": "top"};
var stack_bottom_left = {"dir1": "right", "dir2": "up", "push": "top"};
var stack_bottom_right = {"dir1": "up", "dir2": "left", "firstpos1": 25, "firstpos2": 25};
var stack_custom_left = {"dir1": "right", "dir2": "down"};
var stack_custom_right = {"dir1": "left", "dir2": "up", "push": "top"};
var stack_custom_top = {"dir1": "down", "dir2": "right", "push": "top", "spacing1": 1};
var stack_custom_bottom = {"dir1": "up", "dir2": "right", "spacing1": 1};

 // Custom left position
 function show_stack_custom_left(type, title, text) {
	var opts = {
		title: title,
		text: text,
		addclass: "stack-custom-left bg-primary alert-styled-right",
		stack: stack_custom_left
	};
	switch (type) {
		case 'error':
			opts.title = title;
			opts.text = text;
			opts.addclass = "stack-custom-left bg-danger";
			opts.type = "error";
		break;

		case 'info':
			opts.title = title;
			opts.text = text;
			opts.addclass = "stack-custom-left bg-info";
			opts.type = "info";
		break;

		case 'success':
			opts.title = title;
			opts.text = text;
			opts.addclass = "stack-custom-left bg-success";
			opts.type = "success";
		break;
	}
	new PNotify(opts);
}

/* Custom bottom position*/
function show_stack_custom_bottom(type, title, text) {
	var opts = {
		title: title,
		text: text,
		width: "100%",
		cornerclass: "no-border-radius",
		addclass: "stack-custom-bottom bg-primary",
		stack: stack_custom_bottom
	};
	switch (type) {
		case 'error':
			opts.title = title;
			opts.text = text;
			opts.addclass = "stack-custom-bottom bg-danger";
			opts.type = "error";
		break;

		case 'info':
			opts.title = title;
			opts.text = text;
			opts.addclass = "stack-custom-bottom bg-info";
			opts.type = "info";
		break;

		case 'success':
			opts.title = title;
			opts.text = text;
			opts.addclass = "stack-custom-bottom bg-success";
			opts.type = "success";
		break;
	}
	new PNotify(opts);
}

/* swift alert */
function swiftAlert(itype, ititle, itext,){
	if(itype == 'success'){
		swal({
			title: ititle,
			text: itext,
			confirmButtonColor: "#66BB6A",
			type: "success"
		});
	}else if(itype == 'error'){
		swal({
            title: ititle,
            text: itext,
            confirmButtonColor: "#EF5350",
            type: "error"
        });
	}
}




/* swift alert */
function swiftAlert(itype, ititle, itext,){
	if(itype == 'success'){
		swal({
			title: ititle,
			text: itext,
			confirmButtonColor: "#66BB6A",
			type: "success"
		});
	}else if(itype == 'error'){
		swal({
            title: ititle,
            text: itext,
            confirmButtonColor: "#EF5350",
            type: "error"
        });
	}
}


function loaderContent(stat, thisID){
    $('#'+thisID).css({
        "background": "url('../../img/load3.gif') 50% 50% no-repeat rgb(249,249,249)",
        "left": "0px",
		"top": "0px",
		"width": "100%",
		"height": "100%",
		"z-index": "9999",
        "opacity": ".5",
        "position":"absolute"
    });

    if(stat == false){
        $('#'+thisID).hide();
    }else{
        $('#'+thisID).show();
    }

}

function modalCustom(spanID, modalID, title, content, top, thisIndex){

    $('#'+spanID).empty();

    if(thisIndex !=''){
        thisIndex = 1070;
    }

    $('#'+spanID).append(`<div class="modal fade" data-backdrop="false" id="`+modalID+`" role="dialog" style="position:absolute;z-index:`+thisIndex+`">
        <div class="modal-dialog modal-xs">
            <div class="modal-content" style="border-color: #2a91be;background: aliceblue;margin-top:`+top+`;">
                <div class="modal-header">
                    <h5 class="modal-title">`+title+`</h5>
                </div>

                <div class="modal-body">
                    `+content+`
                </div>
                <div class="modal-footer" style="padding-bottom: 5px;margin-top: -15px;">
                    <span type="button" class="btn btn-link" style="font-size: 13px;color:#166dba; left: 10px;" id="close-`+modalID+`">Ok</span>
                </div>
            </div>
        </div>
    </div>`);


    $("#"+modalID).modal('show');

    $(function() {

        $("#close-"+modalID).on("click", function (e) {
            $("#"+modalID).modal("hide");
            // $('.modal-backdrop').remove();
            e.stopPropagation(); //This line would take care of it
            // $('#'+spanID).empty();
            $('#'+spanID).empty();
        });

    });

}

/* modal dynamic */

function resizeIframeX(obj, _bool = false) {
    console.log(_bool);
    if(_bool == true){
        obj.style.height = obj + 'px';
    }else{
        obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
    }
}

function resizeFrameCustom(thisId, thisHeight){
    // var theFrame = $('#'+thisId, parent.document.body);
    // theFrame.height($(document.body).height() + 100);

    var iframe = $(window.top.document).find('#'+thisId);
    iframe.height(thisHeight+'px');

    // $('iframe').css('height', 500);
    // $( "#"+thisId, parent.document.body).css({
    //     width: "100%",
    //     height: "550px !important",
    // }, "slow" );
}

$('#createZoneType').on('click', function(){
    modalContentDynamic('modalContentFrame', 'modalC1', 'Create Zone', 'content/createzone', '0%', '','','frameZone');
});

$('#createServices').on('click', function(){
    modalContentDynamic('modalContentFrame', 'modalC2', 'Create Service', 'content/createlayanan', '0%', '','','frameService');
});

$('#createCustomers').on('click', function(){
    modalContentDynamic('modalContentFrame', 'modalC3', 'Create Customer', 'content/createcustomer', '0%', '','','frameCustomer');
});

$('#createSociop').on('click', function(){
    modalContentDynamic('modalContentFrame', 'modalC4', 'Create Socio / Profession', 'content/createsocio', '0%', '','','frameSocio');
});


function modalContentDynamic(spanID, modalID, title, content, top, thisIndex, _modalSize, _frameID,  _scroll){
    $('#'+spanID).empty();

    if(thisIndex !=''){
        thisIndex = 1070;
    }

    if(_modalSize ==''){
        _modalSize ='modal-lg';
    }

    if(_scroll!=''){
        _scroll ='frameborder="0" scrolling="no" ';
    }

    $('#'+spanID).append(`<div class="modal fade" data-backdrop="true" id="`+modalID+`" role="dialog" style="position:absolute;z-index:`+thisIndex+`">
        <div class="modal-dialog `+_modalSize+`">
            <div class="modal-content" style="border-color: #2a91be;background: aliceblue;margin-top:`+top+`;">
                <div class="modal-header">
                    <h5 class="modal-title">`+title+`</h5>
                </div>

                <div class="modal-body">
                    <iframe src="`+content+`" id="`+_frameID+`" width="100%" `+_scroll+` onload="resizeIframeX(this)"></iframe>
                </div>
                <div class="modal-footer" style="margin-top: -15px;">
                    <span type="button" class="btn btn-link" style="font-size: 11px;color:#166dba;" id="close-`+modalID+`">Close</span>
                </div>
            </div>
        </div>
    </div>`);


    $("#"+modalID).modal({backdrop: 'static', keyboard: false});

    $(function() {
        $("#close-"+modalID).on("click", function (e) {
            $("#"+modalID).modal("hide");
            $('.modal-backdrop').remove();
            e.stopPropagation();
            $('#'+spanID).empty();
        });
    });

}
