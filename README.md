# Ofx
Transforma em objetos dados de um arquivo de extrato bancario OFX.

este codigo foi retirado do forum https://forum.imasters.com.br/topic/490216-leitura-de-arquivos-ofx-com-php/

# Instalação
composer require asmpkg/ofx

use Asmpkg/Ofx;

$ofx = new Ofx("Arquivo.ofx");

Data inicio e fim
$ofx->dtStar
$ofx->dtEnd

Codigo do Banco
$ofx->bankId

Conta do banco
$ofx->acctId

Nome do banco
$ofx->org

Extrato
foreach($ofx->bankTranList as $extrato)
    