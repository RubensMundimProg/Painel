<?php

namespace Autenticacao\Service;

class Autenticador
{
    public function validate($user,$pass)
    {
        $strUrlAmbienteSsi = 'http://200.130.24.31:9053/SSIServices/seam/resource/v1/private/autenticador/autenticar';
        $strClientId = 'NDI0LENNSSxkNzZhMjJkNjEzMTQ3MGExZjg0MzBiZDk4YmM2OWJhMzY5MDdiNGZkNDIzZGI1NTA3NjQ0NjVmYWYxMTdmMTJh';
        $strUser = $user;
        $strPassword = $pass;

        $intRandUsername = abs(rand());
        $intRandPassword = abs(rand());
        $strHash = hash('sha256', 'r&$T%$@#I%n*e@P');

        $mixTokenId = base64_encode(sprintf(
            "%02d%d%02d%s%02d%d%02d%s",
            strlen(sprintf("%d", $intRandUsername)),
            $intRandUsername,
            strlen(base64_encode($strUser)),
            base64_encode($strUser),
            strlen(sprintf("%d", $intRandPassword)),
            $intRandPassword,
            strlen(base64_encode($strPassword)),
            base64_encode($strPassword)
        ));

        $strHex = '';
        for ($intSequencia = 0; $intSequencia < strlen($strHash); ++$intSequencia) {
            $strHex .= dechex((int)$strHash[$intSequencia]);
        }

        $arrPost = array(
            'login=' . $strUser,
            'senha=' . base64_encode($strHex . base64_encode($strPassword)),
        );

        try{
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $strUrlAmbienteSsi);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'OauthDisabledClientToService: true',
                'Authorization: OAuth oauth_token="",oauth_consumer_key="' . $strClientId .'",tokenId="' . $mixTokenId .'"'
            ));
            curl_setopt($curl, CURLOPT_POST, count($arrPost));
            curl_setopt($curl, CURLOPT_POSTFIELDS, rtrim(implode('&', $arrPost), '&'));
            $strReponse = curl_exec($curl);
            curl_close($curl);
            $dataUser = json_decode($strReponse, true);
            return $dataUser;
        }catch(\Exception $e){
            return $this->addErrorMessage($e->getMessage());
        }

    }


}