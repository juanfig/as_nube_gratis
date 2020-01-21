// Creación del módulo
var RoutingApp = angular.module('routingAdvertising', ['ngRoute', 'ui.bootstrap', 'ngCookies', 'ngSanitize']);

RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'View/list.html',
        controller: 'advertisingController'
    })
    .when('/add', {
        templateUrl: 'View/add.html',
        controller: 'advertisingController'
    })
    .when('/edit:id', {
        templateUrl: 'View/add.html',
        controller: 'advertisingController'
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
RoutingApp.controller('advertisingController', function ($scope, $http, $routeParams,$location, $cookies) {

    
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


    $scope.getListAdvertising = function () {

      var ur = 'advertising';

      if ($routeParams.id != null)
      {
        ur = 'advertising/find/' + $routeParams.id;
      }

        $http.get(uri + ur).success(function (data) {

            if ($routeParams.id != null)
            {
            	$scope.idAdvertising= data.idAdvertising,
                $scope.nameAdvertising= data.nameAdvertising,
                $scope.descAdvertising= data.descAdvertising,
                $scope.linkAdvertising= data.linkAdvertising
                
            }else {
              $scope.listAdvertising = data;
              /* PAGINADOR */
              $scope.totalItems = $scope.listAdvertising.length;
              $scope.currentPage = 1;
              $scope.numPerPage = 25;
              $scope.paginate = function (value) {
                  var begin, end, index;
                  begin = ($scope.currentPage - 1) * $scope.numPerPage;
                  end = begin + $scope.numPerPage;
                  index = $scope.listAdvertising.indexOf(value);
                  return (begin <= index && index < end);
              };
            }


         }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }

    $scope.updateAdvertising = function () {

        $http.post(uri + 'advertising/update', $scope._setAdvertising()).success(function (data) {
            notificate("Operacion Exitosa",data.response,"success");

            $scope.saveImg($('#idAdvertising').val());
            $location.url('/');        

        }).error(function (data) {

            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });

    };


  //Agregar Publicidad
    $scope.addAdvertising = function (){
        $http.post(uri + 'advertising/add', $scope._setAdvertising()).success(function (data){
            notificate("Operacion Exitosa",data.response,"success");
                  $scope.saveImg(data.idAdvertising);
                  $location.url('/');

        }).error(function (data) {

            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };

     $scope._setAdvertising = function () {
        var advertising ={

          advertising:
                {
                idAdvertising:$scope.idAdvertising,
                nameAdvertising:$scope.nameAdvertising,
                descAdvertising:$scope.descAdvertising,
                linkAdvertising:$scope.linkAdvertising
              }
          };


        return advertising;
    }

	//Agregar Imagen de publicidad

	 $scope.saveImg = function(id) {
            $("#message").empty();
            $('#loading').show();
            var datos = new FormData();
            datos.append('idAdvertising', id);
            datos.append('file_image_advertising', $('#file_image_advertising')[0].files[0]); 

            $.ajax({
                url: uriBase+"Frond/Modules/uploadFromAngular.php", // Url to which the request is send
                type: "POST",             // Type of request to be send, called as method
                data: datos, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData:false,        // To send DOMDocument or non processed data file it is set to false
                success: function(data)   // A function to be called if request succeeds
                {
                    // notificate("Operacion Exitosa", "Imagen Actualizado" ,"success");
                    $('#loading').hide();
                    $("#message").html(data);
                }
            });
    };

    $scope.changeStatus = function (id, status) {
        var r = confirm("¿Quiere cambiar el status de la Publicidad?");
        if (r == true)
        {
            var advertising  = {
                advertising:{
                    idAdvertising: id,
                    status: status
                }
            }
            //console.log(JSON.stringify(advertising));
            $http.post(uri + 'advertising/changeStatus', advertising).success(function (data) {
                notificate("Operacion Exitosa",data.response,"success");
                $scope.getListAdvertising ();// Actualizamos la tabla
            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        }
    }


});