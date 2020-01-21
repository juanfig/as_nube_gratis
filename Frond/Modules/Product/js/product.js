
// Creación del módulo
var RoutingApp = angular.module('routingProduct', ['ngRoute', 'ui.bootstrap', 'ngCookies']);

RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'View/ListProduct.html',
        controller: 'ProductController'
    })
    .when("/Add", {
        templateUrl: 'View/FormProduct.html',
        controller: 'ProductController'
    })
    .when("/Edit:id", {
        templateUrl: 'View/FormProduct.html',
        controller: 'ProductController'
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

RoutingApp.controller('ProductController', function ($scope, $http, $routeParams,$location, $cookies) {

    $scope.topFilter = -1;
    $scope.searchFilter = null;
    $scope.disablesalePriceProduct = false;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];

    $scope.getListProduct = function () {
        getProduct();
    }

    function getProduct() {

        $http.get(uri + 'Product').success(function (data)
        {
            $scope.products = data;
            /* PAGINADOR */
            $scope.totalItems = $scope.products.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.products.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.products = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}

            // $scope.products = [];
            // notificate( data.error ,"","error");
        });
    }


    $scope.addProduct = function (){
        $http.post(uri + 'Product/add', $scope._setProduct()).success(function (data){
            notificate("Operacion Exitosa",data.response,"success");
            $scope.saveImg(data.idProduct);
            $location.url('List');
        }).error(function (data) {

            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };


    $scope.updateProduct = function () {

        $http.post(uri + 'Product/update', $scope._setProduct()).success(function (data) {
            notificate("Operacion Exitosa",data.response,"success");
            $scope.saveImg($scope.idProduct);
            $location.url('List');
        }).error(function (data) {

            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };


    $scope.deleteProduct = function (id) {

        var r = confirm("¿En realidad desea eliminar este Servicio?");
        if (r == true)
        {
            $http.delete(uri + 'Product/delete/' + id).success(function (data) {
                notificate("Operacion Exitosa", "Formato Eliminado" ,"success");
                $scope.getListProduct() // Actualizamos la tabla
            }).error(function (data) {
                alert(data.error);
            });
        }
    };

    $scope.searchProduct = function () {
        callSearchProduct()
    }

    function callSearchProduct()
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

        $http.post(uri + 'Product/search', filter).success(function (data) {
            $scope.products = data;
            /* PAGINADOR */
            $scope.totalItems = $scope.products.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 50;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.products.indexOf(value);
                return (begin <= index && index < end);
            };
        }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }

    $scope.callProductById = function() {
        $http.get(uri + 'Product/find/'+ $routeParams.id).success(function (data) {
            $scope.idProduct = data.product.idProduct;
            $scope.nameProduct = data.product.nameProduct;
            $scope.descriptionProduct = data.product.descriptionProduct;
            $scope.salePriceProduct = parseFloat(data.product.salePriceProduct);
            $scope.creditPriceProduct = parseFloat(data.product.creditPriceProduct);

            $scope.loadImg($scope.idProduct);
        });
    }


    $scope._setProduct = function (){
        var data =
        {
            product:
            {
                idProduct: $scope.idProduct,
                nameProduct: $scope.nameProduct,
                descriptionProduct: $scope.descriptionProduct,
                salePriceProduct: $scope.salePriceProduct,
                creditPriceProduct: $scope.creditPriceProduct
            }
        };

        return data;
    }

    $scope.saveImg = function(id) {

        $("#message").empty();
        $('#loading').show();
        var datos = new FormData();
        datos.append('id', id);
        datos.append('folder', "Product");
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

    $scope.loadImg = function(id) {
        d = new Date();
        var img = $("<img src='../../images/Product/"+id+".png' />");
        img.on('load', function(e){
            $("#previewing").attr('src', '../../images/Product/'+id+'.png?'+d.getTime());
        }).on('error', function(e) {
            $("#previewing").attr('src', '../../images/Product/noimage.png?'+d.getTime());
        });

    }

    $scope.changueStatus = function (id, status) {
        var r = confirm("¿Quiere cambiar el status del producto?");
        if (r == true)
        {
            var product  = {
                product:{
                    idProduct: id,
                    idStatusProduct: status
                }
            }
            $http.post(uri + 'Product/changueStatus', product).success(function (data) {
                notificate("Operacion Exitosa",data.response,"success");
                getProduct() // Actualizamos la tabla
            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        }
    }

});