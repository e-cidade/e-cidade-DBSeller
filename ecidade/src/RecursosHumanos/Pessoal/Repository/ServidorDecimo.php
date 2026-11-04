<?php


namespace ECidade\RecursosHumanos\Pessoal\Repository;

use cl_servidordecimo;
use db_utils;
use ECidade\RecursosHumanos\Pessoal\Model\DecimoModel;

class ServidorDecimo
{
    public function salvar(DecimoModel $decimoModel)
    {

        $matricula = $decimoModel->getMatricula();
        $instituicao = $decimoModel->getInstituicao();
        $data = $decimoModel->getData();
        $ano = $decimoModel->getAno();

        $matriculaExists = $this->getMatriculaExist($matricula, $ano);
        if ($matriculaExists) {
            foreach ($matriculaExists as $dadosServidor) {
                if (($dadosServidor->rh311_data == $data &&
                    $dadosServidor->rh311_ano != $ano)) {
                    $this->inserir($matricula, $instituicao, $data, $ano);
                }

                if (($dadosServidor->rh311_data != $data &&
                    $dadosServidor->rh311_ano == $ano)) {
                    $this->updateMatriculas(
                        $data,
                        $instituicao,
                        $ano,
                        $matriculaExists
                    );
                }

                if (($dadosServidor->rh311_data == $data &&
                    $dadosServidor->rh311_ano == $ano)) {
                    continue;
                }
            }
        } else {
            $this->inserir($matricula, $instituicao, $data, $ano);
        }
    }


    public function inserir($matricula, $instituicao, $data, $ano)
    {
        $sql = "INSERT INTO pessoal.servidordecimo( 
            rh311_matricula 
           ,rh311_instit 
           ,rh311_data 
           ,rh311_ano 
           )VALUES(
           $matricula,
           $instituicao,
           '{$data}',
           $ano)";
        $rs = db_query($sql);

        if ($rs) {
            return true;
        } else {
            return false;
        }
    }

    public function updateMatriculas($data, $instituicao, $ano, $dados)
    {

        foreach ($dados as $dado) {
            $sql = "update pessoal.servidordecimo set rh311_data = '{$data}' 
                where rh311_matricula = $dado->rh311_matricula 
                and rh311_instit = $instituicao and rh311_ano = $ano";
        }

        $rs = db_query($sql);

        if ($rs) {
            return true;
        }
    }

    public function getMatriculaExist($matricula, $ano)
    {
        $sql = "select rh311_matricula, rh311_instit, rh311_data, rh311_ano 
                from pessoal.servidordecimo where rh311_matricula in (";
        $matriculas = explode(',', $matricula);
        $inArray = [];
        foreach ($matriculas as $matricula) {
            $inArray[] = "'" . $matricula . "'";
        }
        $sql .= implode(",", $inArray);
        $sql .= ") AND rh311_ano = {$ano}";
        $rs = db_query($sql);

        if (pg_num_rows($rs) > 0) {
            return db_utils::getCollectionByRecord($rs);
        } else {
            return false;
        }
    }

    public function getMatriculas($dados)
    {
        $sql = "select rh01_regist from pessoal.rhpessoal 
            where rh01_regist in (";
        $matriculas = explode(',', $dados);
        $inArray = [];
        foreach ($matriculas as $matricula) {
            $inArray[] = "'" . $matricula . "'";
        }
        $sql .= implode(",", $inArray);
        $sql .= ")";
        $rs = db_query($sql);
        
        if ($rs) {
            return db_utils::getCollectionByRecord($rs);
        } else {
            return false;
        }
    }

    public function getMatriculasCgm($dados)
    {
        $sql = "select rh01_regist from pessoal.rhpessoal where rh01_numcgm = {$dados}";
        $rs = db_query($sql);
        if ($rs) {
            return db_utils::getCollectionByRecord($rs);
        } else {
            return false;
        }
    }

    public function getDataCgm($cgm, $ano)
    {
        $matriculas = $this->getMatriculasCgm($cgm);
        $matriculaData = [];

        $sql = "select rh311_data 
                from pessoal.servidordecimo where rh311_matricula in (";
        foreach ($matriculas as $matricula) {
            $matriculaData[] = $matricula->rh01_regist;
        }
        
        $sql .= implode(",", $matriculaData);
        $sql .= ") and rh311_ano = {$ano}";
        $rs = db_query($sql);
        $data = db_utils::getCollectionByRecord($rs);

        foreach ($data as $dt) {
            return ($dt->rh311_data);
        }
    }
}
