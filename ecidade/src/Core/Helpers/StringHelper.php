<?php

namespace ECidade\Core\Helpers;

class StringHelper
{

    /**
     * Funзгo realiza substituiзгo de caracteres especiais por simples
     */
    public static function normalizeEncode($string)
    {

        // matriz de entrada
        $original = [
            'д','г','а','б','в',
            'Д','Г','А','Б','В',
            'л','?','и','й','к',
            'Л','?','И','Й','К',
            'п','?','м','н','о',
            'П','?','М','Н','О',
            'ц','х','т','у','ф',
            'Ц','Х','Т','У','Ф',
            'ь','?','щ','ъ','ы',
            'Ь','?','Щ','Ъ','Ы',
            'с','С',
            'з',
            'З',
            ' ','-','(',')',',',';',':','|','!','"','#','$','%','&','/',
            '=','?','~','^','>','<','Є','є'];
    
        // matriz de saнda
        $novo   = [
            'a','a','a','a','a',
            'A','A','A','A','A',
            'e','e','e','e','e',
            'E','E','E','E','E',
            'i','i','i','i','i',
            'I','I','I','I','I',
            'o','o','o','o','o',
            'O','O','O','O','O',
            'u','u','u','u','u',
            'U','U','U','U','U',
            'n',
            'N',
            'c',
            'C',
            '_','_','_','_','_','_','_','_','_','_','_','_','_','_','_',
            '_','_','_','_','_','_','_','_'];
    
        // devolver a string
        return str_replace($original, $novo, $string);
    }
}
