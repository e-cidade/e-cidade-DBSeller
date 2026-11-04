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

namespace App\Domain\Educacao\MatriculaOnline\Relatorios;

use ECidade\File\Csv\Dumper\Dumper;

class RelatorioDemandaReprimida extends Dumper
{
    protected $dados = [];

    protected $cabecalho = [
        'protocolo' => 'PROTOCOLO',
        'data_inscricao' => 'DATA INSCRICAO',
        'data_nascimento' => 'DATA NASCIMENTO',
        'situacao_inscricao' => 'SITUACAO DA INSCRICAO'
    ];

    public function __construct($dados)
    {
        $this->dados = $dados;
    }

    public function emitir()
    {
        $this->setCsvControl(";", '"');
        $fileName = 'tmp/demanda-reprimida' . time() . '.csv';
        $this->dumpToFile($this->organizaDados(), $fileName);
        return [
            "name" => "Relatório Demanda Reprimida CSV",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    private function organizaDados()
    {
        $dadosImprimir = [];
        $dadosImprimir[] = $this->cabecalho;

        foreach ($this->dados as $dado) {
            $linha = [
                $dado->protocolo,
                $dado->data_inscricao,
                $dado->data_nascimento,
                utf8_encode($dado->situacao)
            ];
            $dadosImprimir[] = $linha;
        }
        return $dadosImprimir;
    }
}
