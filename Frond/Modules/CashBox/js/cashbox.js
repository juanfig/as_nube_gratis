
// Creación del módulo
var RoutingApp = angular.module('routingCashBox', ['ngRoute', 'ui.bootstrap', 'ngCookies', 'ngSanitize']);


// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'View/list.html',
        controller: 'cashboxController'
    })//--------------------------------------
    .when('/Income', {
        templateUrl: 'View/Income/list.html',
        controller: 'incomeController'
    })
    .when('/Income/Add', {
        templateUrl: 'View/Income/Add.html',
        controller: 'incomeController'
    })
    .when('/Income/Details:id', {
        templateUrl: 'View/Income/Details.html',
        controller: 'cashboxController'
    })//--------------------------------------
    .when('/Expenses', {
        templateUrl: 'View/Expenses/list.html',
        controller: 'expensesController'
    })
    .when('/Expenses/Add', {
        templateUrl: 'View/Expenses/Add.html',
        controller: 'expensesController'
    })
    .when('/Expenses/Details:id', {
        templateUrl: 'View/Expenses/Details.html',
        controller: 'cashboxController'
    })

    //--------------------------------------

    .when('/Config/CashBox', {
        templateUrl: 'View/Config/cashbox/list.html',
        controller: 'cashboxController'
    })
    .when('/Config/CashBox/Add', {
        templateUrl: 'View/Config/cashbox/Add.html',
        controller: 'incomeController'
    })
    .when('/Config/CashBox/Edit:id', {
        templateUrl: 'View/Config/cashbox/Add.html',
        controller: 'cashboxController'
    })//--------------------------------------
    .when('/Config/TypeIncome', {
        templateUrl: 'View/Config/income/list.html',
        controller: 'incomeController'
    })
    .when('/Config/TypeIncome/Add', {
        templateUrl: 'View/Config/income/Add.html',
        controller: 'incomeController'
    })
    .when('/Config/TypeIncome/Edit:id', {
        templateUrl: 'View/Config/income/Add.html',
        controller: 'cashboxController'
    })//--------------------------------------
    .when('/Config/TypeExpense', {
        templateUrl: 'View/Config/expense/list.html',
        controller: 'expensesController'
    })
    .when('/Config/TypeExpense/Add', {
        templateUrl: 'View/Config/expense/Add.html',
        controller: 'incomeController'
    })
    .when('/Config/TypeExpense/Edit:id', {
        templateUrl: 'View/Config/expense/Add.html',
        controller: 'cashboxController'
    })//--------------------------------------
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
/*****************   MODULO DE INGRESOS ******************/
RoutingApp.controller('incomeController', function ($scope, $http, $routeParams,$location, $cookies) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.isAdmin = JSON.parse(localStorage.getItem('session')).userNameUser=="ADMIN";
    $scope.topFilter = -1;
    $scope.statusFilter = null;
    $scope.onlineFilter = null;
    $scope.userFilter = null;
    $scope.searchFilter = null;
    $scope.idAgencyFilter = null;
    $scope.disableConfiguration = false;
    $scope.agencyList = null;

    $scope.tickets = null;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];



});
/*********************************************************/
/*****************   MODULO DE EGRESOS *******************/
RoutingApp.controller('expensesController', function ($scope, $http, $routeParams,$location, $cookies) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.isAdmin = JSON.parse(localStorage.getItem('session')).userNameUser=="ADMIN";
    $scope.topFilter = -1;
    $scope.statusFilter = null;
    $scope.onlineFilter = null;
    $scope.userFilter = null;
    $scope.searchFilter = null;
    $scope.idAgencyFilter = null;
    $scope.disableConfiguration = false;
    $scope.agencyList = null;

    $scope.tickets = null;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];



});
/*********************************************************/
/*****************   MODULO DE CAJAS *********************/
RoutingApp.controller('cashboxController', function ($scope, $http, $routeParams,$location, $cookies) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.isAdmin = JSON.parse(localStorage.getItem('session')).userNameUser=="ADMIN";
    $scope.topFilter = -1;
    $scope.statusFilter = null;
    $scope.onlineFilter = null;
    $scope.userFilter = null;
    $scope.searchFilter = null;
    $scope.idAgencyFilter = null;
    $scope.disableConfiguration = false;
    $scope.agencyList = null;

    $scope.tickets = null;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];



});
