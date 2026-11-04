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

require_once(modification("interfaces/iPadArquivoTxtBase.interface.php"));
require_once(modification("model/contabilidade/arquivos/sigfis/SigfisArquivoBase.model.php"));

/**
 *
 * Retorna os dados do Recurso para o Sigfis
 * @package contabilidade
 * @subpackage sigfis
 * @author Iuri Guntchnigg
 */
class SigfisArquivoFonteRecurso extends SigfisArquivoBase implements iPadArquivoTXTBase
{
    protected $iCodigoLayout = 118;
    protected $sNomeArquivo = 'Fonte';
    /**
     * Retorna os dados dos Recursos
     * @return array
     */
    public function gerarDados()
    {

        $oDaoOrctiporec = db_utils::getDao('orctiporec');
        $dataSessao = date('Y-m-d', db_getsession('DB_datausu'));
        $where = "exercicio = " . db_getsession('DB_anousu');
        $where .= " and (
            o15_datalimite is null
            or o15_datalimite >= '{$dataSessao}'
        )";

        $sSqlRecursos = $oDaoOrctiporec->sqlNovaFonteRecurso(null, "distinct o15_codtri,gestao, o15_recurso,descricao, codigo_siconfi", "", $where);
        $rsDadosRecurso = $oDaoOrctiporec->sql_record($sSqlRecursos);
        $this->addLog("==== Iniciando Processamento Arquivo {$this->getNomeArquivo()}\n");
        $iAnoUsu = db_getsession("DB_anousu");

        if (empty($this->sCodigoTribunal)) {
            throw new Exception("O código do tribunal deve ser informado para geração do arquivo");
        }

        for ($iRecurso = 0; $iRecurso < $oDaoOrctiporec->numrows; $iRecurso++) {

            $oDadosRecurso = db_utils::fieldsMemory($rsDadosRecurso, $iRecurso);
            if (trim($oDadosRecurso->o15_codtri) == "") {

                $sErroLog = "Recurso {$oDadosRecurso->o15_codtri} - {$oDadosRecurso->descricao} ";
                $sErroLog .= "sem informação no campo 'Código Tribunal'.\n";
                $this->addLog($sErroLog);
            }
            if (trim($oDadosRecurso->descricao) == "") {

                $sErroLog = "Recurso {$oDadosRecurso->o15_codtri} - {$oDadosRecurso->descricao} ";
                $sErroLog .= "sem descrição informada.\n";
                $this->addLog($sErroLog);
            }
            $oRecurso = new stdClass();
            $oRecurso->codigolinha = 405;
            $oRecurso->cd_Unidade = str_pad($this->sCodigoTribunal, 4, " ", STR_PAD_LEFT);
            if ($iAnoUsu < 2019) {
                $oRecurso->cd_Fonte = str_pad(str_replace(".", "", $oDadosRecurso->o15_codtri), 4, " ", STR_PAD_LEFT);
                $oRecurso->cd_FonteGestor = str_pad($oDadosRecurso->o15_codigo, 4, " ", STR_PAD_LEFT);
            } else {
                $oRecurso->cd_Fonte = str_pad(' ', 4, " ", STR_PAD_LEFT);
                $oRecurso->cd_FonteGestor = str_pad(' ', 4, " ", STR_PAD_LEFT);
            }

            $oRecurso->de_Fonte = str_pad(substr($oDadosRecurso->descricao, 0, 80), 80, " ", STR_PAD_RIGHT);
            $oRecurso->dt_Ano = $iAnoUsu;
            if ($iAnoUsu > 2018) {
                $oRecurso->Nm_FonteRecurso = str_pad($oDadosRecurso->o15_codtri, 8, " ", STR_PAD_RIGHT);
                $oRecurso->Nm_FonteRecursoGestor = str_pad($oDadosRecurso->codigo_siconfi, 8, " ", STR_PAD_RIGHT);
            } else {
                $oRecurso->Nm_FonteRecurso = str_pad(" ", 8, " ", STR_PAD_LEFT);
                $oRecurso->Nm_FonteRecursoGestor = str_pad(" ", 8, " ", STR_PAD_LEFT);
            }
            $this->aDados[] = $oRecurso;
            unset($oDadosRecurso);

        }
        $this->addLog("==== Fim do Arquivo {$this->getNomeArquivo()}\n");
        return $this->aDados;
    }
}
