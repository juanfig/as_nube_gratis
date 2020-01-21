
/*****************   MODULO DE BANCOS  ***************************************/
RoutingApp.controller('bankController', function ($scope, $http, $routeParams,$location, $cookies, $uibModal) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.BankList = null;
$scope.currentBank = {nameBank:null};
    $scope.searchFilter = null;
    $scope.topFilter = -1;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];


	//---------------------------------------------------------------------------
	    $scope.getListBank = function () {
        getBank();
    }
	//---------------------------------------------------------------------------
    function getBank(lookFor) {

        $http.get(uri + 'Bank/index'+(!!lookFor?'?search='+lookFor:'')).success(function (data)
        {
            $scope.BankList = data;

            //* PAGINADOR *
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.BankList.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.BankList = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }
	//---------------------------------------------------------------------------
    $scope.addBank = function () {

                 $http.post(uri + 'Bank/add',$scope.currentBank).success(function (data) {
									 notificate("Ok","Banco creado.","success");
									 $scope.currentBank = {nameBank:null};
                  $location.path( '/Config/Banks');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.updateBank = function () {

                 $http.post(uri + 'Bank/update',$scope.currentBank).success(function (data) {
									 notificate("Ok","Banco editado.","success");
									 $scope.currentBank = {nameBank:null};
                  $location.path( '/Config/Banks');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.deleteBank = function (item) {

                 $http.post(uri + 'Bank/delete',item).success(function (data) {
									 notificate("Ok","Banco eliminado.","success");
									 $scope.currentBank = {nameBank:null};
									  getBank();
                  $location.path( '/Config/Banks');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.findBank= function () {
    	if (!$routeParams.id) return;
        var Bank = {
                'idBank': $routeParams.id
        }
        $http.post(uri + 'Bank/find', Bank).success(function (data) {
					$scope.currentBank=data.bank

        }).error(function (data) {
            notificate("Error !" + (!!data)?data.error:'' ," Contacte a Soporte","error");
        });
    };
	//---------------------------------------------------------------------------
	  $scope.openModal = function () {
    $uibModal.open({
      templateUrl: 'View/modal.html',
      controller: function ($scope, $uibModalInstance) {
        $scope.ok = function () {
          $uibModalInstance.close();
        };

        $scope.cancel = function () {
          $uibModalInstance.dismiss('cancel');
        };
      }
    })
  };
	//---------------------------------------------------------------------------
    $scope.searchBank = function () {
			getBank($scope.searchFilter);
   };
});

/*****************   MODULO DE TIPOS DE CHEQUES  *****************************/
RoutingApp.controller('typeCheckController', function ($scope, $http, $routeParams,$location, $cookies, $uibModal) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.typeCheckList = null;
$scope.currentTypeCheck = {nameTypeCheck:null};
    $scope.searchFilter = null;
    $scope.topFilter = -1;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];


	//---------------------------------------------------------------------------
	    $scope.getListTypeChecks = function () {
        getTypeCheck();
    }
	//---------------------------------------------------------------------------
    function getTypeCheck(lookFor) {

        $http.get(uri + 'typeCheck/index'+(!!lookFor?'?search='+lookFor:'')).success(function (data)
        {
            $scope.typeCheckList = data;

            //* PAGINADOR *
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.typeCheckList.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.typeCheckList = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }
	//---------------------------------------------------------------------------
    $scope.addTypeCheck = function () {

                 $http.post(uri + 'typeCheck/add',$scope.currentTypeCheck).success(function (data) {
									 notificate("Ok","Banco creado.","success");
									 $scope.currentTypeCheck = {nameTypeCheck:null};
                  $location.path( '/Config/TypeCheck');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.updateTypeCheck = function () {

                 $http.post(uri + 'typeCheck/update',$scope.currentTypeCheck).success(function (data) {
									 notificate("Ok","Banco editado.","success");
									 $scope.currentTypeCheck = {nameTypeCheck:null};
                  $location.path( '/Config/TypeCheck');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.deleteTypeCheck = function (item) {

                 $http.post(uri + 'typeCheck/delete',item).success(function (data) {
									 notificate("Ok","Banco eliminado.","success");
									 $scope.currentTypeCheck = {nameTypeCheck:null};
									  getTypeCheck();
                  $location.path( '/Config/TypeCheck');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.findTypeCheck= function () {
    	if (!$routeParams.id) return;
        var typeCheck = {
                'idTypeCheck': $routeParams.id
        }
        $http.post(uri + 'typeCheck/find', typeCheck).success(function (data) {
					$scope.currentTypeCheck=data.typeCheck;
        }).error(function (data) {
            notificate("Error !" + (!!data)?data.error:'' ," Contacte a Soporte","error");
        });
    };
	//---------------------------------------------------------------------------
	  $scope.openModal = function () {
    $uibModal.open({
      templateUrl: 'View/modal.html',
      controller: function ($scope, $uibModalInstance) {
        $scope.ok = function () {
          $uibModalInstance.close();
        };

        $scope.cancel = function () {
          $uibModalInstance.dismiss('cancel');
        };
      }
    })
  };
	//---------------------------------------------------------------------------
    $scope.searchTypeCheck = function () {
			getTypeCheck($scope.searchFilter);
   };
});

/*****************   MODULO DE CHEQUES  **************************************/
RoutingApp.controller('checksController', function ($scope, $http, $routeParams,$location, $cookies, $uibModal) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.typeCheck = null;
$scope.currentTypeCheck = {nameCheck:null};
    $scope.searchFilter = null;
    $scope.boxFilter = null;
    $scope.bankFilter = null;
    $scope.typeFilter = null;
    $scope.topFilter = -1;
    $scope.filter =
    [
    { name: "10", id: 10 },
    { name: "20", id: 20 },
    { name: "30", id: 30 },
    { name: "40", id: 40 },
    { name: "50", id: 50 }
    ];


	//---------------------------------------------------------------------------
	    $scope.getListTypeCheck = function () {

        $http.get(uri + 'typeCheck/index').success(function (data)
        {
            $scope.typeCheck = data;
        });
    }
	//---------------------------------------------------------------------------
	   $scope.getListBank = function () {

        $http.get(uri + 'Bank/index').success(function (data)
        {
            $scope.banks = data;
        });
    }

	//---------------------------------------------------------------------------
	   $scope.getListCashBox = function () {

        $http.get(uri + 'cashBox/index').success(function (data)
        {
            $scope.cashBoxList = data;
        });
    }
	//---------------------------------------------------------------------------
	    $scope.getListCheck = function (own) {
	    $scope.own = own;
        getCheck('?own='+own+'&use=all');
    }
	//---------------------------------------------------------------------------
    function getCheck(lookFor) {

        $http.get(uri + 'CashBox/checks'+(!!lookFor?lookFor:'')).success(function (data)
        {
            $scope.checksList = data;
            //* PAGINADOR *
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.checksList.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.typeCheck = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }
	//---------------------------------------------------------------------------*/
    $scope.findCheck= function () {
    	if (!$routeParams.id) return;
        var Check = {
                'idCheck': $routeParams.id
        }
        $http.post(uri + 'Check/find', Check).success(function (data) {
					$scope.currentCheck=data.check;
        }).error(function (data) {
            notificate("Error !" + (!!data)?data.error:'' ," Contacte a Soporte","error");
        });
    };

	//---------------------------------------------------------------------------
	  $scope.parseTypeCheck = function (id) {
	  	for (var i=0;i<$scope.typeCheck.length;i++)
	  		if (id==$scope.typeCheck[i].idTypeCheck) return $scope.typeCheck[i].nameTypeCheck;

	  		return null;
		}
	//---------------------------------------------------------------------------/
	  $scope.parseBank = function (id) {
	  	for (var i=0;i<$scope.banks.length;i++)
	  		if (id==$scope.banks[i].idBank) return $scope.banks[i].nameBank;

	  		return null;
		}
	//---------------------------------------------------------------------------/
	  $scope.parseCashBoxName = function (id) {
	  	for (var i=0;i<$scope.cashBoxList.length;i++)
	  		if (id==$scope.cashBoxList[i].idCashBox) return $scope.cashBoxList[i].cashBoxName;

	  		return null;
		}
	//---------------------------------------------------------------------------
    $scope.filterChecks = function (elm) {
			var params='?own='+$scope.own+'&use=all';
			if (elm=='search') {

				$scope.typeFilter = null;
				$scope.bankFilter = null;
				$scope.boxFilter = null;

				if(!!$scope.searchFilter) params+='&number='+$scope.searchFilter;

			}else{

				$scope.searchFilter = null;

				if(!!$scope.typeFilter) params += '&type='+$scope.typeFilter;
				if(!!$scope.bankFilter) params += '&bank='+$scope.bankFilter;
				if(!!$scope.boxFilter) params += '&box='+$scope.boxFilter;

			}
					getCheck(params);
   };

});
