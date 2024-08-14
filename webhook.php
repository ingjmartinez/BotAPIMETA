<?php 
    const TOKEN_HELPPRO = "HELPPROAPIMETA";
    const WEBHOOK_URL = "https://apiconsultas.helppro.tech/webhook.php";

    function verificarToken($req, $res){
        try {
            $token = $req['hub_verify_token'];
            $challenge = $req['hub_challenge'];

            if (isset($challenge) && isset($token) && $token == TOKEN_HELPPRO) {
                $res->send($challenge);
            } else {
                $res->status(400)->send();
            }
        } catch(Exception $e) {
            $res->status(400)->send();
        }
    }

    function recibirMensajes($req, $res){
        try {
            $entry = $req['entry'][0];
            $changes = $entry['changes'][0];
            $value = $changes['value'];
            $objetomensaje = $value['messages'];
            $mensaje = $objetomensaje[0];

            $comentario = $mensaje['text']['body'];
            $numero = $mensaje['from'];

            EnviarMensajeWhatsapp($comentario, $numero);

            $archivo = fopen("log.txt", "a");
            $texto = json_encode($numero);
            fwrite($archivo, $texto);
            fclose($archivo);

            // Enviar solo una respuesta por cada evento
            $res->send("EVENT_RECEIVED");
        } catch(Exception $e) {
            $res->send("EVENT_RECEIVED");
        }
    }

    function EnviarMensajeWhatsapp($comentario, $numero){
        $comentario = strtolower($comentario);

        $data = [];
        if (strpos($comentario,'hola') !== false) {
            $data = [
                "messaging_product" => "whatsapp",    
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => [
                    "preview_url" => false,
                    "body" => "🚀 Hola, visita mi web oficial de validación de premios en premios.helppro.com.do para más información."
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
        } // Añade más condiciones según sea necesario.

        if (!empty($data)) {
            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\nAuthorization: Bearer EAA...TuToken...\r\n",
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        recibirMensajes($data, http_response_code());
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['hub_mode']) && isset($_GET['hub_verify_token']) && isset($_GET['hub_challenge']) && $_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === TOKEN_HELPPRO) {
            echo $_GET['hub_challenge'];
        } else {
            http_response_code(403);
        }
    }
?>
