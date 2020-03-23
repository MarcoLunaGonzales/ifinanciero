<?php
/**
 * @see http://www.jc-mouse.net/
 * @author Mouse
 */
class AllegedRC4 {
    
    /**
     * Retorna mensaje encriptado
     * @param message mensaje a encriptar
     * @param key llave para encriptar
     * @param unscripted sin guion TRUE|FALSE
     * @return String mensaje encriptado
     */
    static function encryptMessageRC4($message, $key,$unscripted=false){
        $state = range(0, 255);
        $x=0;
        $y=0;
        $index1=0;
        $index2=0;        
        $nmen="";        
        $messageEncryption="";
        
        for($i=0;$i<=255;$i++){
            //Index2 = ( ObtieneASCII(key[Index1]) + State[I] + Index2 ) MODULO 256
            $index2 =  ( ord($key[$index1]) +  $state[$i] + $index2) % 256;
            //IntercambiaValor( State[I], State[Index2] )
            $aux = $state[$i];
            $state[$i] = $state[$index2];
            $state[$index2] = $aux;
            //Index1 = (Index1 + 1) MODULO LargoCadena(Key)
            $index1 = ($index1 + 1 ) % strlen($key);
        }        
        //PARA I = 0 HASTA LargoCadena(Mensaje)-1 HACER
        for($i=0; $i<strlen($message);$i++ ){
            //X = (X + 1) MODULO 256
            $x = ($x + 1) % 256;
            //Y = (State[X] + Y) MODULO 256
            $y = ($state[$x] + $y) % 256;
            //IntercambiaValor( State[X] , State[Y] )
            $aux = $state[$x];
            $state[$x] = $state[$y];
            $state[$y] = $aux; 
            //NMen = ObtieneASCII(Mensaje[I]) XOR State[(State[X] + State[Y]) MODULO 256]
            $nmen = ( ord($message[$i])) ^ $state[($state[$x]+$state[$y]) % 256];
            //MensajeCifrado = MensajeCifrado + "-" + RellenaCero(ConvierteAHexadecimal(NMen))            
            $nmenHex = strtoupper(dechex($nmen));               
            $messageEncryption = $messageEncryption . (($unscripted)?"":"-"). ((strlen($nmenHex)==1)?('0'.$nmenHex):$nmenHex);            
        }        
        return (($unscripted)?$messageEncryption:substr($messageEncryption,1,strlen($messageEncryption))); 
    }
    
}//end:class