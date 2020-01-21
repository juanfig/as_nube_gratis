/*****************   MODULO DE EGRESOS ***************************************/
RoutingApp.controller('expensesController', function ($scope, $http, $routeParams, $location, $cookies, $uibModal) {
    $scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;

    $scope.searchFilter = null;
    $scope.typeFilter = null;
    $scope.formFilter = null;
    $scope.boxFilter = null;
    $scope.checkManagement = false;
    $scope.totalCheckAmounts = 0;
    $scope.checksAvailables = [];
    $scope.listChecks = [];
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

        $http.get(uri + 'typeCheck/index').success(function (data) {
            $scope.typesCheck = data;
        });
    }
    //---------------------------------------------------------------------------
    $scope.getListBank = function () {

        $http.get(uri + 'Bank/index').success(function (data) {
            $scope.banks = data;
        });
    }
    $scope.addNewCheck = function () {
        $scope.checkManagement = true;
    }
    //---------------------------------------------------------------------------
    $scope.initCheck = function () {
        $scope.currentCheck = {
            checkNumber: null,
            checkAmount: null,
            checkDate: null,
            idTypeChecKf: null,
            idBankKf: null,
            own: 1
        }
        $scope.checkManagement = false;
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
    $scope.addThisCheck = function (check) {
        $scope.listChecks.push(check);
        $scope.checksAvailables.splice($scope.checksAvailables.indexOf(check), 1);
    }
    //---------------------------------------------------------------------------
    $scope.addCheck = function () {

        if (!$scope.currentCheck.checkNumber) { notificate("Se requiere", "Número de cheque", "warning"); return; }
        if (!$scope.currentCheck.checkAmount) { notificate("Se requiere", "Monto del Cheque", "warning"); return; }
        if (!$scope.currentCheck.checkDate) { notificate("Se requiere", "Fecha del Cheque", "warning"); return; }
        if (!$scope.currentCheck.idTypeChecKf) { notificate("Se requiere", "Tipo de cheque", "warning"); return; }
        if (!$scope.currentCheck.idBankKf) { notificate("Se requiere", "Banco Emisor", "warning"); return; }

        $scope.listChecks.push(angular.copy($scope.currentCheck));

        getTotalAmoutChecks('push', $scope.currentCheck.checkAmount)

        $scope.initCheck();


    }
    //---------------------------------------------------------------------------
    function getTotalAmoutChecks(type, amount) {
        if ($scope.totalCheckAmounts == null) {
            $scope.totalCheckAmounts = amount;
        }
        if (type == 'push') {
            $scope.totalCheckAmounts = parseFloat($scope.totalCheckAmounts) + parseFloat(amount);
        } else if (type = 'splice') {
            $scope.totalCheckAmounts = parseFloat($scope.totalCheckAmounts) - parseFloat(amount);
        }

        $summaryChecks = parseFloat($scope.currentExpense.amountExpense) - parseFloat($scope.totalCheckAmounts);

    }
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
           $scope.findExpense();
        }).error(function (data) {
            notificate("Error !" + data.error, " Contacte a Soporte", "error");
        });

    }
    //---------------------------------------------------------------------------
    $scope.removeCheck = function (item) {
        if (!!item.idCheck) $scope.checksAvailables.push(item)
        $scope.listChecks.splice($scope.listChecks.indexOf(item), 1);
        getTotalAmoutChecks('splice', item.checkAmount)
    }
    //---------------------------------------------------------------------------
    $scope.changeTypeForm = function (item) {
        if ($scope.currentExpense.idPaymentFormKf != 2) {

            $scope.checksAvailables = [];
            $scope.listChecks = [];
            $scope.initCheck();
        } else {
            $scope.checkManagement = true;
        }
    }
    //---------------------------------------------------------------------------
    $scope.parseTypeCheck = function (id) {
        if (undefined !== $scope.typesCheck && $scope.typesCheck.length) {
            for (var i = 0; i < $scope.typesCheck.length; i++)
                if (id == $scope.typesCheck[i].idTypeCheck) return $scope.typesCheck[i].nameTypeCheck;

            return null;
        }

    }
    //---------------------------------------------------------------------------/
    $scope.parseBank = function (id) {
        for (var i = 0; i < $scope.banks.length; i++)
            if (id == $scope.banks[i].idBank) return $scope.banks[i].nameBank;

        return null;
    }
    //---------------------------------------------------------------------------/
    $scope.initExpense = function () {

        $scope.currentExpense = {
            amountExpense: null,
            aditionalInfo: null,
            dateCreated: null,
            dateExpense: null,
            idTypeExpenseKf: null,
            idPaymentFormKf: null,
            idUserKf: null,
            idCashBoxKf: null
        }
    }
    //---------------------------------------------------------------------------
    $scope.getListTypeExpenses = function (lookFor) {

        $http.get(uri + 'typeExpense/index' + (!!lookFor ? lookFor : '')).success(function (data) {
            $scope.typeExpenses = data;

        });
    }
    //---------------------------------------------------------------------------
    $scope.getListPaymentForm = function () {

        $http.get(uri + 'PaymentForm/index').success(function (data) {
            $scope.paymentForms = data;
        });
    }

    //---------------------------------------------------------------------------
    $scope.getListCashBox = function () {

        $http.get(uri + 'cashBox/index').success(function (data) {
            $scope.cashBoxList = data;
        });
    }
    //---------------------------------------------------------------------------
    $scope.addExpense = function () {

        $scope.currentExpense.idUserKf = $scope.idUserKf;

        if (parseInt($scope.currentExpense.idPaymentFormKf) != 2) $scope.listChecks = [];

        $http.post(uri + 'expense/add', { expense: $scope.currentExpense, checks: $scope.listChecks }).success(function (data) {
            notificate("Ok", "Egreso creado.", "success");
            /* 				 $scope.initExpense(); */
            $scope.checksAvailables = [];
            $location.path( '/Expenses');
        }).error(function (data) {
            notificate("Error !" + data.error, " Contacte a Soporte", "error");
        });
    };

    $scope.getListExpenses = function () {
        getExpenses();
    }
    //---------------------------------------------------------------------------
    $scope.updateExpense = function () {

        $http.post(uri + 'expense/update', $scope.currentExpense).success(function (data) {
            notificate("Ok", "Egreso actualizado.", "success");
            $scope.initExpense();
            $location.path('/Expenses');
        }).error(function (data) {
            notificate("Error !" + data.error, " Contacte a Soporte", "error");
        });
    };

    //---------------------------------------------------------------------------
    $scope.deleteExpense = function (item) {

        $http.post(uri + 'expense/delete', item).success(function (data) {
            notificate("Ok", "Egreso eliminado.", "success");
            $scope.currentExpense = { nameExpense: null };
            getExpenses();
            $location.path('/Expenses');
        }).error(function (data) {
            notificate("Error !" + data.error, " Contacte a Soporte", "error");
        });
    };
    //---------------------------------------------------------------------------
    $scope.getListExpenses = function () {
        getExpenses();
    }

    function getExpenses(lookFor) {

        $http.get(uri + 'expense/index' + (!!lookFor ? lookFor : '')).success(function (data) {
            $scope.expensesList = data;

            //* PAGINADOR *
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.expensesList.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if (status == 404) { notificate("!Informacion " + status, data.error, "info"); $scope.typeExpenses = [] }
            else { notificate("Error !" + status, " Contacte a Soporte", "error"); }
        });
    }
    //---------------------------------------------------------------------------
    $('#datetimepicker1').datetimepicker({
        format: 'YYYY/MM/DD',
        collapse: false,
        sideBySide: true
    }).on("dp.hide", function (e) {
        $scope.currentCheck.checkDate = $('#checkDate').val();
    });
    //---------------------------------------------------------------------------*/
    $scope.findExpense = function () {
        if (!$routeParams.id) return;
        var expense = {
            'idExpense': $routeParams.id,
            'use': 'out'
        }
        $http.post(uri + 'expense/find', expense).success(function (data) {
            $scope.currentExpense = data.expense;
            $scope.checksInExpense = data.checks; // cheques que pertenecen a un egreso
        }).error(function (data) {
            notificate("Error !" + (!!data) ? data.error : '', " Contacte a Soporte", "error");
        });
    };
    //---------------------------------------------------------------------------
    $scope.searchExpense = function (elm) {
        var params = '';
        if (elm == 'search') {

            $scope.typeFilter = null;
            $scope.formFilter = null;
            $scope.boxFilter = null;

            if (!!$scope.searchFilter) params = '?info=' + $scope.searchFilter;

        } else {

            $scope.searchFilter = null;

            if (!!$scope.typeFilter) params += (params == '' ? '?' : '&') + 'type=' + $scope.typeFilter;
            if (!!$scope.formFilter) params += (params == '' ? '?' : '&') + 'form=' + $scope.formFilter;
            if (!!$scope.boxFilter) params += (params == '' ? '?' : '&') + 'box=' + $scope.boxFilter;

        }
        getExpenses(params);
    };

});

