
// Creación del módulo
var RoutingApp = angular.module('routingPartner', ['ngRoute', 'ui.bootstrap', 'ngCookies']);

RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'View/ListPartner.html',
        controller: 'PartnerController'
    })
    .when("/Add", {
        templateUrl: 'View/FormPartner.html',
        controller: 'PartnerController'
    })
    .when("/Edit:id", {
        templateUrl: 'View/FormPartner.html',
        controller: 'PartnerController'
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

RoutingApp.controller('PartnerController', function ($scope, $http, $routeParams,$location, $cookies) {

    $scope.topFilter = -1;
    $scope.searchFilter = null;
    $scope.disablesalePricePartner = false;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];

    $scope.getListPartner = function () {
        getPartner();
    }



    function getPartner() {

        $http.get(uri + 'Partner').success(function (data)
        {
            $scope.partners = data;
            /* PAGINADOR */
            $scope.totalItems = $scope.partners.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.partners.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.partners = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }


    $scope.getFilter = function() {
        $http.get(uri + 'partner/filterForm').success(function (data) {
            // $scope.clounds = data.clounds;
            $scope.countries = data.countries;
            //$scope.languages = data.language;
        }).error(function (data, status) {

        });
    }

    // $scope.loadClouds = function(id) {
    //         var partner={
    //             partner:{
    //                 idCountryKf: id,
    //             }
    //         }
    //     $http.post(uri + 'Partner/cloudByCountry', partner).success(function(data){
    //         $scope.idCountryKf = data.clouds;
    //         notificate("Exito!");
    //     }).error(function(data){
    //         notificate("Error01");
    //     });
    // }

    $scope.loadClouds = function() {
        $http.get(uri + 'Partner/cloudByCountry').success(function(data){
            $scope.clouds = data.clouds;
            console.log(data.clouds);
        });
    }

/***/


    $scope.addPartner = function (){
      loading();
        $http.post(uri + 'Partner/add', $scope._setPartner()).success(function (data){
            notificate("Operacion Exitosa",data.response,"success");
            loading();
            $location.url('/');
        }).error(function (data) {
            loading();
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };


    $scope.updatePartner = function () {
        loading();
        $http.post(uri + 'Partner/update', $scope._setPartner()).success(function (data) {
            notificate("Operacion Exitosa",data.response,"success");
            loading();
            $location.url('/');
        }).error(function (data) {
            loading();
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };

    $scope.deletePartner = function (id) {

        var r = confirm("¿En realidad desea eliminar este Partner?");
        if (r == true)
        {

            var partner  = {
                partner:{
                    idPartner: id,
                }
            }
            $http.post(uri + 'Partner/delete/',partner).success(function (data) {
                notificate("Operacion Exitosa", "Partner Eliminado" ,"success");
              $scope.getListPartner();
                 $scope.getListPartner.splice(id, 1);
            }).error(function (data) {
                alert(data.error);
            });
        }
    };

    $scope.searchPartner = function () {
        callSearchPartner()
    }

    function callSearchPartner()
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

        $http.post(uri + 'Partner/search', filter).success(function (data) {
            $scope.partners = data;
            /* PAGINADOR */
            $scope.totalItems = $scope.partners.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 50;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.partners.indexOf(value);
                return (begin <= index && index < end);
            };
        }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }

    $scope.callPartnerById = function() {
        if ($routeParams.id) {
            $http.get(uri + 'Partner/find/'+ $routeParams.id).success(function (data) {
                console.log(data);
                $scope.idUser = data.idUser;
                $scope.idPartner = data.idPartner;
                $scope.firstnamePartner = data.firstnamePartner;
                $scope.lastnamePartner = data.lastnamePartner;
                $scope.dniPartner = data.dniPartner;
                $scope.picturePartner = data.pictureSelle;
                $scope.phonePartner = data.phonePartner;
                $scope.passwordPartner = null;
                $scope.idCloudKf = data.idCloudKf;
                $scope.idCountryKf = data.idCountryKf;

                $scope.emailPartner = data.emailPartner;
                $scope.csalesPartner = data.csalesPartner;
                $scope.ccreditsPartner = data.ccreditsPartner;
                $scope.statusPartner = data.statusPartner;
                $scope.currentAccountLimit = parseFloat(data.currentAccountLimit);
            });
        }else{
            $scope.idPartner = null;
            $scope.idUser = JSON.parse(localStorage.getItem('session')).idUser;
        }
    }


    $scope._setPartner = function (){
        var Partner = {

            partner:
            {
                idPartner: $routeParams.id,
                idUserKf: $scope.idUser,
                firstnamePartner: $scope.firstnamePartner,
                lastnamePartner: $scope.lastnamePartner,
                emailPartner: $scope.emailPartner,
                dniPartner: $scope.dniPartner,
                phonePartner: $scope.phonePartner,
                idCloudKf: $scope.idCloudKf,
                passPartner: $scope.passPartner,
                idCountryKf: $scope.idCountryKf,
                currentAccountLimit: $scope.currentAccountLimit,


            }
        };

        return Partner;
    }

    $scope.changeStatusPartner = function (id, status) {
        var r = confirm("¿Quiere cambiar el status del Partner?");
        if (r == true)
        {
            var partner  = {
                partner:{
                    idPartner: id,
                    idStatus: status
                }
            }
            $http.post(uri + 'Partner/changeStatus', partner).success(function (data) {
                notificate("Operacion Exitosa",data.response,"success");
                getPartner() // Actualizamos la tabla
            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        }
    }

});