<?php 
    const TOKEN_HELPPRO = "HELPPROAPIMETA";
    const WEBHOOK_URL = "https://botapiwhatsap.helppro.tech/webhook.php";

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
                    "body" => "Hola visita mi pagina web helppro.tech"
                ]
            ]);
        }else{

        }

        $options = [
            'http' =>[
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAuthorization: Bearer EAAHk0nOEnX0BO4B7VQqjrmcJVZAGmM5yNbiRBJTfdWglKcoTpjQyPZCb3iy8juImWp2ANR2fOjjGYt3jgjRcVkEgw7zZAOHc40TL9Cphm1XBWUwZBABH25mqf9PwFcdZAm7QpgdDnoZAyfttzif1swjDVfxjY3T4diZC40h0NkilS8YeZBDZAOqzU4aaUAZAUf6FmFz5xP881FZAeTYKd7kynkZD\r\n",
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