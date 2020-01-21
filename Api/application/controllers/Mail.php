<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set("America/Argentina/Buenos_Aires");


require APPPATH . '/libraries/REST_Controller.php';

class Mail extends REST_Controller {




    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
       $this->load->model('consulting_model');

        
    }

    

    public function sendMailLiquidationProvider_post()
    {
        $config = array(
           'protocol' => 'smtp',
           'smtp_host' => 'smtp.googlemail.com',
         'smtp_user' => 'remisasnube@gmail.com', //Su Correo de Gmail Aqui
         'smtp_pass' => 'AdMg1210#*', // Su Password de Gmail aqui
         'smtp_port' => '465',
         'smtp_crypto' => 'ssl',
         'mailtype' => 'html',
         'wordwrap' => TRUE,
         'charset' => 'utf-8'
         );

        $mail = $this->post('mail');

        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('remisasnube@gmail.com');
        $this->email->subject('Reporte de Liquidacion');


        $cuerpo = "Se Genero una Liquidacion para usted!";

        $this->email->message($cuerpo);


        $this->email->to('jorguti58@gmail.com,remisasnube@gmail.com,leonardo@apreciasoft.com'.$mail['header']['emailCompanyAlter']);

        if($this->email->send(FALSE)){
           $this->response("Solicitud Enviada",200);
             //echo $this->email->print_debugger(array('headers'));
       }else {
             //echo "fallo <br/>";

        $this->response($this->email->print_debugger(array('headers')),500);
    }
}

public function sendMail_post()
{
    $config = array(
       'protocol' => 'smtp',
       'smtp_host' => 'smtp.googlemail.com',
         'smtp_user' => 'remisasnube@gmail.com', //Su Correo de Gmail Aqui
         'smtp_pass' => 'AdMg1210#*', // Su Password de Gmail aqui
         'smtp_port' => '465',
         'smtp_crypto' => 'ssl',
         'mailtype' => 'html',
         'wordwrap' => TRUE,
         'charset' => 'utf-8'
         );


    $mail = $this->post('mail');

    $this->load->library('email', $config);
    $this->email->set_newline("\r\n");
    $this->email->from('remisasnube@gmail.com');
    $this->email->subject('Reporte Usuarios');


    $cuerpo = "";
    $cuerpo .=  "Empresa:".$mail['company']."<br> ";
    $cuerpo .=  "ID-USUARIO:".$mail['userId']."<br> ";
    $cuerpo .=  "Nombre:".$mail['fullName']."<br> ";
    $cuerpo .=  "Correo:".$mail['correo']."<br> ";
    $cuerpo .=  "-------------------------------"."<br> ";
    $cuerpo .=  "".$mail['message']."<br> ";

    $this->email->message($cuerpo);


    $this->email->to('jorguti58@gmail.com,remisasnube@gmail.com,leonardo@apreciasoft.com');

    if($this->email->send(FALSE)){
       $this->response("Solicitud Enviada",200);
             //echo $this->email->print_debugger(array('headers'));
   }else {
             //echo "fallo <br/>";

      $this->response($this->email->print_debugger(array('headers')),500);
  }
}

public function sendMailSupport_post()
{
    $config = array(
       'protocol' => 'smtp',
       'smtp_host' => 'smtp.googlemail.com',
       'smtp_user' => 'remisasnube@gmail.com', 
       'smtp_pass' => 'AdMg1210#*', 
       'smtp_port' => '465',
       'smtp_crypto' => 'ssl',
       'mailtype' => 'html',
       'wordwrap' => TRUE,
       'charset' => 'utf-8'
       );


    $mail = $this->post('support');

    $this->load->library('email', $config);
    $this->email->set_newline("\r\n");
    $this->email->from('remisasnube@gmail.com');
    $this->email->subject('Reporte Fallas Solucionado');


    $cuerpo = "Motivo: ";
    $cuerpo.= $mail['reasonSupport']."<br>";
    $cuerpo.= "Detalles: <br>";
    $cuerpo.= $mail['detailSupport'].'<br>';
    $cuerpo.= "------------------------------<br>";
    $cuerpo.= "El status de su Reporte de Falla a pasado a Finalizado!<br>";
    $cuerpo.= "Razón: <br>";
    $cuerpo.= $mail['descriptionFinalizedSupport'].'<br>';

    $this->email->message($cuerpo);

    $this->email->to('jorguti58@gmail.com,remisasnube@gmail.com,leonardo@apreciasoft.com,'.$mail['mailAgency']);

    if($this->email->send(FALSE)){
       $this->response("Notificación Enviada",200);
             //echo $this->email->print_debugger(array('headers'));
   }else {
             //echo "fallo <br/>";

    $this->response($this->email->print_debugger(array('headers')),500);
}
}




