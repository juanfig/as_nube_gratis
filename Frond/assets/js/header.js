// Creación del MODELO
var RoutingApp = angular.module('routinHeader', ['ui.bootstrap',]);

		
/*********************************************************/
/*****************   CONTROLADOR   **********************/
RoutingApp.controller('headController', function($scope,$cookies) {
	
			
		//console.log(JSON.parse($cookies.get("user")))
		//$scope.user = JSON.parse($cookies.get("user")).userNameUser;

		$scope.firstName = "John";

	
});


