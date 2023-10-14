<hr>

<div class="telsender-help">

    <div class="questions">



        <div class="row">
            <?php $this->render('template/help',[])?>
        </div>

        <div class="row">
            <div class="header">
                <h2>Як дізнатись Id Чату?</h2>
                <span class="icon-arows"></span>
            </div>

            <div class="content">
                <h3> Спосіб 1 </h3>
                <p>
                   Додайте <a href="https://t.me/telsender_bot">бота</a> До потрібного чату та введіть команду /start, та отримаєте повідомлення з потрібною інформацією
                </p>
                <h3> Спосіб 2 </h3>
                <p>
                    В браузері ввести <a target="_blank" href="https://api.telegram.org/botXXX/getUpdates">https://api.telegram.org/botXXX/getUpdates</a>
                    де botXXX - ваш Токен бота
                </p>
            </div>
        </div>
<!--row-->
        <div class="row">
                <div class="header">
                    <h2>Шорткоди повідомлення для woocommerce</h2>
                    <span class="icon-arows"></span>
                </div>

                <div class="content">
                    <p>
                        <small>Якщо контент не відображається, перейдіть за <a target="_blank" href="https://gist.github.com/PechenkiUA/7b4e6ba706506cd7e5c489b8ba6b65f4">посиланням</a></small>
                        <script src="https://gist.github.com/PechenkiUA/7b4e6ba706506cd7e5c489b8ba6b65f4.js"></script>
                    </p>
                </div>
        </div>
<!--row-->
        <div class="row">
            <div class="header">
                <h2>Як додати власний шорткод для повідомлення woocommerce? </h2>
                <span class="icon-arows"></span>
            </div>

            <div class="content">
                <p>Додайде код до файлу <i> functions.php</i> активної теми</p>
                <p>
 <pre>
 function castom_function ($list,$order_id){
    $list['{castom}'] = 'example';
  return $list;
 }
 add_filter( 'tscf_filter_codetemplate','castom_function', 20, 2 );
</pre>
                </p>
            </div>
        </div>
<!--row-->
        <div class="row">
            <div class="header">
                <h2>Що таке події (events)?</h2>
                <span class="icon-arows"></span>
            </div>

            <div class="content">
                <p>
                   Події дають можливість надсилати повідомлення до телегруму за певних умов:
                </p>
                <ul>
                    <li>- Помилка авторизації на сайті</li>
                    <li>- Успішний вхід в адмін панель</li>
                    <li>- Перехоплення POST запитів</li>
                    <li>- Додавання товару в кошик (WC)</li>
                </ul>
                <p>
                    Можна використати перехоплення POST, щоб отримати повідомлення з форм як не підтримуються напряму плагіном
                </p>
            </div>
        </div>
    </div>


</div>


<script>
    document.querySelectorAll('.questions .row .header').forEach(el=>{
        el.onclick = (event)=>{
            event.target.closest('.row').classList.toggle('open')
        }
    })
</script>


<style>

    .telsender-help {
        display: block;
        clear: both;
        max-width: 99%;
    }

    .telsender-help .row{
        background: white;
        padding: 10px;
        border-radius: 10px;
        margin-top: 10px;
        box-shadow: 1px 1px 8px #ccc;

    }
    .questions .row .content{
        height: 0;
        overflow: hidden;
    }
    .questions .row.open .content{
        height: auto;
        overflow: hidden;
        transition: .4s all;
    }

    .questions .icon-arows {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        width: 20px;
        height: 20px;
        display: inline-block;
        background-position: center;
        background-repeat: no-repeat;
    }

    .questions .row .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;

    }
    .questions .row .content{
        height: 0;
        overflow: hidden;
    }
    .questions .row.open .icon-arows{
        transform: rotate(180deg);
        transition: .3s all;
    }
</style>
