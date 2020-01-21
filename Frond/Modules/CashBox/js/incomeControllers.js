/*****************   MODULO DE INGRESOS **************************************/
RoutingApp.controller('incomeController', function ($scope, $http, $routeParams,$location, $cookies, $uibModal) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.typeIncome = null;
$scope.currentCheck = null;
$scope.checkManagement = false;
$scope.totalCheckAmounts = 0;
$scope.listChecks = [];
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
	    $scope.getListTypeCheck = function () {

        $http.get(uri + 'typeCheck/index').success(function (data)
        {
            $scope.typesCheck = data;
        });
    }
	//---------------------------------------------------------------------------
	   $scope.getListBank = function () {

        $http.get(uri + 'Bank/index').success(function (data)
        {
            $scope.banks = data;
        });

        $scope.addNewCheck = function () {
            $scope.checkManagement = true;
        }
    }
	//---------------------------------------------------------------------------
	  $scope.initCheck = function () {
	  	$scope.currentCheck = {
				checkNumber:null,
				checkAmount:null,
				checkDate:null,
				idTypeChecKf:null,
				idBankKf:null,
				own:0
            }
        $scope.checkManagement = false;
		}
	//---------------------------------------------------------------------------
	  $scope.addCheck = function () {

	  	if (!$scope.currentCheck.checkNumber)  { notificate("Se requiere", "Número de cheque","warning"); return; }
	  	if (!$scope.currentCheck.checkAmount)  { notificate("Se requiere", "Monto del Cheque","warning"); return; }
	  	if (!$scope.currentCheck.checkDate)    { notificate("Se requiere", "Fecha del Cheque","warning"); return; }
	  	if (!$scope.currentCheck.idTypeChecKf) { notificate("Se requiere", "Tipo de cheque","warning");   return; }
	  	if (!$scope.currentCheck.idBankKf)     { notificate("Se requiere", "Banco Emisor","warning");     return; }

				$scope.listChecks.push(angular.copy($scope.currentCheck));
				$scope.initCheck();
		}
	//---------------------------------------------------------------------------
	  $scope.removeCheck = function (item) {
			$scope.listChecks.splice($scope.listChecks.indexOf(item),1);
		}
	//---------------------------------------------------------------------------
	  $scope.parseTypeCheck = function (id) {
	  	for (var i=0;i<$scope.typesCheck.length;i++)
	  		if (id==$scope.typesCheck[i].idTypeCheck) return $scope.typesCheck[i].nameTypeCheck;

	  		return null;
		}
	//---------------------------------------------------------------------------/
	  $scope.parseBank = function (id) {
	  	for (var i=0;i<$scope.banks.length;i++)
	  		if (id==$scope.banks[i].idBank) return $scope.banks[i].nameBank;

	  		return null;
		}
	//---------------------------------------------------------------------------/
	  $scope.initIncome = function () {

				$scope.currentIncome = {
						amountIncome:null,
						aditionalInfo:null,
						dateCreated:null,
						dateIncome:null,
						idTypeIncomeKf:null,
						idPaymentFormKf:null,
						idUserKf:null,
						idCashBoxKf:null
				}
		}
	//---------------------------------------------------------------------------
	    $scope.getListTypeIncome = function () {

        $http.get(uri + 'typeIncome/index').success(function (data)
        {
            $scope.typeIncome = data;
        });
    }
	//---------------------------------------------------------------------------
	   $scope.getListPaymentForm = function () {

        $http.get(uri + 'PaymentForm/index').success(function (data)
        {
            $scope.paymentForms = data;
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
	    $scope.getListIncome = function () {
        getIncome();
    }
	//---------------------------------------------------------------------------
    function getIncome(lookFor) {

        $http.get(uri + 'Income/index'+(!!lookFor?lookFor:'')).success(function (data)
        {
            $scope.incomeList = data;

            //* PAGINADOR *
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.incomeList.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.typeIncome = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }
	//---------------------------------------------------------------------------
   $('#datetimepicker1').datetimepicker({
            format: 'YYYY/MM/DD',
            collapse:false,
            sideBySide: true
        }).on("dp.hide",function (e) {
    		$scope.currentCheck.checkDate = $('#checkDate').val();
		});
	//---------------------------------------------------------------------------
    $scope.findIncome= function () {
    	if (!$routeParams.id) return;
        var Income = {
                'idIncome': $routeParams.id,
                'use': 'in'
        }
        $http.post(uri + 'Income/find', Income).success(function (data) {
                    $scope.currentIncome=data.income;
                    $scope.checksInIncome = data.checks; // cheques que pertenecen a un egreso
        }).error(function (data) {
            notificate("Error !" + (!!data)?data.error:'' ," Contacte a Soporte","error");
        });
    };
    //---------------------------------------------------------------------------
    $scope.changeTypeForm = function (item) {
        if ($scope.currentIncome.idPaymentFormKf != 2) {
            $scope.checkManagement = true;
            $scope.checksAvailables = [];
            $scope.listChecks = [];
            $scope.initCheck();
        }
    }
    //---------------------------------------------------------------------------
    $scope.toggleCheck = function () {
        if ($scope.currentCheck.own == 0) {
            $scope.currentCheck.own = 1;
            $scope.checksAvailables = [];
        } else {
            $scope.currentCheck.own = 0;
            $scope.getListOthersChecks();
        }
    }
    //---------------------------------------------------------------------------
    $scope.getListOthersChecks = function () {

        $http.get(uri + 'expense/checksAvailables').success(function (data) {
            $scope.checksAvailables = data;

            if ($scope.listChecks.length) {

                for (var i = 0; i < $scope.listChecks.length; i++) {
                    if (!!$scope.listChecks[i].idCheck)
                        for (var j = 0; j < $scope.checksAvailables.length; j++) {
                            if ($scope.listChecks[i].idCheck == $scope.checksAvailables[j].idCheck) {
                                $scope.checksAvailables.splice(j, 1); break
                            }
                        }
                }
            }
        });
    }
    //---------------------------------------------------------------------------
    $scope.addIncome = function () {

						$scope.currentIncome.idUserKf=$scope.idUserKf;

							if(parseInt($scope.currentIncome.idPaymentFormKf)!=2) $scope.listChecks = [];

                 $http.post(uri + 'Income/add',{income:$scope.currentIncome,checks:$scope.listChecks}).success(function (data) {
									 notificate("Ok","Ingreso creado.","success");
									 $scope.initIncome();
									 $scope.listChecks = [];
                  $location.path( '/Income');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
    //---------------------------------------------------------------------------  


    $scope.updateCheck = function (item) {
        $scope.checkManagement = true;
        $scope.currentCheck = {
            idCheck: item.idCheck,
            checkNumber: item.checkNumber,
            checkAmount: parseFloat(item.checkAmount),
            checkDate: item.checkDate,
            idTypeChecKf: item.idTypeChecKf,
            idBankKf: item.idBankKf,
            own: item.own
        }
    }
    //---------------------------------------------------------------------------

    $scope.updateCheckConfirmation = function () {

        $scope.currentCheck = {
            idCheck: $scope.currentCheck.idCheck,
            checkNumber: $scope.currentCheck.checkNumber,
            checkAmount: parseFloat($scope.currentCheck.checkAmount),
            checkDate: $scope.currentCheck.checkDate,
            idTypeChecKf: $scope.currentCheck.idTypeChecKf,
            idBankKf: $scope.currentCheck.idBankKf,
            own: $scope.currentCheck.own
        }
        $http.post(uri + 'check/update', { check: $scope.currentCheck }).success(function (data) {
            notificate("Ok", "Cheque modificado.", "success");
            $scope.initCheck();
            $scope.checksAvailables = [];
           $scope.findIncome();
        }).error(function (data) {
            notificate("Error !" + data.error, " Contacte a Soporte", "error");
        });

    }

    //---------------------------------------------------------------------------
    $scope.updateIncome = function () {

						//$scope.currentIncome.idUserKf=$scope.idUserKf;

                 $http.post(uri + 'Income/update',$scope.currentIncome).success(function (data) {
									 notificate("Ok","Ingreso actualizado.","success");
									 $scope.initIncome();
                  $location.path( '/Income');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.deleteIncome = function (item) {

                 $http.post(uri + 'Income/delete',item).success(function (data) {
									 notificate("Ok","Ingreso eliminado.","success");
									 $scope.currentIncome = {nameIncome:null};
									  getIncome();
                  $location.path( '/Income');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };

	//---------------------------------------------------------------------------
    $scope.searchIncome = function (elm) {
			var params='';
			if (elm=='search') {

				$scope.typeFilter = null;
				$scope.formFilter = null;
				$scope.boxFilter = null;

				if(!!$scope.searchFilter) params='?info='+$scope.searchFilter;

			}else{

				$scope.searchFilter = null;

				if(!!$scope.typeFilter) params += (params=='' ? '?':'&' )+'type='+$scope.typeFilter;
				if(!!$scope.formFilter) params += (params=='' ? '?':'&' )+'form='+$scope.formFilter;
				if(!!$scope.boxFilter) params += (params=='' ? '?':'&' )+'box='+$scope.boxFilter;

			}
					getIncome(params);
   };
});

/*****************   MODULO DE INGRESOS **************************************/
RoutingApp.controller('typeIncomeController', function ($scope, $http, $routeParams,$location, $cookies, $uibModal) {
$scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
$scope.typeIncome = null;
$scope.currentTypeIncome = {nameIncome:null};
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
	    $scope.getListTypeIncome = function () {
        getTypeIncome();
    }
	//---------------------------------------------------------------------------
    function getTypeIncome(lookFor) {

        $http.get(uri + 'typeIncome/index'+(!!lookFor?'?search='+lookFor:'')).success(function (data)
        {
            $scope.typeIncome = data;

            //* PAGINADOR *
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.typeIncome.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info"); $scope.typeIncome = []}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }
	//---------------------------------------------------------------------------
    $scope.addTypeIncome = function () {

                 $http.post(uri + 'typeIncome/add',$scope.currentTypeIncome).success(function (data) {
									 notificate("Ok","Tipo de ingreso creado.","success");
									 $scope.currentTypeIncome = {nameIncome:null};
                  $location.path( '/Config/TypeIncome');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.updateTypeIncome = function () {

                 $http.post(uri + 'typeIncome/update',$scope.currentTypeIncome).success(function (data) {
									 notificate("Ok","Tipo de ingreso editado.","success");
									 $scope.currentTypeIncome = {nameIncome:null};
                  $location.path( '/Config/TypeIncome');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.deleteTypeIncome = function (item) {

                 $http.post(uri + 'typeIncome/delete',item).success(function (data) {
									 notificate("Ok","Tipo de ingreso eliminado.","success");
									 $scope.currentTypeIncome = {nameIncome:null};
									  getTypeIncome();
                  $location.path( '/Config/TypeIncome');
                }).error(function (data) {
                    notificate("Error !" + data.error ," Contacte a Soporte","error");
                });
            };
	//---------------------------------------------------------------------------
    $scope.findTypeIncome= function () {
    	if (!$routeParams.id) return;
        var typeIncome = {
                'idTypeIncome': $routeParams.id
        }
        $http.post(uri + 'typeIncome/find', typeIncome).success(function (data) {
					$scope.currentTypeIncome=data.typeIncome;
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
    $scope.searchIncome = function () {
			getTypeIncome($scope.searchFilter);
   };
});
