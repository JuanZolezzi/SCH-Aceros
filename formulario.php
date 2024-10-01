<?php
    session_start();
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require_once 'phpmailer/src/Exception.php';
    require_once 'phpmailer/src/PHPMailer.php';
    require_once 'phpmailer/src/SMTP.php';

    $nombre   = $_POST["nombre"];
    $telefono = $_POST["telefono"];
    $email    = $_POST["email"];
    $empresa  = $_POST["empresa"];
    $mensaje  = $_POST["mensaje"];
    //MAS CAMPOS
    $cuerpo   = "<p><b>Nombre: </b>" . $nombre . "</p>" . "<p><b>Teléfono: </b>" . $telefono . "</p>" . "<p><b>Email: </b>" . $email . "</p>" . "<p><b>Empresa: </b>" . $empresa . "</p>" . "<p><b>Mensaje: </b>" . $mensaje . "</p>";
    $mail = new PHPMailer();
    
    //HAY QUE LLAMAR ESTA SESSION EN CONTACTO.PHP PARA OBTENER LOS ERRORRES
    $_SESSION["datos_form"] = $_POST;

    $errores = [];

    if(empty($nombre)){
        $errores[] = "El nombre es obligatorio.";
    } else{
        if(strlen($nombre) < 2 || strlen($nombre) > 35){
            $errores[] = "El nombre no puede tener menos de 2 y más de 35 caracteres.";
        }
    }
    if(empty($telefono)){
        $errores[] = "El teléfono es obligatorio";
    }  else{
        if(!is_numeric($telefono)){
            $errores[] = "El número de teléfono no es válido";
        }
    }
    if(empty($email)){
        $errores[] = "El email es obligatorio.";
    } else{
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errores[] = "El email es inválido.";
        }
    }
    if(empty($mensaje)){
        $errores[] = "El mensaje es obligatorio";
    } else{
        if(strlen($mensaje) < 2 || strlen($mensaje) > 400){
            $errores[] = "El mensaje no puede tener menos de 2 y más de 400 caracteres.";
        }
    }

    //VALIDAR MAS CAMPOS SI HUBIESE

    if(!empty($errores)){
        $_SESSION["error"] = $errores;
        //editar el location donde se quiera redirigir cuando hay un error, por lo general en la misma pagina para mostrar los errores.
        header("Location: /index.php");
        die();
    }

    try{
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = 'mail.schaceros.com.ar';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@schaceros.com.ar';
        $mail->Password = 'info.schaceros2024';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        
        $mail->setFrom($email, $nombre . " - Consulta web");
        $mail->AddReplyTo($email, $nombre);
        //MAIL AL QUE LE VA A LLEGAR EL CORREO
		$mail->addAddress('dmglautaro@gmail.com');
		
        $mail->isHTML(true);
        $mail->Subject = 'Contacto vía web';
        $mail->Body = $cuerpo;
        
        $mail->Send();

        $_SESSION["mail"] = "La consulta se envió correctamente.";
        //SE VACIA LA SESION PARA QUE NO QUEDEN LOS DATOS DESPUES DE MANDAR EL MAIL
        unset($_SESSION["datos_form"]);
        //editar el location donde se quiera redirigir cuando se envia un mail, por lo general al index/home.
        header("Location: /index.php");
        die();
    } catch(Exception $e){
        $_SESSION["error_mail"] = "Ocurrió un error al enviar la consulta, intente nuevamente.";
        //editar el location donde se quiera redirigir cuando hay un error al enviar el mail por lo general en la misma pagina.
        header("Location: /index.php");
        die();
    }
