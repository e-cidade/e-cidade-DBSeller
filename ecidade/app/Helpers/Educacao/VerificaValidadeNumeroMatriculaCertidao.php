<?php
if (!function_exists('verificaValidadeNumeroMatriculaCertidao')) {
    function verificaValidadeNumeroMatriculaCertidao($numeroMatricula)
    {
        if (empty($numeroMatricula)) {
            return false;
        }

        $pesosPrimeiroDigitoVerificador = [
                                             2, 3, 4, 5, 6, 7, 8, 9, 10,
                                             0, 1, 2, 3, 4, 5, 6, 7, 8,
                                             9, 10, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9
                                          ];

        $pesosSegundoDigitoVerificador = [
                                            1,  2,  3,  4,  5,  6,  7,  8,  9, 10,
                                            0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10,
                                            0,  1,  2,  3,  4,  5,  6,  7,  8,  9
                                         ];

        $numeroMatriculaCalculo = substr($numeroMatricula, 0, (strlen($numeroMatricula)-2));

         /*
         * Calculo do primeiro digito verificador
         */
        $primeiroDigitoVerificador = 0;
        for ($i = 0; $i < strlen($numeroMatriculaCalculo); $i++) {
            $numero = substr($numeroMatriculaCalculo, $i, 1);
            $primeiroDigitoVerificador += $numero * $pesosPrimeiroDigitoVerificador[$i];
        }

        $primeiroDigitoVerificador = $primeiroDigitoVerificador % 11;
        if ($primeiroDigitoVerificador == 10) {
            $primeiroDigitoVerificador = 1;
        }
        $numeroMatriculaCalculo .= $primeiroDigitoVerificador;

        /*
         * Calculo do segundo digito verificador
         */
        $segundoDigitoVerificador = 0;
        for ($i = 0; $i < strlen($numeroMatriculaCalculo); $i++) {
            $numero = substr($numeroMatriculaCalculo, $i, 1);
            $segundoDigitoVerificador += $numero * $pesosSegundoDigitoVerificador[$i];
        }

        $segundoDigitoVerificador = $segundoDigitoVerificador % 11;
        if ($segundoDigitoVerificador == 10) {
            $segundoDigitoVerificador = 1;
        }
        $numeroMatriculaCalculo .= $segundoDigitoVerificador;

        /*
         * Comparaחדo entre o numero informado e o numero calculado
         * Se resultado igual, retorna true, numero da matricula valido
         */
        if ($numeroMatricula == $numeroMatriculaCalculo) {
            return true;
        } else {
            return false;
        }
    }
}
