<?php
namespace ECidade\Tributario\Juridico\ProcessoEletronico\Documento;

/**
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

/**
 * Classe em processo de refatoração
 * @todo  remover arrobas
 * @todo  adicionar tratamento aos recordsets
 */
class DocumentoInicial extends \pdf3
{

    const IMPRESSAO_INICIAL = 1;
    const IMPRESSAO_CDA = 2;

    const IMPRESSAO_CITACAO = 3;

    private $tipoImpressao = 1;

    public function __construct()
    {
        parent::__construct();
    }

    public function headerCertidao()
    {

        $sql = "select nomeinst,
                    bairro,
                    cgc,
                    trim(ender)||','||trim(cast(numero as text)) as ender,
                    upper(munic) as munic,
                    uf,
                    telef,
                    email,
                    url,
                    logo,
                    db12_extenso
               from db_config
                    inner join db_uf on db12_uf = uf
              where codigo = " . db_getsession("DB_instit");

        $result = db_query($sql);

        global $nomeinst;
        global $ender;
        global $munic;
        global $cgc;
        global $bairro;
        global $uf;
        global $db12_extenso;
        global $logo;
        global $lImpFolha;

        db_fieldsmemory($result, 0);
        $db12_extenso = pg_result($result, 0, "db12_extenso");

        $S = $this->lMargin;
        $this->SetLeftMargin(10);
        $Letra = 'Times';
        $posini = 20;

        $this->Image('imagens/files/' . $logo, $posini, 8, 24);
        $this->Ln(5);
        $this->SetFont($Letra, '', 10);
        $this->MultiCell(0, 4, $db12_extenso, 0, "C", 0);
        $this->SetFont($Letra, 'B', 13);
        $this->MultiCell(0, 6, $nomeinst, 0, "C", 0);
        $this->SetFont($Letra, 'B', 12);
        $this->MultiCell(0, 4, @$GLOBALS["head1"], 0, "C", 0);
        $this->SetLeftMargin($S);
        $this->Ln(1);

        $comprim = ($this->w - $this->rMargin - $this->lMargin);
        $Espaco = $this->w - 80;

        if ($lImpFolha) {
            $this->SetFont('Arial', '', 7);
            $this->SetFillColor(240);
            $this->RoundedRect(160, 10, 40, 20, 2, "FD", '1234');
            $this->setfont('arial', '', 8);
            $this->Text(163, 15, "Livro");
            $this->Text(172, 15, ":" . @$GLOBALS["head2"]);
            $this->Text(163, 18, "Folha");
            $this->Text(172, 18, ":" . @$GLOBALS["head3"]);
            $this->Text(163, 21, "Data do Livro");
            $this->Text(180, 21, ":" . db_formatar(@$GLOBALS["head4"], "d"));
        }

        $this->SetY(35);
        $this->setfont('arial', 'b', 11);
        $this->multicell(0, 4, "CERTIDÃO DE DÍVIDA ATIVA N" . CHR(176) . " " . $GLOBALS["head5"], 0, "C", 0, 0);
        $this->setfont('arial', '', 11);
        $this->ln(3);
    }

    private function headerCitacao()
    {

        $S = $this->lMargin;
        $Letra = 'Times';
        $posini = 20;

        $sEndereco = 'Rua Visconde de Sepetiba, 519 7º andar CEP 24020-206 - Centro - Niterói - RJ';
        $this->Image('imagens/logo_pjerj.jpg', 10, 6, 24);
        $this->SetLeftMargin(33);
        $this->SetFont($Letra, 'B', 10);
        $this->Cell(0, 4, 'Estado do Rio de Janeiro', 0, 1);
        $this->SetFont($Letra, '', 10);
        $this->Cell(0, 4, 'Poder Judiciário', 0, 1);
        $this->Cell(0, 4, 'Comarca de Niterói', 0, 1);
        $this->Cell(0, 4, 'Central da Dívida Ativa', 0, 1);
        $this->MultiCell(150, 4, $sEndereco, 0, 'L');
        $this->SetLeftMargin($S);
        $this->Ln(1);

        $comprim = ($this->w - $this->rMargin - $this->lMargin);
        $Espaco = $this->w - 80;
    }


    public function header()
    {
        if ($this->tipoImpressao == self::IMPRESSAO_INICIAL) {
            parent::Header();
        }
        if ($this->tipoImpressao == self::IMPRESSAO_CDA) {
            $this->headerCertidao();
        }
        if ($this->tipoImpressao == self::IMPRESSAO_CITACAO) {
            $this->headerCitacao();
        }
    }

    /**
     * @return int
     */
    public function getTipoImpressao()
    {
        return $this->tipoImpressao;
    }

    /**
     * @param int $tipoImpressao
     */
    public function setTipoImpressao($tipoImpressao)
    {
        $this->tipoImpressao = $tipoImpressao;
    }
}
