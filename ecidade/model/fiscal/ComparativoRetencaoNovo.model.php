<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2016  DBseller Servicos de Informatica
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

use Illuminate\Database\Capsule\Manager as DB;

include(modification("fpdf151/pdf.php"));

class ComparativoRetencaoNovo
{

    /**
     * Inscrição municipal
     * @var string
     */
    private $sInscricaoMunicipal;

    /**
     * Cgm
     * @var integer
     */
    private $sCgm;

    /**
     * Mês da competência
     * @var string
     */
    private $iCompetenciaMes;

    /**
     * Ano da competência
     * @var string
     */
    private $iCompetenciaAno;


    function __construct($sCgm, $iCompetenciaAno, $iCompetenciaMes, $sInscricaoMunicipal)
    {
        $this->sCgm = $sCgm;
        $this->iCompetenciaAno = $iCompetenciaAno;
        $this->iCompetenciaMes = $iCompetenciaMes;
        $this->sInscricaoMunicipal = $sInscricaoMunicipal;
    }

    public function Pdf()
    {
        $oPdf = new PDFDocument('L');
        $oPdf->Open();
        $oPdf->AliasNbPages();
        $oPdf->setfillcolor(235);
        $oPdf->setfont('arial', 'b', 8);

        $oPdf->addHeaderDescription('Inconsistências de Retenções');
        $oPdf->addHeaderDescription('Filtros Utilizados:');

        if (!empty($this->sCgm)) {
            $oPdf->addHeaderDescription('CGM: ' . $this->sCgm);
        }

        if (!empty($this->sInscricaoMunicipal)) {
            $oPdf->addHeaderDescription('Inscrição Municipal: ' . $this->sInscricaoMunicipal);
        }

        if (!empty($this->iCompetenciaMes)) {
            $oPdf->addHeaderDescription('Mês de Compêtencia: ' . $this->iCompetenciaMes);
        }

        if (!empty($this->iCompetenciaAno)) {
            $oPdf->addHeaderDescription('Ano de Compêtencia: ' . $this->iCompetenciaAno);
        }

        $iAlt = 4;
        $iPag = 0;
        $iClr = 0;

        $oPdf->addpage();
        $oPdf->cell(20, $iAlt, "Nota", 1, 0, "C", 1);
        $oPdf->cell(30, $iAlt, "Declarante", 1, 0, "C", 1);
        $oPdf->cell(30, $iAlt, "CNPJ", 1, 0, "C", 1);
        $oPdf->cell(137, $iAlt, "Razão Social", 1, 0, "C", 1);
        $oPdf->cell(20, $iAlt, "Data", 1, 0, "C", 1);
        $oPdf->cell(20, $iAlt, "Alíquota", 1, 0, "C", 1);
        $oPdf->cell(20, $iAlt, "Valor Base", 1, 0, "C", 1);
        $oPdf->ln();

        $oPdf->setfont('arial', null, 6);

        $dados = $this->dados($this->sCgm, $this->iCompetenciaAno, $this->iCompetenciaMes, $this->sInscricaoMunicipal);

        foreach ($dados as $oNota) {

            $sPrestadorDocumento = db_formatar($oNota->prestador_cnpj, "cpf");
            if (strlen($oNota->prestador_cnpj) > 11) {
                $sPrestadorDocumento = db_formatar($oNota->prestador_cnpj, "cnpj");
            }

            $sTomadorDocumento = db_formatar($oNota->tomador_cnpj, "cpf");
            if (strlen($oNota->tomador_cnpj) > 11) {
                $sTomadorDocumento = db_formatar($oNota->tomador_cnpj, "cnpj");
            }

            $iPushW = 0;
            $iX = $oPdf->GetX();
            $iY = $oPdf->GetY();

            $oPdf->MultiCell($iW = 20, ($iAlt * 2), $oNota->nota_numero, 1, "C", $iClr);

            $oPdf->SetXY($iX + ($iPushW += $iW), $iY);
            $oPdf->MultiCell($iW = 30, $iAlt, "Prestador", 1, "C", $iClr);

            $oPdf->SetXY($iX + $iPushW, $iY + 4);
            $oPdf->MultiCell($iW = 30, $iAlt, "Tomador", 1, "C", $iClr);

            $oPdf->SetXY($iX + ($iPushW += $iW), $iY);
            $oPdf->MultiCell($iW = 30, $iAlt, $sPrestadorDocumento, 1, "C", $iClr);

            $oPdf->SetXY($iX + $iPushW, $iY + 4);
            $oPdf->MultiCell($iW = 30, $iAlt, $sTomadorDocumento, 1, "C", $iClr);

            $oPdf->SetXY($iX + ($iPushW += $iW), $iY);
            $oPdf->MultiCell($iW = 137, $iAlt, utf8_decode($oNota->prestador_razao_social), 1, "L", $iClr);

            $oPdf->SetXY($iX + $iPushW, $iY + 4);
            $oPdf->MultiCell($iW = 137, $iAlt, utf8_decode($oNota->tomador_razao_social), 1, "L", $iClr);

            if (
                (empty(trim($oNota->prestador_data)) and empty($oNota->prestador_aliquota) and empty($oNota->prestador_valor_base))
                and (!empty(trim($oNota->tomador_data)) and !empty($oNota->tomador_aliquota) and !empty($oNota->tomador_valor_base))
            ) {

                $oPdf->SetXY($iX + ($iPushW += $iW), $iY);
                $oPdf->MultiCell($iW = 60, $iAlt, "Não declarado", 1, "C", $iClr);

                $oPdf->SetXY($iX + $iPushW, $iY + 4);

                $sTomadorData = $oNota->tomador_data;

                if (!empty(trim($sTomadorData))) {

                    $oTomadorData = new DBDate((string)$sTomadorData);
                    $sTomadorData = $oTomadorData->getDate(DBDate::DATA_PTBR);
                }

                $oPdf->MultiCell($iW = 20, $iAlt, $sTomadorData, 1, "C", $iClr);

                $oPdf->SetXY($iX + ($iPushW += $iW), $iY + 4);
                $oPdf->MultiCell($iW = 20, $iAlt, db_formatar((string)$oNota->tomador_aliquota, "f"), 1, "R", $iClr);

                $oPdf->SetXY($iX + ($iPushW += $iW), $iY + 4);
                $oPdf->MultiCell($iW = 20, $iAlt, db_formatar((string)$oNota->tomador_valor_base, "f"), 1, "R", $iClr);

            } elseif (
                (!empty(trim($oNota->prestador_data)) and !empty($oNota->prestador_aliquota) and !empty($oNota->prestador_valor_base))
                and (empty(trim($oNota->tomador_data)) and empty($oNota->tomador_aliquota) and empty($oNota->tomador_valor_base))
            ) {

                $oPdf->SetXY($iX + ($iPushW += $iW), $iY);
                $iPushWAux = $iPushW;

                $sPrestadorData = $oNota->prestador_data;

                if (!empty(trim($sPrestadorData))) {

                    $oPrestadorData = new DBDate((string)$sPrestadorData);
                    $sPrestadorData = $oPrestadorData->getDate(DBDate::DATA_PTBR);
                }

                $oPdf->MultiCell($iW = 20, $iAlt, $sPrestadorData, 1, "C", $iClr);

                $oPdf->SetXY($iX + ($iPushWAux += $iW), $iY);
                $oPdf->MultiCell($iW = 20, $iAlt, db_formatar((string)$oNota->prestador_aliquota, "f"), 1, "R", $iClr);

                $oPdf->SetXY($iX + ($iPushWAux += $iW), $iY);
                $oPdf->MultiCell($iW = 20, $iAlt, db_formatar((string)$oNota->prestador_valor_base, "f"), 1, "R", $iClr);

                $oPdf->SetXY($iX + $iPushW, $iY + 4);
                $oPdf->MultiCell($iW = 60, $iAlt, "Não declarado", 1, "C", $iClr);

            } else {

                $sPrestadorData = $oNota->prestador_data;
                $sTomadorData = $oNota->tomador_data;

                if (!empty(trim($sPrestadorData))) {

                    $oPrestadorData = new DBDate((string)$sPrestadorData);
                    $sPrestadorData = $oPrestadorData->getDate(DBDate::DATA_PTBR);
                }

                if (!empty(trim($sTomadorData))) {

                    $oTomadorData = new DBDate((string)$sTomadorData);
                    $sTomadorData = $oTomadorData->getDate(DBDate::DATA_PTBR);
                }

                $oPdf->SetXY($iX + ($iPushW += $iW), $iY);
                $oPdf->MultiCell($iW = 20, $iAlt, $sPrestadorData, 1, "C", $iClr);

                $oPdf->SetXY($iX + $iPushW, $iY + 4);
                $oPdf->MultiCell($iW = 20, $iAlt, $sTomadorData, 1, "C", $iClr);

                $oPdf->SetXY($iX + ($iPushW += $iW), $iY);
                $oPdf->MultiCell($iW = 20, $iAlt, db_formatar((string)$oNota->prestador_aliquota, "f"), 1, "R", $iClr);

                $oPdf->SetXY($iX + $iPushW, $iY + 4);
                $oPdf->MultiCell($iW = 20, $iAlt, db_formatar((string)$oNota->tomador_aliquota, "f"), 1, "R", $iClr);

                $oPdf->SetXY($iX + ($iPushW += $iW), $iY);
                $oPdf->MultiCell($iW = 20, $iAlt, db_formatar((string)$oNota->prestador_valor_base, "f"), 1, "R", $iClr);

                $oPdf->SetXY($iX + $iPushW, $iY + 4);
                $oPdf->MultiCell($iW = 20, $iAlt, db_formatar((string)$oNota->tomador_valor_base, "f"), 1, "R", $iClr);
            }

            $oPdf->ln();

            if ($iClr == 0) {
                $iClr = 1;
            } else {
                $iClr = 0;
            }

            $iPag++;
            if ($iPag == 12) {

                $iPag = 0;
                $oPdf->addpage();
            }
        }

        $sPdfPathFile = 'tmp/comparativoretencao-' . time() . '.pdf';
        $oPdf->Output($sPdfPathFile, false, true);

        return $sPdfPathFile;
    }

