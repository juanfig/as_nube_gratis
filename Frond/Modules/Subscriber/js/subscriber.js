
// Creación del módulo
var RoutingApp = angular.module('routingSubscriber', ['ngRoute', 'ui.bootstrap', 'ngCookies', 'ngSanitize']);


// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'Views/dashboard.html',
        controller: 'subscriberController'
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


/*********************************************************/
/*****************   MODULO DE BASE **********************/
RoutingApp.controller('subscriberController', function ($scope, $http, $routeParams,$location, $cookies) {

    
   

});