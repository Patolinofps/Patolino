<?php

function obterToken() {
    $login = 'abepnisantamariadocambuca@gmail.com';
    $senha = '223344ab';
    $token = base64_encode("$login:$senha");

    $tokenUrl = 'https://servicos-cloud.saude.gov.br/pni-bff/v1/autenticacao/tokenAcesso';
    $headers = [
        'Content-Type: application/json',
        'x-authorization: Basic ' . $token,
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.5735.198 Safari/537.36',
        'Accept-Encoding: gzip, deflate, br',
        'Accept-Language: en-US,en;q=0.9'
    ];

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        $data = json_decode($response, true);
        return $data['accessToken'];
    } else {
        echo json_encode(["error" => "Erro ao obter o token."]);
        return null;
    }
}

function consultarCPF($cpf) {
    $tokenAcess = obterToken();
    
    if (!$tokenAcess) {
        echo json_encode(["error" => "Token de acesso inválido."]);
        return null;
    }
    
    $url = "https://servicos-cloud.saude.gov.br/pni-bff/v1/cidadao/cpf/$cpf";
    $headers = [
        'Host: servicos-cloud.saude.gov.br',
        'Accept: application/json, text/plain, */*',
        'Authorization: Bearer ' . $tokenAcess,
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.5735.198 Safari/537.36',
        'Accept-Encoding: gzip, deflate, br',
        'Accept-Language: en-US,en;q=0.9',
        'Connection: keep-alive',
        'Referer: https://si-pni.saude.gov.br/',
        'Origin: https://si-pni.saude.gov.br',
        'DNT: 1',
        'Upgrade-Insecure-Requests: 1',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-origin'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        header('Content-Type: application/json');
        echo $response;
    } else {
        echo json_encode(["error" => "Erro ao consultar CPF."]);
    }
}

// Obtém o CPF da URL
if (isset($_GET['cpf'])) {
    $cpf = $_GET['cpf'];
    consultarCPF($cpf);
} else {
    echo json_encode(["error" => "CPF não informado na URL."]);
}
?>
