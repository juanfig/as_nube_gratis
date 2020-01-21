// Creación del módulo
var RoutingApp = angular.module('routingProfile', ['ngRoute', 'ui.bootstrap', 'ngCookies']);


// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider 
            .when("/", {
                templateUrl: 'View/list.html',
                controller: 'profileController'
            })
            .when('/Add', {
                templateUrl: 'View/FormProfile.html',
                controller: 'profileController'
            })
            .when('/Edit:id', {
                templateUrl: 'View/FormProfile.html',
                controller: 'profileController'
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
RoutingApp.controller('profileController', function ($scope, $http, $routeParams, $location, $cookies) {

    $scope.topFilter = -1;
    $scope.ModulesToDesactive = [];
    $scope.isUserBasic = null;
    $scope.checkboxModelPermissions = {
        companyTravel : false,
        particularTravel : false,
        expressTravel:false
      };


    $scope.filter =
            [
            { name: "10", id: 10 },
            { name: "20", id: 20 },
            { name: "30", id: 30 },
            { name: "40", id: 40 },
            { name: "50", id: 50 }
        ];




    // LLAMAMOS A EL SERVICIO DE TIPOS DE perfiles //
    $scope.getList = function () {
    //console.log(uri + 'profile');
        $http.get(uri + 'profile').success(function (data) {
            $scope.listProfile = data;
            var length = $scope.listProfile.length;

            /* PAGINADOR */
            $scope.totalItems = $scope.listProfile.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.listProfile.indexOf(value);
                return (begin <= index && index < end);
            };

         }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
         });
    };


    $scope.change = function() {
        $scope.counter++;
      };

    // SERVICIO QUE RETORNA LOS FILTROS //
    $scope.callServiceModules = function () {
        if($routeParams.id!==undefined){
            loading();// Loading
            $http.get(uri + 'profile/find/' + $routeParams.id).success(function (data) {

                $scope.profileNameAdd = data.profile.profileName;
                $scope.profileNameEdit = data.profile.profileName;
               
                if(data.profile.isUserBasic == 1)
                {
                    $scope.isUserBasic = true;
                }else{
                    $scope.isUserBasic = false;
                }

                if(data.profile.permissionConfirmReservaction == 1)
                {
                    $scope.permissionConfirmReservaction = true;
                }else{
                    $scope.permissionConfirmReservaction = false;
                }
                
                if(data.profile.permissionLoadCompanyTravel == 1)
                {
                    $scope.checkboxModelPermissions.companyTravel = true;
                }
                
                if(data.profile.permissionLoadParticularTravel == 1)
                {
                    $scope.checkboxModelPermissions.particularTravel = true;
                }
                if(data.profile.permissionLoadExpressTravel == 1)
                {
                    $scope.checkboxModelPermissions.expressTravel = true;
                }
                

                $scope.idProfile = data.profile.idProfile;
                $scope.active_modules = data.active_modules;
                $scope.inactive_modules = data.inactive_modules;

                loading();// Loading

             }).error(function (data,status) {
                    if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                    else{notificate("Error !"+status," Contacte a Soporte","error");}
                    loading();// Loading
             });
        }
    }



    // SERVICIO QUE BUSCA LOS PERFILES POR FILTRO//
    $scope.callSearchService  = function() {
        // body...

        if ($scope.searchFilter != undefined)
        {

            var filter =
                    {
                        filter:
                                {
                                    searchFilter: $scope.searchFilter,
                                    topFilter: $scope.topFilter
                                }

                    }


            $http.post(uri + 'profile/search', filter).success(function (data) {
                $scope.listProfile = data;
                //alert(JSON.stringify(data));
                /* PAGINADOR */
                $scope.totalItems = $scope.listProfile.length;
                $scope.currentPage = 1;
                $scope.numPerPage = 10;

                $scope.paginate = function (value) {
                    var begin, end, index;
                    begin = ($scope.currentPage - 1) * $scope.numPerPage;
                    end = begin + $scope.numPerPage;
                    index = $scope.listProfile.indexOf(value);
                    return (begin <= index && index < end);
                };

             }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        }
    }




    $scope.saveModules= function () {
        
    };

    
    $scope.addToModule= function (idItem) {

        var profile =
        {
            profile:
                    {
                        idModuleKf: idItem,
                        idProfileKf:$scope.idProfile

                    }
        };

        loading();// Loading
        $http.post(uri + 'profile/addModule', profile).success(function (data) {

            loading();// Loading
            // notificate("Operacion Exitosa",data.response,"success");
            
     
                  $scope.callServiceModules();
  $('#inactives').addClass('in').slideDown('300');
                             $('#actives').addClass('in').slideDown('300');

        }).error(function (data) {
            //alert(data.error);
            loading();// Loading
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
 
    }
    $scope.checkAll = function() {
        angular.forEach($scope.modules, function(item) {
            item.select = $scope.selectAll;
        });
    
      };

    /* ELIMINAR MODULO */
    $scope.deleteModule = function (id) {
        // body...
        var r = confirm("Confirme para desactivar el Modulo!");

        if (r == true)
        {
            loading();// Loading

            var profile =
            {
                profile:
                        {
                            idModuleKf: id,
                            idProfileKf:$routeParams.id

                        }
            };
            $http.post(uri + 'profile/deleteModule',profile).success(function (data) {
                // Actualizamos la tabla
                  $scope.callServiceModules();
                  loading();// Loading

           
  $('#inactives').addClass('in').slideDown('300');
                             $('#actives').addClass('in').slideDown('300');

            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
                  loading();// Loading
            });
        }
    }

    // SERVICIO QUE EDITA Ingreso //
    $scope.updateProfile = function () {
      if($("#profileNameEdit").val() != ""){

        $http.post(uri + 'profile/update', $scope._setProfile()).success(function (data) {
            notificate("Operacion Exitosa",data.response,"success");
        }).error(function (data) {
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
      }else{
        alert("prueba");
      }
    }

    $scope._setProfile = function () {

       var name = ""
      if($scope.idProfile == null)
      {
        name = $("#profileNameAdd").val();
      }else {
        name = $("#profileNameEdit").val();
      }

        var profile =
                {
                    profile:
                            {
                                idProfile: $scope.idProfile,
                                profileName: name,
                            }
                };
        //alert(JSON.stringify(profile));

        return profile;
    }





$scope._setModule = function () {

    var profile =
            {
                profile:
                        {
                            idModuleKf: $scope.idModuleKf,
                            idProfileKf:$scope.idProfile

                        }
            };


    return profile;
};



    // SERVICIO DE AGREGAR PERFIL //
    $scope.addProfile = function () {

      if($("#profileNameAdd").val() != "")
      {

        $http.post(uri +'profile', $scope._setProfile()).success(function (data) {

            notificate("Operacion Exitosa","Perfil Agregado","success");
              $scope.idProfile = data;
                $location.path('Edit'+$scope.idProfile).replace();
              // $location.path(uri + "#/Edit"+$scope.idProfile);
              $scope.callServiceModules();


        }).error(function (data) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });

      }

    };




    $scope.inactiveProfile = function (id) {
        // body...
        var r = confirm("Confirme Inactivar el perfil!");

        if (r == true)
        {
            $http.get(uri + 'profile/inactive/' + id).success(function (data) {
                notificate("Operacion Exitosa","Perfil actualizado","success");
                $scope.getList();// Actualizamos la tabla
            }).error(function (data) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });

        }
    }

    $scope.activeProfile = function (id) {
        // body...
        var r = confirm("Confirme Cambiar el Activar!");

        if (r == true)
        {
            $http.get(uri + 'profile/active/' + id).success(function (data) {
                notificate("Operacion Exitosa","Perfil actualizado","success");
                  $scope.getList(); // Actualizamos la tabla
            }).error(function (data) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });

        }
    }

    $scope.deleteProfile = function (id) {
        // body...
        var r = confirm("Confirme Eliminar el perfil!");

        if (r == true)
        {
            $http.get(uri + 'profile/delete/' + id).success(function (data) {
                notificate("Operacion Exitosa",data.response,"success");
                  $scope.getList(); // Actualizamos la tabla
            }).error(function (data) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });

        }
    }

});
