
// Creación del módulo
var RoutingApp = angular.module('routingCredits', ['ngRoute', 'ui.bootstrap', 'ngCookies', 'ngSanitize']);


RoutingApp.directive('numbersOnly', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                if (text) {
                    var transformedInput = text.replace(/[^0-9]/g, '');

                    if (transformedInput !== text) {
                        ngModelCtrl.$setViewValue(transformedInput);
                        ngModelCtrl.$render();
                    }
                    return transformedInput;
                }
                return undefined;
            }            
            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});


RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'View/list.html',
        controller: 'creditsController'
    })
    .when('/add', {
        templateUrl: 'View/add.html',
        controller: 'creditsController'
    })
    .when('/edit:id', {
        templateUrl: 'View/add.html',
        controller: 'creditsController'
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
RoutingApp.controller('creditsController', function ($scope, $http, $routeParams,$location, $cookies) {


$scope.getFilter = function() {
    $http.get(uri + 'credit/filter').success(function (data)
    {
        $scope.agencys = data.agencys;

    }).error(function (data, status) {

    });
}


    
 $scope.topFilter = -1;
    $scope.searchFilter = null;
    var changueCountry = false;
    $scope.filter =
            [
            { name: "10", id: 10 },
            { name: "20", id: 20 },
            { name: "30", id: 30 },
            { name: "40", id: 40 },
            { name: "50", id: 50 }
        ];


    // LLAMAMOS A EL SERVICIO DE ABONOS //
    $scope.getListCredits = function () {

      var ur = 'credit';

      if ($routeParams.id != null)
      {
        ur = 'credit/find/' + $routeParams.id;
      }

        $http.get(uri + ur).success(function (data) {



            if ($routeParams.id != null)
            {
                $scope.getFilter(data);
            }else {
              $scope.listCredits = data;
              /* PAGINADOR */
              $scope.totalItems = $scope.listCredits.length;
              $scope.currentPage = 1;
              $scope.numPerPage = 25;
              $scope.paginate = function (value) {
                  var begin, end, index;
                  begin = ($scope.currentPage - 1) * $scope.numPerPage;
                  end = begin + $scope.numPerPage;
                  index = $scope.listCredits.indexOf(value);
                  return (begin <= index && index < end);
              };
            }


         }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }

    $scope.updateCredit = function () {

        $http.post(uri + 'credit/update', $scope._setCredit()).success(function (data) {
            notificate("Operacion Exitosa",data.response,"success");


                  $location.url('/');

          

        }).error(function (data) {

            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });

    };

    $scope.searchCredits = function () {
        callSearchCredits();
    }


    $scope.callCreditById = function() {
        $http.get(uri + 'credit/find/'+ $routeParams.id).success(function (data) {

                $scope.idCredit= data.idCredit,
                $scope.nameCredit= data.nameCredit,
                $scope.valueCredit= data.valueCredit,
                $scope.periodCredit= data.periodCredit,
                $scope.descriptionCredit= data.descriptionCredit
               // $scope.idAgencyFk= data.idAgencyFk
        });
    }

    function callSearchCredits()
    {
        if( $scope.topFilter == null)
        {
            $scope.topFilter = -1;
        }
        if ($scope.searchFilter == null)
        {
            $scope.searchFilter == null;
        }

        var filter =
        {
            filter:
            {
                searchFilter: $scope.searchFilter,
                topFilter: $scope.topFilter
            }

        }

        $http.post(uri + 'credit/search', filter).success(function (data) {
         
            $scope.listCredits = data;
            /* PAGINADOR */
            $scope.totalItems = $scope.listCredits.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 50;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.listCredits.indexOf(value);
                return (begin <= index && index < end);
            };
        }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }


    $scope.addCredit = function (){
        $http.post(uri + 'credit/add', $scope._setCredit()).success(function (data){
            notificate("Operacion Exitosa",data.response,"success");
          
                  $location.url('/');

        }).error(function (data) {

            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };

       $scope._setCredit = function () {
        var credit ={

          credit:
                {
                idCredit:$scope.idCredit,
                nameCredit:$scope.nameCredit,
                valueCredit:$scope.valueCredit,
                periodCredit:$scope.periodCredit,
                descriptionCredit:$scope.descriptionCredit
                //idAgencyFk:$scope.idAgencyFk,
              }
          };


        return credit;
    }


    $scope.ejecutarAbonosMensuales = function () {
        $http.get(uri+'credit/ejecutarMensuales').success(function (data) {
            notificate("Operacion Exitosa","Abonos mensuales agregados","success");

         }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });

    }


});