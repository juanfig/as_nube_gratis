function convertDate(inputFormat) {
    function pad(s) {
        return (s < 10) ? '0' + s : s;
    }
    var d = new Date(inputFormat);
    return [pad(d.getFullYear()), pad(d.getMonth() + 1), d.getDate()].join('/');
}

function convertDateAndTime(inputFormat) {
    function pad(s) {
        return (s < 10) ? '0' + s : s;
    }
    var d = new Date(inputFormat);
    var r = [pad(d.getFullYear()), pad(d.getMonth() + 1), d.getDate()].join('/');

    r = r+" "+d.getHours()+":"+d.getMinutes();
    return r;
}


function divfadeIn(id)
{
    $("#"+id).slideToggle();
       
}

   

    var timerDh;// Timer DHBOARD
    var timerMap;// Timer Mapa
    var directionsDisplay = new google.maps.DirectionsRenderer;
    var directionsService = new google.maps.DirectionsService;
    var auto;
    var vehiclePriceKm;
    var priceHour;
    var priceDitanceCompany;
    var priceHourDriverMultiLan;
    var pague = 1;


// dibujamos ruta //
function calculateAndDisplayRoute( latOrigin, lonOrigin, latDestination, lonDestination) {
   

    var selectedMode = 'DRIVING';
    directionsService.route({
        origin: {lat: latOrigin, lng: lonOrigin}, // Haight.
        destination: {lat: latDestination, lng: lonDestination},
        travelMode: google.maps.TravelMode[selectedMode]
    }, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {


            var distHighway = response.routes[0].legs[0].distance.value / 1000;

            $("#distanceLabel").val(response.routes[0].legs[0].distance.text);
            $("#distance").val(distHighway.toFixed(2));
            $("#durationLabel").val(response.routes[0].legs[0].duration.text);
            $("#duration").val(response.routes[0].legs[0].duration.value);
            $("#distanceLabelText").text("Distancia Aproximada : " + response.routes[0].legs[0].distance.text);
            $("#timeLabelText").text("Tiempo Aproximado : " + response.routes[0].legs[0].duration.text);

            var param1;
            var isTravelByHour = $("#isTravelByHour").is(":checked");
            var isTravelComany = $("#isTravelComany").is(":checked");
            var isBilingualDriver = $("#isBilingualDriver").is(":checked");


            // VERIFICAMOS IS EL VIAJE ES POR Hora
            if(isTravelByHour)
            {

                if(isTravelComany)
                {
                    param1 = priceHour;
                }
                else
                {
                     param1 = JSON.parse(localStorage.getItem('param'))[13].value;
                }

               
               
            }
            else
            {

                  //console.log("4");
                 // VERIFICMOS SI SELECIONO UNA CATEROGRIA DE VEHICULOS O BUSAMOS EL PRECIO DE EL PARAMETRO  1 //
                if(vehiclePriceKm != undefined && vehiclePriceKm >0)
                {
                    param1 = vehiclePriceKm;
                }else
                {
                    //console.log("5");
                    if(isTravelComany)
                    {
                         // console.log("6");
                        param1 = priceDitanceCompany;
                    }else
                    {
                        param1 =  JSON.parse(localStorage.getItem('param'))[0].value;
                    }
                }
            }
            

          


            var amount = param1/1000;
            //console.log(amount)

            var disMetros = response.routes[0].legs[0].distance.value;
            //console.log(disMetros)

            var amountCal = disMetros*amount;

            // Verificamos si selecciono un Chofer Bilingue //
            if(isBilingualDriver)
            {
              amountCal = amountCal+parseFloat(priceHourDriverMultiLan);
            }

             
            $("#amountLabelText").text("Precio Aproximado : " + amountCal.toFixed(2)+ "$");




            directionsDisplay.setDirections(response);
        } else {
            window.alert('Directions request failed due to ' + status);
        }
    });
}


// Creación del módulo
var RoutingApp = angular.module('routingTravel',
 ['ngAnimate', 'ngSanitize', 'ngRoute', 'ui.bootstrap']);


