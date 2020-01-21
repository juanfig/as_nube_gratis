
</div>
<!-- END CONTENT BODY -->
</div>
<!-- END CONTENT -->

</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
    <div class="page-footer-inner"> <?php echo date("Y"); ?> &copy; AS-NUBE
        <a target="_blank" href="http://apreciasoft.com/">ApreciaSoft</a> &nbsp;|&nbsp;
        <a href="http://apreciasoft.com/" title="Purchase Metronic just for 27$ and get lifetime updates for free" target="_blank">soporte@apreciasoft.com</a>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->
</div>
<!-- BEGIN QUICK NAV -->







<script src="../../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="../../assets/global/plugins/jquery-ui.min.js" type="text/javascript"></script>
<script src="../../assets/js/index.js" type="text/javascript"></script>






<script type="text/javascript">

        // CARGAMOS EL HEAD DE EL TEMPLATE //
        $(".page-header").load("../PartialView/Head.html");
        //alert("Antes de cargar el html del menu");
        // CARGAMOS EL MENU DE EL TEMPLATE //
        $(".menu").load("../PartialView/Menu.html");

        // ESTE ARCHIVO SOLO CONTIENE NAV BAR  DE EL TEMPLATE //
        $(".page-bar").load("../PartialView/Navbar.html");



        $( document ).ready(function() {
        // VALIDATOR SESSION//

        if(JSON.parse(localStorage.getItem('session')) == null)
        {
            window.location.assign("../../");
        }
        //*****************//
    });


        function changueNav(Title1,Title2,Title3) {
            // body...

            var html = '';



            if(Title1 != null)
            {

                html = html + '<li><a href="index.html" class="font-white">'+Title1+'</a><i class="fa fa-circle"></i></li>';
            }


            if(Title2 != null)
            {

                html = html + '<li><a href="index.html" class="font-white">'+Title2+'</a><i class="fa fa-circle"></i></li>';
            }

            if(Title3 != null)
            {

                html = html + '<li><a href="index.html" class="font-white">'+Title3+'</a><i class="fa fa-circle"></i></li>';
            }



            $(".navig").html(html);
        }


        function notificate(title,sutitle,type)
        {
            $.notify({
                title: '<strong>'+title+'</strong><br>',
                message: sutitle

            },{
             type: type ,
             timer: 4000,
             offset: {
                x: 20,
                y: 110
            }});
        }

    var load = false;
    function loading()
    {
        // consolelog(load)
        $('.btnLoad').attr('data-loading-text','<i class="fa fa-spinner fa-spin"></i> Espere loading ...');

        if(load)
        {
            $(".load-bar").hide();
            load = false;
            $('.btnLoad').button('reset');
            $('.btnCancel').prop('disabled',false);
        }
        else{
            $(".load-bar").show();
            load = true;
             $('.btnLoad').button('loading');
             $('.btnCancel').prop('disabled',true);
        }

    }

        // SERVICIO QUE LISTA LAS NOTIFICACIONES EN EL FRONT
        function notificateFront()
        {
            // console.log(uri);
            $.ajax({
                url: uri+'notifications/find/'+JSON.parse(localStorage.getItem('session')).idUser,
                type: 'GET',
                headers: { 'Authorization': 'Basic '+JSON.parse(localStorage.getItem('session'))['header'] },
                success: function(data){
                    $('.num_notificate_head').html(data.length)
                    var html = "";
                    var html2 = "";
                    for (var i = 0; i < data.length; i++) {
                        html+='<li>'+
                        '<a href="javascript:;" onclick="notificateRead('+data[i].idNotification+",'"+data[i].title+"','"+data[i].subTitle+"','"+data[i].dateCreated+"'"+')">'+
                        '<span class="details">'+
                        '<span class="label label-sm label-icon label-warning" style="padding: 4px;'+data[i].style+'">'+
                        '<i class="'+(data[i].icon==null?'fa fa-bell-o':data[i].icon)+'"></i>'+
                        '</span>'+data[i].title+'</span>'+
                        '</a>'+
                        '</li>';

                        html2+='<li>'+
                        '<a href="javascript:;" onclick="notificateRead('+data[i].idNotification+",'"+data[i].title+"','"+data[i].subTitle+"','"+data[i].dateCreated+"'"+')">'+
                        ' <div class="col1">'+
                        '<div class="cont">'+
                        ' <div class="cont-col1">'+
                        '<div class="label label-sm label-success">'+
                        '<i class="fa fa-bar-chart-o"></i>'+
                        '</div>'+
                        '</div>'+
                        '<div class="cont-col2">'+
                        '<div class="desc">'+data[i].title+'</div>'+
                        '<div><br>'+data[i].subTitle+'</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '<div class="col2">'+
                        '</div>'+
                        ' </a>'+
                        '</li>';
                    }
                    $('#list_notificate_head').html(html);
                    $('#list_notificate_head_general').html(html2);

                },
                error: function(){
                    $('.num_notificate_head').html(0)
                    $('#list_notificate_head').html("");
                    $('#list_notificate_head_general').html("");
                }
            })

        }
        notificateFront()
        setInterval('notificateFront()', 20000);
        function starTabAlert(){
            $("#tabs_alerts_panel").show();
        }

        function notificateRead(id, title, subtitle, date)
        {
            $('#modal-notificate-read').modal('show');
            var html = "";
            html+="Titulo: "+title+'<br>';
            html+="Descripción: "+subtitle+'<br>';
            html+="Fecha: "+date;

            $('#content-notification').html(html);
            $.ajax({
                url: uri+'notifications/read/'+id+'/'+JSON.parse(localStorage.getItem('session')).idUser,
                type: 'GET',
                success: function(data){
                    notificateFront();
                },
                error: function(){
                    notificateFront();
                }
            })
        }

        function readAll()
        {
            var r = confirm("¿Seguro desea marcar todas las notificaciones como leídas?");
            if(r == true){
                $.ajax({
                    url: uri+'notifications/readAll/'+JSON.parse(localStorage.getItem('session')).idUser,
                    type: 'GET',
                    success: function(data){
                        notificateFront();
                    },
                    error: function(){
                        notificateFront();
                    }
                })
            }

        }

    </script>




    <script src="../../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="../../assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/morris/morris.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script>

    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="../../assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../../assets/pages/scripts/dashboard.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="../../assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>

    <script src="../../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <script src="../../assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>

    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="../../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../../assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <script src="../../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>


    <script src="../../assets/js/bootstrap-datetimepicker.js" type="text/javascript"></script>



    <script src="../../assets/js/angular.js"></script>
    <script src="../../assets/js/angular-route.js"></script>
    <script src="../../assets/js/cookies.js" type="text/javascript"></script>



    <script src="../../assets/js/angular-animate.js"></script>
    <script src="../../assets/js/angular-sanitize.js"></script>
    <script src="../../assets/js/ui-bootstrap-tpls-2.3.0.js"></script>

    <script src="../../assets/js/header.js" type="text/javascript"></script>
    <script src="../../assets/js/highcharts.js" type="text/javascript"></script>




    <script src="../../assets/js/bootstrap-notify.js" type="text/javascript"></script>




    <!-- END THEME LAYOUT SCRIPTS -->
</body>

</html>
