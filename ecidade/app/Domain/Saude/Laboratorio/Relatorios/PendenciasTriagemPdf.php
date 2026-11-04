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

namespace App\Domain\Saude\Laboratorio\Relatorios;

use ECidade\Pdf\Pdf;

class PendenciasTriagemPdf extends Pdf
{
    protected $dados;
    protected $filtros;
    protected $quebraLaboratorio;
    protected $quebraAmostra;
    protected $totalizadorAmostras;

    public function __construct($dados, $orientation = 'L')
    {
        parent::__construct($orientation);
        $this->dados = $dados;
        $this->quebraLaboratorio = '';
        $this->quebraAmostra = '';
        $this->totalizadorAmostras = 0;
    }

    /**
     * Cabeçalho do relatório
     */
    public function imprimirCabecalho()
    {
        $this->addTitulo("Relatório de Pendências da Triagem");
        $this->addTitulo("Período: " . $this->filtros['dataInicial'] . ' à '. $this->filtros['dataFinal']);

        if (array_key_exists('laboratorios', $this->filtros)) {
            $this->addTitulo("Laboratórios: " .$this->filtros['laboratorios']);
        }

        if (array_key_exists('situacaoAmostra', $this->filtros)) {
            $this->addTitulo("Situação Amostra: " . $this->filtros['situacaoAmostra']);
        } else {
            $this->addTitulo("Situação Amostra: TODOS");
        }

        if (array_key_exists('totalizacaoAmostra', $this->filtros)) {
            $totalizacaoAmostra = $this->filtros['totalizacaoAmostra'] == 't' ? 'Sim' : 'Não';
            $this->addTitulo("Totalização Amostra: " . $totalizacaoAmostra);
        }
    }

    public function setFiltros($filtros)
    {
        $this->filtros = $filtros;
    }

    public function emitir()
    {
        $this->imprimir();

        $fileName = 'tmp/pendenciasTriagem' . time() . '.pdf';
        $this->Output('F', $fileName);

        return [
            "name" => "Relatório Pendências Triagem PDF",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    public function imprimir()
    {
        $this->imprimirCabecalho();
        $this->initPdf();
        $this->ln();
        $this->setFont('Arial', 'b', '10');
        $this->setFont('Arial', '', '8');
        $this->imprimirDados();
    }


    public function initPdf()
    {
        $this->mostrarRodape();
        $this->mostrarTotalDePaginas();
        $this->setAutoPageBreak(false);
        $this->aliasNbPages();
        $this->setFillColor(235);
        $this->exibeHeader();
    }

    public function imprimirColunas()
    {
        $this->setFont('Arial', 'b', '8');
        $this->cell(25, 5, "Requisição", 1, 0, "C", 1);
        $this->cell(110, 5, "Paciente", 1, 0, "C", 1);
        $this->cell(30, 5, "Data Coleta", 1, 0, "C", 1);
        $this->cell(60, 5, "Material Coleta", 1, 0, "C", 1);
        $this->cell(55, 5, "Situação Amostra", 1, 1, "C", 1);
        $this->setFont('Arial', '', '8');
    }

    public function verificaQuebras($dado)
    {
        $this->verificaQuebraPagina($dado);
             
        $this->verificaQuebraLaboratorio($dado);
        if ($this->filtros['totalizacaoAmostra'] == 't') {
            $this->verificaQuebraAmostra($dado);
        }
    }
        
    public function verificaQuebraPagina($dado)
    {
        if ($this->Gety() > $this->getH() - 45) {
            $this->addPage();
            $this->verificaQuebras($dado);
        }
    }
 
    public function verificaQuebraLaboratorio($dado)
    {
        if ($dado['laboratorio'] != $this->quebraLaboratorio) {
            if ($this->totalizadorAmostras > 0) {
                $this->imprimirTotalizadorAmostras();
            }

            /**
             * Segundo a regra de negócio, necessário quebrar página por
             * laboratório.
             */
            
            $this->addPage();
            
            $this->quebraLaboratorio = $dado['laboratorio'];
            $this->imprimirQuebraLaboratorio();
            $this->ln(3);
            if ($this->filtros['totalizacaoAmostra'] == 'f') {
                $this->imprimirColunas();
            }
            $this->totalizadorAmostras = 0;
        }
    }

    public function verificaQuebraAmostra($dado)
    {
        if ($dado['amostra'] != $this->quebraAmostra) {
            if ($this->totalizadorAmostras > 0) {
                $this->imprimirTotalizadorAmostras();
            }

            $this->quebraAmostra = $dado['amostra'];
            $this->imprimirQuebraAmostra();
            $this->ln(3);
            $this->imprimirColunas();
            $this->totalizadorAmostras = 0;
        }
    }

    public function imprimirDados()
    {
        foreach ($this->dados as $dado) {
            $this->verificaQuebras($dado);
                    
            $posXAnterior = $this->getX();
            $posYAnterior = $this->getY();
            $this->setX($posXAnterior + 225);
            $this->multiCell(55, 5, $dado['motivo_rejeicao_amostra'], 1, "C", 0);
            $posYMotivoRejeicao = $this->getY();
            
            /**
             * A altura das demais colunas é calculada em cima do crescimento
             * da coluna motivo da rejeição. Se ela crescer demais até mudar
             * a página, a nova altura deve ser calcula como referencia no
             * valor inicial.
             */
            $this->setXY($posXAnterior, $posYAnterior);
            $heigthColunas = $posYMotivoRejeicao - $posYAnterior;
            $heigthColunas = $heigthColunas < 5 ? 5 : $heigthColunas;
            
            $this->cell(25, $heigthColunas, $dado['requisicao'], 1, 0, "C", 0);
            $this->cell(110, $heigthColunas, $dado['paciente'], 1, 0, "C", 0);
            $this->cell(30, $heigthColunas, $dado['data_coleta'], 1, 0, "C", 0);
            $this->cell(60, $heigthColunas, $dado['amostra'], 1, 0, "C", 0);
            
            $this->ln();
            $this->totalizadorAmostras++;
        }
        /**
         * Imprime o ultimo totalizador
         */
        if (count($this->dados) > 0) {
            $this->imprimirTotalizadorAmostras();
        }
    }

    public function imprimirQuebraLaboratorio()
    {
        $this->setFont('Arial', 'b', '12');
        $this->cell(280, 5, $this->quebraLaboratorio, 0, 1, "L", 0);
        $this->setFont('Arial', '', '12');
    }

    public function imprimirQuebraAmostra()
    {
        $this->setFont('Arial', 'b', '10');
        $this->cell(280, 5, $this->quebraAmostra, 0, 1, "L", 0);
        $this->setFont('Arial', '', '10');
    }

    public function imprimirTotalizadorAmostras()
    {
        $this->setFont('Arial', 'b', '10');
        $this->cell(225, 5, "Total de amostras:", "BR", 0, "R", 1);
        $this->cell(55, 5, $this->totalizadorAmostras, "BR", 1, "C", 1);
        $this->setFont('Arial', '', '8');
        $this->ln(3);
    }
}
