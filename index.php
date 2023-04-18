<?php

require __DIR__.'/vendor/autoload.php';

$headers = 'POST /produtos/ HTTP/1.1
Accept: */*
Accept-Encoding: gzip, deflate, br
Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7
Cache-Control: no-cache
Connection: keep-alive
Content-Length: 102
Content-Type: application/x-www-form-urlencoded; charset=UTF-8
Cookie: _ga=GA1.4.850928237.1681832180; _gid=GA1.4.743537860.1681832180; session=eyJjc3JmX3Rva2VuIjoiMzc2MjAyMmRlM2E5OWExNGM2YjBhZjYzYzMwMjBhYTc0Njk5NWU3ZSJ9.ZD7QWQ.7Oi8e3j5AYxu5_QUCT26P2LYWUQ; token=S4bQRayTXNWJWDG0BB2y3Dqyn1AWVB3Wx1zKoib4A45avnXcTyTwTbqLZ7ZYIIbU4DB-Yw8rU49hg-4zy4oO-p1ob8k; _gat_gtag_UA_156559903_1=1
Host: precodahora.ba.gov.br
Origin: https://precodahora.ba.gov.br
Pragma: no-cache
Referer: https://precodahora.ba.gov.br/produtos/
Sec-Fetch-Dest: empty
Sec-Fetch-Mode: cors
Sec-Fetch-Site: same-origin
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36
X-CSRFToken: IjM3NjIwMjJkZTNhOTlhMTRjNmIwYWY2M2MzMDIwYWE3NDY5OTVlN2Ui.ZD7ksg.4fl8m4nvLh9X6CM7Fc1vKCO0uK8
X-Requested-With: XMLHttpRequest
sec-ch-ua: "Chromium";v="112", "Google Chrome";v="112", "Not:A-Brand";v="99"
sec-ch-ua-mobile: ?0
sec-ch-ua-platform: "Windows"
';

$headers_array = array();
foreach (explode("\n", $headers) as $header) {
    $parts = explode(': ', $header);
    if (count($parts) === 2) {
        $headers_array[$parts[0]] = trim($parts[1]);
    }
}

use GuzzleHttp\Client;
use \GuzzleHttp\Psr7\Request;
$client = new Client();
$result = $client->request('GET','https://precodahora.ba.gov.br/produtos');
$headers_array['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
$headers_array['Cookie'] = implode("; ",$result->getHeaders()['Set-Cookie']);
$doc = new DOMDocument("1.0", "UTF-8");
libxml_use_internal_errors(true);
$doc->loadHTML(trim($result->getBody()->getContents()));
libxml_use_internal_errors(false);
$headers_array['X-CSRFToken'] = "{$doc->getElementById('validate')->getAttribute('data-id')}";
$termo = $_GET['termo']??'';
$request = new Request('POST', 'https://precodahora.ba.gov.br/produtos/', $headers_array, "termo={$termo}&horas=72&latitude=-13.385508&longitude=-44.202014&raio=15&pagina=1&ordenar=preco.asc");
$response = $client->send($request);
$result = json_decode((string) $response->getBody());
if($result->codigo !== 80){ $result->resultado = [];}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Lista de produtos</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
			font-family: Roboto, arial;
		}
		body{
			padding: 20px;
		}

		table {
			width: 100%;
			margin: 10px auto;
			border-spacing: 0;
			border-radius: 10px;
			border: 1px solid #ccc;
			overflow: hidden;
			box-shadow: 3px 5px 20px #ccc;
		}

		thead {}

		th {
			color: #fbfbfb;
			background-color: #2980b9;
		}

		td {
			color: #444;
			border: solid 1px #ccc;
		}

		tr:nth-child(odd) {
			background-color: #eee;
		}

		tr:nth-child(even) {
			background-color: #fbfbfb;
		}

		th,
		td {
			text-align: left;
			padding: 0 10px;
			height: 40px;
			line-height: 40px;

		}

		tfoot tr td {
			border-top: 1px solid #ccc;
			background-color: #34495e !important;
			color: #fbfbfb !important;
		}

		form{
			padding: 30px;
			background-color: #fbfbfb;
			border: solid #eee 2px;
			border-radius: 10px;
		}

		.text{
			border: solid 1px #ccc;
			border-bottom: 3px solid #ccc;
			font-size: 1em;
			height: 35px;
			padding: 0 5px 0 7px;
			outline: 0;
		}

		.submit{
			border: solid 1px #ccc;
			border-bottom: 3px solid #ccc;
			font-size: 1em;
			height: 35px;
			outline: 0;
			background-color: #2980b9;
			color: white;
			cursor: pointer;
			padding: 0 10px;
		}
		
	</style>

</head>

<body>
	<form method="GET">
		<input class="text" type="text" name="termo" placeholder="Pesquisa de produto">
		<input class="submit" type="submit" value="Pesquisar">
	</form>
	<table style="overflow: visible;" class="table">
<thead>
	<tr>
		<th>IMAGEM</th>
		<th>ID</th>
		<th>Código de Barra</th>
		<th>NOME</th>
		<th>UN</th>
		<th>P UNIT</th>
		<th>P LIQ</th>
		<th>P BRUTO</th>
		<th>DESC</th>
		<th>MERCADO</th>
	</tr>
</thead>
<tbody class="main">
	<?php foreach ($result->resultado as $data):?>
		<tr>
            <td><img width="60" src="<?= $data->produto->foto;?>"></td>
            <td><?= $data->produto->codProduto;?></td>
            <td><?= $data->produto->gtin;?></td>
            <td><?= $data->produto->descricao;?></td>
            <td><?= $data->produto->unidade;?></td>
            <td><?= $data->produto->precoUnitario;?></td>
            <td><?= $data->produto->precoLiquido;?></td>
            <td><?= $data->produto->precoBruto;?></td>
            <td><?= $data->produto->desconto;?></td>
            <td><?= $data->estabelecimento->nomeEstabelecimento."({$data->estabelecimento->cnpj})";?></td>
		</tr>
	<?php endforeach;?>
	</tbody>
<tfoot class="footer">

</tfoot>
</table>
</body>

</html>
