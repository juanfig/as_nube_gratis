<!DOCTYPE html>

<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>AS-NUBE</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="AS-NUBE " name="description" />
        <meta content="ApreciaSoft" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../../assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../../assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/css/as-nube.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="../../assets/pages/css/login-5.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->

        <link rel="shortcut icon" href="../../assets/img/logo.png" />


        <style>

       .user-login-5 .login-footer .login-social,
     .login-footer .login-copyright {
      padding: 0 80px; }


       .user-login-5 .login-footer .login-social {
        padding-right: 0; }
        .user-login-5  .login-footer .login-social li {
          display: inline-block;
          list-style: none;
          margin-right: 1em; }
        .user-login-5 .login-footer .login-social a {
          color: #a9b5be;
          font-size: 18px; }
          .user-login-5 .login-footer .login-social a:hover {
            color: #337ab7;
            text-decoration: none; }
          .user-login-5 .login-footer .login-social a:focus {
            color: #a9b5be; }
      .user-login-5 .login-footer .login-copyright {
        padding-left: 0;
        margin-top: 6px; }
        .user-login-5 .login-footer .login-copyright > p {
          margin: 0;
          font-size: 13px;
          color: #a9b5be; }

         .user-login-5 .login-footer .login-social li {
          margin-right: 0.5em; }


        </style>

         </head>
    <!-- END HEAD -->

    <body  >
        <!-- BEGIN : LOGIN PAGE 5-1 -->
        <div class="user-login-5">
            <div class="row bs-reset">
                <div class="col-md-6 bs-reset mt-login-5-bsfix">
                    <div class="login-bg" style="background-image:url(../../assets/img/fon.jpg)">
                        <img class="login-logo" src="../../assets/img/logo.png" width="15%" /> </div>
                </div>
                <div  class="col-md-6 " style="margin-top:2cm;" >

                    <!-- Espacio para colocar contenido-->

                        <div ng-view ng-app="routingOptionsFrond">







                        </div>
                        <br>
                        <div class="login-footer" >
                            <div class="row bs-reset">
                                <div class="col-xs-5 bs-reset">
                                    <ul class="login-social">
                                        <li>
                                            <a href="javascript:;">
                                                <i class="icon-social-facebook"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="icon-social-twitter"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="icon-social-dribbble"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-xs-7 bs-reset">
                                    <div class="login-copyright text-right">
                                        <p>V:1.2.0  © www.ApreciaSoft.com 2017</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                </div>
                 <!-- FORM AGREGAR AGENCIA -->

            </div>
        </div>
       <!-- BEGIN CORE PLUGINS -->
        <script src="../../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="../../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../../assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>

        <script src="../../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->



        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="../../assets/global/scripts/app.min.js" type="text/javascript"></script>

        <script src="../../assets/js/index.js" type="text/javascript"></script>



         <script src="../../assets/js/angular.js"></script>
         <script src="../../assets/js/angular-route.js"></script>
         <script src="../../assets/js/cookies.js" type="text/javascript"></script>

        <script src="../../assets/js/angular-animate.js"></script>
        <script src="../../assets/js/angular-sanitize.js"></script>
        <script src="../../assets/js/ui-bootstrap-tpls-2.3.0.js"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->

        <script src="js/optionsFrond.js" type="text/javascript"></script>

        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!-- END THEME LAYOUT SCRIPTS -->
         <script src="../../assets/js/bootstrap-notify.js" type="text/javascript"></script>
         <script src="../../assets/js/bootstrap-notify.js" type="text/javascript"></script>
        <script type="text/javascript">
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
                if(load)
                {
                    $(".load-bar").hide();
                    load = false;
                }
                else{
                    $(".load-bar").show();
                    load = true;
                }

            }
        </script>

    </body>

</html>