    /**
     * Retorna a inscrição municipal
     * @return string
     */
    public function getInscricaoMunicipal()
    {
        return $this->sInscricaoMunicipal;
    }

    /**
     * Retorna o CGM
     * @return string
     */
    public function getCgm()
    {
        return $this->sCgm;
    }

    /**
     * Retorna o mês da compêtencia
     * @return string
     */
    public function getCompetenciaMes()
    {
        return $this->iCompetenciaMes;
    }

    /**
     * Retorna o ano da compêtencia
     * @return string
     */
    public function getCompetenciaAno()
    {
        return $this->iCompetenciaAno;
    }

    /**
     * Altera a inscrição municipal
     * @param string $sInscricaoMunicipal
     */
    public function setInscricaoMunicipal($sInscricaoMunicipal)
    {
        $this->sInscricaoMunicipal = $sInscricaoMunicipal;
    }

    /**
     * Altera o Cgm
     * @param integer $sCgm
     */
    public function setCgm($sCgm)
    {
        $this->sCgm = $sCgm;
    }

    /**
     * Altera o mês da compêtencia
     * @param integer $iCompetenciaMes
     */
    public function setCompetenciaMes($iCompetenciaMes)
    {
        $this->iCompetenciaMes = $iCompetenciaMes;
    }

