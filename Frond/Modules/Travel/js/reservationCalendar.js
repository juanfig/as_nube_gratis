


/*********************************************************/
/*****************   MODULO DE BASE **********************/
RoutingApp.controller('reservationCalendarController', function ($scope, $http,$route, $routeParams, $location) {


 $scope.filter = 
            [
            { name: "10", id: 10 }, 
            { name: "20", id: 20 },
            { name: "30", id: 30 },
            { name: "40", id: 40 },
            { name: "50", id: 50 }
        ];


// BUSCAMOS OS PASAJEROS ASOCIADOS ///
    $scope.refreshrReservation = function()
    {
    	$http.get(uri + 'reservation/bycalendar').success(function (data) {

                $scope.listReservation = data;

                $scope.currentPage = 1;
                $scope.numPerPage = 10;

                $scope.paginate = function (value) {
                    var begin, end, index;
                    begin = ($scope.currentPage - 1) * $scope.numPerPage;
                    end = begin + $scope.numPerPage;
                    index = $scope.listReservation.indexOf(value);
                    return (begin <= index && index < end);
                };
               
        }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });

        
    }


   
     $scope.activeReservation = function (id) {
        // body...

         var r = confirm("Confirme Para Activar el Grupo de Viajes fijos!");

        if (r == true)
        {
            $http.get(uri + 'reservation/active/' + id).success(function (data) {
                $scope.refreshrReservation(); // Actualizamos la tabla
                notificate("Operacion Exitosa","Reservas Reactivados","success"); 

             }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        }
    }

    $scope.inactiveReservation = function (id) {
        // body...

         var r = confirm("Confirme Para Inactivar el Grupo de Viajes fijos!");

        if (r == true)
        {
            $http.get(uri + 'reservation/inactive/' + id).success(function (data) {
                $scope.refreshrReservation(); // Actualizamos la tabla
                 notificate("Operacion Exitosa","Reservas Inactivados","success"); 

            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        }

    }

     $scope.deleteReservation = function (id) {
        // body...
        var r = confirm("Confirme Para eliminar el Grupo de Viajes fijos!");

        if (r == true)
        {
            $http.delete(uri + 'reservation/delete/' + id).success(function (data) {
                $scope.refreshrReservation(); // Actualizamos la tabla
                notificate("Operacion Exitosa","Reservas Eliminadas","success"); 

             }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
        }
    }

});