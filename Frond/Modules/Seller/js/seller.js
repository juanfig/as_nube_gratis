// Creación del MODELO
var RoutingApp = angular.module('routingSeller', ['ngRoute', 'ui.bootstrap', 'ngCookies']);
// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider
            .when("/", {
                templateUrl: 'View/list.html',
                controller: 'sellerController'
            })
            .when("/Add", {
                templateUrl: 'View/Form.html',
                controller: 'sellerController'
            })
            .when('/Edit:id', {
                templateUrl: 'View/Form.html',
                controller: 'sellerController'
            })
            .when('/Account:id', {
                templateUrl: 'View/dhboard.html',
                controller: 'sellerController'
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
RoutingApp.controller('sellerController', function ($scope, $http, $routeParams,$location, $cookies) {


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


    // LLAMAMOS A EL SERVICIO DE VENDEDORES //
    $scope.getList = function () {

      var ur = 'seller';

      if ($routeParams.id != null)
      {
        ur = 'seller/find/' + $routeParams.id;
      }

        $http.get(uri + ur).success(function (data) {



            if ($routeParams.id != null)
            {
                $scope.getFilter(data);
            }else {
              $scope.listSellers = data;
              /* PAGINADOR */
              $scope.totalItems = $scope.listSellers.length;
              $scope.currentPage = 1;
              $scope.numPerPage = 25;
              $scope.paginate = function (value) {
                  var begin, end, index;
                  begin = ($scope.currentPage - 1) * $scope.numPerPage;
                  end = begin + $scope.numPerPage;
                  index = $scope.listSellers.indexOf(value);
                  return (begin <= index && index < end);
              };
            }


         }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }


    $scope.searchSeller = function () {
        callSearchSeller()
    }

    function callSearchSeller()
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

        $http.post(uri + 'Seller/search', filter).success(function (data) {
         
            $scope.listSellers = data;
            /* PAGINADOR */
            $scope.totalItems = $scope.listSellers.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 50;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.listSellers.indexOf(value);
                return (begin <= index && index < end);
            };
        }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }

    $scope.addSeller = function (){
        $http.post(uri + 'seller/add', $scope._setSeller()).success(function (data){
            notificate("Operacion Exitosa",data.response,"success");
            $scope.saveImg(data.idSeller);
                  $location.url('/');

        }).error(function (data) {

            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };

    $scope.updateSeller = function () {

        $http.post(uri + 'seller/update', $scope._setSeller()).success(function (data) {
            notificate("Operacion Exitosa",data.response,"success");


                  $location.url('/');
              
            $scope.saveImg($scope.idSeller);
          

        }).error(function (data) {

            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });

    };

    $scope.saveImg = function(id) {

        $("#message").empty();
        $('#loading').show();
        var datos = new FormData();
        datos.append('id', id);
        datos.append('folder', "Seller");
        datos.append('file_img', $('#file_img')[0].files[0]); 
        $.ajax({
            url: uriBase+"Frond/uploadFile.php", 
            type: "POST",             
            data: datos, 
            contentType: false,       
            cache: false,         
            processData:false,     
            success: function(data)  
            {
                $('#loading').hide();
                $("#message").html(data);
            }
        });

    };

    $scope.callSellerById = function() {
        $http.get(uri + 'seller/find/'+ $routeParams.id).success(function (data) {
          $scope.idSeller= data.idSeller;
          $scope.nameSeller= data.nameSeller;
          $scope.lastnameSeller= data.lastnameSeller;
          $scope.dniSeller= data.dniSeller;
          $scope.pictureSeller= data.pictureSelle;
          $scope.phoneSeller= data.phoneSeller;
          $scope.passwordSeller= data.passwordSeller;
          $scope.emailSeller= data.emailSeller;
          $scope.csalesSeller= data.csalesSeller;
          $scope.creditsSeller= data.creditsSeller;
          $scope.statusSeller= data.statusSeller;
          $scope.loadImg($scope.idSeller);
        });
    }


    $scope.deleteSeller = function (id) {

        var r = confirm("¿En realidad desea eliminar este Vendedor?");
        if (r == true)
        {

            var seller  = {
                seller:{
                    idSeller: id,
                }
            }
            $http.post(uri + 'seller/delete/',seller).success(function (data) {
                notificate("Operacion Exitosa", "Vendedor Eliminado" ,"success");
              $scope.getList();
                 $scope.listSellers.splice(id, 1);
            }).error(function (data) {
                alert(data.error);
            });
        }
    };

    $scope.changeStatusSeller = function (id,status) {

        var r = confirm("¿En realidad desea desactivar este Vendedor?");
        if (r == true)
        {
            var seller  = {
                seller:{
                    idSeller: id,
                    statusSeller: status
                }
            }

             $http.post(uri + 'seller/changestatus/',seller).success(function (data) {
                notificate("Operacion Exitosa", data.response ,"success");
              $scope.getList();
            }).error(function (data) {
                alert(data.error);
            });
        }
    };


    $scope.callSellerAccountById = function() {
        $http.get(uri + 'seller/account/'+ $routeParams.id).success(function (data) {
          $scope.idSeller= data.idSeller;
          $scope.nameSeller= data.nameSeller;
          $scope.lastnameSeller= data.lastnameSeller;
          $scope.dniSeller= data.dniSeller;
          $scope.pictureSeller= data.pictureSelle;
          $scope.phoneSeller= data.phoneSeller;
          $scope.passwordSeller= data.passwordSeller;
          $scope.emailSeller= data.emailSeller;
          $scope.csalesSeller= data.csalesSeller;
          $scope.creditsSeller= data.creditsSeller;
          $scope.statusSeller= data.statusSeller;
          $scope.loadImg($scope.idSeller);
        });
    }


    $scope.loadImg = function(id) {
        d = new Date();
        var img = $("<img src='../../images/Seller/"+id+".png' />");
        img.on('load', function(e){
            $("#previewing").attr('src', '../../images/Seller/'+id+'.png?'+d.getTime());
        }).on('error', function(e) {
            $("#previewing").attr('src', '../../images/Seller/noimage.png?'+d.getTime());
        });

    }
    $scope._setSeller = function () {
        var seller ={

          seller:
            {
            idSeller:$routeParams.id,
            nameSeller:$scope.nameSeller,
            lastnameSeller:$scope.lastnameSeller,
            dniSeller:$scope.dniSeller,
            pictureSeller:$scope.pictureSeller,
            phoneSeller:$scope.phoneSeller,
            passwordSeller:$scope.passwordSeller,
            emailSeller:$scope.emailSeller,
            csalesSeller:$scope.csalesSeller,
            creditsSeller:$scope.creditsSeller,
            statusSeller:$scope.statusSeller,
          }
          };


        return seller;
    }


  });
  /*********************************************************/