public function sendMailTienda_post()
{
    $config = array(
       'protocol' => 'smtp',
       'smtp_host' => 'smtp.googlemail.com',
         'smtp_user' => 'remisasnube@gmail.com', //Su Correo de Gmail Aqui
         'smtp_pass' => 'AdMg1210#*', // Su Password de Gmail aqui
         'smtp_port' => '465',
         'smtp_crypto' => 'ssl',
         'mailtype' => 'html',
         'wordwrap' => TRUE,
         'charset' => 'utf-8'
         );

    $mail = $this->post('mail');


    $this->load->library('email', $config);
    $this->email->set_newline("\r\n");
    $this->email->from('remisasnube@gmail.com');
    $this->email->subject('Quiero la Promo');


    $cuerpo = "";
    $cuerpo .=  "Empresa:".$mail['company']."<br> ";
    $cuerpo .=  "ID-USUARIO:".$mail['userId']."<br> ";
    $cuerpo .=  "Nombre:".$mail['fullName']."<br> ";
    $cuerpo .=  "Correo:".$mail['correo']."<br> ";
    $cuerpo .=  "-------------------------------"."<br> ";
    $cuerpo .=  "".$mail['info']."<br> ";

    $this->email->message($cuerpo);


    $this->email->to('jorguti58@gmail.com,remisasnube@gmail.com,leonardo@apreciasoft.com');
    
    if($this->email->send(FALSE)){
       $this->response("Solicitud Enviada",200);
         //echo $this->email->print_debugger(array('headers'));
   }else {
         //echo "fallo <br/>";

    $this->response($this->email->print_debugger(array('headers')),500);
}
}


public function sendMailLiquidation_post()
{
    $config = array(
       'protocol' => 'smtp',
       'smtp_host' => 'smtp.googlemail.com',
         'smtp_user' => 'remisasnube@gmail.com', //Su Correo de Gmail Aqui
         'smtp_pass' => 'AdMg1210#*', // Su Password de Gmail aqui
         'smtp_port' => '465',
         'smtp_crypto' => 'ssl',
         'mailtype' => 'html',
         'wordwrap' => TRUE,
         'charset' => 'utf-8'
         );

    $this->load->library('email', $config);
    $this->email->set_newline("\r\n");
    $this->email->from('remisasnube@gmail.com');
    $this->email->subject('Reporte Liquidacion');

    $mail = $this->post('mailClient');


    $cuerpo = "";

    $cuerpo .=  "Codigo liquidacion:".$mail['header']['codeCardx']."<br> ";
    $cuerpo .=  "Empresa:"."<br> ";
    $cuerpo .=  "********************************"."<br> ";
    $cuerpo .= "Detalles de viajes" . "<br><br>";

    $length =  count($mail['body']['newLisTravel']); 



    for ($i = 0; $i < $length; $i++) {
        $cuerpo .=  "-------------------------------"."<br> ";
        $cuerpo.= "Hora:" . $mail['body']['newLisTravel'][$i]['hour']. "<br>";
        $cuerpo.= "Pasajero:" . $mail['body']['newLisTravel'][$i]['passenger1']. "<br>";
        $cuerpo.= "Origen:" . $mail['body']['newLisTravel'][$i]['nameOrigin']. "<br>";
        $cuerpo.= "Destino:" . $mail['body']['newLisTravel'][$i]['nameDestination']. "<br>";
        $cuerpo.= "Chofer:" . $mail['body']['newLisTravel'][$i]['fisrtNameDriver']. "<br>";
        $cuerpo.= "Monto:" . $mail['body']['newLisTravel'][$i]['totalAmount']. "<br>";
        $cuerpo .=  "-------------------------------"."<br> ";
    }

    $cuerpo .=  "********************************"."<br> ";

    $cuerpo .=  "Sub_total:".$mail['amount']['sub_total']."<br> ";
    $cuerpo .=  "Iva:".$mail['amount']['iva']."<br> ";
    $cuerpo .=  "Total:".$mail['amount']['total']."<br> ";

    $cuerpo .=  ""."<br> ";

    $this->email->message($cuerpo);


    $this->email->to('jorguti58@gmail.com,remisasnube@gmail.com,leonardo@apreciasoft.com'.$mail['header']['emailCompanyAlter']);



    if($this->email->send(FALSE)){
       $this->response("Solicitud Enviada",200);
         //echo $this->email->print_debugger(array('headers'));
   }else {
         //echo "fallo <br/>";

      $this->response($this->email->print_debugger(array('headers')),500);
  }
}

