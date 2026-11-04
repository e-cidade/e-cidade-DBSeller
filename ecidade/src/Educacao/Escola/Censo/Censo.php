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

namespace ECidade\Educacao\Escola\Censo;

use DBDate;
use ECidade\Educacao\Escola\Censo\Identificacao\DadosExportacao as DadosExportacaoIdentificacao;
use ECidade\Educacao\Escola\Censo\Log\LogMatriculaInicial;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Layout\Layout;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\DadosExportacaoFactory;
use ECidade\Educacao\Escola\Censo\Registry\LogCensoRegistry;
use Escola;
use Exception;

class Censo
{

    /**
     * Data base do censo
     * @var DBDate
     */
    private $oDataBase = null;

    /**
     * @var Escola
     */
    private $escola;

    private $arquivoCenso;
    private $arquivoLog;

    public function __construct($iAno = null)
    {
        if (is_null($iAno)) {
            $iAno = date('Y');
        }

        $this->oDataBase = self::calcularDataCenso($iAno);
    }

    public static function calcularDataCenso($iAno)
    {
        if ($iAno == 2020) {
            return new DBDate("11/03/2020");
        }

        for ($dia = 31; $dia > 0; $dia--) {
            if (date("w", mktime(0, 0, 0, 5, $dia, $iAno)) == 3) {
                $iDia = str_pad($dia, 2, '0', STR_PAD_LEFT);
                return new DBDate("{$iDia}/05/{$iAno}");
            }
        }
    }

    /**
     * Retorna a data base do censo para o ano
     * Data base do censo é a última quarta feira do mês de maio
     * @return DBDate
     */
    public function getDataCenso()
    {
        return $this->oDataBase;
    }

    public function getAno()
    {
        return $this->oDataBase->getAno();
    }

    /**
     * @param Escola $escola
     */
    public function setEscola(Escola $escola)
    {
        $this->escola = $escola;
    }

    /**
     * @throws Exception
     */
    public function exportarMatriculaInicial()
    {
        LogCensoRegistry::set(LogCensoRegistry::MATRICULA_INICIAL, new LogMatriculaInicial());
        $exportacao = DadosExportacaoFactory::factory($this->getAno());
        $exportacao->setCenso($this);
        $exportacao->setEscola($this->escola);
        $processou = $exportacao->processar();
        $layout = new Layout();
        $layout->setRegistro00($exportacao->getRegistro00()->getRegistro());
        $layout->setRegistro10($exportacao->getRegistro10()->getRegistro());
        $layout->setRegistros20($exportacao->getRegistro20()->getRegistros());
        $layout->setRegistros30($exportacao->getRegistro30()->getRegistros());
        $layout->setRegistros40($exportacao->getRegistro40()->getRegistros());
        $layout->setRegistros50($exportacao->getRegistro50()->getRegistros());
        $layout->setRegistros60($exportacao->getRegistro60()->getRegistros());

        $this->arquivoCenso = $layout->gerarArquivo();
        if (!$processou) {
            $log = LogCensoRegistry::get(LogCensoRegistry::MATRICULA_INICIAL);
            if ($log->exist()) {
                $this->arquivoLog = $log->write();
            }

            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getArquivoCenso()
    {
        return $this->arquivoCenso;
    }

    /**
     * @return mixed
     */
    public function getArquivoLog()
    {
        return $this->arquivoLog;
    }

    /**
     * @param Escola[] $escolas
     * @throws Exception
     * @return string
     */
    public function exportarArquivoIdentificacao(array $escolas, $dataCensoPersonalizada = null)
    {
        $dadosExportacao = new DadosExportacaoIdentificacao();
        $dadosExportacao->setCenso($this);
        $possuiDataPersonalizada = (!is_null($dataCensoPersonalizada)
                                    && $this->getDataCenso()->getDate(DBDate::DATA_PTBR) != $dataCensoPersonalizada);
        if ($possuiDataPersonalizada) {
            $dadosExportacao->setDataCensoPersonalizada(new DBDate($dataCensoPersonalizada));
        }
        $dadosExportacao->setEscolas($escolas);
        $dadosExportacao->processar();
        $serviceExportacao = $dadosExportacao->getService();
        if ($possuiDataPersonalizada) {
            $serviceExportacao->setDataCensoPersonalizada(new DBDate($dataCensoPersonalizada));
        }

        $layout = new Identificacao\Layout\Layout();
        $layout->setPessoas($serviceExportacao->getPessoas());
        return $layout->gerarArquivo();
    }
}
