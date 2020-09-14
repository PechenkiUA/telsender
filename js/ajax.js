/*telegramSender ajax*/

jQuery(document).ready(function(){
$ = jQuery
function animation(){
  $('#telsetingven').html('Сохраняю..Ждите');

}
  $("#telsetingven").bind( "click", function() {


		var dats = $("#formsetinvendor").serialize();
    console.log(dats);
			 $.ajax({
          		type: 'POST',
          		url: ajaxurl,
          		data:'action=tscfwc_form_reqest&'+dats,
              beforeSend:animation,
          		success: function(data) {
                $('#telsetingven').html('Сохранено');


          },
          error:  function(xhr, str){
	         alert('Возникла ошибка: ' + xhr.responseCode);
          }
        });
	});

$("input[value='no']").bind( "click", function() {
  $('radioinput').css('display','none');
});

/*--------------------*/
$("#sendKey").change(function() {

if($("#sendKey").is(":checked")) {
   $('.radioinputdefaul').hide();
   $('.con-def').hide();
   $('.radioinputkey').show();
   $('.con-key').show();
}
else{
  $('.con-def').show();
  $('.radioinputdefaul').show();
   $('.radioinputkey').hide();
   $('.con-key').hide();
}
/*--------------------*/
});
if($("#sendKey").is(":checked")) {
   $('.radioinputdefaul').hide();
   $('.con-def').hide();
   $('.radioinputkey').show();
   $('.con-key').show();
}
else{
  $('.con-def').show();
  $('.radioinputdefaul').show();
   $('.radioinputkey').hide();
   $('.con-key').hide();
}

$("#selinfo,#wpforms_list").multiSelect({
    selectableHeader: "<div class=\'custom-header\'>Все формы</div>",
    selectionHeader: "<div class=\'custom-header\'>Отправлять в телегу</div>"

  });

  $('#tscfwc_status').selectize({
    plugins: ['remove_button'],
    delimiter: ',',
    persist: false,
    create: function(input) {
        return {
            value: input,
            text: input
        }
    }
});
});
