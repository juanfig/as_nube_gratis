
// Creación del módulo
var RoutingApp = angular.module('routingSupport', ['ngRoute', 'ui.bootstrap', 'ngCookies', 'ngSanitize']);


// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'View/list.html',
        controller: 'supportController'
    })
    .when('/Add', {
        templateUrl: 'View/Add.html',
        controller: 'supportController'
    })
    .when('/Edit:id', {
        templateUrl: 'View/Add.html',
        controller: 'supportController'
    })
    .when('/Agency/Details:id', {
        templateUrl: 'View/AgencyDetails.html',
        controller: 'supportController'
    })
    .when('/Details:id', {
        templateUrl: 'View/Details.html',
        controller: 'supportController'
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
RoutingApp.controller('supportController', function ($scope, $http, $routeParams,$location, $cookies) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
    $scope.topFilter = -1;
    $scope.statusFilter = null;
    $scope.searchFilter = null;
    $scope.idAgencyFilter = null;
    $scope.disableConfiguration = false;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];

    $scope.getListSupport = function () {
        getSupport();
    }

    $scope.getListAgencys = function () {
        getAgencys();
    }

    function getAgencys(){
        $http.get(uri + 'agency').success(function (data)
        {
            $scope.agencys = data;
            $scope.ags = data;            
            /* PAGINADOR */
            $scope.totalItems = $scope.agencys.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 8;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.agencys.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.ag = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}

            // $scope.supports = [];
            // notificate( data.error ,"","error");
        });
    }

    function getSupport() {
        $http.get(uri + 'support/agencyDetail/'+ $routeParams.id).success(function (data)
        {
            $scope.supports = data;
            $scope.idAgencyFilter=$routeParams.id;
            /* PAGINADOR */
            $scope.totalItems = $scope.supports.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.supports.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.supports = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}

            // $scope.supports = [];
            // notificate( data.error ,"","error");
        });
    }

    $scope.getHistorySupport = function () {
    
            $http.get(uri + 'support/supportMessages/'+ $routeParams.id).success(function (data)
            {
                $scope.supportHistory = data.support;
                $scope.fullNameSupport = data.support.fullNameUser;
                /* PAGINADOR */
         
            }).error(function (data, status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.supports = []}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
    
                // $scope.supports = [];
                // notificate( data.error ,"","error");
            });
    
    
    }

    $scope.getfilterList = function () {
    
            $http.get(uri + 'support/filterList').success(function (data)
            {
                $scope.agencys = data.agencys;
                /* PAGINADOR */
         
            }).error(function (data, status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.supports = []}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
    
                // $scope.supports = [];
                // notificate( data.error ,"","error");
            });
    
    
    }

    $scope.changeStatus = function (obj, status) {

        var support = {
            support:{
                'idSupport': obj.idSupport,
                'statusSupport': status
            }
        }

        $http.post(uri + 'support/changeStatus', support).success(function (data) {

            $scope.searchSupport();
            if(status == -1){
                $scope.sendMail(obj)
            }
        }).error(function (data) {
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };

    $scope.sendMail = function(support){
        var mail = {
            mail: support
        }
        $http.post(uri + 'mail/sendMailSupport', support).success(function (data) {
            notificate("Operacion Exitosa",data.response,"success");
            $location.url('FormSupport');
        }).error(function (data) {
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    }

    $scope.deleteSupport = function (id) {

        var r = confirm("¿En realidad desea eliminar este Servicio?");
        if (r == true)
        {
            $http.delete(uri + 'support/delete/' + id).success(function (data) {
                notificate("Operacion Exitosa", "Formato Eliminado" ,"success");
                $scope.getListSupport() // Actualizamos la tabla
            }).error(function (data) {
                alert(data.error);
            });
        }
    };

    $scope.searchSupport = function () {
        callSearchSupport()
    }

    function callSearchSupport()
    {
        if( $scope.topFilter == null)
        {
            $scope.topFilter = -1;
        }
        if ($scope.searchFilter == null)
        {
            $scope.searchFilter == null;
        }

        if( $scope.statusFilter == "")
        {
            $scope.statusFilter = null;
        }

        if( $scope.idAgencyFilter == "")
        {
            $scope.idAgencyFilter = null;
        }

        var filter =
        {
            filter:
            {
                searchFilter: $scope.searchFilter,
                topFilter: $scope.topFilter,
                idAgencyFilter: $scope.idAgencyFilter,
                statusFilter: $scope.statusFilter,
               
            }

        }

        $http.post(uri + 'support/search', filter).success(function (data) {
            $scope.supports = data;
            /* PAGINADOR */
            $scope.totalItems = $scope.supports.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.supports.indexOf(value);
                return (begin <= index && index < end);
            };
        }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }

    $scope.callSupportById = function() {

        $http.get(uri + 'support/find/'+ $routeParams.id).success(function (data) {
            $scope.codeSupport = data.support.codeSupport;
            $scope.attach = data.support.attach;
            $scope.idSupport = data.support.idSupport;
            $scope.detailSupport = data.support.detailSupport;
            $scope.statusSupport = data.support.statusSupport;
            $scope.idAgencyKf = data.support.idAgencyKf;
            $scope.idUserAgencyKf = data.support.idUserAgencyKf;
            $scope.emailResponSupport = data.support.emailResponSupport;
            $scope.emailTwoResponSupport = data.support.emailTwoResponSupport;
            $scope.reasonSupport = data.support.reasonSupport;
            $scope.descriptionFinalizedSupport = data.support.descriptionFinalizedSupport;
            $scope.isTravelSendMovil = data.support.isTravelSendMovil;
            $scope.dateCreatedSupport = data.support.dateCreatedSupport;
            $scope.dateDevelopmentSupport = data.support.dateDevelopmentSupport;
            $scope.dateFinalizedSupport = data.support.dateFinalizedSupport;

        });
    }

    $scope.addSupportMessage = function () {
        
        
                $http.post(uri + 'Support/addMessage',$scope._setSupportMessage()).success(function (data) {

                    $scope.getHistorySupport();
                 $scope.supportMessage="";
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };




    $scope._setSupportMessage = function (){
        var data =
            {
                support_message:
                {
                supportMessage:$scope.supportMessage,
                idAgencyKf: $scope.idAgencyKf,
                idUserKf: $scope.idUserKf,
                idSupportKf: $routeParams.id,
                },
                support_message_email:
                {
                    fullNameSupport:$scope.fullNameSupport,
                    codeSupport:$scope.codeSupport,
                    supportMessage:$scope.supportMessage,
                    idAgencyKf: $scope.idAgencyKf,
                    idUserKf: $scope.idUserKf,
                    idSupportKf: $routeParams.id,
                    emailResponSupport : $scope.emailResponSupport,
                    emailTwoResponSupport : $scope.emailTwoResponSupport
                }
            };

        return data;
    }


    $scope._setSupport = function (){
        var data =
        {
            support:
            {
                idSupport: $scope.idSupport,
                detailSupport: $scope.detailSupport,
                statusSupport: -1,
                idAgencyKf: $scope.idAgencyKf,
                idUserAgencyKf: $scope.idUserAgencyKf,
                emailResponSupport: $scope.emailResponSupport,
                emailTwoResponSupport: $scope.emailTwoResponSupport,
                reasonSupport: $scope.reasonSupport,
                descriptionFinalizedSupport: $scope.descriptionFinalizedSupport,
                isTravelSendMovil: $scope.isTravelSendMovil,
                dateCreatedSupport: $scope.dateCreatedSupport,
                dateDevelopmentSupport: $scope.dateDevelopmentSupport,
                dateFinalizedSupport: $scope.dateFinalizedSupport,
            }
        };

        return data;
    }

    $scope.updateSupport = function () {

        $http.post(uri + 'support/update', $scope._setSupport()).success(function (data) {

            // $scope.sendMail($scope._setSupport())
            $location.url('Agency/Details'+$scope.idAgencyKf);
        }).error(function (data) {
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
        
    };

    $scope.searchAgency = function () {
        callsearchAgency()
    }

    function callsearchAgency()
    {
        if( $scope.topFilter == null)
        {
            $scope.topFilter = -1;
        }
        if ($scope.searchFilter == null)
        {
            $scope.searchFilter == null;
        }

        if( $scope.statusFilter == "")
        {
            $scope.statusFilter = null;
        }

        if( $scope.idAgencyFilter == "")
        {
            $scope.idAgencyFilter = null;
        }

        var filter =
        {
            filter:
            {
                searchFilter: $scope.searchFilter,
                topFilter: $scope.topFilter,
                idAgencyFilter: $scope.idAgencyFilter,
                statusFilter: $scope.statusFilter,
               
            }

        }

        $http.post(uri + 'agency/searchAgency', filter).success(function (data) {
            $scope.agencys = data;
            /* PAGINADOR */
            $scope.totalItems = $scope.agencys.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 8;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.agencys.indexOf(value);
                return (begin <= index && index < end);
            };
        }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }

});