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

namespace App\Domain\Educacao\Escola\Services;

use ECidade\Educacao\Escola\Model\ProfissionalEscola;
use ECidade\Educacao\Escola\Repository\TurmasRegentesEscolaRepository;
use Exception;

class TurmasProfissionaisService
{
    /**
     * @param ProfissionalEscola $profissionalEscola
     * @param $codigoEscola
     * @return array
     * @throws Exception
     */
    public function buscarTurmasPorProfissional(ProfissionalEscola $profissionalEscola, $codigoEscola)
    {
        $permissaoDiarioService = new PermissaoDiarioService();
        $permissaoDiario = $permissaoDiarioService->getMaiorPermissaoProfissional($profissionalEscola);

        $dadosTurmas = [];
        $dataLogin = date("Y-m-d", db_getsession('DB_datausu'));
        $turmasRegentesEscolaRepository = new TurmasRegentesEscolaRepository();
        if ($permissaoDiario == PermissaoDiarioService::PERMISSAO_TOTAL) {
            //buscar turmas da escola
            $dadosTurmas = $turmasRegentesEscolaRepository->get(
                null,
                $codigoEscola,
                $dataLogin,
                [
                    'distinct ed52_i_ano',
                    'ed57_i_codigo',
                    'ed57_c_descr',
                    'ed11_i_codigo',
                    'ed11_c_descr',
                    'ed220_i_codigo'
                ],
                ['ed52_i_ano desc, ed57_c_descr asc'],
                false
            );
        }

        if ($permissaoDiario == PermissaoDiarioService::PERMISSAO_PROFESSOR) {
            //buscar turmas da escola que o professor leciona
            $dadosTurmas = $turmasRegentesEscolaRepository->get(
                $profissionalEscola->getCgm()->getCodigo(),
                $codigoEscola,
                $dataLogin,
                [
                    'ed52_i_ano',
                    'ed57_i_codigo',
                    'ed57_c_descr',
                    'ed11_i_codigo',
                    'ed11_c_descr',
                    'ed220_i_codigo'
                ],
                ['ed52_i_ano desc, ed57_c_descr asc'],
                false
            );
        }
        
        if ($permissaoDiario == PermissaoDiarioService::SEM_PERMISSAO) {
            throw new Exception("Profissional não tem permissão para lançar notas!");
        }

        foreach ($dadosTurmas as $index => $dado) {
            $turma = new \Turma($dado['ed57_i_codigo']);
            $dadosTurmas[$index]['turmaEncerradaEtapa'] = $turma->encerradaNaEtapa(new \Etapa($dado['ed11_i_codigo']));
        }

        return $this->toResource($dadosTurmas);
    }

    /**
     * @param $codigoEscola
     * @return array
     * @throws Exception
     */
    public function buscarTurmasEscola($codigoEscola)
    {
        $dataLogin = date("Y-m-d", db_getsession('DB_datausu'));
        $turmasRegentesEscolaRepository = new TurmasRegentesEscolaRepository();
        $dadosTurmas = $turmasRegentesEscolaRepository->get(
            null,
            $codigoEscola,
            $dataLogin,
            ['distinct ed52_i_ano', 'ed57_i_codigo', 'ed57_c_descr', 'ed11_i_codigo', 'ed11_c_descr', 'ed220_i_codigo'],
            ['ed52_i_ano desc, ed57_c_descr asc'],
            false
        );

        foreach ($dadosTurmas as $index => $dado) {
            $turma = new \Turma($dado['ed57_i_codigo']);
            $dadosTurmas[$index]['turmaEncerradaEtapa'] = $turma->encerradaNaEtapa(new \Etapa($dado['ed11_i_codigo']));
        }

        return $this->toResource($dadosTurmas);
    }

    /**
     * @param array $dadosTurmas
     * @return array
     */
    public function toResource(array $dadosTurmas)
    {
        $turmasPorAno = [];
        foreach ($dadosTurmas as $dado) {
            $ano = $dado['ed52_i_ano'];
            if (!array_key_exists($ano, $turmasPorAno)) {
                $turmasPorAno[$ano] = (object)[
                    'ano' => $ano,
                    'turmas' => []
                ];
            }

            $etapaPorCodigo = \EtapaRepository::getEtapaByCodigoTurmaSerieRegimeMat($dado['ed220_i_codigo'])->getNome();
            $key = $dado['ed220_i_codigo'];
            $turmasPorAno[$ano]->turmas[$key] = (object)[
                "codigo" => $dado['ed57_i_codigo'],
                "descricao" => $dado['ed57_c_descr'],
                "etapa" => $etapaPorCodigo,
                "turmaserieregimemat" => $dado['ed220_i_codigo'],
                "encerrada" => $dado['turmaEncerradaEtapa']
            ];
        }

        $turmasPorAno = array_values($turmasPorAno);
        foreach ($turmasPorAno as $ano) {
            $ano->turmas = array_values($ano->turmas);
        }

        return $turmasPorAno;
    }
}
