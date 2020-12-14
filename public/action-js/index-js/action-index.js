
$(document).ready(function(){

    //send();
    function send(){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseURL + '/jsondata/isheader',
            data: {
                'de78qw4da6s5d4as'  : 'eqweqw64ee5q2s1dfqw9e',
            },
            success: function(result){
                
            }
        });

    }
});

timeup();

function timeup(){
    let isObject   = {};
    isObject.param = 'ACTIVE';
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: baseURL + '/jsondata/loadscheduleta',
		data :{
			iparam	    : cryptoEncrypt(PHRASE, isObject),
		},
		success: function(response){
            if(response.code == CODE_SUCCESS){
                let result = cryptoDecrypt(PHRASE, response.data);
                let data = result.data[0];

                let date = Date.now();
                let datefinish = new Date(data.date_finish);
                // console.log(data);
                if(date > datefinish){

                    let thatIsObject = {};
                    thatIsObject.temp  = data.id_schedule;
                    thatIsObject.status = 20;

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: baseURL + '/jsondata/updatejadwal',
                        data :{
                            iparam	    : cryptoEncrypt(PHRASE, thatIsObject),
                        },
                        success: function(response){
                            if(response.code == CODE_SUCCESS){
                                swal({
                                    title: "Ops!",
                                    text:" Jadwal sudah habis waktu :)",
                                    confirmButtonColor: "#2196F3",
                                    type: "warning",
                                });

                                location.reload();
                            }
                        }
                    })
                }   
            }
		},error:function(xhr){
            alert(xhr);
        }
    });
}

// loadschedule();
// function loadschedule(){
//     let isObject   = {};
//     isObject.param = 'ACTIVE';
    
// 	$.ajax({
// 		type: 'POST',
// 		dataType: 'json',
// 		url: baseURL + '/jsondata/loadscheduleta',
// 		data :{
// 			iparam	    : cryptoEncrypt(PHRASE, isObject),
// 		},
// 		success: function(response){
//             if(response.code == CODE_SUCCESS){
//                 let result = cryptoDecrypt(PHRASE, response.data);
//                 let data = result.data[0];
                
//                 let idjadwal = data['id_schedule'];
//                 let description = data['description'];

//                 // let res = data['date_start']+" - "+data['date_finish'];

//                 // let setjadwal = `<li onclick="loadeditschedule()"><a href="#"><i class="icon-pencil4 pull-right"></i>Edit Jadwal</a></li>
//                 //                 <li onclick="deleteSchedule()"><a href="#"><i class="icon-eraser2 pull-right"></i>Hapus Jadwal</a></li>`

//                 // $('#tempJadwal').val(idjadwal);
//                 // $('#dateTA').html(res);
//                 // $('#description').html(description);
//                 // $('#setjadwal').html(setjadwal);
                
//             }else{
//                 let setjadwal = `<li onclick="createJadwal()"><a><i class="icon-plus-circle2 pull-right"></i>Buat Jadwal</a></li>`
//                 document.getElementById("tempJadwal").value = '';
//                 $('#dateTA').html('Belum Ada Jadwal')
//                 $('#setjadwal').html(setjadwal);
//             }
// 		}
//     });
// }