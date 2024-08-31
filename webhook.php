<?php 
    const TOKEN_HELPPRO = "HELPPROAPIMETA";
    const WEBHOOK_URL = "https://apiconsultas.helppro.tech/webhook.php";

    function verificarToken($req,$res){
        try{
            $token = $req['hub_verify_token'];
            $challenge = $req['hub_challenge'];
    
            if (isset($challenge) && isset($token) && $token === TOKEN_HELPPRO) {
                $res->send($challenge);
            } else {
                $res->status(400)->send();
            }

        }catch(Exception $e){
            $res ->status(400)->send();
        }
    }

    function recibirMensajes($req, $res) {
        
        try {
            
            $entry = $req['entry'][0];
            $changes = $entry['changes'][0];
            $value = $changes['value'];
            $mensaje = $value['messages'][0];
            
            $comentario = $mensaje['text']['body'];
            $numero = $mensaje['from'];
            
            $id = $mensaje['id'];
            
            $archivo = "log.txt";
            
            if (!verificarTextoEnArchivo($id, $archivo)) {
                $archivo = fopen($archivo, "a");
                $texto = json_encode($id).",".$numero.",".$comentario;
                fwrite($archivo, $texto);
                fclose($archivo);
                
                EnviarMensajeWhastapp($comentario,$numero);
            }
    
            $res->header('Content-Type: application/json');
            $res->status(200)->send(json_encode(['message' => 'EVENT_RECEIVED']));

        } catch (Exception $e) {
            $res->header('Content-Type: application/json');
            $res->status(200)->send(json_encode(['message' => 'EVENT_RECEIVED']));
        }
    }

    function EnviarMensajeWhatsapp($comentario, $numero) {
        $comentario = strtolower($comentario);
        $data = [];

        if (strpos($comentario, 'hola') !== false) {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => [
                    "preview_url" => false,
                    "body" => "🚀 Hola, visita mi web oficial validación de premios, premios.helppro.com.do para más información.\n \n📌Por favor, ingresa un número #️⃣ para recibir información.\n \n1️⃣. Consultar sorteos. ❔\n2️⃣. Ubicación de los centro de pago. 📍\n3️⃣. Canales oficiales de sorteos. 📄\n4️⃣. Horarios de sorteos. 🕜\n5️⃣. Canales oficiales de sorteos. ⏯️\n6️⃣. Hablar con soporte. 🙋‍♂️\n7️⃣. Horario de Atención. 🕜"
                ]
            ];
        } else if ($comentario == '1') {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => [
                    "preview_url" => false,
                    "body" => "Consultar sorteos."
                ]
            ];
        } else if ($comentario == '2') {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "location",
                "location" => [
                    "latitude" => "-12.067158831865067",
                    "longitude" => "-77.03377940839486",
                    "name" => "Estadio Nacional del Perú",
                    "address" => "Cercado de Lima"
                ]
            ];
        } else if ($comentario == '3') {
            $data = [
                "messaging_product" => "whatsapp",    
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "document",
                "document" => [
                    "link" => "http://s29.q4cdn.com/175625835/files/doc_downloads/test.pdf",
                    "caption" => "Temario del Curso #0"
                ]
            ];
        } else if ($comentario == '4') {
            $data = [
                "messaging_product" => "whatsapp",    
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "audio",
                "audio" => [
                    "link" => "https://filesamples.com/samples/audio/mp3/sample1.mp3"
                ]
            ];
        } else if ($comentario == '5') {
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $numero,
                "type" => "text",
                "text" => [
                    "preview_url" => true,
                    "body" => "Introducción al curso! https://youtu.be/6ULOE2tGlBM"
                ]
            ];
        } else if ($comentario == '6') {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => [
                    "preview_url" => false,
                    "body" => "🤝 En breve me pondré en contacto contigo. 🤓"
                ]
            ];
        } else if ($comentario == '7') {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => [
                    "preview_url" => false,
                    "body" => "📅 Horario de Atención: Lunes a Viernes. \n🕜 Horario: 9:00 a.m. a 5:00 p.m. 🤓"
                ]
            ];
        } else if (strpos($comentario, 'gracias') !== false) {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => [
                    "preview_url" => false,
                    "body" => "Gracias a ti por contactarme. 🤩"
                ]
            ];
        } else if (strpos($comentario, 'adios') !== false || strpos($comentario, 'bye') !== false || strpos($comentario, 'nos vemos') !== false || strpos($comentario, 'adiós') !== false) {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => [
                    "preview_url" => false,
                    "body" => "Hasta luego. 🌟"
                ]
            ];
        }

        // Solo ejecutar si se ha definido la variable $data
        if (!empty($data)) {
            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\nAuthorization: Bearer EAAL4egoQBUEBOyQ2cXZBKimtjL3cSWnCwRT1T4X5SlpDDjiNmKF2ZC85wcAdk8VhsSldYTFOlN7o3Xcj217CuAX5UAvlu8zLgRplpc1h4OaLRJvSg3wFGIFiQdXXYU0w1zrOfvIfyPqnnXQzxHLAD8zSQV43Ap2ZB18XANaaSWKE0SLe20NO6ZAsZA3ipdmEUHAA7w4DIVItOeUVW4TwZD\r\n",
                    'content' => json_encode($data),
                    'ignore_errors' => true
                ]
            ];

            $context = stream_context_create($options);
            $response = file_get_contents('https://graph.facebook.com/v20.0/397221650140218/messages', false, $context);

            if ($response === false) {
                error_log("Error al enviar el mensaje\n");
            } else {
                error_log("Mensaje enviado correctamente\n");
            }
        }
    }

    function verificarTextoEnArchivo($texto, $archivo) {
        $contenido = file_get_contents($archivo);
        
        if (strpos($contenido, $texto) !== false) {
            return true; // El texto ya existe en el archivo
        } else {
            return false; // El texto no existe en el archivo
        }
    }


    if ($_SERVER['REQUEST_METHOD']==='POST'){
        $input = file_get_contents('php://input');
        $data = json_decode($input,true);

        recibirMensajes($data,http_response_code());
        
    }else if($_SERVER['REQUEST_METHOD']==='GET'){
        if(isset($_GET['hub_mode']) && isset($_GET['hub_verify_token']) && isset($_GET['hub_challenge']) && $_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === TOKEN_HELPPRO){
            echo $_GET['hub_challenge'];
        }else{
            http_response_code(403);
        }
    }
?>
       