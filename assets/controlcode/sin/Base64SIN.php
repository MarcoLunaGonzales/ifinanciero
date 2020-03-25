<?php
/**
 * @see http://www.jc-mouse.net/
 * @author Mouse
 */
class Base64SIN {
    
    static function convert($value){
        $dictionary = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", 
                            "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
                            "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", 
                            "U", "V", "W", "X", "Y", "Z", "a", "b", "c", "d",
                            "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", 
                            "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", 
                            "y", "z", "+", "/");        
        $quotient = 1;                 
        $word = "";
        while ($quotient > 0)
        {
            $quotient = floor($value / 64);
            $remainder = $value % 64;
            $word = $dictionary[$remainder] . $word;
            $value = $quotient;
        }
        return $word;
    }    
}