    /**
     * Altera o ano da compêtencia
     * @param integer $iCompetenciaAno
     */
    public function setCompetenciaAno($iCompetenciaAno)
    {
        $this->iCompetenciaAno = $iCompetenciaAno;
    }

    public function dados($sCgm, $iCompetenciaAno, $iCompetenciaMes, $sInscricaoMunicipal)
    {
        $aWhere = [];
        $sWhere = '';

        if (!empty($sCgm)) {
            $aWhere[] = "z01_numcgm = {$sCgm}";
        }

        if (!empty($sInscricaoMunicipal)) {
            $aWhere[] = "(nfse.usuarios_contribuintes.im = {$sInscricaoMunicipal} or usuarios_contribuintes_dms.im = {$sInscricaoMunicipal})";
        }

        if (!empty($iCompetenciaMes) and !empty($iCompetenciaAno)) {
            $aWhere[] = "nfse.notas.ano_comp = {$iCompetenciaAno} and nfse.notas.mes_comp = {$iCompetenciaMes}";
        }

        if ($aWhere) {
            $sWhere = implode(" and ", $aWhere);
        }

        $dados = DB::select("
            select
            distinct 
                nota_numero,
                prestador_cnpj,
                prestador_razao_social,
                inscricao,
                prestador_data,
                prestador_aliquota,
                prestador_valor_base,
                prestador_categoria,
                tomador_cnpj,
                tomador_razao_social,
                tomador_data,
                tomador_aliquota,
                tomador_valor_base
        from
            (
            select
                distinct nfse.notas.nota as nota_numero,
                nfse.notas.p_cnpjcpf as prestador_cnpj,
                nfse.notas.p_razao_social as prestador_razao_social,
                nfse.notas.p_im as inscricao,
                nfse.notas.dt_servico as prestador_data,
                nfse.notas_servicos.vl_aliquota as prestador_aliquota,
                nfse.notas.s_vl_bc as prestador_valor_base,
                case
                    when nfse.notas.p_categoria_simples_nacional = 0 then 'nao_optante'
                    when nfse.notas.p_categoria_simples_nacional = 1 then 'Simples_me'
                    when nfse.notas.p_categoria_simples_nacional = 2 then 'simples_epp'
                    when nfse.notas.p_categoria_simples_nacional = 3 then 'simples_mei'
                    when nfse.notas.p_categoria_simples_nacional = 4 then 'nao_optante_fixado'
                    when nfse.notas.p_categoria_simples_nacional = 5 then 'simples_optante'
                    else 'nao_optante'
                end as prestador_categoria,
                nfse.notas.t_cnpjcpf as tomador_cnpj,
                nfse.notas.t_razao_social as tomador_razao_social,
                null as tomador_data,
                null as tomador_aliquota,
                null as tomador_valor_base
            from
                cgm
            inner join nfse.usuarios_contribuintes on
                z01_cgccpf = cnpj_cpf
            inner join nfse.usuarios on nfse.usuarios.id = nfse.usuarios_contribuintes.id_usuario
            inner join nfse.notas on nfse.usuarios_contribuintes.id = nfse.notas.id_contribuinte
            inner join nfse.notas_servicos on nfse.notas.id = nfse.notas_servicos.id_nota
            left join nfse.dms_nota on nfse.dms_nota.nota = nfse.notas.nota
                and nfse.dms_nota.p_cnpjcpf = nfse.notas.p_cnpjcpf
            left join nfse.dms_nota_servicos on nfse.dms_nota.id = nfse.dms_nota_servicos.id_dms_nota
            left join nfse.usuarios_contribuintes usuarios_contribuintes_dms on
                usuarios_contribuintes_dms.cnpj_cpf = nfse.notas.t_cnpjcpf
            where
                {$sWhere}
                and nfse.notas.s_dados_iss_retido = 2
                and nfse.notas.natureza_operacao = 1
                and nfse.notas.cancelada = false
                and nfse.notas.nota is not null
                and nfse.dms_nota.nota is null
                and nfse.dms_nota.p_cnpjcpf is null
                and nfse.notas.t_cnpjcpf not in (select cnpj from nfse.parametrosprefeitura)
            union all 
            select
                distinct nfse.notas.nota as nota_numero,
                nfse.notas.p_cnpjcpf as prestador_cnpj,
                nfse.notas.p_razao_social as prestador_razao_social,
                nfse.notas.p_im as inscricao,
                nfse.notas.dt_servico as prestador_data,
                nfse.notas_servicos.vl_aliquota as prestador_aliquota,
                nfse.notas.s_vl_bc as prestador_valor_base,
                case
                    when nfse.notas.p_categoria_simples_nacional = 0 then 'nao_optante'
                    when nfse.notas.p_categoria_simples_nacional = 1 then 'Simples_me'
                    when nfse.notas.p_categoria_simples_nacional = 2 then 'simples_epp'
                    when nfse.notas.p_categoria_simples_nacional = 3 then 'simples_mei'
                    when nfse.notas.p_categoria_simples_nacional = 4 then 'nao_optante_fixado'
                    when nfse.notas.p_categoria_simples_nacional = 5 then 'simples_optante'
                    else 'nao_optante'
                end as prestador_categoria,
                nfse.notas.t_cnpjcpf as tomador_cnpj,
                nfse.notas.t_razao_social as tomador_razao_social,
                null as tomador_data,
                null as tomador_aliquota,
                null as tomador_valor_base
            from
                cgm
            inner join nfse.usuarios_contribuintes on z01_cgccpf = cnpj_cpf
            inner join nfse.usuarios on nfse.usuarios.id = nfse.usuarios_contribuintes.id_usuario
            inner join nfse.notas on nfse.notas.t_cnpjcpf = nfse.usuarios_contribuintes.cnpj_cpf
            inner join nfse.notas_servicos on
                nfse.notas.id = nfse.notas_servicos.id_nota
            left join nfse.dms_nota on nfse.dms_nota.nota = nfse.notas.nota
                and nfse.dms_nota.p_cnpjcpf = nfse.notas.p_cnpjcpf
            left join nfse.dms_nota_servicos on nfse.dms_nota.id = nfse.dms_nota_servicos.id_dms_nota
                left join nfse.usuarios_contribuintes usuarios_contribuintes_dms on
                usuarios_contribuintes_dms.cnpj_cpf = nfse.notas.t_cnpjcpf
            where
                {$sWhere}
                and nfse.notas.s_dados_iss_retido = 2
                and nfse.notas.natureza_operacao = 1
                and nfse.notas.cancelada = false
                and nfse.notas.nota is not null
                and nfse.dms_nota.nota is null
                and nfse.dms_nota.p_cnpjcpf is null
                and nfse.notas.t_cnpjcpf not in (select cnpj from nfse.parametrosprefeitura)
            ) as x

        ");
        return $dados;
    }
}