public function sendMailInvoice_post()
{

    $config = array(
       'protocol' => 'smtp',
       'smtp_host' => 'smtp.googlemail.com',
         'smtp_user' => 'remisasnube@gmail.com', //Su Correo de Gmail Aqui
         'smtp_pass' => 'AdMg1210#*', // Su Password de Gmail aqui
         'smtp_port' => '465',
         'smtp_crypto' => 'ssl',
         'mailtype' => 'html',
         'wordwrap' => TRUE,
         'charset' => 'utf-8'
         );


    $this->load->library('email', $config);
    $this->email->set_newline("\r\n");
    $this->email->from('remisasnube@gmail.com');
    $this->email->subject('Reporte Liquidacion');

    $mail = $this->post('mailClient');


    $cuerpo = "";

    $cuerpo .=  "Codigo liquidacion:".$mail['header']['codeCardx']."<br> ";
    $cuerpo .=  "Empresa:"."<br> ";
    $cuerpo .=  "********************************"."<br> ";
    $cuerpo .= "Detalles de viajes" . "<br><br>";

    $length =  $mail['body']['t_travel']; 

    for ($i = 0; $i < $length; $i++) {
        $cuerpo .=  "-------------------------------"."<br> ";
        $cuerpo.= "Hora:" . $mail['body']['hour'][$i]. "<br>";
        $cuerpo.= "Pasajero:" . $mail['body']['passenger1'][$i]. "<br>";
        $cuerpo.= "Origen:" . $mail['body']['nameOrigin'][$i]. "<br>";
        $cuerpo.= "Destino:" . $mail['body']['nameDestination'][$i]. "<br>";
        $cuerpo.= "Km:" . $mail['body']['distanceLabel'][$i]. "<br>";
        $cuerpo.= "Chofer:" . $mail['body']['fisrtNameDriver'][$i]. "<br>";
        $cuerpo.= "Monto:" . $mail['body']['totalAmount'][$i]. "<br>";
        $cuerpo .=  "-------------------------------"."<br> ";
    }

    $cuerpo .=  "********************************"."<br> ";

    $cuerpo .=  "Sub_total:".$mail['amount']['sub_total']."<br> ";
    $cuerpo .=  "Iva:".$mail['amount']['iva']."<br> ";
    $cuerpo .=  "Total:".$mail['amount']['total']."<br> ";

    $cuerpo .=  ""."<br> ";

    $this->email->message($cuerpo);


    $this->email->to('jorguti58@gmail.com,remisasnube@gmail.com,leonardo@apreciasoft.com,'.$mail['header']['emailCompany'],$mail['header']['emailCompanyAlter']);


    
    if($this->email->send(FALSE)){
       $this->response("Solicitud Enviada",200);
         //echo $this->email->print_debugger(array('headers'));
   }else {
         //echo "fallo <br/>";

      $this->response($this->email->print_debugger(array('headers')),500);
  }
}

    public function sendMailConsultant_get($id_consulting)
    {
        $config = array(
             'protocol' => 'smtp',
             'smtp_host' => 'smtp.googlemail.com',
             'smtp_user' => 'remisasnube@gmail.com', //Su Correo de Gmail Aqui
             'smtp_pass' => 'AdMg1210#*', // Su Password de Gmail aqui
             'smtp_port' => '465',
             'smtp_crypto' => 'ssl',
             'mailtype' => 'html',
             'wordwrap' => TRUE,
             'charset' => 'utf-8'
             );


        $consulting = $this->consulting_model->getThisWithConsultant($id_consulting);
        $tipo='presencial';
        if ($consulting['consulting']['online']) {
            $tipo='online';
        }

        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('remisasnube@gmail.com');
        $this->email->subject('Asignacion de nueva consultoria');

        $cuerpo = "Se le ha asignado una nueva consultoria";
        $cuerpo .=  "Tipo de consulta:".$tipo."<br> ";
        $cuerpo .=  "codigo:".$consulting['consulting']['code']."<br> ";
        $cuerpo .=  "-------------------------------"."<br> ";


        $this->email->message($cuerpo);


        $this->email->to('Roiner123@gmail.com');

        if($this->email->send(FALSE)){
           $this->response("Solicitud Enviada",200);
        }else {
            $this->response("El correo no se logro enviar, error de servidor",500);    
        }
    }

    public function sendMailConsulting_get($id_consulting)
    {
        $config = array(
             'protocol' => 'smtp',
             'smtp_host' => 'smtp.googlemail.com',
             'smtp_user' => 'remisasnube@gmail.com', //Su Correo de Gmail Aqui
             'smtp_pass' => 'AdMg1210#*', // Su Password de Gmail aqui
             'smtp_port' => '465',
             'smtp_crypto' => 'ssl',
             'mailtype' => 'html',
             'wordwrap' => TRUE,
             'charset' => 'utf-8'
             );


        $consulting = $this->consulting_model->getThisWithConsultant($id_consulting);
        $tipo='presencial';
        if ($consulting['consulting']['online']) {
            $tipo='online';
        }

        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('remisasnube@gmail.com');
        $this->email->subject('Asignacion de nueva consultoria');

        $cuerpo = "Se le ha asignado una nueva consultoria";
        $cuerpo .=  "Tipo de consulta:".$tipo."<br> ";
        $cuerpo .=  "codigo:".$consulting['consulting']['code']."<br> ";
        $cuerpo .= "Consultor:".$consulting['consulting']['userNameUser']."<br> ";
        $cuerpo .=  "-------------------------------"."<br> ";


        $this->email->message($cuerpo);


        $this->email->to($consulting['consulting']['emailUser']);

        if($this->email->send(FALSE)){
           $this->response("Solicitud Enviada",200);
        }else {
            $this->response("El correo no se logro enviar, error de servidor",500);    
        }
    }
}
?>