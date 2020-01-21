var RoutingApp = angular.module('routingOptionsFrond',
 ['ngAnimate', 'ngSanitize', 'ngRoute', 'ui.bootstrap']);


// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider
            .when("/", {
                templateUrl: 'View/Form.html',
                controller: 'optionsFrondController'
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

RoutingApp.controller('optionsFrondController', function ($scope, $http, $routeParams,$filter) { 

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


     //Evento que se ejecuta cuando el usuario selecciona un pais
    $scope.changeSelectCountry = function() {

       if($scope.idCountryKf!="" && $scope.idCountryKf!=null){
          $scope.clouds = $filter('filter')($scope.cloudsAddAngency, {'idCountryKf':$scope.idCountryKf});
          $scope.idCloudKf="";
       }else{
          $scope.clouds =[];
          $scope.idCloudKf="";
       }
       
       //console.log(JSON.stringify($scope.clouds));
    }

	$scope.cancel = function () {

		window.location.assign(uriBase);
	}


	$scope.addAgency = function (){
        $http.post(uri + 'agency/add', $scope._setAgencyAdd()).success(function (data){
            notificate("Operacion Exitosa",data.response,"success");
            $scope.nameAgency="";
            $scope.phoneAgnecy="";
            $scope.mailAgency="";
            $scope.idCloudKf="";
            $scope.idCountryKf="";
            //$location.url('/');
        }).error(function (data,status) {

          if(status == 501){notificate("Información","Nombre de agencia existente, por favor ingrese otro nombre.","info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}

            
        });
    }


    $scope._setAgencyAdd = function () {

      var agency ={

        agency:
        {
          nameAgency: $scope.nameAgency,
          phoneAgnecy: $scope.phoneAgnecy,
          mailAgency: $scope.mailAgency,
          idCloudKf: $scope.idCloudKf,
          statusAgency:1,
          statusRevision:0         
        }
      };

      //console.log(JSON.stringify(agency));
      return agency;
    }
    

});