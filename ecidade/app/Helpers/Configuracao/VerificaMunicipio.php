<?php

if (!function_exists('isNiteroi')) {
    function isNiteroi()
    {
        $municipio = InstituicaoRepository::getInstituicaoPrefeitura()->getMunicipio();

            return strtoupper(trim($municipio)) == "NITEROI";
    }
}

if (!function_exists('isVoltaRedonda')) {
    function isVoltaRedonda()
    {
        $municipio = InstituicaoRepository::getInstituicaoPrefeitura()->getMunicipio();
        
            return strtoupper(trim($municipio)) == "VOLTA REDONDA";
    }
}

if (!function_exists('isSantanaDoLivramento')) {
    function isSantanaDoLivramento()
    {
        $municipio = InstituicaoRepository::getInstituicaoPrefeitura()->getMunicipio();

            return strtoupper(trim($municipio)) == "SANTANA DO LIVRAMENTO";
    }
}

if (!function_exists('isValenca')) {
    function isValenca()
    {
        $municipio = InstituicaoRepository::getInstituicaoPrefeitura()->getMunicipio();
            return strtoupper(trim($municipio)) == "VALENÇA";
    }
}

if (!function_exists('isCapivari')) {
    function isCapivari()
    {
        $municipio = InstituicaoRepository::getInstituicaoPrefeitura()->getMunicipio();
            return strtoupper(trim($municipio)) == "CAPIVARI DO SUL";
    }
}
