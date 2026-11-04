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

namespace App\Domain\Saude\Laboratorio\Resources;

use JSON;
use Illuminate\Support\Carbon;

class TriagemLaboratorialResource
{
    public function toArray($dados)
    {
        $triagens = [];
        foreach ($dados['triagens'] as $triagem) {
            $triagens[] = $this->condicionarTriagem($triagem, $dados['motivosRejeicaoAmostra']);
        }
        return $triagens;
    }

    public function condicionarTriagem($triagem, $motivosRejeicaoAmostra)
    {
        return [
            'codigo' => $triagem['codigo'],
            'laboratorio' => $triagem['laboratorio'],
            'amostra' => $triagem['amostra'],
            'coleta' => $triagem['coleta'],
            'hora' => $triagem['hora'],
            'exames' => JSON::create()->parse($triagem['exames']),
            'motivosRejeicaoAmostra' =>$motivosRejeicaoAmostra
        ];
    }

    public function condicionarBuscaRequisicoes($requisicoes)
    {
        $requisicoesCondicionadas = [];
        foreach ($requisicoes as $requisicao) {
            $requisicoesCondicionadas[] = [
                'codigoRequisicao' => $requisicao['codigo_requisicao'],
                'laboratorio' => $requisicao['laboratorio'],
                'paciente' => $requisicao['nome_paciente'],
                'idadePaciente' => $this->processaDataNascimento($requisicao['data_nascimento_paciente']),
                'dataRequisicao' => $this->formatData($requisicao['data_requisicao']),
                'exames' => JSON::create()->parse($requisicao['exames'])
            ];
        }
        return $requisicoesCondicionadas;
    }

    private function processaDataNascimento($dataNascimento)
    {
        $idade = $this->calculaIdadeDetalhada($dataNascimento);
        return $idade;
    }
    
    private function calculaIdadeDetalhada($dataNascimento)
    {
        $dataNascimento = new \DateTime($dataNascimento);
        $hoje = new \DateTime();
    
        $intervalo = $dataNascimento->diff($hoje);
    
        $anos = $intervalo->y;
        $meses = $intervalo->m;
        $dias = $intervalo->d;
    
        return "{$anos} Anos, {$meses} Meses e {$dias} Dias";
    }

    private function formatData($data)
    {
        return Carbon::createFromFormat('Y-m-d', $data)->format('d/m/Y');
    }
}
