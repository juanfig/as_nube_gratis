// Creación del MODELO
var RoutingApp = angular.module('routingAgency', ['ngRoute', 'ui.bootstrap', 'ngCookies']);
// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

  $routeProvider
  .when("/", {
    templateUrl: 'View/list.html',
    controller: 'agencyController'
  })
  .when('/Edit:id', {
    templateUrl: 'View/dhboard.html',
    controller: 'agencyController'
  })

  .when("/Add", {
    templateUrl: 'View/Form.html',
    controller: 'agencyController'
  })

  .when("/Params", {
    templateUrl: 'View/Params.html',
    controller: 'agencyController'
  })

  .when("/State:id", {
    templateUrl: 'View/estadoCuenta.html',
    controller: 'agencyController'
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
/*****************   CONTROLADOR   **********************/
RoutingApp.controller('agencyController', function ($scope, $http, $routeParams,$filter,$location,$uibModal) {
  var country;
  var changueCountry = false;
  $scope.clouds =[];
  $scope.profile = JSON.parse(localStorage.getItem('session')).idProfileUser;
  $scope.filter =
  [
  { name: "10", id: 10 },
  { name: "20", id: 20 },
  { name: "30", id: 30 },
  { name: "40", id: 40 },
  { name: "50", id: 50 }
  ];


    // LLAMAMOS A EL SERVICIO DE CLIENTES //
    $scope.getList = function () {

      var ur = 'agency';

      if ($routeParams.id != null)
      {
        ur = 'agency/find/' + $routeParams.id;
      }

      $http.get(uri + ur).success(function (data) {



        if ($routeParams.id != null)
        {
          $scope.getFilter(data);
          $scope.agency = data[0];
        }else {
          $scope.listAgnecys = data;
          /* PAGINADOR */
          $scope.totalItems = $scope.listAgnecys.length;
          $scope.currentPage = 1;
          $scope.numPerPage = 25;
          $scope.paginate = function (value) {
            var begin, end, index;
            begin = ($scope.currentPage - 1) * $scope.numPerPage;
            end = begin + $scope.numPerPage;
            index = $scope.listAgnecys.indexOf(value);
            return (begin <= index && index < end);
          };
        }


      }).error(function (data,status) {
        if(status == 404){notificate("Información "+status,data.error,"info");}
        else{notificate("Error !"+status," Contacte a Soporte","error");}
      });
    }


    $scope.getFilter =  function (agencys) {

      
      $http.get(uri + 'agency/filterForm').success(function (data) {

        $scope.filterForm = data;

        // if(agencys!=undefined){
        //   $scope.nameAgency= agencys.nameAgency;
        //   $scope.phoneAgnecy  = agencys.phoneAgnecy;
        //   $scope.mailAgency  = agencys.mailAgency;
        //   $scope.idCloudKf  = agencys.idCloudKf;
        //   $scope.idCreditFk  = agencys.idCreditFk;
        // }
               
      }).error(function (data,status) {
        if(status == 404){notificate("Información "+status,data.error,"info");}
        else{notificate("Error !"+status," Contacte a Soporte","error");}

      });
    }


    // SERVICIO QUE EDITA USUARIO EMPRESA //
    $scope.updateAgency = function () {
      loading();
      $http.post(uri + 'agency/update', $scope._setAgency()).success(function (data) {
        notificate("Operacion Exitosa", data.response ,"success");
        loading();
        $location.url('/');
      }).error(function (data) {
        loading();
        notificate("Error !" + data.error ," Contacte a Soporte","error");
      });
    }

    $scope.deleteAgency = function (id) {
      loading();
      $http.post(uri + 'agency/delete', { agency: { id:id } }).success(function (data) {
        loading();
        notificate("Operacion Exitosa", data.response ,"success");
        $location.url('/');
      }).error(function (data) {
        loading();
        notificate("Error !" + data.error ," Contacte a Soporte","error");
      });
    }


    $scope.suspenderServicio = function (id, name) {
      $http.post(uri + 'agency/updatePayment/' +id ).success(function (data) {
        var status = {
          status:{
            status:0
          }
        };
        $http.post('https://as-nube.com/'+name+'/Api/index.php/agency/updateparam', status).success(function (data) {
          notificate("Operacion Exitosa", data.response ,"success");
            $scope.getList();
            return;
        }).error(function (data) {
          notificate("Error !" + data.error ," Contacte a Soporte","error");
          $scope.getList();
          return;
        });  
      }).error(function (data) {
        notificate("Error !" + data.error ," Contacte a Soporte","error");
        $scope.getList();
        return;
      });
      $scope.getList();
      }

    $scope.activarServicio = function (id, name2) {
      $http.post(uri + 'agency/updatePayment2/' +id ).success(function (data) {
        var status = {
          status:{
            status:1
          }
        };
        $http.post('https://as-nube.com/'+name2+'/Api/index.php/agency/updateparam', status).success(function (data) {
          notificate("Operacion Exitosa", data.response ,"success");
            $scope.getList();
            return;
        }).error(function (data) {
          notificate("Error !" + data.error ," Contacte a Soporte","error");
          $scope.getList();
          return
        });
      }).error(function (data) {
        notificate("Error !" + data.error ," Contacte a Soporte","error");
        $scope.getList();
        return;
      });
      $scope.getList();
    }

    $scope._setAgency = function () {
      
      console.log($scope.phoneAgnecy);

      console.log($scope.mailAgency);
      var agency ={

        agency:
        {
          phoneAgnecy: $scope.agency.phoneAgnecy,
          mailAgency: $scope.agency.mailAgency,
          idCloudKf: $scope.agency.idCloudKf,
          idCreditFk: $scope.agency.idCreditFk,
          idAgency:$routeParams.id,
          liquidationPercent: parseInt($scope.agency.idCreditFk) == 1 ? $scope.agency.liquidationPercent : 0,
          liquidationFixed: parseInt($scope.agency.idCreditFk) == 2 ? $scope.agency.liquidationFixed : 0
        }
      };


      return agency;
    }

  
    $scope.addAgency = function (){
      loading();
        $http.post(uri + 'agency/add', $scope._setAgencyAdd()).success(function (data){
            notificate("Operacion Exitosa",data.response,"success");
            loading();
            $location.url('/');
        }).error(function (data,status) {
            loading();
          if(status == 501){notificate("Información","Nombre de agencia existente, por favor ingrese otro nombre.","info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
            
        });
    }


    $scope._setAgencyAdd = function () {

      var agency ={

        agency:
        {
          nameAgency: $scope.agency.nameAgency,
          phoneAgnecy: $scope.agency.phoneAgnecy,
          mailAgency: $scope.agency.mailAgency,
          idCloudKf: $scope.agency.idCloudKf,
          idCreditFk: $scope.agency.idCreditFk,
          statusAgency:1,
          liquidationPercent: parseInt($scope.agency.idCreditFk) == 1 ? $scope.agency.liquidationPercent : 0,
          liquidationFixed: parseInt($scope.agency.idCreditFk) == 2 ? $scope.agency.liquidationFixed : 0
        }

      };

  
      return agency;
    }



    $scope.searchAgency = function () {
        callSearchAgency()
    }

    function callSearchAgency()
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


        $http.post(uri + 'agency/search', filter).success(function (data) {
            $scope.listAgnecys = data;

            /* PAGINADOR */
            $scope.totalItems = $scope.listAgnecys.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 50;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.listAgnecys.indexOf(value);
                return (begin <= index && index < end);
            };
        }).error(function (data,status) {
            if(status == 404){notificate("Información "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }



    //Obtener paises 
    $scope.getCountryAddAgency = function() {
        $http.get(uri + 'cloud/filter').success(function (data)
        {
            $scope.countrys = data.country;
            //$scope.languages = data.language;
        }).error(function (data, status) {
            notificate("Problema al cargar los paises",data.error,"info");
        });
    }

    //Obtener nubes
    $scope.getCloudAddAgency = function() {

        $http.get(uri + 'Cloud').success(function (data)
        {
            $scope.cloudsAddAngency = data;
           //console.log(JSON.stringify($scope.cloudsAddAngency));

        }).error(function (data, status) {
            notificate("Problema al cargar las nubes",data.error,"info");
        });
    }

    //Obtener credits
    $scope.getCredit = function() {
        var ur = 'credit';

        $http.get(uri + ur).success(function (data)
        {
            $scope.credits = data;
           //console.log(JSON.stringify($scope.cloudsAddAngency));

        }).error(function (data, status) {
            notificate("Problema al cargar creditos",data.error,"info");
        });
    }

    //Obtener credits
    $scope.getClouds = function() {
        console.log('estoy en las nubes');
        var ur = 'cloud';

        $http.get(uri + ur).success(function (data)
        {
            $scope.clouds = data;
           //console.log(JSON.stringify($scope.cloudsAddAngency));

        }).error(function (data, status) {
            notificate("Problema al cargar creditos",data.error,"info");
        });


    }
   
   //Evento que se ejecuta cuando el usuario selecciona un pais
    $scope.changeSelectCountry = function() {

       if($scope.agency.idCountryKf!="" && $scope.agency.idCountryKf!=null){
          $scope.clouds = $filter('filter')($scope.cloudsAddAngency, {'idCountryKf':$scope.agency.idCountryKf});
          $scope.idCloudKf="";
       }else{
          $scope.clouds =[];
          $scope.idCloudKf="";
       }
       
       //console.log(JSON.stringify($scope.clouds));
    }


    //Obtener parametros version android y ios de la api asociada a la agencia
    $scope.getParam =  function (nbAgency,descParam) {

      if(nbAgency!="" && nbAgency!=null){

        var api_as=server+nbAgency+'/Api/index.php/config/param';
        //uri + 'config/param'
        $http.get(api_as).success(function (data) {
     
            var parametro = $filter('filter')(data, {'idParam':descParam});
            if(parametro[0]!=null && parametro[0]!=[] && parametro[0]!=undefined){
              $scope.param=[parametro[0].idParam,parametro[0].value,parametro[0].description,nbAgency];
              $scope.openModal('sm');
            }else{
              notificate("Información","No se encontró el parametro","info");
            }
                       
        }).error(function (data,status) {

            if(status == 404){notificate("Información","No se encontró el parametro","info");}
            else{notificate("Error !"," Contacte a Soporte","error");}
        });

      }else{

          notificate("Información","Problemas con el servidor","info");
      }
    }

    $scope.getCreditAgency = function () {

      if ($routeParams.id != null){
        ur = 'agency/creditAgency/' + $routeParams.id;

        $http.get(uri + ur).success(function (data) {


            $scope.listCreditAgency = data.creditAgency;        
            $scope.agency = data.agency[0];

            $http.get(uri + 'cloud').success(function (data) {
              $scope.clouds= data;
            }).error(function (data,status) {
              if(status == 404){notificate("Información "+status,data.error,"info");}
              else{notificate("Error !"+status," Contacte a Soporte","error");}
            });

            $http.get(uri + 'cloud').success(function (data) {
              $scope.credits= data;
            }).error(function (data,status) {
              if(status == 404){notificate("Información "+status,data.error,"info");}
              else{notificate("Error !"+status," Contacte a Soporte","error");}
            });

            if(data.saldoAgency[0].saldoTotal!=null){

              $scope.saldoTotal = data.saldoAgency[0].saldoTotal;
            }else{

                $scope.saldoTotal = 0;
            }

          /* PAGINADOR */
          $scope.totalItems = !$scope.listCreditAgency ? 0 : $scope.listCreditAgency.length;
          $scope.currentPage = 1;
          $scope.numPerPage = 25;
          $scope.paginate = function (value) {
            var begin, end, index;
            begin = ($scope.currentPage - 1) * $scope.numPerPage;
            end = begin + $scope.numPerPage;
            index = $scope.listCreditAgency.indexOf(value);
            return (begin <= index && index < end);
          };

        }).error(function (data,status) {
            if(status == 404){notificate("Información "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
      }
    }

    //Funciones para modal
     $scope.openModal = function (size) 
     {
         var modalInstance = $uibModal.open({
         templateUrl: 'myModal.html',
         controller: 'myModalController',
         size: size,
         resolve: {
          Items: function() 
             {
                return $scope.param;
             }
         }
         });
     }

    $scope.openModalEstado = function (size) 
     {
         var modalInstance = $uibModal.open({
         templateUrl: 'myModal2.html',
         controller: 'myModalController2',
         size: size,
         resolve: {
          Items: function() 
             {
                return $scope.cobro;
             }
         }
         });
     }

      $scope.ingresarCobro = function (cobro,nbAgency) {
          $scope.cobro=cobro;
          $scope.cobro.nbAgency=nbAgency;
          $scope.openModalEstado();
      }

      $scope.obtenerMesPeriodo = function (fecha,periodo) {
        var meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        var f = new Date(fecha);
        return meses[f.getMonth()];
      }


      $scope.editAgency = function (id) {
        $location.url('Edit'+id);    
      }

  });
/*********************************************************/


RoutingApp.controller('myModalController', function($scope,$uibModalInstance,Items,$http,$location){
  $scope.items = Items;
  $scope.updateParam=Items[1];
 
  $scope.save = function (){
    //console.log(Items[0]);
    //console.log($scope.updateParam);
    chagueParam(Items[0],Items[3],$scope.updateParam);
   };
 
  $scope.cancel = function () {
      $uibModalInstance.dismiss({$value: 'cancel'});
  };

  //Update de parametros version android y ios
  function chagueParam(id,nbAgency,valor){

        var api_as=server+nbAgency+'/Api/index.php/config/chagueParam';
        var filter =
        {
            filter:
            {
                valueParam: valor,  
                idParam: id
            }
        }

     // console.log("filter"+JSON.stringify(filter));
     //uri+'config/chagueParam'
      $http.post(api_as,filter).success(function (data){
            notificate("Información","Parametro actualizado exitosamente","success");
             $scope.cancel();
      }).error(function (data,status) {

          if(status == 404){
            notificate("Información "+status,"No se actulizo el parametro","info");
          }else{
            notificate("Error !","No se actulizo el parametro","error");
          }

          $scope.cancel();
      });
  }  

});




RoutingApp.controller('myModalController2', function($scope,$uibModalInstance,Items,$http,$location,$route){

 
  $scope.cancel = function () {
      $uibModalInstance.dismiss({$value: 'cancel'});
  };


  $scope.valoresCobro = function () {
      //$scope.cliente=Items.;
      //alert(JSON.stringify(Items));
      $scope.concepto=Items.nameCredit;
      $scope.periodo=Items.monthClosure;
      $scope.monto=Items.amount;
      $scope.nbAgency=Items.nbAgency;
  };

  $scope.ingresarCobro = function () {
    //let f = new Date();
    //let mes=parseInt(f.getMonth())+1;
    //let fech= f.getFullYear()+"-"+mes+"-"+f.getDate()+" "+f.getHours()+":"+ f.getMinutes()+":"+f.getSeconds();
    let userNameUser =JSON.parse(localStorage.getItem('session')).userNameUser;

    let credit =
    {
            credit:
            {
              idLiquidation: Items.idLiquidation,
              userNameUser:userNameUser//,
              //fechaCobro:fech
            }
     }



      $http.post(uri+'agency/ingresarCobro',credit).success(function (data){
            notificate("Información","Operación Exitosa","success");          
            $scope.cancel(); 
           // $location.url('State'+Items.idAgencyFk);    
           $route.reload();     
      }).error(function (data,status) {

          if(status == 404){
            notificate("Información "+status,"No se realizo el cobro","info");
          }else{
            notificate("Error !","No se realizo el cobro","error");
          }

          $scope.cancel();
      });
  };

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
