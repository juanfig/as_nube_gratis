
// Creación del módulo
var RoutingApp = angular.module('routingCloud', ['ngRoute', 'ui.bootstrap', 'ngCookies']);

RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'View/ListCloud.html',
        controller: 'CloudController'
    })
    .when("/Add", {
        templateUrl: 'View/FormCloud.html',
        controller: 'CloudController'
    })
    .when("/Edit:id", {
        templateUrl: 'View/FormCloud.html',
        controller: 'CloudController'
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

RoutingApp.controller('CloudController', function ($scope, $http, $routeParams,$location, $cookies) {

    $scope.topFilter = -1;
    $scope.searchFilter = null;
    $scope.disablesalePriceCloud = false;
    $scope.profile = JSON.parse(localStorage.getItem('session')).idProfileUser;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];

    $scope.getListCloud = function () {
        getCloud();
    }



    function getCloud() {

        $http.get(uri + 'Cloud').success(function (data)
        {
            $scope.clouds = data;
            /* PAGINADOR */
            $scope.totalItems = $scope.clouds.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.clouds.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.clouds = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }


    $scope.getFilter = function() {
        $http.get(uri + 'cloud/filter').success(function (data)
        {
            $scope.countrys = data.country;
            $scope.languages = data.language;
        }).error(function (data, status) {

        });
    }


    $scope.addCloud = function (){
        $http.post(uri + 'Cloud/add', $scope._setCloud()).success(function (data){
            notificate("Operacion Exitosa",data.response,"success");
            
            $location.url('/');
        }).error(function (data) {

            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };


    $scope.updateCloud = function () {

        $http.post(uri + 'Cloud/update', $scope._setCloud()).success(function (data) {
            notificate("Operacion Exitosa",data.response,"success");
            
            $location.url('/');
        }).error(function (data) {

            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };


    $scope.deleteCloud = function (id) {

        var r = confirm("¿En realidad desea eliminar este Servicio?");
        if (r == true)
        {
            $http.delete(uri + 'Cloud/delete/' + id).success(function (data) {
                notificate("Operacion Exitosa", "Formato Eliminado" ,"success");
                $scope.getListCloud() // Actualizamos la tabla
            }).error(function (data) {
                alert(data.error);
            });
        }
    };

    $scope.searchCloud = function () {
        callSearchCloud()
    }

    function callSearchCloud()
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

        $http.post(uri + 'Cloud/search', filter).success(function (data) {
            $scope.clouds = data;
            /* PAGINADOR */
            $scope.totalItems = $scope.clouds.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 50;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.clouds.indexOf(value);
                return (begin <= index && index < end);
            };
        }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }

    $scope.callCloudById = function() {
        $http.get(uri + 'Cloud/find/'+ $routeParams.id).success(function (data) {
            $scope.idCloud = data.cloud.idCloud;
            $scope.nameCloud = data.cloud.nameCloud;
            $scope.uri = data.cloud.uri;
            $scope.uriLogo =  data.cloud.uriLogo;
            $scope.idCountryKf =  data.cloud.idCountryKf;
            $scope.idLanguageKf =  data.cloud.idLanguageKf;
            $scope.titleFastTravel =  data.cloud.titleFastTravel;
            $scope.iva =  data.cloud.iva;


            if(data.cloud.activateReport == 1){
                $scope.activateReport = true;
            }

            $scope.loadImg($scope.idCloud);
        });
    }


    $scope._setCloud = function (){
        var data =
        {
            cloud:
            {
                idCloud: $scope.idCloud,
                nameCloud: $scope.nameCloud,
                uri: $scope.uri,
                uriLogo: $scope.uriLogo,
                idCountryKf: $scope.idCountryKf,
                idLanguageKf: $scope.idLanguageKf,
                activateReport: $scope.activateReport,
                titleFastTravel: $scope.titleFastTravel,
                iva: $scope.iva
            }
        };

        return data;
    }

    $scope.changueStatus = function (id, status) {
        var r = confirm("¿Quiere cambiar el status del Cloudo?");
        if (r == true)
        {
            var cloud  = {
                cloud:{
                    idCloud: id,
                    idStatuCloud: status
                }
            }
            $http.post(uri + 'Cloud/changueStatus', cloud).success(function (data) {
                notificate("Operacion Exitosa",data.response,"success");
                getCloud() // Actualizamos la tabla
            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        }
    }

});

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
