<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cron</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script type="text/javascript" src="assets/js/index.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Expiracion de Fletes CRON </th>
                <th>Notificacion de Reserva CRON</th>
                <th>Notificacion de Expiracion User CRON</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <ul id="noticationExpirationFleet"></ul>
                </td>
                <td >
                    <ul id="noticationRervationComing"></ul>
                </td>
                <td >
                    <ul id="notificationExpirationUser"></ul>
                </td>
            </tr>
        </tbody>
    </table>
    <script type="text/javascript">
        function noticationExpirationFleet(){

            $.ajax({
              url: uri+'cron/noticationExpirationFleet',
              method: "GET",
              dataType: "json",
              beforeSend: function( ) {
                $('#noticationExpirationFleet').prepend('<li class="text-info"><b>Ejecutando Espere ...</b></li>');
              },
              success: function(res){
                var date = new Date();
                $('#noticationExpirationFleet').prepend('<li class="text-success"><b>Exito! '+date+'</b></li>');
              },
              error: function(res){
                var date = new Date();
                $('#noticationExpirationFleet').prepend('<li class="text-danger"><b>Error! '+date+'</b></li>');
              }
            });
        }
        noticationExpirationFleet()
        setInterval(function(){noticationExpirationFleet()}, 60000);

        function noticationRervationComing(){
            $.ajax({
              url: uri+'cron/noticationRervationComing',
              method: "GET",
              dataType: "json",
              success: function(res){
                var date = new Date();
                $('#noticationRervationComing').prepend('<li class="text-success"><b>Exito! '+date+'</b></li>');
              },
              error: function(res){
                var date = new Date();
                $('#noticationRervationComing').prepend('<li class="text-danger"><b>Error! '+date+'</b></li>');
              }
            });
        }
        noticationRervationComing()
        setInterval(function(){noticationRervationComing()}, 60000);


        function notificationExpirationUser(){
            $.ajax({
              url: uri+'cron/notificationExpirationUser',
              method: "GET",
              dataType: "json",
              success: function(res){
                var date = new Date();
                $('#notificationExpirationUser').prepend('<li class="text-success"><b>Exito! '+date+'</b></li>');
              },
              error: function(res){
                var date = new Date();
                $('#notificationExpirationUser').prepend('<li class="text-danger"><b>Error! '+date+'</b></li>');
              }
            });
        }
        notificationExpirationUser()
        setInterval(function(){notificationExpirationUser()}, 60000);
        
    </script>
</body>
</html>