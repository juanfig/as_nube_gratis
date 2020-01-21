
// Creación del módulo
var RoutingApp = angular.module('routingCashBox', ['ngRoute', 'ui.bootstrap', 'ngCookies', 'ngSanitize']);


// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'View/list.html',
        controller: 'cashboxController'
    })
    .when('/Details:id', {
        templateUrl: 'View/Details.html',
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
    .when('/Income/Edit:id', {
        templateUrl: 'View/Income/Add.html',
        controller: 'incomeController'
    })//--------------------------------------
    .when('/Expenses', {
        templateUrl: 'View/Expenses/list.html',
        controller: 'expensesController'
    })
    .when('/Expenses/Add', {
        templateUrl: 'View/Expenses/Add.html',
        controller: 'expensesController'
    })
    .when('/Expenses/Edit:id', {
        templateUrl: 'View/Expenses/Add.html',
        controller: 'expensesController'
    })//--------------------------------------
    .when('/Checks/Ours', {
        templateUrl: 'View/Checks/oursList.html',
        controller: 'checksController'
    })
    .when('/Checks/Others', {
        templateUrl: 'View/Checks/othersList.html',
        controller: 'checksController'
    })

    //--------------------------------------

    .when('/Config/CashBox', {
        templateUrl: 'View/Config/cashbox/list.html',
        controller: 'cashboxController'
    })
    .when('/Config/CashBox/Add', {
        templateUrl: 'View/Config/cashbox/Add.html',
        controller: 'cashboxController'
    })
    .when('/Config/CashBox/Edit:id', {
        templateUrl: 'View/Config/cashbox/Add.html',
        controller: 'cashboxController'
    })//--------------------------------------
    .when('/Config/TypeIncome', {
        templateUrl: 'View/Config/income/list.html',
        controller: 'typeIncomeController'
    })
    .when('/Config/TypeIncome/Add', {
        templateUrl: 'View/Config/income/Add.html',
        controller: 'typeIncomeController'
    })
    .when('/Config/TypeIncome/Edit:id', {
        templateUrl: 'View/Config/income/Add.html',
        controller: 'typeIncomeController'
    })//--------------------------------------
    .when('/Config/TypeExpense', {
        templateUrl: 'View/Config/expense/list.html',
        controller: 'typeExpensesController'
    })
    .when('/Config/TypeExpense/Add', {
        templateUrl: 'View/Config/expense/Add.html',
        controller: 'typeExpensesController'
    })
    .when('/Config/TypeExpense/Edit:id', {
        templateUrl: 'View/Config/expense/Add.html',
        controller: 'typeExpensesController'
    })//--------------------------------------
    .when('/Config/PaymentForm', {
        templateUrl: 'View/Config/paymentform/list.html',
        controller: 'paymentFormController'
    })
    .when('/Config/PaymentForm/Add', {
        templateUrl: 'View/Config/paymentform/Add.html',
        controller: 'paymentFormController'
    })
    .when('/Config/PaymentForm/Edit:id', {
        templateUrl: 'View/Config/paymentform/Add.html',
        controller: 'paymentFormController'
    })//--------------------------------------
    .when('/Config/TypeCheck', {
        templateUrl: 'View/Config/checks/list.html',
        controller: 'typeCheckController'
    })
    .when('/Config/TypeCheck/Add', {
        templateUrl: 'View/Config/checks/Add.html',
        controller: 'typeCheckController'
    })
    .when('/Config/TypeCheck/Edit:id', {
        templateUrl: 'View/Config/checks/Add.html',
        controller: 'typeCheckController'
    })//--------------------------------------
    .when('/Config/Banks', {
        templateUrl: 'View/Config/banks/list.html',
        controller: 'bankController'
    })
    .when('/Config/Banks/Add', {
        templateUrl: 'View/Config/banks/Add.html',
        controller: 'bankController'
    })
    .when('/Config/Banks/Edit:id', {
        templateUrl: 'View/Config/banks/Add.html',
        controller: 'bankController'
    })//--------------------------------------
 /*    .otherwise({
        redirectTo: '/'
    });*/
}).config(['$httpProvider', function ($httpProvider) {
    $httpProvider.defaults.headers.common = { 
        'Authorization': 'Basic '+JSON.parse(localStorage.getItem('session'))['header'],
        'Accept': 'application/json;odata=verbose'
      };
}]);
function allowPatternDirective() {
    return {
        restrict: 'A',
        compile: function() {
            return function(scope, element, attrs) {
                element.bind('keypress', function(event) {
                    var keyCode = event.which || event.keyCode;
                    var keyCodeChar = String.fromCharCode(keyCode);
                    if (!keyCodeChar.match(new RegExp(attrs.allowPattern, 'i')) && !(event.keyCode == 8 || event.keyCode == 13 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 39)) {
                        event.preventDefault();
                        return false;
                    }

                });
            };
        }
    };
} //para usar en un input colocar allow-pattern='[jgvJGV0-9\\\-]'
RoutingApp.directive('allowPattern', [allowPatternDirective]); //permite la validacion de campos usando expreciones regulares
