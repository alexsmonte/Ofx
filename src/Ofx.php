<?php
namespace Asmpkg\Ofx;
use SimpleXMLElement;

/*** 

este codigo foi retirado do forum https://forum.imasters.com.br/topic/490216-leitura-de-arquivos-ofx-com-php/

***/

class Ofx
{
    private  $arquivo;
    public $bankTranList;
    public $dtStar;
    public $dtEnd;
    public $bankId;
    public $acctId;
    public $org;


    public function __construct($arquivo)
    {
        $this->arquivo  =   $arquivo;

        return $this->retorno();
    }

    public function converterOfxEmXml()
    {

        $content = utf8_decode(file_get_contents($this->arquivo));
        $line = strpos($content, "<OFX>");
        $ofx = substr($content, $line - 1);
        $buffer = $ofx;
        $count = 0;

        while ($pos = strpos($buffer, '<'))
        {
            $count++; $pos2 = strpos($buffer, '>');
            $element = substr($buffer, $pos + 1, $pos2 - $pos - 1);

            if (substr($element, 0, 1) == '/')
                $sla[] = substr($element, 1);
            else $als[] = $element;
            $buffer = substr($buffer, $pos2 + 1);
        }
        $adif = array_diff($als, $sla);


        $adif = array_unique($adif);
        $ofxy = $ofx;

        foreach ($adif as $dif)
        {
            $dpos = 0;
            while ($dpos = strpos($ofxy, $dif, $dpos + 1))
            {
                $npos = strpos($ofxy, '<', $dpos + 1);
                echo $dif."<br>";
                $ofxy = substr_replace($ofxy, "</$dif>".chr(10)."<", $npos, 1);
                $dpos = $npos + strlen($element) + 3;
            }
        }
        $ofxy = str_replace('&', '&', $ofxy);

        //return new SimpleXMLElement($ofxy);

        return $ofxy;
    }


    public function closeTags($ofx=null) {
        $buffer = '';
        $source = fopen($ofx, 'r') or die("Unable to open file!");
        while(!feof($source)) {
            $line = trim(fgets($source));
            if ($line === '') continue;

            if (substr($line, -1, 1) !== '>') {
                list($tag) = explode('>', $line, 2);
                $line .= '</' . substr($tag, 1) . '>';
            }
            $buffer .= $line ."\n";
        }


        $xmlOut =   explode("<OFX>", $buffer);

        //$name = realpath(dirname($ofx)) . '/' . date('Ymd') . '.ofx';
        //$file = fopen($name, "w") or die("Unable to open file!");
        //fwrite($file, $buffer);
        //fclose($file);

        return isset($xmlOut[1])?"<OFX>".$xmlOut[1]:$buffer;
    }

    public function retorno()
    {
        $retorno    =   new SimpleXMLElement(utf8_encode($this->closeTags($this->arquivo)));

        $this->bankTranList =   $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->STMTTRN;
        $this->dtStar   =   $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->DTSTART;
        $this->dtEnd    =   $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->DTEND;

        $this->org      =   $retorno->SIGNONMSGSRSV1->SONRS->FI->ORG;
        $this->acctId   =   $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->ACCTID;
        $this->bankId   =   $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->BANKID;

        return $this;
    }

}