/*****************   MODULO DE TIPOS DE EGRESOS ******************************/
RoutingApp.controller('typeExpensesController', function ($scope, $http, $routeParams, $location, $cookies, $uibModal) {
    $scope.idUserKf = JSON.parse(localStorage.getItem('session')).idUser;
    $scope.typeExpenses = null;
    $scope.currentTypeExpense = { nameExpense: null };
    $scope.topFilter = -1;
    $scope.filter =
        [
            { name: "10", id: 10 },
            { name: "20", id: 20 },
            { name: "30", id: 30 },
            { name: "40", id: 40 },
            { name: "50", id: 50 }
        ];


    $scope.getListTypeExpenses = function () {
        getTypeExpenses();
    }
    //---------------------------------------------------------------------------
    function getTypeExpenses(lookFor) {

        $http.get(uri + 'typeExpense/index' + (!!lookFor ? lookFor : '')).success(function (data) {
            $scope.typeExpenses = data;

            /* PAGINADOR */
            $scope.currentPage = 1;
            $scope.numPerPage = 10;
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.typeExpenses.indexOf(value);
                return (begin <= index && index < end);
            }
        }).error(function (data, status) {
            if (status == 404) { notificate("!Informacion " + status, data.error, "info"); $scope.typeExpenses = [] }
            else { notificate("Error !" + status, " Contacte a Soporte", "error"); }
        });
    }
    //---------------------------------------------------------------------------
    $scope.addTypeExpense = function () {

        $http.post(uri + 'typeExpense/add', $scope.currentTypeExpense).success(function (data) {
            notificate("Ok", "Tipo de egreso creado.", "success");
            $scope.currentTypeExpense = { nameExpense: null };
            $location.path('/Config/TypeExpense');
        }).error(function (data) {
            notificate("Error !" + data.error, " Contacte a Soporte", "error");
        });
    };
    //---------------------------------------------------------------------------
    $scope.updateTypeExpense = function () {

        $http.post(uri + 'typeExpense/update', $scope.currentTypeExpense).success(function (data) {
            notificate("Ok", "Tipo de egreso editado.", "success");
            $scope.currentTypeExpense = { nameExpense: null };
            $location.path('/Config/TypeExpense');
        }).error(function (data) {
            notificate("Error !" + data.error, " Contacte a Soporte", "error");
        });
    };
    //---------------------------------------------------------------------------
    $scope.deleteTypeExpense = function (item) {

        $http.post(uri + 'typeExpense/delete', item).success(function (data) {
            notificate("Ok", "Tipo de egreso eliminado.", "success");
            $scope.currentTypeExpense = { nameExpense: null };
            getTypeExpenses();
            $location.path('/Config/TypeExpense');
        }).error(function (data) {
            notificate("Error !" + data.error, " Contacte a Soporte", "error");
        });
    };
    //---------------------------------------------------------------------------
    $scope.findTypeExpense = function () {
        if (!$routeParams.id) return;
        var typeExpense = {
            'idTypeExpense': $routeParams.id
        }
        $http.post(uri + 'typeExpense/find', typeExpense).success(function (data) {
            $scope.currentTypeExpense = data.typeExpense;
        }).error(function (data) {
            notificate("Error !" + (!!data) ? data.error : '', " Contacte a Soporte", "error");
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
    $scope.searchTypeExpense = function () {
        getTypeExpenses($scope.searchFilter);
    };

});
