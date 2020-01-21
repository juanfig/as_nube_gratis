<!-- AGREGAMOS LOS LINK -->
<?php include_once '../PartialView/linkMoules.php'; ?>


<div class="clearfix" ng-view ng-app="routingTravel">
	
	
</div>

<?php 
include_once '../PartialView/footerMoules.php';
 ?>


<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAi3ag3Cgtzioujs6KEm1nxYchk_YNMbjc&signed_in=true&libraries=places"></script>
<script src="js/travel.js"></script> 
<script src="js/reservation.js"></script>
<script src="js/reservationCalendar.js"></script>

<script src='../build/pdfmake.min.js'></script>
<script src='../build/vfs_fonts.js'></script>
 <!-- AGREGAMOS LOS SCRIPT -->                       
