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

namespace App\Domain\Educacao\Escola\Controllers\Relatorios;

use DBDate;
use ECidade\File\Csv\Dumper\Dumper;
use ParameterException;

class InscritosTransposicaoCSV extends Dumper
{
    private $inscritos = [];

    public function __construct(array $inscritos)
    {
        $this->setCsvControl(';', '"');
        $this->inscritos = $inscritos;
    }

    public function emitir()
    {
        $filename = sprintf('tmp/relatrio-inscritos-%s.csv', time());
        $this->dumpToFile($this->organizarDados(), $filename);
        return [
            'name' => 'Relatório de Inscritos CSV',
            'path' => $filename,
            'url' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    /**
     * @throws ParameterException
     */
    private function organizarDados()
    {
        $dadosImprimir = [$this->cabecalho()];

        $dataAtual = new DBDate(date("Y-m-d"));
        foreach ($this->inscritos as $inscrito) {
            if (is_object($inscrito)) {
                $dateInterval = DBDate::getIntervaloEntreDatas($dataAtual, $inscrito->aluno_datanascimento);
                $idadeCompleta  = DBDate::getIdadeCompleta($dateInterval->days);
                $dadosImprimir[] = [
                    $inscrito->escola_nome,
                    $inscrito->etapa,
                    $inscrito->opcao,
                    $inscrito->aluno_codigo,
                    $inscrito->aluno_nome,
                    $inscrito->candidatoOpcoes->escolaOrigem->getNome(),
                    $idadeCompleta,
                    $inscrito->aluno_necessidadeespecial ? 'SIM' : 'NÃO',
                    $inscrito->servidor ? 'SIM' : 'NÃO',
                    $inscrito->irmao ? 'SIM' : 'NÃO',
                    $inscrito->bolsafamilia ? 'SIM' : 'NÃO',
                    $inscrito->designado ? 'SIM' : 'NÃO',
                    $inscrito->designado ? $inscrito->pontuacao : '-'
                ];
            }
        }

        return $dadosImprimir;
    }

    private function cabecalho()
    {
        return [
            'Escola Destino',
            'Etapa',
            'Opção',
            'Código',
            'Nome',
            'Escola Origem',
            'Idade',
            'Deficiente',
            'Servidor',
            'Irmão',
            'Bolsa Família',
            'Designado',
            'Pontuação'
        ];
    }
}
