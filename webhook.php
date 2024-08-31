<?php 
    const TOKEN_HELPPRO = "HELPPROAPIMETA";
    const WEBHOOK_URL = "https://apiconsultas.helppro.tech/webhook.php";

    function verificarToken($req, $res) {
        try {
            if (isset($req['hub_verify_token']) && isset($req['hub_challenge'])) {
                $token = $req['hub_verify_token'];
                $challenge = $req['hub_challenge'];

                if ($token === TOKEN_HELPPRO) {
                    echo $challenge;
                    exit; // Control de salida para evitar múltiples respuestas
                } else {
                    http_response_code(400);
                    echo "Invalid token";
                    exit; // Control de salida para evitar múltiples respuestas
                }
            } else {
                http_response_code(400);
                echo "Missing token or challenge";
                exit; // Control de salida para evitar múltiples respuestas
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo "Error processing token verification";
            exit; // Control de salida para evitar múltiples respuestas
        }
    }

    function recibirMensajes($req) {
        try {
            if (isset($req['entry'][0]['changes'][0]['value']['messages'][0])) {
                $entry = $req['entry'][0];
                $changes = $entry['changes'][0];
                $value = $changes['value'];
                $mensaje = $value['messages'][0];

                $comentario = $mensaje['text']['body'];
                $numero = $mensaje['from'];

                EnviarMensajeWhatsapp($comentario, $numero);

                // Registrar el número en un archivo log
                $archivo = fopen("log.txt", "a");
                $texto = json_encode($numero);
                fwrite($archivo, $texto . PHP_EOL);
                fclose($archivo);

                // Enviar respuesta estándar al servidor
                http_response_code(200);
                echo "EVENT_RECEIVED";
                exit; // Control de salida para evitar múltiples respuestas
            } else {
                http_response_code(400);
                echo "No message data found";
                exit; // Control de salida para evitar múltiples respuestas
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error processing the message";
            exit; // Control de salida para evitar múltiples respuestas
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
                    "caption" => "Temario del Curso #001"
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if ($data) {
            recibirMensajes($data);
        } else {
            http_response_code(400);
            echo "Invalid JSON input";
            exit; // Control de salida para evitar
        }
    }  
?>
       