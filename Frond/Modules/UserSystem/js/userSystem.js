
// Creación del módulo
var RoutingApp = angular.module('routingUserSystem', ['ngRoute', 'ui.bootstrap', 'ngCookies']);


// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider
            .when("/", {
                templateUrl: 'View/list.html',
                controller: 'userSystemController'
            })
            .when('/Add', {
                templateUrl: 'View/Add.html',
                controller: 'userSystemController'
            })
            .when('/Edit:id', {
                templateUrl: 'View/Add.html',
                controller: 'userSystemController'
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

RoutingApp.directive('googlePlace', directiveFunction);

directiveFunction.$inject = ['$rootScope'];

// DIRECTIVA PARA ACTIVAR EL INOPUT DE LA UBICACION 
function directiveFunction($rootScope) {
    return {
        require: 'ngModel',
        scope: {
            ngModel: '=',
            details: '=?'
        },
        link: function (scope, element, attrs, model) {
            var options = {
                types: ['geocode'],
                componentRestrictions: {}
            };
            scope.gPlace = new google.maps.places.Autocomplete(element[0], options);

            google.maps.event.addListener(scope.gPlace, 'place_changed', function () {
                scope.$apply(function () {
                    scope.details = scope.gPlace.getPlace();
                    model.$setViewValue(element.val());
                    $rootScope.$broadcast('place_changed', scope.details);
                });
            });



        }
    };
}

/*********************************************************/
/*****************   MODULO DE BASE **********************/
RoutingApp.controller('userSystemController', function ($scope, $http, $routeParams, $location, $cookies) {

    $("#schedule").hide();
    var country;
    var changueCountry = false;
    $scope.topFilter = -1; // Filtro Inicializado

    //console.log(JSON.parse($cookies.get("user")))

    $scope.$on('place_changed', function (e, place) {
        country = place;
        changueCountry = true;
    });

    $scope.getList = function () {
        
        get();
    };

    function get() {
        //console.log(uri + 'userSystem');
        $http.get(uri+'userSystem').success(function (data) {
            $scope.listUserSystem = data;
            //console.log(JSON.stringify(data));
            /* PAGINADOR */
            $scope.totalItems = $scope.listUserSystem.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 10;

            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.listUserSystem.indexOf(value);
                return (begin <= index && index < end);
            };

        }).error(function (data) {
            //alert(data.error);
			notificate( data.error ,"","error");
        });
    }
    ;

    // SERVICIO QUE BUSCA LOS USUARIOS POR FILTRO//
    $scope.searchUserSystem = function () {
        // body...
        callSearchService();
    }


    function callSearchService() {
        // body...
        
        if ($scope.searchFilter != undefined)
        {

            var filter =
                    {
                        filter:
                                {
                                    searchFilter: $scope.searchFilter,
                                    topFilter: $scope.topFilter,
                                }

                    }

            $http.post(uri + 'userSystem/search', filter).success(function (data) {
                $scope.listUserSystem = data;

                /* PAGINADOR */
                $scope.totalItems = $scope.listUserSystem.length;
                $scope.currentPage = 1;
                $scope.numPerPage = 10;

                $scope.paginate = function (value) {
                    var begin, end, index;
                    begin = ($scope.currentPage - 1) * $scope.numPerPage;
                    end = begin + $scope.numPerPage;
                    index = $scope.listUserSystem.indexOf(value);
                    return (begin <= index && index < end);
                };

            }).error(function (data) {
                alert(data.error);
            });
        }
    }

    // SERVICIO QUE RETORNA LOS FILTROS //
    $scope.callServiceFilterForm = function () {
        
        var dataFilter;
        
        // LLAMAMOS A EL SERVICIO FILTROS PARA EL FORMULARIO //
        $http.get(uri + 'userSystem/filterForm').success(function (data) {

            dataFilter = data;

            if ($routeParams.id)
            {
                callUserSystemById($routeParams.id, dataFilter);
            } else
            {
                
                $scope.filterForm = dataFilter;
               
                $scope.isMonday = false;
                $scope.isTuesday = false;
                $scope.isWednesday = false;
                $scope.isThursday = false;
                $scope.isFriday = false;
                $scope.isSaturday = false;
                $scope.isSunday = false;
                
            }



        }).error(function (data) {
            alert(data.error);
        });
    }

    // LLAMAMOS A EL SERVICIO QUE RETORNA UN USUARIO POR IDUSERCOMPANY //
    function callUserSystemById(id, dataFilter) {

        $http.get(uri + 'userSystem/find/' + id).success(function (data) {
            
            $scope.filterForm = dataFilter;
            $scope.idUser = data.usersystem.idUser;
            $scope.firstNameUser = data.usersystem.firstNameUser;
            $scope.lastNameUser = data.usersystem.lastNameUser;
            $scope.emailUser = data.usersystem.emailUser;
            $scope.phoneUserSystem = data.usersystem.phoneUserSystem;
            $scope.isEditUser = false;
            country = data.usersystem.country;
            $scope.idProfileUser = data.usersystem.idProfileUser;
            $scope.idUserKf = data.usersystem.idUserKf;
            $("#addresUserSystem").val(data.usersystem.addresUserSystem).trigger("change");
            
            if($scope.idProfileUser == 6){
                
                $('#hourStart').val(data.usersystem.hourStart);
                $('#hourEnd').val(data.usersystem.hourEnd);

                if(data.usersystem.isMonday == 1){
                    $scope.isMonday = true;
                }else{
                    $scope.isMonday = false;
                }
                
                if(data.usersystem.isTuesday == 1){
                    $scope.isTuesday = true;
                }else{
                    $scope.isTuesday = false;
                }
                
                if(data.usersystem.isWednesday == 1){
                    $scope.isWednesday = true;
                }else{
                    $scope.isWednesday = false;
                }
                
                if(data.usersystem.isThursday == 1){
                    $scope.isThursday = true;
                }else{
                    $scope.isThursday = false;
                }
                
                if(data.usersystem.isFriday == 1){
                    $scope.isFriday = true;
                }else{
                    $scope.isFriday = false;
                }
                
                if(data.usersystem.isSaturday == 1){
                    $scope.isSaturday = true;
                }else{
                    $scope.isSaturday = false;
                }
                
                if(data.usersystem.isSunday == 1){
                    $scope.isSunday = true;
                }else{
                    $scope.isSunday = false;
                }
                
                $("#schedule").show();
                    
            }

        }).error(function (data) {
            //alert(data.error);
        });

    }

    // SERVICIO DE AGREGAR USUARIO EMPRESA //
    $scope.addUserSystem = function () {
        
        var correo = false;

        var length = $scope.filterForm.user.length;
        for (var i = 0; i < length; i++) {
            if ($scope.filterForm.user[i].emailUser == $scope.emailUser) {
                correo = true;
            }
        }
        
        
        if ($("#passClientNew").val() != "" &&  correo == false)
        {
            $http.post(uri + 'userSystem', $scope._setUserSystem()).success(function (data) {
                //alert(data.response);
				notificate("Operacion Exitosa", data.response ,"success");
                $location.url('List');
            }).error(function (data) {
                //alert(data.error);
				notificate("Error !" + data.error ," Contacte a Soporte","error");
            });
        } else {
            if ($("#passClientNew").val() == "") {
                $("#spanPass").show();
                $("#passClientNew").focus();
            }else{
                $("#spanPass").hide();
            }
            if (correo) {
                $("#spanEmail").show();
                $("#emailUser").focus();
            } else {
                $("#spanEmail").hide();
            }
        }

    };

    // SERVICIO QUE EDITA USUARIO EMPRESA //
    $scope.updateUserSystem = function () {
        console.log("entro");
        $http.post(uri + 'userSystem/update', $scope._setUserSystemUpdate()).success(function (data) {
            //alert(data.response);
			notificate("Operacion Exitosa", data.response ,"success");
            $location.url('List');
        }).error(function (data) {
            //alert(data.error);
			notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    }

    $scope._setUserSystem = function () {

        var countryName;
        var lat;
        var lng;

        if ($routeParams.id != null) {

            if (changueCountry)
            {
                lat = country.geometry.location.lat();
                lng = country.geometry.location.lng();
                countryName = country.formatted_address;
            } else
            {
                lat = country.addresLatUserSystem;
                lng = country.addresLongUserSystem;
                countryName = country.addresUserSystem;
            }

        } else
        {
            lat = country.geometry.location.lat();
            lng = country.geometry.location.lng();
            countryName = country.formatted_address;
        }

        var user =
                {
                    user:
                            {
                                
				country:
				{
					addresUserSystem : countryName,
					addresLatUserSystem : lat,
					addresLongUserSystem : lng,
				},

                                firstNameUser: $scope.firstNameUser,
                                lastNameUser: $scope.lastNameUser,
                                phoneUserSystem: $scope.phoneUserSystem,
                                isEditUser: $scope.isEditUser,
                                //addresUserSystem:$scope.addresUserSystem,
                                idProfileUser: $scope.idProfileUser,
                                emailUser: $scope.emailUser,
                                passUser: $scope.passUser,
                                
                                hourStart: $("#hourStart").val(),
                                hourEnd: $("#hourEnd").val(),
                                isMonday: $scope.isMonday,
                                isTuesday: $scope.isTuesday,
                                isWednesday: $scope.isWednesday,
                                isThursday: $scope.isThursday,
                                isFriday: $scope.isFriday,
                                isSaturday: $scope.isSaturday,
                                isSunday: $scope.isSunday

                            }
                };


        return user;
    }
    
    $scope._setUserSystemUpdate = function () {

        var countryName;
        var lat;
        var lng;

        if ($routeParams.id != null) {

            if (changueCountry)
            {
                lat = country.geometry.location.lat();
                lng = country.geometry.location.lng();
                countryName = country.formatted_address;
            } else
            {
                lat = country.addresLatUserSystem;
                lng = country.addresLongUserSystem;
                countryName = country.addresUserSystem;
            }

        } else
        {
            lat = country.geometry.location.lat();
            lng = country.geometry.location.lng();
            countryName = country.formatted_address;
        }

        var user =
                {
                    user:
                            {
                                idUser: $routeParams.id,
				country:
				{
                                    addresUserSystem:countryName,
                                    addresLatUserSystem:lat,
                                    addresLongUserSystem:lng,
				},

                                firstNameUser: $scope.firstNameUser,
                                lastNameUser: $scope.lastNameUser,
                                phoneUserSystem: $scope.phoneUserSystem,
                                isEditUser: $scope.isEditUser,
                                //addresUserSystem:$scope.addresUserSystem,
                                idProfileUser: $scope.idProfileUser,
                                emailUser: $scope.emailUser,
                                passUser: $scope.passUser,
                                
                                hourStart: $("#hourStart").val(),
                                hourEnd: $("#hourEnd").val(),
                                isMonday: $scope.isMonday,
                                isTuesday: $scope.isTuesday,
                                isWednesday: $scope.isWednesday,
                                isThursday: $scope.isThursday,
                                isFriday: $scope.isFriday,
                                isSaturday: $scope.isSaturday,
                                isSunday: $scope.isSunday

                            }
                };


        return user;
    }

    $scope.inactive = function (id) {
        // body...
        var r = confirm("Confirme Para Inactivar el Usuario!");

        if (r == true)
        {
            $http.get(uri + 'userSystem/inactive/' + id).success(function (data) {
                get() // Actualizamos la tabla
            }).error(function (data) {
                alert(data.error);
            });
        }



    }

    $scope.delete = function (id) {
        // body...
        var r = confirm("Confirme Para eliminar el Usuario!");

        if (r == true)
        {
            $http.delete(uri + 'userSystem/delete/' + id).success(function (data) {
                get() // Actualizamos la tabla
            }).error(function (data) {
                alert(data.error);
            });
        }
    }
    
    $scope.typeUser = function () {
        
        if ($scope.idProfileUser == 6) {
            $("#schedule").show();
        } else {
            $("#schedule").hide();
        }
    };

    $scope.getProfiles = function () {

       // console.log(uri + 'profile');
        $http.get(uri + 'profile/index').success(function (data) {
            $scope.listProfile = data;
            var length = $scope.listProfile.length;
console.log(data)
         }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
         });
    };

    $scope.addUser = function (){
      loading();
        $http.post(uri + 'userSystem/add', $scope._setUserSystemAdd()).success(function (data){
            notificate("Operacion Exitosa",data.response,"success");
            loading();
            $location.url('/');
        }).error(function (data,status) {
          loading();
          if(status == 501){notificate("Información","No se agrego el usuario.","info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }


    $scope._setUserSystemAdd = function () {

      var user ={

        user:
        {
          userNameUser: $scope.userNameUser,
          emailUser: $scope.emailUser,
          passUser: $scope.passUser, 
          idProfileUser:$scope.idProfileUser,     
        }
      };

     // console.log(JSON.stringify(user));
      return user;
    }


    $scope.getUserprofilename= function () {
      for (let i=0;i<$scope.listProfile.length;i++) {
        if ($scope.idProfileUser == $scope.listProfile[i].idProfile) return $scope.listProfile[i].profileName;
      }
    }

    $scope.getUserId= function () {

        if($routeParams.id!==undefined){
            //console.log(uri + 'userSystem/find/' + $routeParams.id);
            $http.get(uri + 'userSystem/find/' + $routeParams.id).success(function (data) { 
                $scope.usuario=data.usersystem;
                $scope.idUser=$scope.usuario.idUser;
                $scope.userNameUser=$scope.usuario.userNameUser;
                $scope.emailUser=$scope.usuario.emailUser;
                //$scope.passUser=$scope.usuario.passUser;
                $scope.idProfileUser=$scope.usuario.idProfileUser;
                 //console.log(JSON.stringify(data));
            }).error(function (data,status) {

              if(status == 501){notificate("Información","Problema al cargar los datos.","info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}         
            });
        }
    }

    $scope.changueStatus = function (id, status) {
        var r = confirm("¿Quiere cambiar el status del Usuario?");
        if (r == true)
        {
            var user  = {
                user:{
                    idUser: id,
                    idStatusUser: status
                }
            }
            //console.log(JSON.stringify(user));
            $http.post(uri + 'userSystem/changueStatusUser', user).success(function (data) {
                notificate("Operacion Exitosa",data.response,"success");
                get();// Actualizamos la tabla
            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        }
    }

    $scope.updateUser= function () {
        loading();
        $http.post(uri + 'userSystem/updateUser', $scope._setUserSystemUpd()).success(function (data) {
            loading();
            notificate("Operacion Exitosa", data.response ,"success");
            $location.url('/');
        }).error(function (data) {
             loading();
             if(status == 501){notificate("Información","No se modifico el usuario.","info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }


    $scope._setUserSystemUpd = function () {
      var pass="";
      if($scope.isEditUser){
        pass=$scope.passUser;
      }else{
        $scope.isEditUser=false;
      }



      var user ={

        user:
        {
           idUser:$scope.idUser,
          userNameUser: $scope.userNameUser,
          emailUser: $scope.emailUser,
          passUser:pass, 
          idProfileUser:$scope.idProfileUser, 
          isEditUser: $scope.isEditUser    
        }
      };

      console.log(JSON.stringify(user));
      return user;
    }



});