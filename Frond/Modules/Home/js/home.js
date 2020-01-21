
var RoutingApp = angular.module('routingTravel',
 ['ngAnimate', 'ngSanitize', 'ngRoute', 'ui.bootstrap']);


// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider
            .when("/", {
                templateUrl: 'View/home.html',
                controller: 'homeController'
            })

            .otherwise({
                redirectTo: '/'
            });
}).config(['$httpProvider', function ($httpProvider) {
    $httpProvider.defaults.headers.common = { 
        'Authorization': 'Basic '+JSON.parse(localStorage.getItem('session'))['header'],
        'Accept': 'application/json;odata=verbose'
      };
}]);

RoutingApp.controller('homeController', function ($scope, $http, $routeParams) {

$scope.profile = JSON.parse(localStorage.getItem('session')).idProfileUser;
$scope.active = 0;

	function iconDashboard(p) {
		$http.get(uri + 'dashboard/index'+(!p?'':'/'+p)).success(function (data) {

			$scope.dashboard=data;
			//$scope.dashboard.total = parseFloat($scope.dashboard.total).toFixed(2)
			//$scope.dashboard.suspended = parseFloat($scope.dashboard.suspended).toFixed(2)
			$scope.dashboard.actives = parseFloat($scope.dashboard.actives).toFixed(2)
			//$scope.dashboard.acumulated = parseFloat($scope.dashboard.acumulated).toFixed(2)
			//$scope.dashboard.unpaid = parseFloat($scope.dashboard.unpaid).toFixed(2)
			//$scope.dashboard.outpaid = parseFloat($scope.dashboard.outpaid).toFixed(2)
         }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
	}

	$scope.setActive = function(a){
	  $scope.active = a;
	  iconDashboard(a);
	}

	$scope.initDashboard= function(){
		iconDashboard();
	}

});

//RoutingApp.directive('googlePlace', directiveFunction);
//directiveFunction.$inject = ['$rootScope'];



