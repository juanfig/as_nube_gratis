<!DOCTYPE html>
<html lang="en" >
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>AS-NUBE</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="ANUBE" name="description" />
        <meta content="ApreciaSoft" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../../assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../../assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../../assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="../../assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/layouts/layout/css/themes/mostaza.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="../../assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->

        <link href="../../assets/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/css/stylenube.css" rel="stylesheet" type="text/css" />

        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="../../assets/img/logo.png" /> </head>

        <style type="text/css">

::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}
::-webkit-scrollbar-button {
  width: 0px;
  height: 0px;
}
::-webkit-scrollbar-thumb {
  background: #e1e1e1;
  border: 0px none #ffffff;
  border-radius: 50px;
}
::-webkit-scrollbar-thumb:hover {
  background: #ffffff;
}
::-webkit-scrollbar-thumb:active {
  background: #000000;
}
::-webkit-scrollbar-track {
  background: #666666;
  border: 0px none #ffffff;
  border-radius: 50px;
}
::-webkit-scrollbar-track:hover {
  background: #666666;
}
::-webkit-scrollbar-track:active {
  background: #333333;
}
::-webkit-scrollbar-corner {
  background: transparent;
}

.clearfix
{


    padding-left: 5px;
    padding-right: 15px;
}
.page-bar
{

    margin: -25px -2px 0 !important;
}

.page-content{
  padding: 25px 2px 5px !important;
}

.load-bar {
  position: fixed;
    margin-top: -25px;
    width: 100%;
    height: 5px;
    background-color: #e1e1e1;
    z-index: 1000;
    display: none;
}

.bar {
  content: "";
  display: inline;
  position: absolute;
  width: 0;
  height: 100%;
  left: 50%;
  text-align: center;
}
.bar:nth-child(1) {
  background-color: #26c281;
  animation: loading 3s linear infinite;
}
.bar:nth-child(2) {
  background-color: #4d5d6f;
  animation: loading 3s linear 1s infinite;
}
.bar:nth-child(3) {
  background-color: #e1e1e1;
  animation: loading 3s linear 2s infinite;
}
@keyframes loading {
    from {left: 50%; width: 0;z-index:100;}
    33.3333% {left: 0; width: 100%;z-index: 10;}
    to {left: 0; width: 100%;}
}



body
{
}
        </style>
    <!-- END HEAD -->

    <body  class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">


     <div class="page-wrapper">

            <!-- BEGIN HEADER -->
            <div class="page-header navbar navbar-fixed-top nav-as-nube" s></div>
            <!-- END HEADER -->


            <!-- BEGIN HEADER & CONTENT DIVIDER -->
            <div class="clearfix"> </div>







            <!-- END HEADER & CONTENT DIVIDER -->



            <!-- BEGIN CONTAINER -->
            <div class="page-container" >

                <!-- BEGIN SIDEBAR -->
                <div class=" page-sidebar-wrapper"></div>
                <!-- END SIDEBAR -->

                <div class="page-sidebar navbar-collapse collapse">

                        <ul class="page-sidebar-menu menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" >
                        </ul>
                        <!-- END SIDEBAR MENU -->
                    </div>

                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content" style="background-color: #ECEDF0; margin-top:-100px;" >

        <!-- BEGIN PAGE BAR -->
        <div class="load-bar">
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
        </div>
                        <div class="page-bar bg-dark " style="border-bottom: 1px solid #2f353b;box-shadow: 0 5px 0 rgba(0,0,0,0.2)">
                          
                        </div>
