<?php

namespace ECidade\RecursosHumanos\Pessoal\Service;

use BusinessException;
use ECidade\RecursosHumanos\Pessoal\Model\DecimoModel;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorDecimo;

class DecimoServidorService
{

    public static function inserir($dados)
    {
        $decimo = new ServidorDecimo();
        $decimoModel = new DecimoModel();
        foreach ($dados as $dado) {
            $decimoModel->setMatricula($dado['matriculas']);
            $decimoModel->setInstituicao($dado['instituicao']);
            $decimoModel->setData($dado['data_decimo_13']);
            $decimoModel->setAno($dado['ano']);
            $decimo->salvar($decimoModel);
        }
    }

    public static function getDataDecimo($matricula, $ano)
    {
        $decimo = new ServidorDecimo();
        return $decimo->getDataCgm($matricula, $ano);
    }
}
