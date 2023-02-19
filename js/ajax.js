/*telegramSender ajax*/

jQuery(document).ready(function () {
    $ = jQuery

    function animation() {
        $('#telsetingven').html('Load...');

    }

    $('body').on("click", "#telsetingven", function () {


        const dats = $("#formsetinvendor").serialize();

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: 'action=tscfwc_form_reqest&' + dats,
            beforeSend: animation,
            success: function (data) {
                $('#telsetingven').html('Saved');


            },
            error: function (xhr, str) {
                alert('Возникла ошибка: ' + xhr.responseCode);
            }
        });
    });

    $("input[value='no']").bind("click", function () {
        $('radioinput').css('display', 'none');
    });

    /*--------------------*/
    $("#sendKey").change(function () {

        if ($("#sendKey").is(":checked")) {
            $('.radioinputdefaul').hide();
            $('.con-def').hide();
            $('.radioinputkey').show();
            $('.con-key').show();
        } else {
            $('.con-def').show();
            $('.radioinputdefaul').show();
            $('.radioinputkey').hide();
            $('.con-key').hide();
        }
        /*--------------------*/
    });
    if ($("#sendKey").is(":checked")) {
        $('.radioinputdefaul').hide();
        $('.con-def').hide();
        $('.radioinputkey').show();
        $('.con-key').show();
    } else {
        $('.con-def').show();
        $('.radioinputdefaul').show();
        $('.radioinputkey').hide();
        $('.con-key').hide();
    }

    $("#selinfo,#wpforms_list").multiSelect({
        selectableHeader: "<div class=\'custom-header\'>All form</div>",
        selectionHeader: "<div class=\'custom-header\'>Send to telegram</div>"

    });

    $('#tscfwc_status').selectize({
        plugins: ['remove_button'],
        delimiter: ',',
        persist: false,
        create: function (input) {
            return {
                value: input,
                text: input
            }
        }
    });
});



let tokenstr = document.querySelector('#getUpdates')
const token =  document.querySelector('[name="tscfwc_setting_token"]').value

let newurl = tokenstr.outerHTML.replaceAll('{token}',token)

tokenstr.innerHTML = newurl

function telsenderInfo(){
    const token =  document.querySelector('[name="tscfwc_setting_token"]').value
    const url  = `https://api.telegram.org/bot${token}/getUpdates`
    telsenderTestSend()
    fetch(url).then(res=>{
        res.json().then(response=>{
            telsenderOut(response)
        })
    })
}

/**
 *
 * @param response
 */
function telsenderOut(response){
    let resultHtml =  document.querySelector('.result-tested')
    if (response.ok){
        let data = []

        response.result.forEach(res=>{
            if (res.my_chat_member){
                data.push({
                    id:res.my_chat_member.chat.id,
                    name:res.my_chat_member.chat.title
                })
            }
            if (res.message){
                data.push({
                    id:res.message.from.id,
                    name:res.message.from.username
                })
            }

        })
        data = data.filter((v,i,a)=>a.findIndex(t=>(t.id === v.id))===i)

       let resultHtml =  document.querySelector('.result-tested')
        let html = ''

        data.forEach(el=>{
            html +=`${el.name} :  <b >${el.id}</b> <span class="id-chat-list" title="Inset to form" onclick="insertId(${el.id})">&#10063</span> <br/>`

        })
        resultHtml.innerHTML = html

    }else{
        resultHtml.innerHTML = response.description
    }
}

function insertId(id){
    document.querySelector('[name="tscfwc_setting_chatid"]').value = id
}

/**
 *
 */
function telsenderTestSend(){

    const token =  document.querySelector('[name="tscfwc_setting_token"]').value
    const chatid =  document.querySelector('[name="tscfwc_setting_chatid"]').value
    const url  = `https://api.telegram.org/bot${token}/sendMessage?text=Is works 🙃 &chat_id=${chatid}`
    fetch(url).then(res=>{
        res.json().then(response=>{

        })
    })

}