// Configuración de las rutas desde el menu
RoutingApp.config(function ($routeProvider) {

    $routeProvider
            .when("/", {
                templateUrl: 'View/list.html',
                controller: 'travelController'
            })
            .when('/Add', {
                templateUrl: 'View/Form.html',
                controller: 'travelController'
            })
            .when('/AddReservation', {
                templateUrl: 'View/AddReservationStatic.html',
                controller: 'travelController'
            })
            .when('/AddReservationCalendar', {
                templateUrl: 'View/AddReservationCalendar.html',
                controller: 'travelController'
            })
            .when('/Edit:id', {
                templateUrl: 'View/Form.html',
                controller: 'travelController'
            })
            .when('/Reservation', {
                templateUrl: 'View/staticReservation.html',
                controller: 'reservationController'
            })
            .when('/ReservationCalendar', {
                templateUrl: 'View/calendarReservation.html',
                controller: 'reservationCalendarController'
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




RoutingApp.directive('googlePlace', directiveFunction);
directiveFunction.$inject = ['$rootScope'];



// DIRECTIVA PARA ACTIVAR EL INOPUT DE LA UBICACION 
function directiveFunction($rootScope) {



    return {
        require: 'ngModel',
        scope: {
            ngModel: '=',
            details: '=?'
        },
        link: function (scope, element, attrs, model) {
            var options = {
                types: [],
                componentRestrictions: {}
            };
            scope.gPlace = new google.maps.places.Autocomplete(element[0], options);

            auto = scope.gPlace;

            google.maps.event.addListener(scope.gPlace, 'place_changed', function () {
                scope.$apply(function () {
                    scope.details = scope.gPlace.getPlace();
                    model.$setViewValue(element.val());
                    $rootScope.$broadcast('place_changed', scope.details);
                });
            });



        }
    };
}









/*********************************************************/
/*****************   MODULO DE BASE **********************/
RoutingApp.controller('travelController', function ($scope, $http,$route, $routeParams, $location) {

    $scope.Checks = [];

    
   
    var country = null;
    var origin = null;//origen
    var destination = null;//destino
    var countryClient = null;
    var countryPasager = null;
    var point = null;// variable para control de seteo de objetos origen y destino 
    var markers = [];
    var date = Date();
    $scope.date = convertDate(date);
    $scope.filter = "";
    $scope.idStatusTravel = -1;
    $scope.isTravelComany = false;
    $scope.typeTravelFilter = -1;
    $scope.viewMap = false;
    $scope.filterDate = false;
    $scope.idProfileUser = JSON.parse(localStorage.getItem('session')).idProfileUser;
    $scope.param3 = JSON.parse(localStorage.getItem('param'))[2].value;
    $scope.currentPage = 1;
    $scope.idUser = JSON.parse(localStorage.getItem('session')).idUser;
    $scope.amountBilingualDriver = 0;
    $scope.isBilingualDriver = false;

    if($scope.idProfileUser == "4")
    {
        $("#headerDhBoardTravel").hide();
    }


    $scope.$on('$routeChangeStart', function(next, current) { 
   
    clearInterval(timerDh);
    clearInterval(timerMap);
 });


   
    $scope.$on('$viewContentLoaded', function() {
        //call it here

        if($scope.idTravel == undefined)
        {
            setTimeout(function(){ 

             var date = new Date(); 
             var hours = date.getHours() > 24 ? date.getHours() - 24 : date.getHours();
              $('#dateTravel').val(date.getFullYear()+ "/" +(date.getMonth()+1)+ "/" +date.getDate() +" "+hours  + ":" + date.getMinutes());
            
             }, 3000);

            
        }

        if($location.path() === "/")
        {
            $scope.activeTimer();
        }else
        {
            clearInterval(timerDh);
        }
    });

    // LISTENER PARA ACTULIZAR LITADO DE VIAJES //
    $scope.activeTimer = function()
    {
        timerDh =  setInterval(function () {
                $scope.serviceDataFromDashboard();
            }, 9000);
    }

    
     $scope.setDestino = function () {

        var ndestination =
        {
            nameDestination:"",
            latDestination:"",
            lonDestination:""
        };
               
        for (var i = 0; i < $scope.filterForm.preTravel.length; i++) {

            if ($scope.filterForm.preTravel[i].idPreDestination == $scope.idPreDestination)
            {
               
                ndestination.nameDestination =  $scope.filterForm.preTravel[i].namePreDestination;
                ndestination.latDestination  =  $scope.filterForm.preTravel[i].latPreDestination;
                ndestination.lonDestination  =  $scope.filterForm.preTravel[i].lonPreDestination;

                 destination = ndestination;

                 $scope.refreshMap();
                 $("#nameDestination").val($scope.filterForm.preTravel[i].namePreDestination);
             
                break;
            }
        }
    }



    $scope.setOriguinClient = function ()
    {
         var nOriguin =
            {
                nameOrigin:"",
                latOrigin:"",
                lonOrigin:""
            };
                   
            for (var i = 0; i < $scope.originClient.length; i++) {

                if ($scope.originClient[i].idTravel == $scope.idOriginClient)
                {


                   
                    nOriguin.nameOrigin =  $scope.originClient[i].nameOrigin;
                    nOriguin.latOrigin  =  $scope.originClient[i].latOrigin;
                    nOriguin.lonOrigin  =  $scope.originClient[i].lonOrigin;

                     origin = nOriguin;

                     $scope.refreshMap();
                     $("#nameOrigin").val($scope.originClient[i].nameOrigin);
                 
                    break;
                }
            }
    }


    $scope.setDestinationClient = function ()
    {
         var ndestination =
            {
                nameDestination:"",
                latDestination:"",
                lonDestination:""
            };
                   
            for (var i = 0; i < $scope.desinationClient.length; i++) {

                if ($scope.desinationClient[i].idTravel == $scope.idDestinationClient)
                {
                   
                    ndestination.nameDestination =  $scope.desinationClient[i].nameDestination;
                    ndestination.latDestination  =  $scope.desinationClient[i].latDestination;
                    ndestination.lonDestination  =  $scope.desinationClient[i].lonDestination;

                     destination = ndestination;

                     $scope.refreshMap();
                     $("#nameDestination").val($scope.desinationClient[i].nameDestination);
                 
                    break;
                }
            }
    }

    $scope.$on('place_changed', function (e, place) {

     var changueCountry = false;
        $scope.topFilter = -1; // Filtro Inicializado
        
        if (point === 1)
        {
            origin = place;
            changueCountry = true;
        } else if (point === 2)
        {
            destination = place;
            changueCountry = true;
        }else if (point === 3)
        {
           country = place;
           changueCountry = true;
        }
        else if (point === 4)
        {
           countryClient = place;
        }else if (point === 5)
        {
            countryPasager = place;
        }

        

        $scope.refreshMap();


    });



    $scope.refreshMap = function()
    {
       

        if (origin != undefined && destination != undefined)
        {
            var latO;  
            var lngO;  

           


            if(origin.geometry  != undefined)
                {
                    latO = origin.geometry.location.lat();
                    lngO = origin.geometry.location.lng();
                }
                else
                {
                    latO = parseFloat(origin.latOrigin);
                    lngO = parseFloat(origin.lonOrigin);
                }


            var latD;
            var lngD;

                if(destination.geometry  != undefined)
                {
                    latD = destination.geometry.location.lat();
                    lngD = destination.geometry.location.lng();
                }
                else
                {
                    latD = parseFloat(destination.latDestination);
                    lngD = parseFloat(destination.lonDestination);
                }




            calculateAndDisplayRoute(latO, lngO, latD, lngD);

        }

    }

    $scope.initMap = function () {



        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            center: {lat: 37.77, lng: -122.447}
        });

        directionsDisplay.setMap(map);

    }



    var map;
    $scope.initMap2 = function () {



        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: {lat: -34.603722, lng: -58.381592}
        });

        directionsDisplay.setMap(map);

    }


    $scope.setMarckMa = function ()
    {
        var image = '../../assets/img/tx.png';


        var locations = [];
        $scope.hideMarkers();


        if ($scope.allDriver != undefined)
        {

            for (i = 0; i < $scope.allDriver.length; i++) {
                var dr = [
                    $scope.allDriver[i].driver,
                    $scope.allDriver[i].latAddresVehicle,
                    $scope.allDriver[i].longAddresVehicle,
                ];

                locations.push(dr);
            }


            var infowindow = new google.maps.InfoWindow();

            var marker, i;


            for (i = 0; i < locations.length; i++) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                    map: map,
                    icon: image
                });

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        infowindow.setContent(locations[i][0]);
                        infowindow.open(map, marker);
                    }
                })(marker, i));

                markers.push(marker);

            }

        }
    }


    $scope.viewDetaill = function (id) {


        // LLAMAMOS A EL SERVICIO FILTROS PARA EL FORMULARIO //
        $http.get(uri + 'travel/findDriverHeader/' + id).success(function (data) {



            $scope.infocodTravel = data[0].codTravel;
            $scope.infoclient = data[0].client;
            $scope.infodriver = data[0].driver;
            $scope.infonameOrigin = data[0].nameOrigin;
            $scope.infonameDestination = data[0].nameDestination;
            $scope.infototalAmount = data[0].totalAmount;
            $scope.infotime = data[0].durationLabel;




        }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }





    $scope.hideMarkers = function () {

        if (markers != undefined)
        {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null); //Remove the marker from the map
            }
        }
    }


    $scope.setLocation = function (id) {
        point = id;
    }



    $scope.getClient = function (name) {

        var filter =
                {
                    filter:
                            {
                                isTravelComany: $scope.isTravelComany,
                                name: name
                            }

                }

        return $http.post(uri + 'travel/clientByName', filter).then(function (response) {
            return response.data.map(function (item) {
                return item;
            });
        });
    };



    // SELECCIONA EL CLIENTE  //
    $scope.selectedClient = function (item) {
        $scope.idClient = item.idClient;


        $scope.passenger1 = "";
        $scope.passenger2 = "";
        $scope.passenger3 = "";
        $scope.passenger4 = "";

        priceHour = item.priceHour;
        priceDitanceCompany =  item.priceDitanceCompany;
        priceHourDriverMultiLan = item.priceHourDriverMultiLan;

        if(!$scope.isTravelComany)
        {
            $scope.setInfoPasaguer(
            item.firtNameClient + ' ' + item.lastNameClient,
            item.phoneClient,
            item.mailClient,
            item.addresClient
            );
        }


        $scope.refreshPasaguer();
    }


    $scope.setPasagerInfo = function(index)
    {
      
        var pasaguer;
        var id;

        if(index == 1){  id = $scope.passenger1;  }
        if(index == 2){  id = $scope.passenger2;  }
        if(index == 3){  id = $scope.passenger3;  }
        if(index == 4){  id = $scope.passenger4;  }

        

      for (var i = 0; i < $scope.listPasagger.length; i++) {
            
            if ($scope.listPasagger[i].idPasaguer == id)
            {
                pasaguer = $scope.listPasagger[i];
                break;
            }
        }


        if(index == 1)
        {
             $scope.pasaguerInfo1 = pasaguer;
        }

        if(index == 2)
        {
             $scope.pasaguerInfo2 = pasaguer;
        }

        if(index == 3)
        {
             $scope.pasaguerInfo3 = pasaguer;
        }

        if(index == 4)
        {
             $scope.pasaguerInfo4 = pasaguer;
        }
       
    }

    $scope.viewInfoPasaguer = function(index)
    {

        if(index == 1)
        {
            $scope.setInfoPasaguer(
            $scope.pasaguerInfo1.fullNamePanguer,
            $scope.pasaguerInfo1.phonePasaguer,
            $scope.pasaguerInfo1.emailPasaguer,
            $scope.pasaguerInfo1.direcctionPasaguer
            );
        }

        if(index == 2)
        {
            $scope.setInfoPasaguer(
            $scope.pasaguerInfo2.fullNamePanguer,
            $scope.pasaguerInfo2.phonePasaguer,
            $scope.pasaguerInfo2.emailPasaguer,
            $scope.pasaguerInfo2.direcctionPasaguer
            );
        }

        if(index == 3)
        {
            $scope.setInfoPasaguer(
            $scope.pasaguerInfo3.fullNamePanguer,
            $scope.pasaguerInfo3.phonePasaguer,
            $scope.pasaguerInfo3.emailPasaguer,
            $scope.pasaguerInfo3.direcctionPasaguer
            );
        }

        if(index == 4)
        {
            $scope.setInfoPasaguer(
            $scope.pasaguerInfo4.fullNamePanguer,
            $scope.pasaguerInfo4.phonePasaguer,
            $scope.pasaguerInfo4.emailPasaguer,
            $scope.pasaguerInfo4.direcctionPasaguer
            );
        }
       
    }


    $scope.setInfoPasaguer = function(namePasaguerInfo,phonePasaguerInfo,
        emailPasaguerInfo,direccionPasaguerInfo)
    {
        $scope.namePasaguerInfo = namePasaguerInfo;
        $scope.phonePasaguerInfo = phonePasaguerInfo;
        $scope.emailPasaguerInfo = emailPasaguerInfo;
        $scope.direccionPasaguerInfo = direccionPasaguerInfo;
    }


    // BUSCAMOS TODOS PASAJEROS ASOCIADOS ///
    $scope.refreshPasaguer = function()
    {

        if($scope.idClient !=  undefined)
        {
          $http.get(uri + 'travel/pasangue/' + $scope.idClient).success(function (data) {

               // $scope.listPasagger = data.pasangue;
                $scope.acountcompany = data.acountcompany;
                $scope.originClient = data.originClient;
                $scope.desinationClient =  data.desinationClient;
                $scope.risponsable = data.risponsable;

            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });

        }else
        {
          
            notificate("Debe seleccionar un Cliente","","success"); 
        }
    }

    $scope.callServiceFilterFormReservation = function () {
       // clearInterval(timerDh);
         // LLAMAMOS A EL SERVICIO FILTROS PARA EL FORMULARIO //
        $http.get(uri + 'reservation/filterFormReservation').success(function (data) {

            $scope.filterFormRes = data;
         }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });

    }

    // SERVICIO QUE RETORNA LOS FILTROS //
    $scope.callServiceFilterForm = function () {

        $('#datetimepicker1').datetimepicker({
            format: 'YYYY/MM/DD hh:mm'
        });

        $('#datetimepicker2').datetimepicker({
            format: 'LT'
        });


        var dataFilter;

        // LLAMAMOS A EL SERVICIO FILTROS PARA EL FORMULARIO //
        $http.get(uri + 'travel/filterForm').success(function (data) {

            dataFilter = data;

            if ($routeParams.id != null)
            {
                $scope.callTravelById($routeParams.id,dataFilter);	
            } else
            {
                $scope.emailDriver = "---";
                $scope.filterForm = dataFilter;
            }


            if (JSON.parse(localStorage.getItem('session')).idProfileUser == "4")
            {
                $scope.isTravelComany = true;
                $scope.asyncSelected = JSON.parse(localStorage.getItem('session')).nameClientCompany;
                $scope.idClient = JSON.parse(localStorage.getItem('session')).idClient;


                $("#isTravelComany").prop('disabled', true);
                $("#inputClient2").prop('disabled', true);

                $scope.refreshPasaguer();
            }



        }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
    }



    $scope.callTravelById = function (id,dataFilter = null)
    {
         $http.get(uri + 'travel/find/' + id).success(function (data) {


            $scope.idTravel = id;
            $scope.filterForm = dataFilter;

            $scope.codTravel = data.codTravel;


            if (data.isTravelComany == 1)
            {
                $scope.isTravelComany = true;
                $("#inputClient2").val(data.client);
                $scope.idClienteKf = data.idClient;
                

                var item = 
                {
                    idClient:data.idClient
                };

                $scope.selectedClient(item);
            }else
            {
                 $("#inputClient").val(data.client);
                 
            }

            //$("#dateTravel").val();
            $("#hoursAribo").val(data.hoursAribo);
            
            $scope.idUserRes   = data.idUserRes;


            $scope.idHotel   = data.idHotel;
            
            $scope.idDriverKf = data.idDriverKf;

            if(data.emailDriver != null)
            {
                $scope.emailDriver = data.emailDriver;
            }

           

            if(data.idCompanyAcountKf != null)
            {
                 $scope.idCompanyAcountFilter   = data.idCompanyAcountKf;
                 $scope.getCostCenterByidAcount();
                 $scope.idCostCenter   = data.idCostCenter;
            }
            
            $scope.idClient = data.idClient;
            $scope.idTypeVehicle   = data.idTypeVehicle;
            $scope.isDriverHeader   = data.isDriverHeader;
            $scope.idAribos   = data.idAribos;
            $scope.flight   = data.flight;
            $scope.precedence   = data.precedence;
            $scope.companyLabel   = data.companyLabel;
            $scope.floor   = data.floor;
            $scope.department   = data.department;
            $scope.lot   = data.lot;
            $scope.phoneNumber   = data.phoneNumber;
            $scope.localNumber   = data.localNumber;
            $scope.isDriverBilin   = data.isDriverBilin;
            $scope.isExtraLuggage   = data.isExtraLuggage;
            $scope.idSatatusTravel   = data.idSatatusTravel;
            $scope.passenger1   = data.passenger1;
            $scope.passenger2   = data.passenger2;
            $scope.passenger3   = data.passenger3;
            $scope.passenger4   = data.passenger4;
            
             $("#distanceLabel").val(data.distanceLabel);
             $("#distance").val(data.distance);
             $("#durationLabel").val(data.durationLabel);
             $("#duration").val(data.duration);
            

             $('#dateTravel').val(convertDateAndTime(data.dateTravel));
             
             origin = data.origin;
             $("#nameOrigin").val(data.origin.nameOrigin).trigger("change");
             

             destination = data.destination;
             $("#nameDestination").val(data.destination.nameDestination).trigger("change");
            

            $scope.refreshMap();


       }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }


    $scope.dateCalendar = [];
    // metodo que agrega fechas  por el calendario //
    $scope.addDate = function () {
        $scope.dateCalendar.push($("#dateReservationInput").val());
        //console.log($("#dateReservationInput").val());
    }

     // SERVICION QUE AGREGA SERSERVAS Por calendario //
    $scope.addReservationByCalendar = function () {
    
        $("#newTraveBtn").prop("disabled",true);
      

        if( $scope.dateCalendar.length > 0)
            
        {

            if ($scope.idClient > 0)
            {
                if ($("#dateTravel").val() != "")
                {

                    var idDriverKf;

                    if ($scope.changueCountry)
                    {
                        idDriverKf = $("#idDriverKf").val();
                    } else
                    {
                        idDriverKf = $scope.idDriverKf;
                    }


                if(origin != null)
                {

                    var reservation =
                        {
                            nameGroupRes : $scope.nameGroupRes,
                            idDayWeek : $scope.dateCalendar,
                            idUser : JSON.parse(localStorage.getItem('session')).idUser
                        }

                      var tr = $scope._setTravel();
                      var reservation =
                        {
                            reservation:
                                    {
                                        header: reservation,
                                        tr
                                    }

                        }

                        

                    // VALIDAMOS BILINGUE //
                    var validatorBilingue = true;
                    
                    if(isTravelComany)
                    {
                        if($scope.isBilingualDriver == 1 && $scope.idDriverKf == null)
                        {
                            validatorBilingue = false;
                        } 
                    }

                    
                    if(validatorBilingue)
                    {

                        $http.post(uri + 'travel/addReservationCalendar', reservation).success(function (data) {
                            
                            notificate("Operacion Exitosa","Reservas Agregadas","success"); 


                            $location.url('ReservationCalendar');
                            $("#newTraveBtn").prop("disabled",false);
                        }).error(function (data,status) {
                            $("#newTraveBtn").prop("disabled",false);
                            
                            $("#spanClient").hide();
                            $("#spanDate").hide();


                            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                            else{notificate("Error !"+status," Contacte a Soporte","error");}
                    
                        });
                     }else
                    {
                        $("#newTraveBtn").prop("disabled",false);
                        notificate("!Informacion","Debe Indicar el Chofer Bilingue","info"); 
                    }

                }else
                {
                    $("#newTraveBtn").prop("disabled",false);
                    notificate("!Informacion"," Indique un origen","info"); 
                }
                   


                } else
                {

                    $("#newTraveBtn").prop("disabled",false);
                    $("#spanDate").show();
                    $("#dateTravel").focus();
                }

            } else
            {
                $("#newTraveBtn").prop("disabled",false);
                $("#spanClient").show();
                $("#inputClient").focus();
            }
        }else
        {
            notificate("Campos Requeridos "+status,"!Complete todos los campos de la primera ficha","info");
        }
    }


    // SERVICION QUE AGREGA SERSERVAS FIJAS //
    $scope.addReservation = function () {
    
        $("#newTraveBtn").prop("disabled",true);
        var hora = $("#hours").val();

        if($scope.hours != "" && $scope.idDayWeek != "" && 
            $scope.idMounth != "" && $scope.idYear != "" 
            )
        {

            if ($scope.idClient > 0)
            {
                if ($("#dateTravel").val() != "")
                {

                    var idDriverKf;

                    if ($scope.changueCountry)
                    {
                        idDriverKf = $("#idDriverKf").val();
                    } else
                    {
                        idDriverKf = $scope.idDriverKf;
                    }


                if(origin != null)
                {

                    var reservation =
                        {
                            nameGroupRes : $scope.nameGroupRes,
                            idDayWeek : $scope.idDayWeek,
                            idMounth : $scope.idMounth,
                            idYear : $scope.idYear,
                            hours:hora,
                            idUser : JSON.parse(localStorage.getItem('session')).idUser
                        }


                        var tr = $scope._setTravel();


                      var reservation =
                        {
                            reservation:
                                    {
                                        header: reservation,
                                        tr
                                    }

                        }

                    // VALIDAMOS BILINGUE //
                    var validatorBilingue = true;
                    
                    if(isTravelComany)
                    {
                        if($scope.isBilingualDriver == 1 && $scope.idDriverKf == null)
                        {
                            validatorBilingue = false;
                        } 
                    }

                    
                    if(validatorBilingue)
                    {
                        $http.post(uri + 'travel/addReservation', reservation).success(function (data) {
                            
                            notificate("Operacion Exitosa","Viaje Agregado","success"); 


                            $location.url('Reservation');
                            $("#newTraveBtn").prop("disabled",false);
                        }).error(function (data,status) {
                            $("#newTraveBtn").prop("disabled",false);
                            
                            $("#spanClient").hide();
                            $("#spanDate").hide();

                            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                            else{notificate("Error !"+status," Contacte a Soporte","error");}
                         });
                    }
                    else
                    {
                        $("#newTraveBtn").prop("disabled",false);
                        notificate("!Informacion","Debe Indicar el Chofer Bilingue","info"); 
                    }   

                }else
                {
                    $("#newTraveBtn").prop("disabled",false);
                    notificate("Operacion Exitosa","Indique un origen","success"); 
                }
                   


                } else
                {

                    $("#newTraveBtn").prop("disabled",false);
                    $("#spanDate").show();
                    $("#dateTravel").focus();
                }

            } else
            {
                $("#newTraveBtn").prop("disabled",false);
                $("#spanClient").show();
                $("#inputClient").focus();
            }
        }else
        {
            notificate("Campos Requeridos "+status,"!Complete todos los campos de la primera ficha","info");
        }
    }


    // SERVICIO DE AGREGAR VIAJE //
    $scope.addTravel = function () {

        $("#newTraveBtn").prop("disabled",true);

        if ($scope.idClient > 0)
        {
            if ($("#dateTravel").val() != "")
            {

                var idDriverKf;

                if ($scope.changueCountry)
                {
                    idDriverKf = $("#idDriverKf").val();
                } else
                {
                    idDriverKf = $scope.idDriverKf;
                }


            if(origin != null)
            {

                // VALIDAMOS BILINGUE //
                var validatorBilingue = true;
                
                if(isTravelComany)
                {
                    if($scope.isBilingualDriver == 1 && $scope.idDriverKf == null)
                    {
                        validatorBilingue = false;
                    } 
                }


                if(validatorBilingue)
                {

                    $http.post(uri + 'travel', $scope._setTravel()).success(function (data) {
                        
                        notificate("Operacion Exitosa","Viaje Agregado","success"); 


                        $location.url('Travel');
                        $("#newTraveBtn").prop("disabled",false);
                    }).error(function (data,status) {
                        $("#newTraveBtn").prop("disabled",false);
                        
                        $("#spanClient").hide();
                        $("#spanDate").hide();


                        if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                        else{notificate("Error !"+status," Contacte a Soporte","error");}
               
                       
                    });
                }else
                {
                    $("#newTraveBtn").prop("disabled",false);
                    notificate("!Informacion","Debe Indicar el Chofer Bilingue","info"); 
                }

                
            }else
            {
                $("#newTraveBtn").prop("disabled",false);
                notificate("!Informacion","Indique un origen","info"); 
            }
               


            } else
            {

                $("#newTraveBtn").prop("disabled",false);
                $("#spanDate").show();
                $("#dateTravel").focus();
            }

        } else
        {
            $("#newTraveBtn").prop("disabled",false);
            $("#spanClient").show();
            $("#inputClient").focus();
        }
    }


    $scope.clickBilingual = function ()
    {
            if($scope.isBilingualDriver == false)
            {
                $scope.idLanguajeKf = -1;
                $scope.getDriverBilingual();

                $scope.amountBilingualDriver = 0;
            }else
            {
                $scope.amountBilingualDriver = priceHourDriverMultiLan;
            }

            $scope.refreshMap();
    }

    //  Buscamos Choferes Bilingues //
    $scope.getDriverBilingual = function () {

        // LLAMAMOS A EL SERVICIO FILTROS PARA EL FORMULARIO //
        $http.get(uri + 'driver/byLang/' + $scope.idLanguajeKf).success(function (data) {

            $scope.filterForm.drivers = data;

        }).error(function (data,status) {
            if(status == 404){
                notificate("!Informacion "+status,data.error,"info");
                $scope.filterForm.drivers = null;
            }
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    };



    /* Buscamos el Chofer de cabecera */
    $scope.getDriverHeaders = function () {

        if ($scope.idClient > 0)
        {
            if ($scope.isDriverHeader)
            {

                // LLAMAMOS A EL SERVICIO FILTROS PARA EL FORMULARIO //
                $http.get(uri + 'travel/findDriverHeader/' + $scope.idClient).success(function (data) {



                    $scope.idDriverKf = data['idDriverKf'];
                    $scope.emailDriver = data['emailDriver'];

                }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
                });
            }
        }

    }


    $scope.setDriver = function (id) {
        // body...


        for (var i = 0; i < $scope.filterForm.drivers.length; i++) {

            if ($scope.filterForm.drivers[i].idDriver == $scope.idDriverKf)
            {
                $scope.emailDriver = $scope.filterForm.drivers[i].emailDriver;
                break;
            }
        }

    }


     $scope.setTipeVehicle =  function()
     {
         for (var i = 0; i < $scope.filterForm.typevehicle.length; i++) {

            if ($scope.filterForm.typevehicle[i].idVehicleType == $scope.idTypeVehicle)
            {
                vehiclePriceKm = $scope.filterForm.typevehicle[i].vehiclePriceKm;
                break;
            }
        }
     }


    $scope.setValueStatus = function (id) {

        $scope.idStatusTravel = id;
        $scope.serviceDataFromDashboard();
    }



    // SERVICO QUE BUSCA INFORMACION DE DASHBOARD//
    $scope.serviceDataFromDashboard = function () {

        pague = $scope.currentPage;


        if ($('#date').val() != "")
        {
            $scope.date = $('#date').val();
        }
        
        $scope.appModeIs = JSON.parse(localStorage.getItem('appMode'));
        
        if ($scope.appModeIs == undefined) {
            $scope.appModeIs = 0;
        }

      

        var idClientFilter = -1;

        if (JSON.parse(localStorage.getItem('session')).idProfileUser == "4")
        {
            idClientFilter = JSON.parse(localStorage.getItem('session')).idClient;
        }

        var filter =
                {
                    filter:
                            {
                                date: $scope.date,
                                filter: $scope.filter,
                                idStatusTravel: $scope.idStatusTravel,
                                idClientKf: idClientFilter,
                                typeTravelFilter: $scope.typeTravelFilter,
                                filterDate: $scope.filterDate
                            }

                }



        $http.post(uri + 'travel/dataFromDashboard', filter).success(function (data) {
            $scope.lisTravel = data['data'];
            $scope.lisFilet = data['infoFilter'];
           
            /* PAGINADOR */
            $scope.totalItems = $scope.lisTravel.length;
            

            if($scope.totalItems > 10)
            {
                pague = pague;
            }
            else
            {
                pague = 1
            }

            $scope.currentPage = pague;
            $scope.numPerPage = 10;

            
            $scope.paginate = function (value) {
                var begin, end, index;
                begin = ($scope.currentPage - 1) * $scope.numPerPage;
                end = begin + $scope.numPerPage;
                index = $scope.lisTravel.indexOf(value);
                return (begin <= index && index < end);
            };

        }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });

    }

    $scope.setDriverTop = function (id)
    {
        $("#idDriverTopTxt").val(id);
        $(".diver_row").css("background-color", "#fafafa");
        $("#dr_row"+id).css("background-color", "#b8d9ff");
    }


    $scope.setDriverFree = function ()
    {
        $("#idDriverPreFree").prop('selectedIndex', 0);
        $("#idDriverFreeTxt").val("");
        $("#idDriverTopTxt").val("");
        $(".diver_row").css("background-color", "#fafafa");
    }

    $scope.setDriverPreFree = function ()
    {

        $("#idDriverFree").prop('selectedIndex', 0);
        $("#idDriverTopTxt").val("");
        $("#idDriverPreFreeTxt").val("");

        $(".diver_row").css("background-color", "#fafafa");
    }

     $scope.setDriverBilingual = function ()
    {

        $("#idDriverBilingual").prop('selectedIndex', 0);
        $("#idDriverTopTxt").val("");
        $("#idDriverPreFreeTxt").val("");
        $("#idDriverByCategoryTxt").val("");
        

        $(".diver_row").css("background-color", "#fafafa");
    }

     $scope.setDriverByCategory = function ()
    {

        $("#idDriverFree").prop('selectedIndex', 0);
        $("#idDriverTopTxt").val("");
        $("#idDriverPreFreeTxt").val("");
        $("#idDriverBilingual").val("");

        $(".diver_row").css("background-color", "#fafafa");
    }


    




    $scope.editTravelInfo = function (travel) {
        // LLAMAMOS A EL SERVICIO FILTROS PARA EL FORMULARIO //
       
        travel['dateFilter'] = $("#date").val();
        var objTravel =
                {
                    travel
                };

       
        $http.post(uri + 'travel/getFilterFormAsignDriver', objTravel).success(function (data) {

            $scope.filterFronEdit = data;
            $scope.idTravel = travel.idTravel;
            $('#myEdit').modal('toggle');
        

        }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
               
        });

    }


    // EDITAR VIAJE FULL //
    $scope.updateTravelFull = function ()
    {
         $("#updateTraveBtn").prop("disabled",true);

        if ($scope.idClient > 0)
        {
            if ($("#dateTravel").val() != "")
            {

                var idDriverKf;

                if ($scope.changueCountry)
                {
                    idDriverKf = $("#idDriverKf").val();
                } else
                {
                    idDriverKf = $scope.idDriverKf;
                }


            if(origin != null)
            {

                $http.post(uri + 'travel/updatefull', $scope._setTravel()).success(function (data) {
                    notificate("Operacion Exitosa",data.response,"success"); 
                    $location.url('Travel');
                    $("#updateTraveBtn").prop("disabled",false);
                }).error(function (data,status) {
                    $("#updateTraveBtn").prop("disabled",false);
                   
                    $("#spanClient").hide();
                    $("#spanDate").hide();

                    if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                    else{notificate("Error !"+status," Contacte a Soporte","error");}
         
                   
                });
            }else
            {
                $("#updateTraveBtn").prop("disabled",false);
                notificate("Operacion Exitosa","Indique un origen","success"); 
            }
               


            } else
            {

                $("#updateTraveBtn").prop("disabled",false);
                $("#spanDate").show();
                $("#dateTravel").focus();
            }

        } else
        {
            $("#updateTraveBtn").prop("disabled",false);
            $("#spanClient").show();
            $("#inputClient").focus();
        }
    }


    $scope.updateTravel = function ()
    {
        loading();// Loading

        // SERVICIO QUE EDITA VIAJE SOLO CHOFER //
        $http.post(uri + 'travel/update', $scope._setAsigClient()).success(function (data) {
            
            
            notificate("Operacion Exitosa","Chofer Asignado","success"); 
            $("#idDriverPreFree").prop('selectedIndex', 0);
            $("#idDriverFreeTxt").val("");
            $("#idDriverFree").prop('selectedIndex', 0);
            $("#idDriverPreFreeTxt").val("");

            if($scope.idDriverFree != undefined)
            {
                $scope.idDriverFree = 0;
            }

            if($scope.idDriverPreFree != undefined)
            {
                $scope.idDriverPreFree = 0;
            }

            if($scope.idDriverBilingual != undefined)
            {
                $scope.idDriverBilingual = 0;
            }

            if($scope.idDriverByCategory != undefined)
            {
                $scope.idDriverByCategory = 0;
            }


            
            
            $("#closeModal").click();
            

            $scope.serviceDataFromDashboard();
            loading();// Loading
        }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
                loading();// Loading
            });
    }


    $scope.changueView = function ()
    {

        if ($scope.viewMap)
        {
            $scope.viewMap = false;
            clearInterval(timerMap); 
            $scope.activeTimer();
        } else
        {
            $scope.viewMap = true;
             clearInterval(timerDh);
        }

    }


   

    $scope.getDriverMap = function () {

       timerMap = setInterval(function () {

            $http.get(uri + 'travel/getDriverMap').success(function (data) {

                $scope.allDriver = data;
                $scope.setMarckMa();


            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });

        }, 5000);


    }

    

    $scope.getPasagerByidCosCnter = function ()
    {
         $http.get(uri + 'pasaguer/pasagerbyidcostcenter/' + $scope.idCostCenter).success(function (data) {

                $scope.listPasagger = data;

            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        
    }


    $scope.getCostCenterByidAcount = function ()
    {
         $http.get(uri + 'travel/costCenterByidAcount/' + $scope.idCompanyAcountFilter).success(function (data) {

                $scope.costcenter = data;

            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        
    }

    // ventana modal para ver Firma //
    $scope.openModalFirma = function($id)
    {

        $scope.idTravelFirma = $id;
        
                $('#myModalFirma').modal('toggle');


           
    }

   
    




    // ventana odal para cancelar viajes //
    $scope.openModalCancelTravel = function($id)
    {
        

        $http.get(uri + 'travel/reason').success(function (data) {

                $scope.reason = data;
                $('#myModalCancelTravel').modal('toggle');
                $scope.idCancelTravel = $id;


            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            }); 
    }


     // ventana odal para Ver choferes disponibles  viajes //
    $scope.openModalAllDrivers = function($id)
    {
        
        loading();// Loading
        $http.get(uri + 'driver/all').success(function (data) {

                $scope.driversfree = data;
                $('#myModalDrivers').modal('toggle');
                loading();// Loading

            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
                loading();// Loading
            }); 
    }



    $scope.cancelTravel = function () {
        // body...
        
        if($scope.idCancelTravel >0)
        {
             var travel =
                        {
                            travel:
                                    {
                                        idSatatusTravel: 0,
                                        descriptionCancelTravel: $("#descriptionCancelTravel").val(),
                                        idReasonCancelKf : $("#idReasonCancelKf").val(),
                                        idTravel: $scope.idCancelTravel,
                                        idUserCancel: JSON.parse(localStorage.getItem('session')).idUser
                                    }

                        }


                $http.post(uri + 'travel/cancel',travel).success(function (data) {
                   
                    
                    notificate("Operacion Exitosa","Viaje Cancelado!","success"); 
                    $('#myModalCancelTravel').modal('toggle');
                    $scope.serviceDataFromDashboard(); // Actualizamos la tabla
                
                }).error(function (data,status) {
                    if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                    else{notificate("Error !"+status," Contacte a Soporte","error");}
                });
        }
        
    }


    $scope.openModalFinisfTravel = function($travel)
    {
        if($travel.isTravelComany)
        {
                $("#txtmodalFinishfAmountSlepp").prop('disabled', true);
                 $("#txtmodalFinishfAmountSlepp").val("0");
        }

             var travel =
                        {
                            travel:
                                    {
                                       
                                        idTravel: $scope.modalFinishfIdTravel,
                                        isTravelByHour: $scope.modalFinisiIsTravelByHour,
                                        isTravelComany: $scope.modalFinisIsTravelComany,
                                        idClientKf: $scope.modalFinisIdClientKf,
                                    }

                        }

                loading();// Loading
                $http.post(uri + 'travel/infoStatusFinishHour', travel).success(function (data) {
                    

                    $scope.modalFinishfAmount = data;
                    $("#modalFinishfAmount").val(data)
                    $("#txtmodalFinishfAmount").val(data);
                    $("#txtmodalFinishfAmount").prop('disabled', true);
                    loading();// Loading

                }).error(function (data,status) {
                    if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                     else{notificate("Error !"+status," Contacte a Soporte","error");}
                     loading();// Loading
                });
        
    }


     $scope.openModalCloudTravel = function($travelCloud)
    {
        
        $scope.travelCloud = $travelCloud;

        $http.get(uri + 'cloud').success(function (data) {

                $scope.cloudCompany = data;
                $('#modalCloud').modal('toggle');
            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            }); 
    }



    $scope.sendCloud = function () {
        // body...



        var length = $scope.cloudCompany.length;
        for (var i = 0; i < length; i++) {

            

            if ($scope.cloudCompany[i].id == $("#idCloudCompany").val()) {
                    
                    
                             var travel =
                                        {
                                            data:
                                            {
                                                travel:$scope.travelCloud,
                                                fullUri:$scope.cloudCompany[i].fullUri

                                            }
                                        };

                                      


                                $http.post(uri + 'cloud/sendTravel', travel).success(function (data) {
                                   
                                    
                                    notificate("Operacion Exitosa","Viaje Enviado a la nube!","success"); 
                                    $('#modalCloud').modal('toggle');
                                    $scope.serviceDataFromDashboard(); // Actualizamos la tabla
                                    

                                }).error(function (data,status) {
                                    if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                                    else{notificate("Error !"+status," Contacte a Soporte","error");}
                                });
                        
               break;
            }
        }
        
       
        
    }



    $scope.setIdTravelFisinh = function (travel){
        $scope.modalFinishfIdTravel = travel['idTravel'] ;
        $scope.modalFinisiIsTravelByHour = travel['isTravelByHour'] ;
        $scope.modalFinisIsTravelComany = travel['isTravelComany'] ;
        $scope.modalFinisIdClientKf = travel['idClientKf'] ;
    }

    $scope.finalizeTravelAmount = function () {
        // body...


        
            if($("#txtmodalFinishfAmount").val() != "")
            {

                var travel =
                        {
                            travel:
                                    {
                                        totalAmount: $("#txtmodalFinishfAmount").val(),
                                        amountTiemeSlepp: $("#txtmodalFinishfAmountSlepp").val(),
                                        amounttoll: $("#txtmodalFinishfAmountSlepp").val(),
                                        idTravel: $scope.modalFinishfIdTravel,
                                        isTravelByHour: $scope.modalFinisiIsTravelByHour,
                                        isTravelComany: $scope.modalFinisIsTravelComany,
                                        idClientKf: $scope.modalFinisIdClientKf,
                                    }

                        }

                loading();
                $http.post(uri + 'travel/finish', travel).success(function (data) {
                    
                    notificate("Operacion Exitosa","Viaje Finalizado!","success"); 
                    

                    $("#txtmodalFinishfAmount").val("0");
                    $("#txtmodalFinishfAmountSlepp").val("0");
                    $("#txtmodalFinishfAmountTax").val("0");

                    $scope.modalFinishfAmount = 0;
                    $scope.modalFinishfAmountSlepp = 0;
                    $scope.modalFinishfAmountTax = 0;


                    $('#myModalFinish').modal('toggle');
                    $scope.serviceDataFromDashboard(); // Actualizamos la tabla
                     loading();
                }).error(function (data,status) {
                    if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                     else{notificate("Error !"+status," Contacte a Soporte","error");}
                     loading();
                });
            }
        
    }


    



    $scope.addPasager = function()
    {



        if($scope.fullNamePanguer != undefined && $scope.emailPasaguer != undefined)
        {

            var country = "";

            if(countryPasager != null)
            {
                country = countryPasager.formatted_address;
            }

         var pasaguer =
                    {
                        pasaguer:
                                {
                                    idClienteKf: $scope.idClient,
                                    fullNamePanguer: $scope.fullNamePanguer,
                                    IdUserKf: JSON.parse(localStorage.getItem('session')).idUser,
                                    idCostCenter: $scope.idCostCenter,
                                    idCompanyAcount: $scope.idCompanyAcountFilter,
                                    emailPasaguer: $scope.emailPasaguer,
                                    direcctionPasaguer: country,
                                    phonePasaguer: $scope.phonePasaguer
                                }

                    }


            $http.post(uri + 'travel/addPasaguer', pasaguer).success(function (data) {
               $scope.getPasagerByidCosCnter(); // Actualizamos la tabla
               $scope.fullNamePanguer = ""; 
               $scope.emailPasaguer = "";
               $scope.phonePasaguer = "";
               $("#direcctionPasaguer").val("");

                notificate("Operacion Exitosa","Pasajero Agregado!","success"); 
            
            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
            
            
        }else{
            notificate("!Nombre de Pasajero y Email Requeridos  ","info");
        }
    }


    //* REPORTE DE VIAJES FINALIZADOS *//
    $scope.printPdfVouche = function(id)
    {

        $http.get(uri + 'travel/find/' + id).success(function (data) 
        {
            $scope.travelInfoReport = data;
             $scope.printPdf();

        }).error(function (data,status) {
            if(status == 404){notificate("!Informacion "+status,data.error,"info");}
            else{notificate("Error !"+status," Contacte a Soporte","error");}
        });


    }


    function toDataUrl(url, callback) {
      var xhr = new XMLHttpRequest();
      xhr.onload = function() {
        var reader = new FileReader();
        reader.onloadend = function() {
          callback(reader.result);
        }
        reader.readAsDataURL(xhr.response);
      };
      xhr.open('GET', url);
      xhr.responseType = 'blob';
      xhr.send();
    }


     $scope.printPdf = function () {

        var base64Img;

        var array = [];

        if($scope.travelInfoReport.pointlocation != null)
        {
             
            array.push(
                [{text: 'Ubicaciones - Distancia:'+
                    $scope.travelInfoReport.distanceGps, style: 'tableHeader'},
                {text: 'Horas', style: 'tableHeader'}]
                );
                    
             //for (var i = 0; i <$scope.travelInfoReport.pointlocation.length; i++) {
                 
                
                 var temp;
                  temp = [
                  $scope.travelInfoReport.pointlocation[0].location,
                  $scope.travelInfoReport.pointlocation[0].hour
                  ];

                  array.push(temp);

                  var temp;
                  temp = [
                  $scope.travelInfoReport.pointlocation[$scope.travelInfoReport.pointlocation.length-1].location,
                  $scope.travelInfoReport.pointlocation[$scope.travelInfoReport.pointlocation.length-1].hour
                  ];
                  array.push(temp);

                  

                  
            // }

            
        }else{
                array.push(
                [{
                    text: 'Ubicaciones - Distancia:'+
                    $scope.travelInfoReport.distanceGps, style: 'tableHeader'},
                {text: 'Horas', style: 'tableHeader'}]
                );
        }

       


        if($scope.travelInfoReport.isTravelComany == 1 &&
          doesFileExist('../../images/'+$scope.travelInfoReport.idTravel+'.JPEG') ==  true
       )
        {
              toDataUrl('../../images/'+$scope.travelInfoReport.idTravel+'.JPEG', function(img) {
            base64Img = img;

            
  
            
        var docDefinition = 
        {  
               content: 
            [
                {
                    image: 'logo',
                    width: 80,
                    height: 80,
                    absolutePosition: {x: 480, y: 0}
                },
                {
                    text: 'Nro: '+$scope.travelInfoReport.codTravel,
                     style: 'subheader'
                },
                {
                    text: $scope.travelInfoReport.dateTravel,
                     style: 'subheader',
                      italics: true,
                       color: 'gray'
                },
                
                {
                    style: 'tableExample',
                    table: 
                    {
                        widths: [ '*', '*'],
                        body: 
                        [
                            [
                             {text: 'Cliente', style: 'tableHeader'},
                             {text: 'Chofer', style: 'tableHeader'}
                             ],
                            [
                                $scope.travelInfoReport.client+" - "+ $scope.travelInfoReport.phoneClient,
                                $scope.travelInfoReport.driver
                            ]
                        ]
                    }
                },
                {
                    text: 'Pasajeros'
                },

                {
                    style: 'tableExample',
                    table: 
                    {
                        widths: [ '*', '*'],
                        body: 
                        [
                            [
                                $scope.travelInfoReport.pasaguer1,
                                $scope.travelInfoReport.pasaguer2
                            ],
                            [
                                $scope.travelInfoReport.pasaguer3,
                                $scope.travelInfoReport.pasaguer4
                            ]
                        ]
                    }
                },

                {
                    style: 'tableExample',
                    table: 
                    {
                        widths: ['*',45],
                        body: 
                        array
                    }
                },

                {
                    style: 'tableExample',
                    table: 
                    {
                        widths: [250,'*'],
                        body: 
                        [
                            [
                            {text: 'Firma', style: 'tableHeader'},
                            {text: 'Totales', style: 'tableHeader'}
                            ],
                            [{
                   /* image: '../../../images/'+$scope.travelInfoReport.idTravel+'.JPEG',
                    */
                    image: base64Img,
                    
                    width: 250,
                    height: 250,
                },
                            
                        {
            style: 'tableExample',
            color: '#444',
            table: {
                widths: [ 'auto', 'auto'],
                body: [
                     ['Total Viaje: ',  $scope.travelInfoReport.amountGps+" $"],
                     ['Total Espera: ', $scope.travelInfoReport.amountTiemeSlepp+" $"],
                     ['Total Peaje: ',  $scope.travelInfoReport.amounttoll+" $"],
                    [{text: 'Total Final :', style: 'tableHeader', alignment: 'center'},
                     {text: $scope.travelInfoReport.totalAmount+" $", style: 'tableHeader', alignment: 'center'}],
                   
                    
                ]
            },
            layout: 'noBorders'
        }
                    



                        ]
                        ]
                    }
                }
    ],
        images: {
                    logo: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAB9AAAAdTCAYAAAACHKIPAAAACXBIWXMAAC4jAAAuIwF4pT92AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAABikRJREFUeNrs3Vts3Nd94PHfUKTuFClLlmVLtqS4DYwUqAe5eDdOsHawbbHFPMSPCyyQEbDPbhQUCzQL7FpGikUXSRdyA+xi3zRAge1j8zDIAikQO7tJHBebjBvkAri1JFuy7hYpWRQvImfnHJq1JetCUhzyzPDzAQYTOZT0nzN//knx+z/nVNrtdgAAAAAAAADAejdgCAAAAAAAAABAQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT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AAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAA/n97dkADAACAMOj9U7uZA2oAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AYnIQo7f31vpQAAAABJRU5ErkJggg=='
                },
                    styles: {
                            header: {
                                    fontSize: 18,
                                    bold: true,
                                    margin: [0, 0, 0, 10]
                            },
                            subheader: {
                                    fontSize: 16,
                                    bold: true,
                                    margin: [0, 10, 0, 5]
                            },
                            tableExample: {
                                    margin: [0, 5, 0, 15]
                            },
                            tableHeader: {
                                    bold: true,
                                    fontSize: 13,
                                    color: 'black'
                            }
                    },
                    defaultStyle: {
                            // alignment: 'justify'
                    }
            };
                    
            var date = new Date();  
            date = moment(date).format('DD_MMM_YYYY_HH_mm_ss');  
            pdfMake.createPdf(docDefinition).open('PDF_' + date + '.pdf'); 


        }); 
    }
    else
    {
         
            
        var docDefinition = 
        {  
               content: 
            [
                {
                    image: 'logo',
                    width: 80,
                    height: 80,
                    absolutePosition: {x: 480, y: 0}
                },
                {
                    text: 'Nro: '+$scope.travelInfoReport.codTravel,
                     style: 'subheader'
                },
                {
                    text: $scope.travelInfoReport.dateTravel,
                     style: 'subheader',
                      italics: true,
                       color: 'gray'
                },
                
                {
                    style: 'tableExample',
                    table: 
                    {
                        widths: [ '*', '*'],
                        body: 
                        [
                            [
                             {text: 'Cliente', style: 'tableHeader'},
                             {text: 'Chofer', style: 'tableHeader'}
                             ],
                            [
                                $scope.travelInfoReport.client+" - "+ $scope.travelInfoReport.phoneClient,
                                $scope.travelInfoReport.driver
                            ]
                        ]
                    }
                },

                {
                    style: 'tableExample',
                    table: 
                    {
                        widths: ['*',45],
                        body: 
                        array
                    }
                },

                {
                    style: 'tableExample',
                    table: 
                    {
                        widths: [250,'*'],
                        body: 
                        [
                            [
                            {text: 'Formas de Pago', style: 'tableHeader'},
                            {text: 'Totales', style: 'tableHeader'}
                            ],
                            [  {text: '-', style: 'tableHeader'},
                            
                        {
            style: 'tableExample',
            color: '#444',
            table: {
                widths: [ 'auto', 'auto'],
                body: [
                     ['Total Viaje: ',  $scope.travelInfoReport.amountGps+" $"],
                     ['Total Espera: ', $scope.travelInfoReport.amountTiemeSlepp+" $"],
                     ['Total Peage: ',  $scope.travelInfoReport.amounttoll+" $"],
                    [{text: 'Total Final :', style: 'tableHeader', alignment: 'center'},
                     {text: $scope.travelInfoReport.totalAmount+" $", style: 'tableHeader', alignment: 'center'}],
                   
                    
                ]
            },
            layout: 'noBorders'
        }
                    



                        ]
                        ]
                    }
                }
    ],
        images: {
                    logo: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAB9AAAAdTCAYAAAACHKIPAAAACXBIWXMAAC4jAAAuIwF4pT92AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAABikRJREFUeNrs3Vts3Nd94PHfUKTuFClLlmVLtqS4DYwUqAe5eDdOsHawbbHFPMSPCyyQEbDPbhQUCzQL7FpGikUXSRdyA+xi3zRAge1j8zDIAikQO7tJHBebjBvkAri1JFuy7hYpWRQvImfnHJq1JetCUhzyzPDzAQYTOZT0nzN//knx+z/nVNrtdgAAAAAAAADAejdgCAAAAAAAAABAQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT0AEAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAAgBHQAAAAAAAAAyAR0AAAAAAAAAQkAHAAAAAAAAgExABwAAAAAAAIAQ0AEAAAAAAAAgE9ABAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAAIAR0AAAAAAAAAMgEdAAAAAAAAAEJABwAAAAAAAIBMQAcAAAAAAACAENABAAAAAAAAIBPQAQAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAAACAEdAAAAAAAAADIBHQAAAAAAAABCQAcAAAAAAACATEAHAAAAAAAAgBDQAQAAAAAAACAT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AAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAAAIAggQ4AAAAAAAAAFQl0AAAAAAAAAAgS6AAAAAAAAABQkUAHAAAAAAAAgCCBDgAAAAAAAAAVCXQAAAAAAAAACBLoAAAAAAAAAFCRQAcAAAAAAACAIIEOAAAAAAAAABUJdAAAAAAAAAAIEugAAAAAAAAAUJFABwAAAAAA/n97dkADAACAMOj9U7uZA2oAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AQ6AAAAAAAAACTQAQAAAAAAAOAEOgAAAAAAAAAk0AEAAAAAAADgBDoAAAAAAAAAJNABAAAAAAAA4AYnIQo7f31vpQAAAABJRU5ErkJggg=='
                },
                    styles: {
                            header: {
                                    fontSize: 18,
                                    bold: true,
                                    margin: [0, 0, 0, 10]
                            },
                            subheader: {
                                    fontSize: 16,
                                    bold: true,
                                    margin: [0, 10, 0, 5]
                            },
                            tableExample: {
                                    margin: [0, 5, 0, 15]
                            },
                            tableHeader: {
                                    bold: true,
                                    fontSize: 13,
                                    color: 'black'
                            }
                    },
                    defaultStyle: {
                            // alignment: 'justify'
                    }
            };
                    
            var date = new Date();  
            date = moment(date).format('DD_MMM_YYYY_HH_mm_ss');  
            pdfMake.createPdf(docDefinition).open('PDF_' + date + '.pdf'); 


      


       
    }


         
            
    }

    // METODO QUE VERIFICA SI EXISTE LA IMAGEN DE LA FIRMA //
    function doesFileExist(urlToFile)
    {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();

    if (xhr.status == "404") {
        //alert("File doesn't exist");
        return false;
    } else {
        //alert("File exists");
        return true;
    }
    }



     $scope.addRisponsable = function()
    {
         var risponsable =
                    {
                        risponsable:
                                {
                                    idClienteKf: $scope.idClient,
                                    nameResposable: $scope.nameResposable
                                }

                    }


            $http.post(uri + 'travel/addResponsable', risponsable).success(function (data) {
               $scope.refreshPasaguer(); // Actualizamos la tabla
               $scope.nameResposable = "";
            
            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
    }


    $scope.finalizeTravel = function (id) {
        
        var r = confirm("Confirme Para Finalizar el Viaje!");

        if (r == true)
        {
            $http.get(uri + 'travel/finish/' + id).success(function (data) {
                
                notificate("Operacion Exitosa","Viaje Finalizado!","success"); 
                $scope.serviceDataFromDashboard(); // Actualizamos la tabla
            
            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        }
    }
    
    $scope.progressTravel = function (id) {
        
        var r = confirm("Confirme Para Cambia El Estado En Curso el Viaje!");

        if (r == true)
        {
            $http.get(uri + 'travel/progress/' + id).success(function (data) {
                
                notificate("Operacion Exitosa","Viaje en Curso!","success"); 
                $scope.serviceDataFromDashboard(); // Actualizamos la tabla
            
            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });
        }
    }


    $scope._setAsigClient = function () {

        var idDriverKf;

        if ($("#idDriverFreeTxt").val() != "")
        {
            idDriverKf = $("#idDriverFreeTxt").val();
        } else if ($("#idDriverPreFreeTxt").val() != "")
        {
            idDriverKf = $("#idDriverPreFreeTxt").val();
        }
        else if ($("#idDriverTopTxt").val() != "")
        {
            idDriverKf = $("#idDriverTopTxt").val();
        } 
        else if ($("#idDriverBilingualTxt").val() != "")
        {
            idDriverKf = $("#idDriverBilingualTxt").val();
        }
        else if ($("#idDriverByCategoryTxt").val() != "")
        {
            idDriverKf = $("#idDriverByCategoryTxt").val();
        }


        var travel =
                {
                    travel:
                            {
                                idDriverKf: idDriverKf,
                                idTravel: $scope.idTravel
                            }
                };


        return travel;

    }


  

    $scope._setTravel = function () {

        var nameOrigin;
        var latOrigin;
        var lonOrigin;

        var nameDestination;
        var latDestination;
        var lonDestination;

        var idClientKf;
        var idDriverKf



        if ($scope.changueCountry)
        {

            idDriverKf = $scope.idDriverKf;
            idClientKf = $scope.idClient;



            if (origin == undefined)
            {
                nameOrigin = origin.formatted_address;
                latOrigin = origin.geometry.location.lat();
                lonOrigin = origin.geometry.location.lng();
            } else
            {
                nameOrigin = origin.nameOrigin;
                latOrigin = origin.latOrigin;
                lonOrigin = origin.lonOrigin;
            }

            if (destination == undefined)
            {
                nameDestination = destination.formatted_address;
                latDestination = destination.geometry.location.lat();
                lonDestination = destination.geometry.location.lng();
            } else
            {

                nameDestination = destination.nameDestination;
                latDestination = destination.latDestination;
                lonDestination = destination.lonDestination;
            }

        } else
        {


            idDriverKf = $scope.idDriverKf;
            idClientKf = $scope.idClient;



            

              if (origin.geometry != undefined)
            {
                nameOrigin = origin.formatted_address;
                latOrigin = origin.geometry.location.lat();
                lonOrigin = origin.geometry.location.lng();
            } else
            {
                nameOrigin = origin.nameOrigin;
                latOrigin = origin.latOrigin;
                lonOrigin = origin.lonOrigin;
            }



            if(destination != null)
            {

                if(destination.geometry  != undefined)
                 {
                    nameDestination = destination.formatted_address;
                    latDestination = destination.geometry.location.lat();
                    lonDestination = destination.geometry.location.lng();
                } else
                {

                    nameDestination = destination.nameDestination;
                    latDestination = destination.latDestination;
                    lonDestination = destination.lonDestination;
                }
            }

            }


        var travel =
                {
                    travel:
                            {
                                idTravel: $scope.idTravel,
                                destination:
                                        {
                                            nameDestination: nameDestination,
                                            latDestination: latDestination,
                                            lonDestination: lonDestination,
                                        },
                                origin:
                                        {
                                            nameOrigin: nameOrigin,
                                            latOrigin: latOrigin,
                                            lonOrigin: lonOrigin,
                                        },
                                dateTravel: $("#dateTravel").val(),
                                hoursAribo: $("#hoursAribo").val(),
                                idHotel: $scope.idHotel,
                                isTravelComany: $scope.isTravelComany,
                                idClientKf: idClientKf,
                                idUserRes: $scope.idUserRes,
                                idCostCenter: $scope.idCostCenter,
                                idTypeVehicle: $scope.idTypeVehicle,
                                idDriverKf: idDriverKf,
                                isDriverHeader: $scope.isDriverHeader,
                                idAribos: $scope.idAribos,
                                flight: $scope.flight,
                                precedence: $scope.precedence,
                                companyLabel: $scope.companyLabel,
                                floor: $scope.floor,
                                department: $scope.department,
                                lot: $scope.lot,
                                phoneNumber: $scope.phoneNumber,
                                localNumber: $scope.localNumber,
                                isDriverBilin: $scope.isDriverBilin,
                                isExtraLuggage: $scope.isExtraLuggage,
                                idSatatusTravel: $scope.idSatatusTravel,
                                passenger1: $scope.passenger1,
                                passenger2: $scope.passenger2,
                                passenger3: $scope.passenger3,
                                passenger4: $scope.passenger4,
                                distanceLabel: $("#distanceLabel").val(),
                                distance: $("#distance").val(),
                                durationLabel: $("#durationLabel").val(),
                                duration: $("#duration").val(),
                                codTravel:$scope.codTravel,
                                idCompanyAcountKf:$scope.idCompanyAcountFilter,
                                isTravelByHour:$scope.isTravelByHour,
                                isBilingualDriver:$scope.isBilingualDriver,
                                amountBilingualDriver:$scope.amountBilingualDriver,
                                IdUserKf:$scope.idUser

                            }
                };


        return travel;
    }



    // SERVICIO DE AGREGAR CLIENTE //
    $scope.addClient = function () {

        var correo = false;
        var dni = false;

        if($scope.filterForm.client != undefined)
        {
            var length = $scope.filterForm.client.length;
            for (var i = 0; i < length; i++) {
                if ($scope.filterForm.client[i].mailClient == $scope.mailClient) {
                    correo = true;
                }
                if ($scope.filterForm.client[i].dniClient == $scope.dniClient) {
                    dni = true;
                }
            }
        }

        if ($("#passClientNew").val() != "" && correo == false && dni == false)
        {
            $http.post(uri + 'client', $scope._setCliet()).success(function (data) {
                
                 notificate("Operacion Exitosa",data.response,"success"); 
                 $('#myModalAddClient').modal('hide');

            }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
            });

        } else {
            if ($("#passClientNew").val() == "") {
                $("#spanPass").show();
                $("#passClientNew").focus();
            }

            if (correo) {
                $("#spanEmail").show();
                $("#mailClient").focus();
            } else {
                $("#spanEmail").hide();
            }
            if (dni) {
                $("#spanDni").show();
                $("#dniClient").focus();
            } else {
                $("#spanDni").hide();
            }
        }

    }

    $scope._setCliet = function () {

        var countryName;
        var lat;
        var lng;
        if ($routeParams.id != null) {

            if (changueCountry)
            {
                lat = countryClient.geometry.location.lat();
                lng = countryClient.geometry.location.lng();
                countryName = countryClient.formatted_address;
            } else
            {
                lat = countryClient.addresLatClient;
                lng = countryClient.addresLongClient;
                countryName = countryClient.addresLatClient;
            }

        } else
        {
            lat = countryClient.geometry.location.lat();
            lng = countryClient.geometry.location.lng();
            countryName = countryClient.formatted_address;
        }

        var client =
                {
                    client:
                            {
                                idClient: $routeParams.id,
                                country:
                                        {
                                            addresClient: countryName,
                                            addresLatClient: lat,
                                            addresLongClient: lng,
                                        },
                                firtNameClient: $scope.firtNameClient,
                                lastNameClient: $scope.lastNameClient,
                                dniClient: $scope.dniClient,
                                phoneClient: $scope.phoneClient,
                                mailClient: $scope.mailClient,
                                addresClient: $scope.addresClient,
                                idCondicionTypeClient: $scope.idCondicionTypeClient,
                                /*dateBithDayClient:$scope.dateBithDayClient,*/
                                addresLongClient: $scope.addresLongClient,
                                addresLatClient: $scope.addresLatClient,
                                aditionalInfoAddres: $scope.aditionalInfoAddres,
                                idFavoriteDrive: $scope.idFavoriteDrive,
                                /*localPhone:$scope.localPhone,*/
                                idTypeCarClient: $scope.idTypeCarClient,
                                /*numberCar:$scope.numberCar,*/
                                isSendNotificMonth: $scope.isSendNotificMonth,
                                isPaymentCash: $scope.isPaymentCash,
                                isActiveCurrentAcount: $scope.isActiveCurrentAcount,
                                isSendMailFronAppClient: $scope.isSendMailFronAppClient,
                                passClient: $scope.passClient,
                                idTypeClient: 1,
                                isEditUser: $scope.isEditUser,
                                idUserClient: $scope.idUserClient
                            }
                };
        return client;
    }

    $scope.newClient = function () {

        var dataFilter;
        // LLAMAMOS A EL SERVICIO FILTROS PARA EL FORMULARIO //
        $http.get(uri + 'client/filterForm').success(function (data) {

            dataFilter = data;
            if ($routeParams.id != null)
            {
                callClientById($routeParams.id, dataFilter);
            } else
            {
                $scope.isSendNotificMonth = false;
                $scope.isPaymentCash = false;
                $scope.isActiveCurrentAcount = false;
                $scope.isSendMailFronAppClient = false;
                $scope.filterForm = dataFilter;
            }



        }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });
    }
    
     // SERVICIO DE AGREGAR COSTO //
    $scope.addCostCenter = function () 
    {
        $http.post(uri + 'costCenter', $scope._setCostCenter()).success(function (data) {
            

            notificate("Operacion Exitosa",data.response,"success"); 
            $('#myModalAddCostCenter').modal('hide');
            $scope.costCenter = '';
            $scope.codCostCenter = '';
            $scope.getCostCenterByidAcount();
        
        }).error(function (data,status) {
                if(status == 404){notificate("!Informacion "+status,data.error,"info");}
                else{notificate("Error !"+status," Contacte a Soporte","error");}
        });

    }
    
    $scope._setCostCenter = function () {

        var cost =
                {
                    cost:
                            {
                                
                                costCenter: $scope.costCenter,
                                codCostCenter: $scope.codCostCenter,
                                IdUserKf: JSON.parse(localStorage.getItem('session')).idUser,
                                idCompanyKf: $scope.idClient,
                                idCompanyAcount: $scope.idCompanyAcountFilter

                            }
                };
        return cost;
    };
    
    $scope.addNrAcount = function () {

        $http.post(uri + 'costCenter/add_nracount', $scope._setNrAcount()).success(function (data) {
            //alert(data.response);
            notificate("Operacion Exitosa", data.response ,"success");
            $('#myModalAddNrAcount').modal('hide');
            $scope.nrAcount = '';
            $scope.refreshPasaguer();
        }).error(function (data) {
            //alert(data.error);
            notificate("Error !" + data.error ," Contacte a Soporte","error");
        });

    }
    
    $scope._setNrAcount = function () {
        
        $scope.Checks.push({
            'nrAcount': $scope.nrAcount, 
            'idStatusAcount': 1
        });

        var acount =
                {
                    acount:
                            {
                                idClientKf: $scope.idClient,
                                listChecks: $scope.Checks

                            }
                };


        return acount;
    };

});
/*********************************************************/
