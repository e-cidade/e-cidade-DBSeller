<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\Saude\Laboratorio\Service;

use DateTime;
use ECidade\Saude\Laboratorio\Repository\GrupoExameRepository;
use ECidade\Saude\Laboratorio\Repository\SetorExameRepository;
use ECidade\Saude\Laboratorio\Model\GrupoExame;
use Exception;

/**
 * Class GrupoExameService
 * @package ECidade\Saude\Laboratorio\Service
 */
class GrupoExameService
{
    private $repositorio;

    private $setorExameRepository;

    /**
     * GrupoExameService constructor.
     * @param GrupoExameRepository $repositorio
     */
    public function __construct(GrupoExameRepository $repositorio, SetorExameRepository $setorExameRepository)
    {
        $this->repositorio = $repositorio;
        $this->setorExameRepository = $setorExameRepository;
    }

    /**
     * Buscar exames vinculados aos grupos do laboratório
     */
    public function buscarGrupoExames($parametros)
    {
        $examesGrupo = $this->repositorio->buscar("la68_labgrupoexame=".$parametros->grupoLaboratorio);
        for ($i = 0; $i < count($examesGrupo); $i++) {
            $setorExame = $this->setorExameRepository->buscar(
                "lab_setor.la23_i_codigo, lab_exame.la08_i_codigo",
                "la02_i_codigo = ". $parametros->laboratorio . "and la08_i_codigo = " . $examesGrupo[$i]['codigoexame']
            );
            $examesGrupo[$i]['codigosetor'] = $setorExame[0]['la23_i_codigo'];
        }
        return $examesGrupo;
    }

    /**
     * Buscar exames vinculados ao grupo por laboratorio e por grupo
     */
    public function buscarGrupoExamesPorGrupoELaboratprio($parametros)
    {
        $examesGrupo = $this->repositorio->buscar(
            "la67_laboratorio=".$parametros->laboratorio." and la67_grupo=".$parametros->grupo
        );
        for ($i = 0; $i < count($examesGrupo); $i++) {
            $camposSetorExame = "la09_i_codigo,la09_i_ativo,la08_i_ativo,la03_i_laboratorio";
            $whereSetorExame = " la02_i_codigo = ".$parametros->laboratorio;
            $whereSetorExame .= " and la08_i_codigo = " . $examesGrupo[$i]['codigoexame'];
            $whereSetorExame .= " and la03_i_departamento = ".db_getsession('DB_coddepto');
            
            $setorExame = $this->setorExameRepository->buscarSetorDepartamento($camposSetorExame, $whereSetorExame);
            
            $exameAtivo = false;
            $examesGrupo[$i]['codigosetorexame'] = null;
            if (count($setorExame) > 0) {
                if ($setorExame[0]['la09_i_ativo'] == 1 && $setorExame[0]['la08_i_ativo'] == 1) {
                    $exameAtivo = true;
                }
                $examesGrupo[$i]['codigosetorexame'] = $setorExame[0]['la09_i_codigo'];
            }
            
            $examesGrupo[$i]['exameativo'] = $exameAtivo;
            $examesGrupo[$i]['descricao'] = urlencode($examesGrupo[$i]['descricao']);
            $examesGrupo[$i]['descricaolaboratorio'] = urlencode($examesGrupo[$i]['descricaolaboratorio']);
            $examesGrupo[$i]['obriga_peso'] = urlencode($examesGrupo[$i]['obriga_peso']);
            $examesGrupo[$i]['obriga_altura'] = urlencode($examesGrupo[$i]['obriga_altura']);
            $examesGrupo[$i]['obriga_volume_amostra'] = urlencode($examesGrupo[$i]['obriga_volume_amostra']);
        }
        return $examesGrupo;
    }

    /**
     * Buscar um exame vinculado ao grupo do laboratório
     */
    public function buscarGrupoExame($parametros)
    {
        $grupoExame = new GrupoExame();
        $grupoExame->setGrupoLaboratorio($parametros->grupoLaboratorio);
        return $this->repositorio->buscar("la68_labgrupoexame= ".$grupoExame->getGrupoLaboratorio()." limit 1");
    }

    /**
     * Salva os dados do grupo
     */
    public function salvar($parametros)
    {
        $grupoExame = new GrupoExame();
        $grupoExame->setCodigo(isset($parametros->codigo) ? $parametros->codigo : '');
        $grupoExame->setGrupoLaboratorio(isset($parametros->grupoLaboratorio) ? $parametros->grupoLaboratorio : '');
        $grupoExame->setExame(isset($parametros->exame) ? $parametros->exame : '');

        $campos = "lab_setor.la23_i_codigo, lab_exame.la08_i_codigo";

        if ($parametros->setor) {
            $exameJaCadastrado = $this->buscarGrupoExame($parametros)[0]['codigoexame'];

            if ($exameJaCadastrado) {
                $where = "la02_i_codigo = " .$parametros->laboratorio;
                $where .= " and la08_i_codigo = ". $exameJaCadastrado;
                $where .= " and la23_i_codigo = ". $parametros->setor;
                $setorExameJaCadastrado =
                $this->setorExameRepository->buscar($campos, $where)[0]['la23_i_codigo'];

                if ($parametros->setor != $setorExameJaCadastrado) {
                    $mensagem = "O setor do exame é diferente do setor dos exames cadastrados.";
                    throw new Exception($mensagem);
                }
            }
        } else {
            $mensagem = "Vinculo de exame com o grupo não realizado";
            $mensagem .= ", pois o exame não possui setor cadastrado.";
            throw new Exception($mensagem);
        }
        $grupoExame = $this->repositorio->salvar($grupoExame);

        return $grupoExame;
    }

    /**
     * Excluir grupo por código
     */
    public function excluirExameGrupo($codigo)
    {
        return $this->repositorio->excluir($codigo);
    }
}
