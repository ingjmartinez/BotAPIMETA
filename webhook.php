<?php 
    const TOKEN_HELPPRO = "HELPPROAPIMETA";
    const WEBHOOK_URL = "https://apiconsultas.helppro.tech/webhook.php";

    function verifdicarToken($req,$res){
        try{
            $token = $res['hub_verify_token'];
            $challenge = $req['hub_challenge'];

            if (isset($challenge) && isset($token) && $token == TOKEN_HELPPRO){
                $res->send($challenge);
            }else{
                $res ->status(400)->send();
            }


        }catch(Exception $e){
            $res ->status(400)->send();
        }

    }

    function recibirMensajes($req,$res){
        try{
            $entry = $req['entry'][0];
            $changes = $entry['changes'][0];
            $value = $changes['value'];
            $objetomensaje =$value['messages'];
            $mensaje = $objetomensaje[0];

            $comentario = $mensaje['text']['body'];
            $numero = $mensaje['from'];

            EnviarMensajeWhatsapp($comentario,$numero);
            
            $archivo = fopen("log.txt","a");
            $texto = json_encode($numero);
            fwrite($archivo,$texto);
            fclose($archivo);

            $res ->send("EVENT_RECEIVED");
        }catch(Exception $e){
            $res ->send("EVENT_RECEIVED");
        }
    }

    function EnviarMensajeWhatsapp($comentario,$numero){
        $comentario = strtolower($comentario);

        if (strpos($comentario,'hola') !==false){
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => [
                    "preview_url" => false,
                    "body" => "🚀 Hola, visita mi web oficial validacion de premios, premios.helppro.com.do para mas informacion.\n \n📌Por favor, ingresa un número #️⃣ para recibir información.\n \n1️⃣. Consultar sorteos. ❔\n2️⃣. Ubicación de los centro de pago. 📍\n3️⃣. Canales oficiales de sorteos. 📄\n4️⃣. Horarios de sorteos. 🕜\n5️⃣. Canales oficiales de sorteos. ⏯️\n6️⃣. Hablar con soporte. 🙋‍♂️\n7️⃣. Horario de Atención. 🕜"
                ]
            ]);
        }else if ($comentario=='1') {
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $numero,
                "type" => "text",
                "text"=> [
                    "preview_url" => false,
                    "body"=> "Consultar sorteos."
                ]
            ]);

        }else if ($comentario=='2') {
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $numero,
                "type" => "location",
                "location"=> [
                    "latitude" => "-12.067158831865067",
                    "longitude" => "-77.03377940839486",
                    "name" => "Estadio Nacional del Perú",
                    "address" => "Cercado de Lima"
                ]

            ]);

        }else if ($comentario=='3') {
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $numero,
                "type" => "document",
                "document"=> [
                    "link" => "http://s29.q4cdn.com/175625835/files/doc_downloads/test.pdf",
                    "caption" => "Temario del Curso #001"
                ]
            ]);

        }else if ($comentario=='4') {
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $numero,
                "type" => "audio",
                "audio"=> [
                    "link" => "https://filesamples.com/samples/audio/mp3/sample1.mp3",
                ]
            ]);

        }else if ($comentario=='5') {
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $numero,
                "text" => array(
                    "preview_url" => true,
                    "body" => "Introducción al curso! https://youtu.be/6ULOE2tGlBM"
                )
            ]);

        }else if ($comentario=='6') {
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => array(
                    "preview_url" => false,
                    "body" => "🤝 En breve me pondré en contacto contigo. 🤓"
                )
            ]);
        }else if ($comentario=='7') {
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => array(
                    "preview_url" => false,
                    "body" => "📅 Horario de Atención: Lunes a Viernes. \n🕜 Horario: 9:00 a.m. a 5:00 p.m. 🤓"
                )
            ]);    

        }else if (strpos($comentario,'gracias') !== false) {
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => array(
                    "preview_url" => false,
                    "body" => "Gracias a ti por contactarme. 🤩"
                )
            ]);

        }else if (strpos($comentario,'adios') !== false || strpos($comentario,'bye') !== false || strpos($comentario,'nos vemos') !== false || strpos($comentario,'adiós') !== false){
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => array(
                    "preview_url" => false,
                    "body" => "Hasta luego. 🌟"
                )
            ]);

        }else{
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type" => "individual",
                "to" => $numero,
                "type" => "text",
                "text" => [
                    "preview_url" => false,
                    "body" => "🚀 Hola, visita mi web oficial validacion de premios, premios.helppro.com.do para mas informacion.\n \n📌Por favor, ingresa un número #️⃣ para recibir información.\n \n1️⃣. Consultar sorteos. ❔\n2️⃣. Ubicación de los centro de pago. 📍\n3️⃣. Canales oficiales de sorteos. 📄\n4️⃣. Horarios de sorteos. 🕜\n5️⃣. Canales oficiales de sorteos. ⏯️\n6️⃣. Hablar con soporte. 🙋‍♂️\n7️⃣. Horario de Atención. 🕜"
                ]
            ]);

        }

        $options = [
            'http' =>[
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAuthorization: Bearer EAAL4egoQBUEBOZBpGP09zL8zt27mNSbmc27Yc3imTtfxSzHlXaHDO4pbhZCVry7PL3T2lZBZAauxrpK7kmlVzhOxRREADCOyUGdaZCPp7GmAygaZBZAbUm0xdhf1FPUjZBN6PKp6OTny26IBX2Lc52tISdAcCyhYOachauJpvcTBDF83z81XVBOsDh8ZB3HHEX8Vcsq6fmXZBva0g01Tff1oEZD\r\n",
                'content' => $data,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents('https://graph.facebook.com/v20.0/397221650140218/messages', false, $context);
    
        if($response === false){
            echo "Error al enviar el mensaje\n";
        }else{
            echo "Mensaje enviado correctamente\n";
        }
    }


    if($_SERVER['REQUEST_METHOD']==='POST'){
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