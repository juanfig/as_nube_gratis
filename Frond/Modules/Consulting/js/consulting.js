
// Creación del módulo
var RoutingApp = angular.module('routingConsulting', ['ngRoute', 'ui.bootstrap', 'ngCookies', 'ngSanitize']);


// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider
    .when("/", {
        templateUrl: 'View/list.html',
        controller: 'consultingController'
    })
    .when('/Add', {
        templateUrl: 'View/Add.html',
        controller: 'consultingController'
    })
    .when('/Edit:id', {
        templateUrl: 'View/Add.html',
        controller: 'consultingController'
    })
    .when('/Details:id', {
        templateUrl: 'View/Details.html',
        controller: 'consultingController'
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
RoutingApp.controller('consultingController', function ($scope, $http, $routeParams,$location, $cookies) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.isAdmin = JSON.parse(localStorage.getItem('session')).userNameUser=="ADMIN";
    $scope.topFilter = -1;
    $scope.statusFilter = null;
    $scope.onlineFilter = null;
    $scope.userFilter = null;
    $scope.searchFilter = null;
    $scope.idAgencyFilter = null;
    $scope.disableConfiguration = false;
    $scope.typeConsulting = [{id:'o',label:'Online'},{id:'p',label:'Presencial'}];
    $scope.agencyList = null;
    $scope.consultingList = null;
    $scope.currentConsulting = null;
    $scope.tickets = null;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];

    $scope.getListconsulting = function () {
        getconsulting();
    }
	//---------------------------------------------------------------------------
    function getconsulting() {
    	$scope.consultingList = null;
        $http.get(uri + 'consulting/index/?idUser='+$scope.idUserKf).success(function (data)
        {
            $scope.consultings = data;
             $scope.consultingList = angular.copy($scope.consultings);

            //* PAGINADOR *
            $scope.totalItems = $scope.consultings.length;
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.consultings.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.consultings = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }

    function mailConsultant(idConsulting) {
        $http.get(uri + 'mail/sendMailConsultant/'+idConsulting).success(function (data) {
            notificate("Ok","Mensaje enviado al consultor.","success");
        }).error(function (data) {
            notificate("Error !" + (!!data)?data.error:'' ," Contacte a Soporte","error");
        });
    }

    function mailConsulting(idConsulting) {
        $http.get(uri + 'mail/sendMailConsulting/'+idConsulting).success(function (data) {
            notificate("Ok","Mensaje enviado a la agencia.","success");
        }).error(function (data) {
            notificate("Error !" + (!!data)?data.error:'' ," Contacte a Soporte","error");
        });
    }

    function agSupport(profileName){
        $scope.newSupport.support.idAgencyKf = $scope.currentConsulting.idAgencyKf;
        $scope.newSupport.support.idUserAgencyKf = $scope.idUserKf;
        $scope.newSupport.support.emailResponSupport = JSON.parse(localStorage.getItem('session')).emailUser;
        $scope.newSupport.support.isTravelSendMovil = 0;
        $scope.newSupport.support.attach = 0;
        $scope.newSupport.idConsultingKf = $scope.currentConsulting.idConsulting;
        $scope.newSupport.fullNameUser = JSON.parse(localStorage.getItem('session')).userNameUser;
        $scope.newSupport.support.codeSupport = $scope.currentConsulting.code;
        if (profileName=='consultor') {
            $scope.newSupport.support.statusSupport= -1;
            $scope.newSupport.support.dateDevelopmentSupport = new Date();
            $scope.newSupport.support.dateFinalizedSupport = new Date();
        }

        $http.post(uri + 'support/add',$scope.newSupport).success(function (data) {
           notificate("Ok","Ticket agregado.","success");
           $scope.initSupport();
           $scope.callconsultingById();
        }).error(function (data) {
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    }
	//---------------------------------------------------------------------------
    $scope.callconsultingById = function() {
        $http.get(uri + 'consulting/findWithConsultant/'+$routeParams.id).success(function (data) {
						$scope.currentConsulting = data.consulting;
            $scope.tickets = data.tickets;
        });
    }
	//---------------------------------------------------------------------------
    $scope.getAgencies = function() {
        $http.get(uri + 'agency/index').success(function (data) {
            $scope.agencyList = data;
        });
    }

	//---------------------------------------------------------------------------
    $scope.getusersystem = function() {
        $http.get(uri + 'userSystem/index').success(function (data) {
            $scope.userList = data;
        });
    }

	//---------------------------------------------------------------------------
    $scope.changeStatus = function (obj) {
        var consulting = {
                'idConsulting': obj.idConsulting,
                'statusConsulting': obj.statusConsulting==0 ? 1 : -1,
                'idUser':$scope.idUserKf
        }
        $http.post(uri + 'consulting/changeStatus', consulting).success(function (data) {
					getconsulting();
        }).error(function (data) {
            notificate("Error !" + (!!data)?data.error:'' ," Contacte a Soporte","error");
        });
    };

	//---------------------------------------------------------------------------
    $scope.findConsulting = function () {
    	if (!$routeParams.id) return;
        var consulting = {
                'idConsulting': $routeParams.id,
                'idUser':$scope.idUserKf
        }
        $http.post(uri + 'consulting/find', consulting).success(function (data) {
					$scope.currentConsulting=data.consulting;
					$scope.currentConsulting.online = parseInt($scope.currentConsulting.online)?'o':'p';
        }).error(function (data) {
            notificate("Error !" + (!!data)?data.error:'' ," Contacte a Soporte","error");
        });
    };
  //---------------------------------------------------------------------------

    $scope.addConsulting = function () {
        var params = angular.copy($scope.currentConsulting);
        params.idUserKf=angular.copy($scope.idUserKf);
        params.online=$scope.currentConsulting.online=='o'?1:0;
    									//params.dateOpenConsulting=$('#dateOpenConsulting').val();

        $http.post(uri + 'consulting/add',params).success(function (data) {
            notificate("Ok","Consultoria creada.","success");
            $scope.currentConsulting=null;
            idConsulting= data.consulting;
            mailConsultant(idConsulting);
            mailConsulting(idConsulting);
            $location.path( '/Details'+ idConsulting);
          }).error(function (data) {
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
    };
	//---------------------------------------------------------------------------

	    $scope.updateConsulting = function () {
    							var params = angular.copy($scope.currentConsulting);
									params.online=$scope.currentConsulting.online=='o'?1:0;
									//params.dateOpenConsulting=$('#dateOpenConsulting').val();
        $http.post(uri + 'consulting/update',params).success(function (data) {
									$scope.currentConsulting=null;
                  $location.path( "/consulting" );
        }).error(function (data) {
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });
     };

	//---------------------------------------------------------------------------
	    $scope.initSupport = function () {
	    	$scope.newSupport = {support:{reasonSupport:null,detailSupport:null}};
			}
			$scope.initSupport();
	//---------------------------------------------------------------------------
    $scope.addSupport = function () {

        $http.get(uri + 'usersystem/find/'+JSON.parse(localStorage.getItem('session')).idUser).success(function (data) {
            agSupport(data.usersystem.profileName);
        }).error(function (data) {
            notificate("Error !" + (!!data)?data.error:'' ," Contacte a Soporte","error");
        });
    };
	//---------------------------------------------------------------------------

    function filterArray(arr,prop,val) {
    	var newArr=[];
			for (var i=0;i<arr.length;i++) {
						if (arr[i][prop] == val){
							newArr.push(angular.copy(arr[i]));
						}
				}
				return newArr;
		}
	//---------------------------------------------------------------------------
    $scope.searchConsulting = function () {
    	 $scope.consultings = angular.copy($scope.consultingList);

				if (!!$scope.searchFilter){
							$scope.idAgencyFilter=null;
							$scope.onlineFilter=null;
							$scope.statusFilter=null;
						if (!!$scope.searchFilter)
							$scope.consultings=filterArray($scope.consultings,'code',$scope.searchFilter);

				}else if(!!$scope.idAgencyFilter || !!$scope.onlineFilter || !!$scope.statusFilter || !!$scope.userFilter){
							$scope.searchFilter=null;

						if (!!$scope.idAgencyFilter)
							$scope.consultings = filterArray($scope.consultings,'idAgencyKf',$scope.idAgencyFilter);

						if (!!$scope.onlineFilter)
							$scope.consultings = filterArray($scope.consultings,'online',$scope.onlineFilter);

						if (!!$scope.statusFilter)
							$scope.consultings = filterArray($scope.consultings,'statusConsulting',$scope.statusFilter);

						if (!!$scope.userFilter)
							$scope.consultings = filterArray($scope.consultings,'idUserKf',$scope.userFilter);

				}

   };

   $scope.getConsultants = function() {
        $http.get(uri + 'userSystem/userType/consultor').success(function (data) {
            $scope.userList = data;
        });
    }

});
