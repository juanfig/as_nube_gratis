/*****************   MODULO DE CAJAS *****************************************/
RoutingApp.controller('cashboxController', function ($scope, $http, $routeParams,$location, $cookies, $uibModal) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.cashBox = null;
$scope.currentCashBox = {cashBoxName:null} //,idAgencyKf:null};
    $scope.balanceFilter = null;
    $scope.searchFilter = null;
    $scope.idAgencyFilter = null;
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
    $scope.balance = function(item) {
    	var diff = parseFloat(item.income) - parseFloat(item.expense);
    	return diff.toFixed(2);
    }
	//---------------------------------------------------------------------------
    $scope.initbalance = function() {
    	$scope.balanceFilter = '?balance=1';
    }
	//---------------------------------------------------------------------------
    $scope.getAgencies = function() {
        $http.get(uri + 'agency/index').success(function (data) {
            $scope.agencyList = data;
        });
    }
	//---------------------------------------------------------------------------
	    $scope.getListCashBox = function (lookFor) {

        $http.get(uri + 'cashBox/index'+(!!lookFor?lookFor:'')).success(function (data)
        {
            $scope.cashBoxList = data;
            //* PAGINADOR *
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.cashBoxList.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.cashBox = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }
	//---------------------------------------------------------------------------
    $scope.addCashBox = function () {

                 $http.post(uri + 'cashBox/add',$scope.currentCashBox).success(function (data) {
									 notificate("Ok","Caja creada.","success");
									 $scope.currentCashBox = {cashBoxName:null} //,idAgencyKf:null};
                  $location.path( '/Config/CashBox');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.updateCashBox = function () {

                 $http.post(uri + 'cashBox/update',$scope.currentCashBox).success(function (data) {
									 notificate("Ok","Caja editada.","success");
									 $scope.currentCashBox = {cashBoxName:null} //,idAgencyKf:null};
                  $location.path( '/Config/CashBox');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.deleteCashBox = function (item) {

                 $http.post(uri + 'cashBox/delete',item).success(function (data) {
									 notificate("Ok","Caja eliminada.","success");
									 $scope.currentCashBox = {cashBoxName:null} //,idAgencyKf:null};
									  $scope.getListCashBox();
                  $location.path( '/Config/CashBox');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.findCashBox= function () {
    	if (!$routeParams.id) return;
        var cashBox = {
                'idCashBox': $routeParams.id
        }
        if (!!$scope.balanceFilter) cashBox.balance=1;
        $http.post(uri + 'cashBox/find', cashBox).success(function (data) {
					$scope.currentCashBox=data;
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
    $scope.searchCashBox = function (param) {
    	var urlVars = !!$scope.balanceFilter ? $scope.balanceFilter : '';

    	if(param == 'agency' && !!$scope.idAgencyFilter){
    		$scope.searchFilter=null;
				urlVars += (urlVars==''?'?':'&')+'agency='+$scope.idAgencyFilter;
			}else if(param == 'search' && $scope.searchFilter){
				$scope.idAgencyFilter=null;
				urlVars += (urlVars==''?'?':'&')+'search='+$scope.searchFilter;
			}else{
				$scope.idAgencyFilter=null;
				$scope.searchFilter=null;
			}
		$scope.getListCashBox(urlVars);
   };
});


/*****************   MODULO DE FORMAS DE PAGO  *******************************/
RoutingApp.controller('paymentFormController', function ($scope, $http, $routeParams,$location, $cookies, $uibModal) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.PaymentForm = null;
$scope.currentPaymentForm = {namePaymentForm:null};
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
	    $scope.getListPaymentForm = function () {
        getPaymentForm();
    }
	//---------------------------------------------------------------------------
    function getPaymentForm(lookFor) {

        $http.get(uri + 'PaymentForm/index'+(!!lookFor?'?search='+lookFor:'')).success(function (data)
        {
            $scope.paymentForms = data;

            //* PAGINADOR *
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.paymentForms.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.PaymentForm = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }
	//---------------------------------------------------------------------------
    $scope.addPaymentForm = function () {

                 $http.post(uri + 'PaymentForm/add',$scope.currentPaymentForm).success(function (data) {
									 notificate("Ok","Forma de pago creada.","success");
									 $scope.currentPaymentForm = {namePaymentForm:null};
                  $location.path( '/Config/PaymentForm');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.updatePaymentForm = function () {

                 $http.post(uri + 'PaymentForm/update',$scope.currentPaymentForm).success(function (data) {
									 notificate("Ok","Forma de pago editada.","success");
									 $scope.currentPaymentForm = {namePaymentForm:null};
                  $location.path( '/Config/PaymentForm');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.deletePaymentForm = function (item) {

                 $http.post(uri + 'PaymentForm/delete',item).success(function (data) {
									 notificate("Ok","Forma de pago eliminada.","success");
									 $scope.currentPaymentForm = {namePaymentForm:null};
									  getPaymentForm();
                  $location.path( '/Config/PaymentForm');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.findPaymentForm= function () {
    	if (!$routeParams.id) return;
        var PaymentForm = {
                'idPaymentForm': $routeParams.id
        }
        $http.post(uri + 'PaymentForm/find', PaymentForm).success(function (data) {
					$scope.currentPaymentForm=data.paymentForm;
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
    $scope.searchPaymentForm= function () {
			getPaymentForm($scope.searchFilter);
   };

});
