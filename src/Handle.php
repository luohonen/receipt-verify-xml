<?php
namespace Luohonen\Receipt;

use \DOMDocument;
use GuzzleHttp\Client;
use RobRichards\XMLSecLibs\XMLSecurityDSig;

class Handle
{
    public static function verify($xmlData)
    {
        $xmlData = str_replace(array("\n","\t", "\r"), "", $xmlData);
        $xmlData = preg_replace('/\s+/', " ", $xmlData);
        $xmlData = str_replace("> <", "><", $xmlData);

        $document = new DOMDocument();
        $document->loadXML($xmlData);

        $receipt = $document->getElementsByTagName('Receipt')->item(0);
        $certificateId = $receipt->getAttribute('CertificateId');
        if(empty($certificateId))
        {
            throw new \Exception("CertificateId cannot be null");
        }

        $apiUrl = "https://go.microsoft.com/fwlink/p/?linkid=246509&cid=$certificateId";

        $client = new Client();
        $response = $client->request('GET', $apiUrl,array('verify' => false));
        $publicKey = $response->getBody();

        $objXMLSecDSig = new XMLSecurityDSig();
        $objDSig = $objXMLSecDSig->locateSignature($document);
        if(!$objDSig)
        {
            throw new \Exception("Couldnt locate signature");
        }
        $objXMLSecDSig->canonicalizeSignedInfo();
        if(!$objXMLSecDSig->validateReference())
        {
            throw new \Exception("Fail to validate reference");
        }
        $objKey = $objXMLSecDSig->locateKey();
        if(!$objKey)
        {
            throw new \Exception("Fail to locate key");
        }
        $objKey->loadKey($publicKey);
        if(!$objXMLSecDSig->verify($objKey))
        {
            throw new \Exception("Fail to verify signature");
        }

        return true;
    }

}


