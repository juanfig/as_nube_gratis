// Creación del MODELO
var RoutingApp = angular.module('routingAuth',
								 ['ui.bootstrap',
								  'ngCookies']);



/*********************************************************/
/*****************   CONTROLADOR   **********************/
RoutingApp.controller('authController', function($scope,$cookies,$http) {




	 localStorage.clear();

	 session = null;



    $( document ).ready(function() {


    if (window.history && window.history.pushState) {


    $(window).on('popstate', function() {

    	 window.location.assign("");
    });

  }

});




	$scope.auth = function()
	{
		$("#gif").show();
		//console.log(uri+'auth/nube');
		$http.post(uri+'auth/nube',$scope._setUser()).success(function(data,status){


			if(status == 203)
			{
				alert(data.error);
				$("#gif").hide();
			}else
			{
				localStorage.setItem('session',JSON.stringify(data.user));
				localStorage.setItem('modulesNube',JSON.stringify(data.modules));
				//alert(JSON.stringify(data.modules));
				$("#gif").hide();
				window.location.assign(uriClient+"Home");
			}



		}).error(function(data){
			alert(data.error);
			$("#gif").hide();
		});


	}


	


	$scope._setUser = function () {



		var user =
		{
			user:
			{
				userName:$scope.name,
				userPass:$scope.pass,
			}
		};


		return user;
	}



	$scope.addAgency = function () {

		window.location.assign(uriClient+"OptionsFrond");
	}




});
