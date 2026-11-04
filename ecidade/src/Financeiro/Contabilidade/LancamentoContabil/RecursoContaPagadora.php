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

namespace ECidade\Financeiro\Contabilidade\LancamentoContabil;

use cl_conlancamrecurso;
use ILancamentoAuxiliar;

/**
 * Quando NÃO configurado Domicílio bancário
 *
 * @package ECidade\Financeiro\Contabilidade\LancamentoContabil
 */
class RecursoContaPagadora extends RecursoAbstract
{
    public function processar($codigoLancamnento, ILancamentoAuxiliar $lancamentoAuxiliar = null)
    {
        $daoConlancam = new \cl_conlancam();
        $sqlRecurso = $daoConlancam->sql_consulta_recursos_lancamentos($codigoLancamnento);

        $rsRecursos = db_query($sqlRecurso);
        if (pg_num_rows($rsRecursos) == 0) {
            return;
        }

        $daoConlancamrecurso = new cl_conlancamrecurso();
        $sqlVerifica = $daoConlancamrecurso->sql_query_file(null, "*", null, "c130_conlancam = {$codigoLancamnento}");
        $daoConlancamrecurso->sql_record($sqlVerifica);
        if ($daoConlancamrecurso->numrows > 0) {
            return;
        }

        $recurso = null;
        $dadosLancamento = \db_utils::fieldsMemory($rsRecursos, 0);
        if ($dadosLancamento->recurso_conta_pagadora != '') {
            $this->salvarRecurso($codigoLancamnento, $dadosLancamento->recurso_conta_pagadora);
            return;
        }

        if ($dadosLancamento->recurso_receita !== '') {
            $this->salvarRecurso($codigoLancamnento, $dadosLancamento->recurso_receita);
            return;
        }

        if ($dadosLancamento->recurso_resto != '') {
            $this->salvarRecurso($codigoLancamnento, $dadosLancamento->recurso_resto);
            return;
        }

        if ($dadosLancamento->recurso_empenho !== '') {
            $this->salvarRecurso($codigoLancamnento, $dadosLancamento->recurso_empenho);
            return;
        }

        if ($dadosLancamento->dotacao_suplementacao != '') {
            $this->salvarRecurso($codigoLancamnento, $dadosLancamento->dotacao_suplementacao);
            return;
        }

        if ($dadosLancamento->abertura_exercicio != '') {
            $this->salvarRecurso($codigoLancamnento, $dadosLancamento->abertura_exercicio);
            return;
        }

        if ($dadosLancamento->recurso_retencao_receita != '') {
            $this->salvarRecurso($codigoLancamnento, $dadosLancamento->recurso_retencao_receita);
            return;
        }

        if ($dadosLancamento->recurso_apropriacao != '') {
            $this->salvarRecurso($codigoLancamnento, $dadosLancamento->recurso_apropriacao);
            return;
        }

        if (!empty($lancamentoAuxiliar)) {
            $reflection = new \ReflectionClass($lancamentoAuxiliar);
            if ($reflection->hasMethod('getRecurso')) {
                $codigoRecurso = $lancamentoAuxiliar->getRecurso();
                if (!empty($codigoRecurso) && $codigoRecurso instanceof \Recurso) {
                    $recurso = $codigoRecurso->getCodigo();
                }
            }
            $this->salvarRecurso($codigoLancamnento, $recurso);
            return;
        }
    }
}
