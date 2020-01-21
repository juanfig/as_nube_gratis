// Creación del módulo
var RoutingApp = angular.module('routingProductOnline', ['ngRoute', 'ui.bootstrap', 'ngCookies']);

RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'View/ListProduct.html',
        controller: 'ProductOnlineController'
    })
    .when("/Add", {
        templateUrl: 'View/FormProduct.html',
        controller: 'ProductOnlineController'
    })
    .when("/Edit:id", {
        templateUrl: 'View/FormProduct.html',
        controller: 'ProductOnlineController'
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
RoutingApp.controller('ProductOnlineController', function ($scope, $http, $routeParams,$location, $cookies) {
  
  
  $scope.products;
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
  $scope.product = {
    idProductOnline: null,
    nameProduct: '',
    price: 0,
    principalPicture: '',
    detailPicture: ''
  }
  $scope.uri = uriImages
  $scope.oldPrincipalPicture = null;
  $scope.oldDetailPicture = null;

  $scope.init = function(){
    $http.get(uri + 'ProductOnline').success(function (data)
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
        if(status == 404){
          notificate("!Informacion "+status,data.error,"info"); $scope.products = []
        }
        else{
          notificate("Error !"+status," Contacte a Soporte","error");
        }
        // $scope.products = [];
        // notificate( data.error ,"","error");
    });
  }

  $scope.addProduct = function(){

    $scope.determinated_img(false)

    if(!$scope.product.principalPicture){
      alert('Debe esocoger la imagen principal del producto')
      return false
    }
    
    var store = { product : $scope.product }
    $http.post(uri+'ProductOnline/add',store).success(function(data){

      notificate("Operacion Exitosa",data.response,"success");
      $scope.saveImg(data.idProduct,'file_img',$scope.product.principalPicture);
      $scope.saveImg(data.idProduct,'file_img1',$scope.product.detailPicture);
      $location.url('List');

    }).error(function(data,status){
      if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.reset_product()}
        else{notificate("Error !"+status," Contacte a Soporte","error");}
    })
  }

  $scope.editProduct = function(){
    
    if($routeParams.id !== undefined){
      $http.get(uri + 'ProductOnline/find/'+ $routeParams.id).success(function (data) {
          
          var imgPrincipal = data.principalPicture ? data.idProductOnline+data.principalPicture : null
          var imgDetail = data.detailPicture ? data.idProductOnline+data.detailPicture : null

          $scope.product.idProductOnline = data.idProductOnline
          $scope.product.nameProduct = data.name
          $scope.product.price = parseFloat(data.price)
          $scope.product.principalPicture = data.principalPicture
          $scope.product.detailPicture = data.detailPicture
        
          if(imgPrincipal){
            $('#previewing').attr('src',uriImages+'ProductOnline/'+imgPrincipal)
          }      
          if(imgDetail){
            $('#previewing1').attr('src',uriImages+'ProductOnline/'+imgDetail)
          }


      }).error(function(error,status){
        $location.url('List')
      });
    }
      
  }

  $scope.updateProduct = function(){
    
    $scope.determinated_img(true)
    
    var product = { product : $scope.product }

    $http.post(uri+'ProductOnline/update',product).success(function(data){


      var img = $('#file_img')[0].files[0]
      var img1 = $('#file_img1')[0].files[0]
      
      if(img !== undefined){
        var obj = {principalImg: $scope.oldPrincipalPicture}
        $scope.deleteImg(obj,$scope.product.idProductOnline)
        $scope.saveImg($scope.product.idProductOnline,'file_img',$scope.product.principalPicture);
      }

      if(img1 !== undefined){
        var obj = {detailImg: $scope.oldDetailPicture}
        $scope.deleteImg(obj,$scope.product.idProductOnline)
        $scope.saveImg($scope.product.idProductOnline,'file_img1',$scope.product.detailPicture);
      }

      notificate("Operacion Exitosa",data.response,"success");
      $location.url('List')

    }).error(function(data,status){
      notificate("Error!"+status,data.error,'error')
    })
  }

  $scope.deleteProduct = function(id){
    if(confirm("¿Esta seguro de querer eliminar este registro?"))
    $http.post(uri+'ProductOnline/delete/'+id).success(function(data){
      notificate('Operación Exitosa',data.response,'success')
      $scope.deleteImg(data.images,id)
      $scope.init()
    }).error(function(data,status){
      notificate('Error! '+status,data.error,'error')
    })
  }

  $scope.searchProduct = function(query){
    
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

    $http.post(uri+"ProductOnline/show",filter).success(function(data){
      $scope.products = data 
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

    }).error(function(data,status){
      notificate('Error!'+status,data.error,"error")
    })
  }

  $scope.saveImg = function(id,selector,name_img) {

    $("#message").empty();
    $('#loading').show();
    var datos = new FormData();
    datos.append('id', id);
    datos.append('folder', "ProductOnline");
    datos.append('file_img_product_online', $('#'+selector)[0].files[0]); 
    datos.append('name', name_img); 
    $.ajax({
        url: uriBase+"Frond/uploadFile.php", 
        type: "POST",             
        data: datos, 
        contentType: false,       
        cache: false,         
        processData:false,     
        success: function(data)  
        {

        }
    });

  };

  $scope.determinated_img = function(edit){
    var img = $('#file_img')[0].files[0]
    var img1 = $('#file_img1')[0].files[0]

    if(edit){
      if(img !== undefined){
        $scope.oldPrincipalPicture = $scope.product.principalPicture
        $scope.product.principalPicture = img.name.substring(0, img.name.length - 3)+"png"
      }

      if(img1 !== undefined){
        $scope.oldDetailPicture = $scope.product.detailPicture
        $scope.product.detailPicture = img1.name.substring(0, img1.name.length - 3)+"png"
      }

    }else{
      if(img !== undefined){
        $scope.product.principalPicture = img.name.substring(0, img.name.length - 3)+"png"
      }else{
        $scope.product.principalPicture = null
      }

      if(img1 !== undefined){
        $scope.product.detailPicture = img1.name.substring(0, img1.name.length - 3)+"png"
      }else{
        $scope.product.detailPicture = null
      }
    }
  }

  $scope.deleteImg = function($images,id){

    var route = "images/ProductOnline/";
    $.ajax({
      url: uriBase+"Frond/deleteFiles.php",
      type: 'POST',
      data: {data : $images, route, id },
    })
    .done(function() {
      console.log("success");
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });
    
  }
})