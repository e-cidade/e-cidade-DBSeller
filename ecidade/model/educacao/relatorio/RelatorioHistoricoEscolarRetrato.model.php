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

use \ECidade\Pdf\Pdf;

/**
 * Renderiza o relatório de acordo com os parametros
 *
 * @package educacao
 * @subpackage relatorio
 * @author andrio.costa <andrio.costa@dbseller.com.br>
 * @version $Revision: 1.42 $
 */

class RelatorioHistoricoEscolarRetrato extends RelatorioHistoricoEscolar
{

    const NUMERO_ETAPAS_PAGINA = 9;
    const LARGURA_DISCIPLINA = 60;
    const ALTURA_LINHA = 4;
    const COR_PREENCHIMENTO = 255;
    const DISPOSICAO_CABECALHO_1 = 1;
    const DISPOSICAO_CABECALHO_2 = 2;
    const MAXIMO_LINHAS_ATOS_LEGAIS = 4;
    const LARGURA_ASSINATURA = 93;
    const LARGURA_TOTAL = 203;
    const NACIONALIDADE_NASCIDO_EXTERIOR_OU_NATURALIZADO = 2;

    private $sPrefixoTitulo = "HISTÓRICO ESCOLAR";
    private $sTituloRelatorio = "HISTÓRICO ESCOLAR";

    private static $aLarguraColunaEtapa = array(
        'etapa' => 21,
        'ano' => 8,
        'periodo' => 15,
        'dias' => 8,
        'turma' => 19,
        'carga_horaria' => 8,
        'percentual_frequencia' => 11,
        'resultado' => 14,
        'escola' => 58,
        'cidade' => 36,
        'uf' => 5
    );

    private static $aLabelColunaEtapa = array(
        'etapa' => 'ETAPA',
        'ano' => 'ANO',
        'periodo' => 'PERIODO',
        'dias' => 'DIAS',
        'turma' => 'TURMA',
        'carga_horaria' => 'C.H.',
        'percentual_frequencia' => '% FREQ',
        'resultado' => 'RESULTADO',
        'escola' => 'ESCOLA',
        'cidade' => 'CIDADE/DISTRITO',
        'uf' => 'UF'
    );

    /**
     * Instancia da Biblioteca FPDF
     * @var Pdf
     */
    protected $oPdf;

    /**
     * Disposição do cabeçalho do relatorio
     * @var integer
     */
    private $iDisposicao = null;

    /**
     * Controla se deve ou NÃO exibir somente as etapas cursadas ou todas do curso
     * @var boolean
     */
    private $lExibirTodasEtapasCurso = false;

    /**
     * Resultados de aprovação em uma etapa
     */
    private $resultadosAprovacao = ['A', 'P'];

    /**
     * Salva o estado original das etapas por ensino
     */
    private $estadoOriginalTodasEtapas = null;

    /**
     * Construtor da classe
     * @param Pdf $oPdf
     * @param Aluno $oAluno
     * @param Escola $oEscola
     * @param integer $iTipoRelatorio
     * @param boolean $lExibirReclassificacao
     */
    public function __construct(Pdf $oPdf, Aluno $oAluno, Escola $oEscola, $iTipoRelatorio, $lExibirReclassificacao)
    {
        parent::__construct($oAluno, $oEscola, $iTipoRelatorio, $lExibirReclassificacao);
        $this->oPdf = $oPdf;
        $this->oPdf->AddPage();
        $this->oPdf->setfillcolor(223);

        if (!$this->oParametros->exibirperiodo) {
            RelatorioHistoricoEscolarRetrato::$aLarguraColunaEtapa['ano'] = 15;
        }
        if (!$this->oParametros->exibirdistrito) {
            RelatorioHistoricoEscolarRetrato::$aLabelColunaEtapa['cidade'] = 'CIDADE';
        }
    }

    /**
     * Atribui a Disposição selecionada para impressão
     * @param integer $iDisposicao
     */
    public function setDisposicao($iDisposicao = 1)
    {
        $this->iDisposicao = $iDisposicao;
    }

    /**
     * @throws DBException
     */
    public function setEstadoOriginalEtapas($estadoOriginal)
    {
        $this->estadoOriginalTodasEtapas = $estadoOriginal;
        foreach ($this->estadoOriginalTodasEtapas as &$listaEtapas) {
            $this->ordenaEtapasPorEquivalenciaEOrdem($listaEtapas);
        }
    }

    /**
     * Escreve o cabecalho do Relatório
     *
     * @return void
     * @throws ParameterException
     * @throws DBException
     * @throws Exception
     */
    public function escreveCabecalho()
    {
        if (!is_null($this->oCurso)) {
            $this->sTituloRelatorio = "{$this->sPrefixoTitulo} - {$this->oCurso->getNome()}";
        } else {
            $curso = CursoRepository::getCursoAtualAluno($this->oAluno);
            $this->sTituloRelatorio .= " - {$curso->getNome()}";
        }

        if (!empty($this->iAnoLimite)) {
            $curso = CursoRepository::getCursoAlunoHistorico($this->oAluno, $this->iAnoLimite);
            $this->sTituloRelatorio = "{$this->sPrefixoTitulo} - {$curso}";
        }

        $iAlturaLinha = self::ALTURA_LINHA;
        $nome = empty($this->oAluno->getNomeSocial()) || is_null($this->oAluno->getNome()) ?
            $this->oAluno->getNome() :
            $this->oAluno->getNomeSocial();
        $sNomeMae = trim($this->oAluno->getNomeMae());
        $sNomePai = trim($this->oAluno->getNomePai());
        $sFiliacao = "";

        if (empty($sNomePai) && !empty($sNomeMae)) {
            $sFiliacao = $sNomeMae;
        } elseif (!empty($sNomePai) && empty($sNomeMae)) {
            $sFiliacao = $sNomePai;
        } elseif (!empty($sNomePai) && !empty($sNomeMae)) {
            $sFiliacao = "{$sNomePai} e de {$sNomeMae}";
        }

        $aNacionalidade = array(
            "1" => "BRASILEIRA",
            "2" => "BRASILEIRA NASCIDO NO EXTERIOR OU NATURALIZADO",
            "3" => "ESTRANGEIRA"
        );

        if ($this->oAluno->getDataNascimento() == "") {
            $sAluno = "{$this->oAluno->getCodigoAluno()} - {$nome}";
            throw new Exception("Aluno {$sAluno} não possui data de Nascimento, atualize o cadastro.");
        }
        $oDtNascimento = new DBDate($this->oAluno->getDataNascimento());
        $sLocalNascimento = $oDtNascimento->convertTo(DBDate::DATA_PTBR);
        if ($this->oAluno->getNaturalidade() != "") {
            $sNaturalidade = "";

            if (!is_null($this->oAluno->getNaturalidade()->getCodigo())) {
                $sNaturalidade = "{$this->oAluno->getNaturalidade()->getNome()} / ";
                $sNaturalidade .= "{$this->oAluno->getNaturalidade()->getUF()->getUF()}";
            }

            if ($this->oAluno->getNacionalidade() == 1) {
                $sLocalNascimento .= " em {$sNaturalidade} ";
            }
        }

        $iInstituicao = db_getsession("DB_instit");
        $sImagem = RelatorioHistoricoEscolar::getBrasao($this->oParametros->brasao, new Instituicao($iInstituicao));
        if (file_exists($sImagem)) {
            $this->oPdf->image($sImagem, 10, 10, 20, 20);
        }
        $this->oPdf->setfont('arial', 'B', 6);

        if ($this->iDisposicao == 1) {
            $this->escreverCabecalhoDisposicao1();
        } else {
            $this->escreverCabecalhoDisposicao2();
        }

        $this->oPdf->ln(2);
        $this->oPdf->setfont('arial', '', 8);
        $this->oPdf->cell(20, $iAlturaLinha, "Nome:", 0, 0, "L", 0);
        $this->oPdf->setfont('arial', 'b', 8);
        $this->oPdf->cell(95, $iAlturaLinha, "{$this->oAluno->getCodigoAluno()} - {$nome}", 0, 1, "L", 0);
        $this->oPdf->setfont('arial', '', 8);

        $this->oPdf->cell(20, $iAlturaLinha, "Filho(a) de:", 0, 0, "l", 0);
        $this->oPdf->cell(93, $iAlturaLinha, $sFiliacao, 0, 1, "L", 0);

        $this->oPdf->cell(20, $iAlturaLinha, "Nacionalidade:", 0, 0, "L", 0);
        $larguraNacionalidade = 25;
        if (
            $this->oAluno->getNacionalidade() == self::NACIONALIDADE_NASCIDO_EXTERIOR_OU_NATURALIZADO
        ) {
            $larguraNacionalidade = 80;
        }
        $this->oPdf->cell($larguraNacionalidade, $iAlturaLinha, $aNacionalidade[$this->oAluno->getNacionalidade()], 0, 0, "L", 0);

        $this->oPdf->cell(20, $iAlturaLinha, "Nascido(a) em:", 0, 0, "L", 0);
        $this->oPdf->cell(80, $iAlturaLinha, $sLocalNascimento, 0, 1, "L", 0);

        $this->oPdf->cell(20, $iAlturaLinha, "Identidade: ", 0, 0, "L", 0);
        $this->oPdf->cell(35, $iAlturaLinha, $this->oAluno->getIdentidade(), 0, 0, "L", 0);

        if ($this->oParametros->exibiridentidade) {
            $oDaoUF  = new cl_censouf();
            $sSqlUF  = $oDaoUF->sql_query($this->oAluno->getUFIdentidade(), "ed260_c_sigla");
            $rsUF    = $oDaoUF->sql_record($sSqlUF);
            $siglaUF = "";
            if ($rsUF && $this->oAluno->getUFIdentidade() && $oDaoUF->numrows > 0) {
                $siglaUF = db_utils::fieldsMemory($rsUF, 0)->ed260_c_sigla;
            }

            $oOrgaoEmissorRG = new CensoOrgaoEmissorRG($this->oAluno->getOrgaoEmissorIdentidade());

            $this->oPdf->cell(5, $iAlturaLinha, "UF:", 0, 0, "L", 0);
            $this->oPdf->cell(11, $iAlturaLinha, $siglaUF, 0, 0, "L", 0);
            $this->oPdf->cell(26, $iAlturaLinha, "Data de Expedição: ", 0, 0, "L", 0);

            $dtExpedicaoRG = $this->oAluno->getDataExpedicaoIdentidade();
            $dtExpedicaoRG = !empty($dtExpedicaoRG) ? new DBDate($dtExpedicaoRG) : "";
            $dtExpedicaoRG = !empty($dtExpedicaoRG) ? $dtExpedicaoRG->convertTo(DBDate::DATA_PTBR) : "";
            $this->oPdf->cell(18, $iAlturaLinha, $dtExpedicaoRG, 0, 0, "R", 0);
            $this->oPdf->cell(18, $iAlturaLinha, "Orgão Emissor: ", 0, 0, "L", 0);
            $this->oPdf->cell(65, $iAlturaLinha, $oOrgaoEmissorRG->getNome(), 0, 1, "R", 0);
        }

        $cpfAluno = '';
        if (!empty($this->oAluno->getCpf())) {
            $cpfAluno = db_formatar($this->oAluno->getCpf(), 'CPF');
        }
        $this->oPdf->cell(8, $iAlturaLinha, "CPF: ", 0, 0, "L", 0);
        $this->oPdf->cell(88, $iAlturaLinha, $cpfAluno, 0, 0, "L", 0);
        $this->oPdf->cell(12, $iAlturaLinha, "ID INEP: ", 0, 0, "L", 0);
        $this->oPdf->cell(5, $iAlturaLinha, $this->oAluno->getCodigoInep(), 0, 1, "L", 0);

        if ($this->oParametros->exibircertidao) {
            $oCartorio = new CensoCartorio($this->oAluno->getCartorioCertidao());

            $data_certidao_de_nascimento = $this->oAluno->getDataCertidaoNascimento();
            if (!empty($data_certidao_de_nascimento)) {
                $oDtCertidaoNascimento = new DBDate($this->oAluno->getDataCertidaoNascimento());
                $DtCertidaoNascimento = $oDtCertidaoNascimento->convertTo(DBDate::DATA_PTBR);
            } else {
                $DtCertidaoNascimento = "";
            }

            $this->oPdf->cell(14, $iAlturaLinha, "Cartório: ", 0, 0, "L", 0);
            $this->oPdf->cell(80, $iAlturaLinha, $oCartorio->getNome(), 0, 1, "L", 0);
            $this->oPdf->cell(25, $iAlturaLinha, "Número do Termo:", 0, 0, "L", 0);
            $this->oPdf->cell(15, $iAlturaLinha, $this->oAluno->getNumeroCertidao(), 0, 0, "L", 0);
            $this->oPdf->cell(8, $iAlturaLinha, "Folha:", 0, 0, "L", 0);
            $this->oPdf->cell(10, $iAlturaLinha, $this->oAluno->getFolhaCertidao(), 0, 0, "L", 0);
            $this->oPdf->cell(8, $iAlturaLinha, "Livro:", 0, 0, "L", 0);
            $this->oPdf->cell(10, $iAlturaLinha, $this->oAluno->getLivroCertidao(), 0, 0, "L", 0);
            $this->oPdf->cell(29, $iAlturaLinha, "Emissão da Certidão:", 0, 0, "L", 0);
            $this->oPdf->cell(15, $iAlturaLinha, $DtCertidaoNascimento, 0, 1, "R", 0);
        }

        $this->oPdf->ln();
        $this->oPdf->SetFontSize($this->oParametros->fonte_observacao);
        $this->oPdf->SetFont('arial');
    }

    /**
     * Escreve Disposicao do Relatório
     *
     * @return void
     */
    private function escreverCabecalhoDisposicao1()
    {
        $iPosicaoXDadosEscola = 110;
        $this->oPdf->setX(32); //POsicao Texto Cabecalho
        $this->oPdf->multicell(75, 4, $this->oParametros->cabecalho, 0, "C", 0, 0);

        /**
         * Monta a string dos atos
         */
        $aAtoEscola = $this->getAtosLegais();
        $sAtoEscola = "";
        $iLinhas = 0;

        $this->oPdf->SetFontSize(6);
        foreach ($aAtoEscola as $sAtoLegal) {
            $iLinhas += $this->oPdf->NbLines(100, $sAtoLegal);
            if ($iLinhas > self::MAXIMO_LINHAS_ATOS_LEGAIS) {
                continue;
            }
            $sAtoEscola .= $sAtoLegal . "\n";
        }

        $sTelefoneEscola = "";
        $aTelefones = $this->oEscola->getTelefones();
        if (count($aTelefones) > 0) {
            $sTelefoneEscola = "Fone: ({$aTelefones[0]->iDDD}) {$aTelefones[0]->iNumero}";
            $sTelefoneEscola .= !empty($aTelefones[0]->iRamal) ? "Ramal: {$aTelefones[0]->iRamal}" : "";
        }

        $sEnderecoEscola = "{$this->oEscola->getEndereco()}, {$this->oEscola->getNumeroEndereco()}";
        $sEnderecoEscola .= " - Bairro : {$this->oEscola->getBairro()} ";
        $sEnderecoEscola .= "\nCEP: {$this->oEscola->getCep()} - {$this->oEscola->getMunicipio()}/ {$this->oEscola->getUf()} - ";
        $sEnderecoEscola .= $sTelefoneEscola;

        $sNomeEscola = $this->oEscola->getNome();
        $iCodigoReferencia = $this->oEscola->getCodigoReferencia();

        if ($iCodigoReferencia != null) {
            $sNomeEscola = "{$iCodigoReferencia} - {$sNomeEscola}";
        }

        $mCabecalhoEscola = $sNomeEscola;
        if ($this->oParametros->exibirmantenedora) {
            $mCabecalhoEscola .= "\nMantenedora: ";
            $mCabecalhoEscola .= $this->oEscola->getDepartamento()->getInstituicao()->getDescricao();
        }
        $mCabecalhoEscola .= "\nEndereço: {$sEnderecoEscola}";

        $this->oPdf->setxy($iPosicaoXDadosEscola, $this->oPdf->getTopMargin());
        $this->oPdf->setfont('arial', '', 6);
        $this->oPdf->multicell(95, 3, $mCabecalhoEscola, 0, "L", 0, 0);
        $this->oPdf->Ln(4);
        $this->oPdf->setfont('arial', '', 6);
        $this->oPdf->SetX($iPosicaoXDadosEscola);
        $this->oPdf->multicell(95, 3, $sAtoEscola, 0, "L", 0, 0);

        $this->oPdf->SetY(35);
        $this->oPdf->setfont('arial', 'b', 10);
        $this->oPdf->cell(193, 4, $this->sTituloRelatorio, 0, 1, "L", 0);
        $this->oPdf->setfont('arial', 'b', 8);
    }

    /**
     * Mostra o cabeçalho das informações conforme Disposicao 2 do Sistema
     *
     * @access private
     * @return void
     */
    private function escreverCabecalhoDisposicao2()
    {
        $sTelefoneEscola = "";
        $aTelefones = $this->oEscola->getTelefones();
        if (count($aTelefones) > 0) {
            $sTelefoneEscola = "Fone: ({$aTelefones[0]->iDDD}) {$aTelefones[0]->iNumero}";
            $sTelefoneEscola .= !empty($aTelefones[0]->iRamal) ? " Ramal: {$aTelefones[0]->iRamal}" : "";
        }

        $sEnderecoEscola1 = "Endereço: {$this->oEscola->getEndereco()}, {$this->oEscola->getNumeroEndereco()}";
        $sEnderecoEscola1 .= " - Bairro : {$this->oEscola->getBairro()} ";
        $sEnderecoEscola2 = "CEP: {$this->oEscola->getCep()} - {$this->oEscola->getMunicipio()}/ {$this->oEscola->getUf()} - ";
        $sEnderecoEscola2 .= $sTelefoneEscola;

        if ($this->oParametros->exibirmantenedora) {
            $mCabecalhoEscola = "Mantenedora: ";
            $mCabecalhoEscola .= $this->oEscola->getDepartamento()->getInstituicao()->getDescricao();
        }

        /**
         * Monta a string dos atos
         */
        $aAtoEscola = $this->getAtosLegais();
        $sAtoEscola = "";
        $iLinhas = 0;

        foreach ($aAtoEscola as $sAtoLegal) {
            $iLinhas += $this->oPdf->NbLines(100, $sAtoLegal);
            if ($iLinhas > self::MAXIMO_LINHAS_ATOS_LEGAIS) {
                continue;
            }
            $sAtoEscola .= $sAtoLegal . "\n";
        }

        $sNomeEscola = $this->oEscola->getNome();
        $iCodigoReferencia = $this->oEscola->getCodigoReferencia();

        if ($iCodigoReferencia != null) {
            $sNomeEscola = "{$iCodigoReferencia} - {$sNomeEscola}";
        }

        $this->oPdf->SetY(10);
        $this->oPdf->setfont('arial', 'b', 8);
        $this->oPdf->SetX(60);
        $this->oPdf->multicell(100, 4, $this->oParametros->cabecalho, 0, "C", 0, 0);
        $this->oPdf->SetX(60);
        $this->oPdf->setfont('arial', 'b', 8);
        $this->oPdf->cell(100, 5, strtoupper($sNomeEscola), 0, 1, "C", 0);
        $this->oPdf->setfont('arial', 'b', 10);
        $this->oPdf->ln(2);
        $this->oPdf->cell(193, 4, mb_strtoupper($this->sTituloRelatorio), 0, 1, "C", 0);
        $this->oPdf->ln(4);
        $this->oPdf->setfont('arial', '', 6);
        $this->oPdf->multicell(110, 3, $mCabecalhoEscola, 0, "L", 0, 0);
        $this->oPdf->setfont('arial', '', 6);
        if ($sAtoEscola != "") {
            $this->oPdf->multicell(110, 4, $sAtoEscola, "", "L", 0, 0);
        }
        $this->oPdf->cell(98, 5, $sEnderecoEscola1, 0, 0, "L", 0);
        $this->oPdf->cell(98, 5, $sEnderecoEscola2, 0, 1, "L", 0);
    }

    /**
     * Verifica se o aluno cursou uma disciplina diversificada no seu Histórico
     * @return boolean
     */
    private function possuiBaseDiversificada()
    {
        foreach ($this->aDadosOrganizados as $oEtapaCursada) {
            foreach ($oEtapaCursada->aDisciplinas as $oDisciplina) {
                if (!$oDisciplina->lBaseComum) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Reorganiza a estrutura da grade agora devemos separar as disciplinas e as etapas pelo tipo da Base curricular;
     * @return array
     * @throws DBException
     */
    private function reorganizaEstrutura()
    {
        $aGrade = [];

        $sql = "select ed182_id from tipobase order by ed182_ordem_historico";
        $rs = db_query($sql);

        $tiposBases = [];
        while ($linha = pg_fetch_assoc($rs)) {
            $tiposBases[$linha['ed182_id']] = [];
        }
        foreach ($this->aDadosOrganizados as $oEtapaCursada) {
            $tipoEnsino = $oEtapaCursada->ensino->getTipoEnsino();
            $keyTipoEnsino = $tipoEnsino->getValue();

            if (!array_key_exists($keyTipoEnsino, $aGrade)) {
                $aGrade[$keyTipoEnsino] = (object)[
                    "tipoEnsino" => $tipoEnsino->name(),
                    "tiposBases" => $tiposBases,
                    "lTemAreaConhecimento" => false
                ];
            }

            $disciplinas = [];
            foreach ($oEtapaCursada->aDisciplinas as $disciplinaEtapa) {
                $chave_etapa = $oEtapaCursada->iAno . "_" . $oEtapaCursada->iEtapa;

                if (!array_key_exists(
                    $chave_etapa,
                    $aGrade[$keyTipoEnsino]->tiposBases[$disciplinaEtapa->lTipoBase]
                )) {
                    $etapaPorBase = new stdClass();
                    $etapaPorBase->iEtapa = $oEtapaCursada->iEtapa;
                    $etapaPorBase->sEtapa = $oEtapaCursada->sEtapa;
                    $etapaPorBase->ordem =  $oEtapaCursada->ordem;
                    $etapaPorBase->resultadoEtapaAno = $oEtapaCursada->resultadoEtapaAno;
                    $etapaPorBase->areasConhecimento = [];
                    $etapaPorBase->aDisciplinas = [];
                    $etapaPorBase->ensino = $oEtapaCursada->ensino;
                    $aGrade[$keyTipoEnsino]
                        ->tiposBases[$disciplinaEtapa->lTipoBase][$chave_etapa] = $etapaPorBase;
                }

                if (!empty($oEtapaCursada->areasConhecimento)) {
                    foreach ($oEtapaCursada->areasConhecimento as $codigo => $areaConhecimento) {
                        $areaConhecimento->disciplinas = [];
                        foreach ($areaConhecimento->disciplinasArea as $oDisciplina) {
                            $etapaPorBase = $aGrade[$keyTipoEnsino]
                                ->tiposBases[$disciplinaEtapa->lTipoBase][$chave_etapa];
                            if ($oEtapaCursada->iAno . "_" . $etapaPorBase->iEtapa == $chave_etapa) {
                                if ($oDisciplina->lTipoBase == $disciplinaEtapa->lTipoBase) {
                                    if (!array_key_exists($codigo, $etapaPorBase->areasConhecimento)) {
                                        $areaConhecimentoPorBase = clone ($areaConhecimento);
                                        $etapaPorBase->areasConhecimento[$codigo] = $areaConhecimentoPorBase;
                                        if ($etapaPorBase->areasConhecimento[$codigo]->resultadoObtido != '-') {
                                            foreach ($etapaPorBase->areasConhecimento[$codigo]->disciplinasArea as $chave => $valor) {
                                                $etapaPorBase->areasConhecimento[$codigo]->disciplinasArea[$chave]->mAvaliacao = '-';
                                            }
                                        }
                                    }
                                    $etapaPorBase
                                        ->areasConhecimento[$codigo]
                                        ->disciplinas[$oDisciplina->iCadDisciplina] = $oDisciplina;
                                }
                            }
                        }
                    }
                }
                if (!isset($codigo)
                    || !property_exists($etapaPorBase, 'areasConhecimento')
                    || !array_key_exists($codigo, $etapaPorBase->areasConhecimento)
                    || !property_exists($etapaPorBase->areasConhecimento[$codigo], 'disciplinas')
                    || !array_key_exists('iCadDisciplina', $etapaPorBase->areasConhecimento[$codigo]->disciplinas)
                    || !$etapaPorBase->areasConhecimento[$codigo]->disciplinas[$disciplinaEtapa->iCadDisciplina]
                ) {
                    $aGrade[$keyTipoEnsino]
                        ->tiposBases[$disciplinaEtapa->lTipoBase][$chave_etapa]
                        ->aDisciplinas[$disciplinaEtapa->iCadDisciplina] = $disciplinaEtapa;
                }
            }
        }
        foreach ($aGrade as $tipoEnsino) {
            foreach ($tipoEnsino->tiposBases as $iTipoBase => $etapas) {
                if (count($etapas) == 0) {
                    unset($tipoEnsino->tiposBases[$iTipoBase]);
                }
            }
        }

        if ($this->lExibirTodasEtapasCurso && count($aGrade) > 0) {
            $ultimoEnsino = end($aGrade);
            $ordemUltimaEtapa = 0;
            foreach ($ultimoEnsino->tiposBases as $etapaPorBase) {
                foreach ($etapaPorBase as $etapa) {
                    if ($etapa->ordem > $ordemUltimaEtapa) {
                        $ordemUltimaEtapa = $etapa->ordem;
                    }
                }
            }
            $ensinos = [];
            if (!empty($this->aEtapasAnterior)) {
                foreach ($this->aEtapasAnterior as $oEtapaAnterior) {
                    foreach ($aGrade as $tipoEnsino) {
                        foreach ($tipoEnsino->tiposBases as $iTipoBase => $etapaPorBase) {
                            if (end($tipoEnsino->tiposBases[$iTipoBase])->ensino->getTipoEnsino()->value()
                                == $oEtapaAnterior->ensino->getTipoEnsino()->value()
                            ) {
                                $existeEtapaAdicionada = $this->verificarExisteEquivalenteAdicionada(
                                    $oEtapaAnterior,
                                    $etapaPorBase
                                );
                                if (!$existeEtapaAdicionada) {
                                    $tipoEnsino->tiposBases[$iTipoBase][$oEtapaAnterior->iEtapa] = $oEtapaAnterior;
                                }
                            }
                        }
                    }
                }
            }

            foreach ($this->aEtapasPosterior as $chaveEtp => $oEtapaPosterior) {
                foreach ($aGrade as $tipoEnsino) {
                    foreach ($tipoEnsino->tiposBases as $key => $etapaPorBase) {
                        foreach ($etapaPorBase as $etapa) {
                            $ensinos[$etapa->ensino->getCodigo()] = $etapa->ensino;
                        }
                        $existeEtapaAdicionada = $this->verificarExisteEquivalenteAdicionada(
                            $oEtapaPosterior,
                            $tipoEnsino->tiposBases[$key]
                        );
                        if ($existeEtapaAdicionada) {
                            continue;
                        }
                        if (end($tipoEnsino->tiposBases[$key])->ensino->getTipoEnsino()->value()
                            == $oEtapaPosterior->ensino->getTipoEnsino()->value()
                        ) {
                            $tipoEnsino->tiposBases[$key][$oEtapaPosterior->iEtapa] = $oEtapaPosterior;
                        }
                    }
                }
            }

            $this->setEstadoOriginalEtapas($this->buscaEtapasPorEnsino($ensinos));

            foreach ($aGrade as $tipoBaseKey => $tipoBase) {
                foreach ($tipoBase->tiposBases as $etapaKey => $etapas) {
                $todasEtapasPorEnsino = $this->estadoOriginalTodasEtapas;
                    foreach ($etapas as $etapa) {
                        if (isset($todasEtapasPorEnsino[$tipoBaseKey][$etapa->iEtapa])) {
                            unset($todasEtapasPorEnsino[$tipoBaseKey][$etapa->iEtapa]);
                        }
                    }
                    $tipoBase->tiposBases[$etapaKey] = array_merge(
                        $todasEtapasPorEnsino[$tipoBaseKey],
                        $etapas
                    );
                }
                $aGrade[$tipoBaseKey]->tiposBases = $tipoBase->tiposBases;
            }
        }
        // corrige areas de conhecimento
        foreach ($aGrade as $tipoEnsino) {
            foreach ($tipoEnsino->tiposBases as $iTipoBase => $etapas) {
                foreach ($etapas as $etapa) {
                    if (isset($etapa->areasConhecimento) && count($etapa->areasConhecimento) > 0) {
                        foreach ($etapa->areasConhecimento as $areaConhecimento) {
                            foreach ($areaConhecimento->disciplinasArea as $disciplinaArea) {
                                $disciplinaArea->mAvaliacao = "-";
                            }
                        }
                    }
                }
            }
        }

        // Filtrando as etapas conforme o curso selecionado pelo usuário
        if (!is_null($this->oCurso)) {
            foreach ($aGrade as $tipoEnsino) {
                foreach ($tipoEnsino->tiposBases as $iTipoBase => &$etapas) {
                    $etapas = array_filter($etapas, function ($item) {
                        return $item->ensino ===  $this->oCurso->getEnsino();
                    });
                }
            }
        }

        $aGrade = $this->sortArrayEtapasPorOrdem($aGrade);

        if ($this->lExibirTodasEtapasCurso) {
            $aGrade = $this->organizaEJA($aGrade);
        }

        $this->verificaBaseVazia($aGrade);

        return $aGrade;
    }

    /**
     * Cria a tabela de ComponentesCurriculares
     * @return void
     * @throws DBException
     * @throws ParameterException
     */
    public function criarTabelaComponentesCurriculares()
    {
        $aDisciplinasCursadas = $this->disciplinasCursadas();
        list($areasConhecimento, $disciplinasComArea) = $this->disciplinasCursadasPorArea($aDisciplinasCursadas);
        foreach ($disciplinasComArea as $disciplinaComArea) {
            unset($aDisciplinasCursadas[$disciplinaComArea]);
        }

        /**
         * Reorganizamos as etapas em um array controlado por página
         */
        $aGrade = $this->reorganizaEstrutura();


        $iFonteGrade = $this->oParametros->fonte_grade_nota;
        $iAlturaLinha = RelatorioHistoricoEscolarRetrato::ALTURA_LINHA;
        $iLarguraDisciplina = RelatorioHistoricoEscolarRetrato::LARGURA_DISCIPLINA;
        $iNumeroEtapasPagina = RelatorioHistoricoEscolarRetrato::NUMERO_ETAPAS_PAGINA;

        /**
         * Impressão da grade
         */
        foreach ($aGrade as $basesPorEnsino) {
            foreach ($basesPorEnsino->tiposBases as $iTipoBase => $aEtapa) {
                $alturaGrade = $this->calcularAlturaGrade($aEtapa);
                if ($alturaGrade > $this->oPdf->getAvailHeight()) {
                    $this->oPdf->AddPage();
                    $this->escreveCabecalho();
                }
                $sql = "select ed182_descricao from tipobase where ed182_id = {$iTipoBase}";
                $tipoBase = pg_fetch_assoc(db_query($sql));
                $sBase = $tipoBase['ed182_descricao'];
                if ($basesPorEnsino->tipoEnsino != $tipoBase['ed182_descricao']) {
                    $sBase .= " - " . $basesPorEnsino->tipoEnsino;
                }

                $this->oPdf->ln(0, 5);
                $this->oPdf->SetFont("arial", "B", 8);
                $this->oPdf->Cell($iLarguraDisciplina + 135, $iAlturaLinha, $sBase, 0, 1, "L");

                $this->oPdf->SetFont("arial", "B", $iFonteGrade);
                $this->oPdf->SetFillColor(200);
                $this->oPdf->Cell($iLarguraDisciplina, $iAlturaLinha, "COMPONENTES CURRICULARES", 1, 0, "C", 1);

                $larguraEtapas = 135 / count($aEtapa);

                foreach ($aEtapa as $oEtapa) {
                    $this->oPdf->Cell($larguraEtapas, $iAlturaLinha, $oEtapa->sEtapa, 1, 0, "C", 1);
                }
                $this->oPdf->SetFillColor(223);

                $this->oPdf->SetFont("arial", "", $iFonteGrade);
                $this->oPdf->ln();

                $disciplinasImpressas = [];
                $areasConhecimentoImpressas = [];
                $temAreaDeConhecimento = false;
                $discByArea = [];
                foreach ($aEtapa as $key => $etapa) {
                    if (property_exists($etapa, 'areasConhecimento')) {
                        if (!empty($etapa->areasConhecimento)) {
                            foreach ($etapa->areasConhecimento as $key => $area) {
                                $discByArea[$area->codigo] = $area->disciplinasArea;
                            }
                        }
                    }
                }

                foreach ($aEtapa as $key => $etapa) {
                    if (property_exists($etapa, 'areasConhecimento')) {
                        if (!empty($etapa->areasConhecimento)) {
                            foreach ($etapa->areasConhecimento as $key => $area) {
                                if (array_key_exists($area->codigo, $discByArea)) {
                                    $area->disciplinasArea = $discByArea[$area->codigo];
                                    $area->disciplinas = $discByArea[$area->codigo];
                                }
                            }
                        }
                    }
                }
                foreach ($aEtapa as $etapa) {
                    if (property_exists($etapa, 'areasConhecimento')) {
                        foreach ($etapa->areasConhecimento as $areaConhecimento) {
                            $temAreaDeConhecimento = true;
                            if (array_key_exists($areaConhecimento->codigo, $areasConhecimentoImpressas)) {
                                continue;
                            }

                            $this->imprimeAreaEtapas(
                                $areaConhecimento,
                                $aEtapa,
                                $iLarguraDisciplina,
                                $iAlturaLinha,
                                $iFonteGrade,
                                $iNumeroEtapasPagina
                            );
                            $areasConhecimentoImpressas[$areaConhecimento->codigo] = $areaConhecimento->codigo;
                            foreach ($areaConhecimento->disciplinas as $disciplina) {
                                $disciplina->iDisciplina = $disciplina->iCadDisciplina;
                                $ix = "{$disciplina->iCadDisciplina}#{$areaConhecimento->codigo}";

                                if (array_key_exists($ix, $disciplinasImpressas)) {
                                    continue;
                                }

                                $disciplinasImpressas[$ix] = $ix;

                                $this->imprimeDisciplinaEtapas(
                                    $disciplina,
                                    $aEtapa,
                                    $iLarguraDisciplina,
                                    $iAlturaLinha,
                                    $iFonteGrade,
                                    $iNumeroEtapasPagina,
                                    true
                                );
                            }
                        }
                    }
                }
                if ($temAreaDeConhecimento && count($aDisciplinasCursadas) > 0) {
                    $this->oPdf->SetFont("arial", "B", $iFonteGrade);
                    $this->oPdf->setFillColor(200);
                    $this->oPdf->Cell($iLarguraDisciplina, $iAlturaLinha, "COMPONENTES CURRICULARES", 1, 0, "C", 1);

                    $larguraEtapas = 135 / count($aEtapa);
                    foreach ($aEtapa as $oEtapa) {
                        $sEtapa = '';
                        $this->oPdf->Cell($larguraEtapas, $iAlturaLinha, $sEtapa, 1, 0, "C", 1);
                    }
                    $this->oPdf->setFillColor(223);

                    $this->oPdf->SetFont("arial", "", $iFonteGrade);
                    $this->oPdf->ln();
                }


                foreach ($aDisciplinasCursadas as $oDisciplina) {
                    $this->imprimeDisciplinaEtapas(
                        $oDisciplina,
                        $aEtapa,
                        $iLarguraDisciplina,
                        $iAlturaLinha,
                        $iFonteGrade,
                        $iNumeroEtapasPagina
                    );
                }
                $this->oPdf->Ln();
            }
        }
    }

    /**
     * Cria a tabela de ResumoEtapas
     * @throws Exception
     */
    public function criarTabelaResumoEtapas()
    {
        $iAlturaLinha = RelatorioHistoricoEscolarRetrato::ALTURA_LINHA;
        $aDadosRelatorio = $this->montarEstruturaDeDados();
        $iFonteGrade = $this->oParametros->fonte_grade_etapa;

        /*
         Retornamos o tamanho padrão das colunas pois estava sempre incrementando em novas paginas quando
         exibirperiodo = true ou exibe_percentual_frequencia = true
        */
        RelatorioHistoricoEscolarRetrato::$aLarguraColunaEtapa['escola'] = 58;
        RelatorioHistoricoEscolarRetrato::$aLarguraColunaEtapa['cidade'] = 36;

        if ($this->oParametros->exibirperiodo) {
            RelatorioHistoricoEscolarRetrato::$aLarguraColunaEtapa['escola'] = 50;
        }

        if (!$this->oParametros->exibe_turma) {
            RelatorioHistoricoEscolarRetrato::$aLarguraColunaEtapa['escola'] += RelatorioHistoricoEscolarRetrato::$aLarguraColunaEtapa['turma'];
        }
        if (!$this->oParametros->exibe_percentual_frequencia) {
            RelatorioHistoricoEscolarRetrato::$aLarguraColunaEtapa['cidade'] += RelatorioHistoricoEscolarRetrato::$aLarguraColunaEtapa['percentual_frequencia'];
        }

        $aLargura = RelatorioHistoricoEscolarRetrato::$aLarguraColunaEtapa;
        $aLabel = RelatorioHistoricoEscolarRetrato::$aLabelColunaEtapa;
        $iAlturaTotalGrade = $iAlturaLinha * (count($aDadosRelatorio) + 1); // + pois tem que escrever cabecalho
        $iAlturaDisponivel = $this->oPdf->getAvailHeight();

        if ($iAlturaTotalGrade > $iAlturaDisponivel) {
            $this->oPdf->AddPage();
            $this->escreveCabecalho();
        }

        /**
         * Impressão da grade
         */
        $this->oPdf->SetFont("arial", "B", 6);

        $this->oPdf->Cell($aLargura['etapa'], $iAlturaLinha, $aLabel['etapa'], 1, 0, "C");
        $this->oPdf->Cell($aLargura['ano'], $iAlturaLinha, $aLabel['ano'], 1, 0, "C");
        if ($this->oParametros->exibirperiodo) {
            $this->oPdf->Cell($aLargura['periodo'], $iAlturaLinha, $aLabel['periodo'], 1, 0, "C");
        }

        $this->oPdf->Cell($aLargura['dias'], $iAlturaLinha, $aLabel['dias'], 1, 0, "C");

        if ($this->oParametros->exibe_turma) {
            $this->oPdf->Cell($aLargura['turma'], $iAlturaLinha, $aLabel['turma'], 1, 0, "C");
        }

        $this->oPdf->Cell($aLargura['carga_horaria'], $iAlturaLinha, $aLabel['carga_horaria'], 1, 0, "C");

        if ($this->oParametros->exibe_percentual_frequencia) {
            $this->oPdf->Cell($aLargura['percentual_frequencia'], $iAlturaLinha, $aLabel['percentual_frequencia'], 1, 0, "C");
        }
        $this->oPdf->Cell($aLargura['resultado'], $iAlturaLinha, $aLabel['resultado'], 1, 0, "C");
        $this->oPdf->Cell($aLargura['escola'], $iAlturaLinha, $aLabel['escola'], 1, 0, "C");
        $this->oPdf->Cell($aLargura['cidade'], $iAlturaLinha, $aLabel['cidade'], 1, 0, "C");
        $this->oPdf->Cell($aLargura['uf'], $iAlturaLinha, $aLabel['uf'], 1, 1, "C");

        $this->oPdf->SetFont("arial", "", $iFonteGrade);

        $iLinhaPadrao = RelatorioHistoricoEscolarRetrato::ALTURA_LINHA;

        if ($this->lExibirTodasEtapasCurso && !empty($this->aEtapasAnterior)) {
            $array_keys = array_keys($this->aEtapasAnterior);
            foreach ($array_keys as $chave => $valor) {
                $chaveAssociativaDecrescente = $array_keys[(count($array_keys) - $chave) - 1];
                array_unshift($aDadosRelatorio, $this->aEtapasAnterior[$chaveAssociativaDecrescente]);
            }
        }

        if (!is_null($this->oCurso)) {
            $aDadosRelatorio = array_filter($aDadosRelatorio, function ($item) {
                return $item->ensino === $this->oCurso->getEnsino();
            });

            $this->aEtapasAnterior = array_filter($this->aEtapasAnterior, function ($item) {
                return $item->ensino === $this->oCurso->getEnsino();
            });

            $this->aEtapasPosterior = array_filter($this->aEtapasPosterior, function ($item) {
                return $item->ensino === $this->oCurso->getEnsino();
            });
        }

        $etapasResumo = $this->reorganizarResumoEtapas(
            $aDadosRelatorio,
            $this->aEtapasAnterior,
            $this->aEtapasPosterior
        );

        $this->ordenaEtapasPorEquivalenciaEOrdem($etapasResumo);

        foreach ($etapasResumo as $etapa) {
            $this->escreverResumoEtapas($etapa);
        }

        $this->oPdf->ln();
    }

    /**
     * Organiza as disciplinas em uma segunda estrutura para facilitar Impressão
     * @return stdClass[]
     */
    private function disciplinasCursadas()
    {
        $aDisciplinas = array();

        foreach ($this->aDadosOrganizados as $oEtapaCursada) {
            foreach ($oEtapaCursada->aDisciplinas as $oDisciplinaCursada) {
                if (array_key_exists($oDisciplinaCursada->sDisciplina, $aDisciplinas)) {
                    continue;
                }
                $oDisciplina = new stdClass();
                $oDisciplina->iDisciplina = $oDisciplinaCursada->iCadDisciplina;
                $oDisciplina->sDisciplina = $oDisciplinaCursada->sDisciplina;
                $oDisciplina->lBaseComum = $oDisciplinaCursada->lBaseComum;
                $aDisciplinas[$oDisciplinaCursada->sDisciplina] = $oDisciplina;
            }
        }

        ksort($aDisciplinas);
        return $aDisciplinas;
    }

    /**
     * Monta o quadro das observações
     * Ordem das informações
     * - Observação dos Parâmetros
     * - Convenções (Removido a pedido do Tiago)
     * - Observação do Histórico
     * - Aprovado pelo conselho
     * - Colocar na Observação se os dados da trocou de série se houver
     * @throws Exception
     */
    public function montaQuadroObservacao()
    {
        $qtd_chr_por_linha = 0;
        switch ($this->oParametros->fonte_observacao) {
            case '6':
                $qtd_chr_por_linha = 190;
                break;

            case '8':
                $qtd_chr_por_linha = 145;
                break;
        }

        $sObsParametros = $this->oPdf->quebrarTextoEmLinhas(
            $qtd_chr_por_linha,
            $this->oParametros->observacao
        );

        $sObsParametros = implode("\n", $sObsParametros);
        $sObsHistorico = implode("\n", $this->aObservacaoHistorico);
        $sProAprovacaoComProgressao = "";
        if ($this->lAlunoTeveAprovacaoComProgressao) {
            $sProAprovacaoComProgressao .= " * = Aprovado com progressão parcial / Dependência";
        }
        $sObsAprovadoPeloConselho = "";

        if ($this->oParametros->exibe_obs_diario == 't') {
            $sObsAprovadoPeloConselho = $this->getObservacaoAprovadoPeloConselho();
        }
        $sObsTrocaSerie = $this->getObservacaoTrocaSerie();

        $disciplinasAbreviadas = $this->buscarDisciplinasAbreviadas();

        $sObservacao = [];

        if (!empty($disciplinasAbreviadas)) {
            $sObservacao = ['<b>Legenda: </b>'];
            $sObservacao = array_merge($sObservacao, $disciplinasAbreviadas);
        }
        $sObservacao[] = "<b>Observações:</b> ";
        if (!empty($sObsParametros)) {
            $sObservacao[] = $sObsParametros;
        }
        if (!empty($sProAprovacaoComProgressao)) {
            $sObservacao[] = $sProAprovacaoComProgressao;
        }
        if (!empty($sObsHistorico)) {
            $sObservacao[] = $sObsHistorico;
        }
        if (!empty($sObsAprovadoPeloConselho)) {
            $sObservacao[] = $sObsAprovadoPeloConselho;
        }
        if (!empty($sObsTrocaSerie)) {
            $sObservacao[] = $sObsTrocaSerie;
        }

        $aObservacaoHistoricoEtapa = $this->getObservacaoHistoricoEtapa();
        sort($aObservacaoHistoricoEtapa);
        foreach ($aObservacaoHistoricoEtapa as $observacaoEtapa) {
            $linhas = $this->oPdf->quebrarTextoEmLinhas(
                $qtd_chr_por_linha,
                $observacaoEtapa
            );
            // retira espaços no início e fim de cada linha
            $linhas = array_map(function ($linha) {
                return trim($linha);
            }, $linhas);

            $sObservacao = array_merge($sObservacao, $linhas);
        }

        /*PLUGIN DIARIO PROGRESSAO - ADICIONADO observações DA EVASÃO DA PROGRESSÃO - NÃO APAGAR*/

        $this->oPdf->SetFontSize($this->oParametros->fonte_observacao);


        $iAlturaDisponivel = $this->oPdf->getAvailHeight();

        $this->oPdf->SetAutoPageBreak(true, 15);
        if ($iAlturaDisponivel < 15) {
            $this->oPdf->addPage();
            $this->escreveCabecalho();
            $this->oPdf->ln();
        }

        $this->oPdf->line($this->oPdf->getX(), $this->oPdf->getY(), 203, $this->oPdf->getY());
        foreach ($sObservacao as $observacao) {
            $this->oPdf->SetFont('arial');
            $iTotalLinhasObservacao = $this->oPdf->nbLines(195, $observacao);
            $iAlturaLinhasObservacao = $iTotalLinhasObservacao * (self::ALTURA_LINHA);
            $iAlturaDisponivel = $this->oPdf->getAvailHeight() - 10;
            if ($iAlturaLinhasObservacao >= $iAlturaDisponivel) {
                $this->oPdf->line($this->oPdf->getX(), $this->oPdf->getY(), 203, $this->oPdf->getY());
                $this->oPdf->addPage();
                $this->escreveCabecalho();
                $this->oPdf->ln();
                $this->oPdf->line($this->oPdf->getX(), $this->oPdf->getY(), 203, $this->oPdf->getY());
            }

            $yAntes = $this->oPdf->getY();
            $this->oPdf->writeHTML($observacao . '<br>');
            $yDepois = $this->oPdf->getY();
            $this->oPdf->line(8, $yAntes, 8, $yDepois);
            $this->oPdf->line(203, $yAntes, 203, $yDepois);
        }
        $this->oPdf->line($this->oPdf->getX(), $this->oPdf->getY(), 203, $this->oPdf->getY());
        $this->oPdf->SetAutoPageBreak(false);
    }

    /**
     * Retorna uma as Convenções
     * @return string
     */
    private function getObservacaoConvencao()
    {
        $sConvencoes = "";
        if (!empty($this->iDisposicao) && $this->iDisposicao == 1) {
            $sConvencoes = " Convenções: CH = Carga Horária RF = Resultado Final PL = Período Letivo ";
            $sConvencoes .= " ESC = Escola DL = Dias Letivos Aprov. = Aproveitamento";
        }

        return $sConvencoes;
    }

    /**
     * Retorna o nome do último curso cursado pelo aluno.
     * @return string
     */
    private function getNomeUltimoCurso()
    {
        $sCamposHist = "*";
        $sWhereHist = " ed61_i_aluno = {$this->oAluno->getCodigoAluno()}";
        $sOrderHist = " ed47_v_nome ";

        $oDaoHistorico = new cl_historico();
        $sSqlHist = $oDaoHistorico->sql_query("", $sCamposHist, $sOrderHist, $sWhereHist);
        $rsHist = $oDaoHistorico->sql_record($sSqlHist);
        $iLinhasHist = $oDaoHistorico->numrows;

        $sCurso = "";
        /**
         * Aplicada mesma lógica do relatório
         */
        for ($iContHist = 0; $iContHist < $iLinhasHist; $iContHist++) {
            $oDadosHist = db_utils::fieldsmemory($rsHist, $iContHist);
            if (!empty($oDadosHist->ed61_i_anoconc)) {
                $sCurso = $oDadosHist->ed29_c_descr;
            }
        }

        return $sCurso;
    }

    /**
     * Escreve a assinatura no Documento Definindo Posição Inicial
     *
     * @param string $sTexto
     * @param integer $iPosicaoInicial
     */
    private function escreverAssinatura($sTexto, $iPosicaoInicial, $sFuncao)
    {

        if (empty($sTexto)) {
            $sTexto = '-' . $sFuncao;
        }
        $iLarguraAssinatura = self::LARGURA_ASSINATURA;
        $aTexto = explode("-", $sTexto);
        $sNome = $aTexto[1];
        $sFuncao = $aTexto[0] . (array_key_exists(2, $aTexto) && trim($aTexto[2]) != "" ? " ($aTexto[2])" : "");
        $sNomeCargo = "{$sNome}\n {$sFuncao}";
        $iYAntesEscrever = $this->oPdf->GetY();

        $this->oPdf->SetX($iPosicaoInicial);

        $this->oPdf->Line(
            $this->oPdf->GetX(),
            $this->oPdf->GetY(),
            $this->oPdf->GetX() + $iLarguraAssinatura,
            $this->oPdf->GetY()
        );

        $this->oPdf->Ln(1);
        $this->oPdf->SetX($iPosicaoInicial);
        $this->oPdf->MultiCell($iLarguraAssinatura, 4, $sNomeCargo, 0, "C");
        $this->oPdf->SetY($iYAntesEscrever);
    }

    /**
     * Escreve a data por extenso
     * @throws ParameterException
     */
    private function escreverDataPorExtenso()
    {
        $this->oPdf->Ln();
        $oData = new DBDate(date("d/m/Y"));

        if (!empty($this->sDataEmissao)) {
            $oData = new DBDate($this->sDataEmissao);
        }

        $sData = "{$this->oEscola->getMunicipio()}, ";
        $sData .= $oData->dataPorExtenso();
        $this->oPdf->Cell(205, 4, $sData, 0, 1, "C");
        $this->oPdf->Ln();
    }

    /**
     * Define o Título do Relatório
     *
     * @param string $sTitulo
     */
    public function setTitulo($sTitulo)
    {
        $this->sTituloRelatorio = $sTitulo;
    }

    /**
     * Escreve o Rodapé do Relatório
     *
     * @param string $sDiretor
     * @param string $sSecretario
     * @throws ParameterException
     */
    public function escreverRodape($sDiretor, $sSecretario)
    {
        $this->oPdf->Ln(6);

        $this->oPdf->setfont('arial', '', 6);
        $this->escreverDataPorExtenso();
        $this->oPdf->Ln(6);
        $sSecretario = DBString::utf8_decode_all($sSecretario);
        $sDiretor = DBString::utf8_decode_all($sDiretor);
        $this->escreverAssinatura($sSecretario, $this->oPdf->getLeftMargin(), 'SECRETÁRIO DE ESCOLA');
        $this->escreverAssinatura($sDiretor, 110, 'DIRETOR');
    }

    /**
     * @param $oDadosEtapa
     */
    private function escreverResumoEtapas($oDadosEtapa)
    {
        $aLargura = RelatorioHistoricoEscolarRetrato::$aLarguraColunaEtapa;
        $iAlturaLinha = RelatorioHistoricoEscolarRetrato::ALTURA_LINHA;

        $fonte = $this->oParametros->fonte_grade_etapa;

        $iLarguraTotal = RelatorioHistoricoEscolarRetrato::LARGURA_TOTAL;

        if ($iAlturaLinha > $this->oPdf->getAvailableHeight()) {
            $this->oPdf->addPage();
            $this->escreveCabecalho();
        }

        $iYInicial = $this->oPdf->GetY();
        $iXInicial = $this->oPdf->GetX();
        $this->oPdf->Cell($aLargura['etapa'], $iAlturaLinha, trim($oDadosEtapa->sEtapa), 0, 0, 'C');
        $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);
        $this->oPdf->Cell($aLargura['ano'], $iAlturaLinha, trim($oDadosEtapa->iAno), 0, 0, 'C');
        $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);
        if ($this->oParametros->exibirperiodo) {
            $this->oPdf->Cell($aLargura['periodo'], $iAlturaLinha, trim($oDadosEtapa->periodo), 0, 0, 'C');
        }
        $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);
        $this->oPdf->Cell($aLargura['dias'], $iAlturaLinha, $this->trataValorParaNaoExibirZero($oDadosEtapa->iDiasLetivos), 0, 0, 'C');
        $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);

        if ($this->oParametros->exibe_turma) {
            $iYAtual = $this->oPdf->GetY();
            $iXAtual = $this->oPdf->GetX();
            $this->oPdf->cellAdapt($fonte, $aLargura['turma'], $iAlturaLinha, trim($oDadosEtapa->sTurma), 0, 0, 'C');
            $this->oPdf->setY($iYAtual);
            $this->oPdf->SetX($iXAtual + $aLargura['turma']);
            $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);
        }

        $this->oPdf->Cell($aLargura['carga_horaria'], $iAlturaLinha, $this->trataValorParaNaoExibirZero($oDadosEtapa->iCargaHoraria), 0, 0, 'C');
        $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);

        if ($this->oParametros->exibe_percentual_frequencia) {
            $this->oPdf->Cell($aLargura['percentual_frequencia'], $iAlturaLinha, trim($oDadosEtapa->nPercentualFalta), 0, 0, 'C');
            $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);
        }

        $this->oPdf->Cell($aLargura['resultado'], $iAlturaLinha, trim($oDadosEtapa->sResultado), 0, 0, 'C');
        $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);

        $iYAtual = $this->oPdf->GetY();
        $iXAtual = $this->oPdf->GetX();

        $this->oPdf->cellAdapt($fonte, $aLargura['escola'], $iAlturaLinha, trim($oDadosEtapa->sEscola) . ' ', 0, 'L');
        $this->oPdf->setY($iYAtual);
        $this->oPdf->SetX($iXAtual + $aLargura['escola']);
        $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);

        $iYAtual = $this->oPdf->GetY();
        $iXAtual = $this->oPdf->GetX();
        $this->oPdf->cellAdapt($fonte, $aLargura['cidade'], $iAlturaLinha, trim($oDadosEtapa->sMunicipio), 0, 'L');
        $this->oPdf->setY($iYAtual);
        $this->oPdf->SetX($iXAtual + $aLargura['cidade']);
        $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);

        $this->oPdf->Cell($aLargura['uf'], $iAlturaLinha, trim($oDadosEtapa->sUF), 0, 0, 'C');
        $this->oPdf->Line($this->oPdf->GetX(), $iYInicial, $this->oPdf->GetX(), $iYInicial + $iAlturaLinha);

        /**
         * Monta a grade utilizando line
         */
        $this->oPdf->Line($iXInicial, $iYInicial, $iLarguraTotal, $iYInicial);

        $this->oPdf->SetY($iYInicial + $iAlturaLinha);
        $this->oPdf->Line($iXInicial, $iYInicial, $iXInicial, $this->oPdf->getY());
        $this->oPdf->Line($iLarguraTotal, $iYInicial, $iLarguraTotal, $this->oPdf->getY());
        $this->oPdf->Line($iXInicial, $this->oPdf->getY(), $iLarguraTotal, $this->oPdf->getY());
    }

    /**
     * Define se deve exibir todas as etapas do curso ou somente as etapas cursadas
     * @param boolean $lExibirTodasEtapasCurso
     */
    public function setExibirTodasEtapasCurso($lExibirTodasEtapasCurso)
    {
        $this->lExibirTodasEtapasCurso = $lExibirTodasEtapasCurso;
    }

    private function disciplinasCursadasPorArea(array $aDisciplinasCursadas)
    {
        $areasConhecimento = [];
        $disciplinasComArea = [];

        foreach ($this->aDadosOrganizados as $oEtapaCursada) {
            foreach ($oEtapaCursada->areasConhecimento as $codigo => $areaConhecimento) {
                if (!array_key_exists($codigo, $areasConhecimento)) {
                    $areasConhecimento[$codigo] = (object)[
                        "codigo" => $codigo,
                        "descricao" => $areaConhecimento->descricao,
                        "baseComum" => false,
                        "baseDiversificada" => false,
                        "disciplinas" => []
                    ];
                }

                foreach ($areaConhecimento->disciplinasArea as $oDisciplinaCursada) {
                    if (array_key_exists($oDisciplinaCursada->sDisciplina, $areasConhecimento[$codigo]->disciplinas)) {
                        continue;
                    }

                    $oDisciplina = new stdClass();
                    $oDisciplina->iDisciplina = $oDisciplinaCursada->iCadDisciplina;
                    $oDisciplina->sDisciplina = $oDisciplinaCursada->sDisciplina;

                    $areasConhecimento[$codigo]->baseComum = $oDisciplinaCursada->lBaseComum == true;
                    $areasConhecimento[$codigo]->baseDiversificada = $oDisciplinaCursada->lBaseComum == false;
                    $areasConhecimento[$codigo]->disciplinas[$oDisciplinaCursada->sDisciplina] = $oDisciplina;
                    $disciplinasComArea[] = $oDisciplinaCursada->sDisciplina;
                }
            }
        }

        return [$areasConhecimento, $disciplinasComArea];
    }

    private function imprimeDisciplinaEtapas(stdClass $oDisciplina, $aEtapa, $iLarguraDisciplina, $iAlturaLinha, $iFonteGrade, $iNumeroEtapasPagina, $porArea = false)
    {


        /**
         * Antes da impressao da Disciplina, validamos se o aluno tem avaliacoes em todas as etapas.
         * caso nao tenha nota em nenhuma etapa, a disciplina nao deverá ser impressa
         */
        $larguraEtapas = 135 / count($aEtapa);
        $lPossuiAvaliacaoNaDisciplina = $porArea;
        foreach ($aEtapa as $oEtapa) {
            if (isset($oEtapa->aDisciplinas[$oDisciplina->iDisciplina])) {
                if ($oEtapa->aDisciplinas[$oDisciplina->iDisciplina]->mAvaliacao != ""
                    || ($oEtapa->aDisciplinas[$oDisciplina->iDisciplina]->mAvaliacao == ""
                        && $oEtapa->aDisciplinas[$oDisciplina->iDisciplina]->sSituacao == 'AMPARADO')
                ) {
                    $lPossuiAvaliacaoNaDisciplina = true;
                    break;
                }
            }
        }

        if (!$lPossuiAvaliacaoNaDisciplina) {
            return;
        }

        $iAlturaCelula = $this->oPdf->NbLines($iLarguraDisciplina, $oDisciplina->sDisciplina);
        $iAlturaAproveitamento = $iAlturaLinha * $iAlturaCelula;

        $this->oPdf->SetFont("arial", "B", $iFonteGrade);

        $iYInicio = $this->oPdf->GetY();
        $content = "{$oDisciplina->sDisciplina}   ";
        $tamanhoString = $this->oPdf->GetStringWidth($content);

        $this->oPdf->CellAdapt($iFonteGrade, $iLarguraDisciplina, $iAlturaAproveitamento, $oDisciplina->sDisciplina, 1, 0, "L");
        $this->oPdf->SetXY($this->oPdf->GetLeftMargin() + $iLarguraDisciplina, $iYInicio);
        $this->oPdf->SetFont("arial", "", $iFonteGrade);

        foreach ($aEtapa as $oEtapa) {
            $sAproveitamento = ' - ';
            if (isset($oEtapa->aDisciplinas[$oDisciplina->iDisciplina])) {
                if (trim($oEtapa->aDisciplinas[$oDisciplina->iDisciplina]->mAvaliacao) != ''
                    || (trim($oEtapa->aDisciplinas[$oDisciplina->iDisciplina]->mAvaliacao) == ''
                        && $oEtapa->aDisciplinas[$oDisciplina->iDisciplina]->sSituacao == 'AMPARADO')
                ) {
                    $sAproveitamento = $oEtapa->aDisciplinas[$oDisciplina->iDisciplina]->mAvaliacao;

                    if ($oEtapa->aDisciplinas[$oDisciplina->iDisciplina]->sSituacao == 'AMPARADO') {
                        $sAproveitamento = 'AMPARO';
                    }
                }
            }
            if (isset($oEtapa) && property_exists($oEtapa, 'areasConhecimento') && count($oEtapa->areasConhecimento) > 0) {
                $sAproveitamento = "-";
            }
            $this->oPdf->Cell($larguraEtapas, $iAlturaAproveitamento, $sAproveitamento, 1, 0, "C");
        }

        $iNumeroEtapaImpressa = count($aEtapa);
        $this->oPdf->ln(); // Quebra linha ao imprimir disciplina mais etapas
    }

    private function imprimeAreaEtapas($areaConhecimento, $aEtapa, $iLarguraDisciplina, $iAlturaLinha, $iFonteGrade, $iNumeroEtapasPagina)
    {
        $lZebra = true;

        $iAlturaCelula = $this->oPdf->NbLines($iLarguraDisciplina, $areaConhecimento->descricao);
        $iAlturaAproveitamento = $iAlturaLinha * $iAlturaCelula;

        $this->oPdf->SetFont("arial", "B", $iFonteGrade);

        $iYInicio = $this->oPdf->GetY();
        $this->oPdf->MultiCell($iLarguraDisciplina, $iAlturaLinha, $areaConhecimento->descricao, 1, "C", $lZebra);
        $this->oPdf->SetXY($this->oPdf->GetLeftMargin() + $iLarguraDisciplina, $iYInicio);

        $this->oPdf->SetFont("arial", "", $iFonteGrade);
        $larguraEtapas = 135 / count($aEtapa);
        foreach ($aEtapa as $oEtapa) {
            $sAproveitamento = ' - ';

            if (isset($oEtapa->areasConhecimento[$areaConhecimento->codigo])) {
                if (trim($oEtapa->areasConhecimento[$areaConhecimento->codigo]->resultadoObtido) != '') {
                    $sAproveitamento = $oEtapa->areasConhecimento[$areaConhecimento->codigo]->resultadoObtido;
                    $oEtapa->areasConhecimento[$areaConhecimento->codigo]->sSituacao = '';
                    if ($oEtapa->areasConhecimento[$areaConhecimento->codigo]->sSituacao == 'AMPARADO') {
                        $sAproveitamento = 'AMPARO';
                    }
                }
            }
            $this->oPdf->Cell($larguraEtapas, $iAlturaAproveitamento, $sAproveitamento, 1, 0, "C", $lZebra);
        }
        $this->oPdf->ln(); // Quebra linha ao imprimir disciplina mais etapas
    }

    private function buscarDisciplinasAbreviadas()
    {
        $disciplinasAbreviadas = [];
        foreach ($this->aDadosOrganizados as $etapa) {
            foreach ($etapa->aDisciplinas as $disciplina) {
                if (strlen($disciplina->sNomeCompleto) >= 65) {
                    $disciplinasAbreviadas[$disciplina->iCadDisciplina] = sprintf(
                        '*%s: %s',
                        $disciplina->sAbrevDisciplina,
                        $disciplina->sNomeCompleto
                    );
                }
            }
        }

        return $disciplinasAbreviadas;
    }

    /**
     * @param $aDadosRelatorio
     * @param $aEtapasAnterior
     * @param $aEtapasPosterior
     * @return array
     * @throws Exception
     */
    private function reorganizarResumoEtapas($aDadosRelatorio, $aEtapasAnterior, $aEtapasPosterior)
    {
        $etapas = [];
        $etapas_somente_codigo = [];
        foreach ($aDadosRelatorio as $oDadosEtapa) {
            if ($oDadosEtapa->iAno != '-') {
                $etapas[$oDadosEtapa->iAno . '_' . $oDadosEtapa->iEtapa] = $oDadosEtapa;
                $etapas_somente_codigo[$oDadosEtapa->iEtapa] = true;
            }
        }

        if ($this->lExibirTodasEtapasCurso) {
            foreach ($aEtapasAnterior as $oEtapaAnterior) {
                $existeEtapaEquivalente = $this->verificarExisteEquivalenteAdicionada($oEtapaAnterior, $etapas);
                if (!$existeEtapaEquivalente && !array_key_exists($oEtapaAnterior->iEtapa, $etapas_somente_codigo)) {
                    $etapas[$oEtapaAnterior->iEtapa] = $oEtapaAnterior;
                }
            }
        }

        if ($this->lExibirTodasEtapasCurso) {
            foreach ($aEtapasPosterior as $oEtapaPosterior) {
                $existeEtapaEquivalente = $this->verificarExisteEquivalenteAdicionada($oEtapaPosterior, $etapas);
                if (!$existeEtapaEquivalente && !array_key_exists($oEtapaPosterior->iEtapa, $etapas_somente_codigo)) {
                    $etapas[$oEtapaPosterior->iEtapa] = $oEtapaPosterior;
                }
            }

            $ensinos = [];
            foreach ($etapas as $key => $etapa) {
                $ensinos[$etapa->ensino->getTipoEnsino()->getValue()] = $etapa->ensino;
            }
            $todasEtapasPorEnsino = $this->buscaEtapasPorEnsino($ensinos);

            foreach ($etapas as $key => $etapa) {
                $ensino = $etapa->ensino->getTipoEnsino()->getValue();
                if (array_key_exists($etapa->iEtapa, $todasEtapasPorEnsino[$ensino])) {
                    unset($todasEtapasPorEnsino[$ensino][$etapa->iEtapa]);
                }
            }
        }
        return $etapas;
    }

    /**
     * @throws DBException
     */
    private function organizaEJA($grade)
    {
        // Verifica se a grade tem uma ou duas bases
        $bases = [];
        foreach ($grade as $tiposBases) {
            foreach ($tiposBases->tiposBases as $tipoBases) {
                $bases[] = $tipoBases;
            }
        }
        $numeroBases = count($bases);

        if ($numeroBases == 1) {
            foreach ($grade as $tiposBases) {
                foreach ($tiposBases->tiposBases as &$tipoBases) {
                    $tipoBases = $this->reorganizaEtapas($tipoBases);
                }
            }
        } else if ($numeroBases > 1) {
            // Primeiro verifico as etapas registradas em cada base
            // e recupero o código das etapas de cada base
            $etapasPorBase = [];
            foreach ($grade as $tiposBases) {
                foreach ($tiposBases->tiposBases as $keyBase => &$tipoBases) {
                    $etapasRecuperadas = [];
                    $this->filtrarEtapasRegistradas($tipoBases);
                    foreach ($tipoBases as $etapa) {
                        if (property_exists($etapa, 'iEtapa')) {
                            $etapasRecuperadas[] = $etapa->iEtapa;
                        }
                    }
                    $etapasPorBase[$keyBase] = $etapasRecuperadas;
                }
            }

            // Verifica se as etapas entre as bases são as mesmas
            $isBaseIguais = $this->comparaBases($etapasPorBase);

            // Caso as bases não sejam iguais
            if (!$isBaseIguais) {
                foreach ($grade as $tiposBases) {
                    $this->ajustarBases($tiposBases->tiposBases);
                }
            }

            foreach ($grade as $tiposBases) {
                foreach ($tiposBases->tiposBases as &$etapas) {
                   $this->ordenaEtapasPorEquivalenciaEOrdem($etapas);

                   $primeiraEtapa = reset($etapas);
                   $isPrimeiraEtapa = $this->verificaPrimeiraEtapa($primeiraEtapa);

                   if (!$isPrimeiraEtapa) {
                       $this->preencheEtapasAnteriores($primeiraEtapa, $etapas);
                   }

                   /* Verifica se precisa preencher com etapas posteriores a última registrada */
                    $ultimaEtapa = end($etapas);
                    $isUltimaEtapa = $this->verificaUltimaEtapa($ultimaEtapa);

                    if (!$isUltimaEtapa) {
                        $this->preencheEtapasPosteriores($ultimaEtapa, $etapas);
                    }

                    $this->ordenaEtapasPorEquivalenciaEOrdem($etapas);
                }
            }

            $this->verificaBaseVazia($grade);

        }
        return $grade;
    }

    /**
     * @throws DBException
     */
    private function reorganizaEtapas($etapas)
    {
        $this->filtrarEtapasRegistradas($etapas);

        if (count($etapas) == 0) {
            return $etapas;
        }

        /* Verifica se precisa preencher com etapas anteriores a primeira registrada */
        $primeiraEtapa = reset($etapas);
        $isPrimeiraEtapa = $this->verificaPrimeiraEtapa($primeiraEtapa);

        if (!$isPrimeiraEtapa) {
            $this->preencheEtapasAnteriores($primeiraEtapa, $etapas);
        }

        /* Verifica se precisa preencher com etapas posteriores a última registrada */
        $ultimaEtapa = end($etapas);
        $isUltimaEtapa = $this->verificaUltimaEtapa($ultimaEtapa);

        if (!$isUltimaEtapa) {
            $this->preencheEtapasPosteriores($ultimaEtapa, $etapas);
        }

        $this->ordenaEtapasPorEquivalenciaEOrdem($etapas);

        return $etapas;
    }

    private function verificaPrimeiraEtapa($etapa)
    {
        if ($etapa->ordem == '1') {
            return true;
        }
        return false;
    }

    private function verificaUltimaEtapa($ultimaEtapa)
    {
        $ordemUltima = null;
        foreach ($this->estadoOriginalTodasEtapas as $todasEtapas) {
            foreach ($todasEtapas as $etapa) {
                if ($etapa->ordem > $ordemUltima) {
                    $ordemUltima = $etapa->ordem;
                }
            }
            if ($ultimaEtapa->ordem >= $ordemUltima) {
                return true;
            }
        }
        return false;
    }

    /* Função criada para preencher etapas antes da primeira registrada, caso a primeira
    etapa registrada não tenha a ordem igual a 1.
    REGRAS:
    1 - As etapas anteriores devem pertencer ao mesmo ensino da primeira etapa registrada

    Nota: No foreach da função eu faço a verificação se a etapa verificada pertence ao mesmo
    ensino e possui ordem anterior a primieira etapa registrada. Além disso, faço a verificação
    se a etapa já está na lista, evitando duplicação de etapas que não tenham registros de disciplinas.
    */
    /**
     * @throws DBException
     */
    private function preencheEtapasAnteriores($primeiraEtapa, array &$etapas)
    {
        $codEnsinoPrimeiraEtapaReg = $primeiraEtapa->ensino->getCodigo();
        $ordemPrimeiraEtapaReg = $primeiraEtapa->ordem;

        foreach ($this->estadoOriginalTodasEtapas as $ensinos) {
            foreach ($ensinos as $etapaOriginal) {
                $codEnsinoEtapaOriginal = $etapaOriginal->ensino->getCodigo();
                $ordemEtapaOriginal = $etapaOriginal->ordem;
                $isMesmoEnsino = $codEnsinoPrimeiraEtapaReg == $codEnsinoEtapaOriginal;
                $isEtapaAnterior = $ordemEtapaOriginal < $ordemPrimeiraEtapaReg;
                if ($isMesmoEnsino && $isEtapaAnterior) {
                    $etapas[] = $etapaOriginal;
                }
            }
        }

        $this->ordenaEtapasPorEquivalenciaEOrdem($etapas);
    }

    /**
     * @throws DBException
     */
    private function preencheEtapasPosteriores($ultimaEtapa, array &$etapas)
    {
        $codEnsinoUltimaEtapaReg = $ultimaEtapa->ensino->getCodigo();
        $ordemUltimaEtapaReg = $ultimaEtapa->ordem;

        foreach ($this->estadoOriginalTodasEtapas as $todasEtapas) {
            foreach ($todasEtapas as $etapaOriginal) {
                $codEnsinoEtapaOriginal = $etapaOriginal->ensino->getCodigo();
                $ordemEtapaOriginal = $etapaOriginal->ordem;
                $isMesmoEnsino = $codEnsinoUltimaEtapaReg == $codEnsinoEtapaOriginal;
                $isEtapaPosterior = $ordemEtapaOriginal > $ordemUltimaEtapaReg;
                if ($isMesmoEnsino && $isEtapaPosterior) {
                    $etapas[] = $etapaOriginal;
                }
            }
        }

    }

    private function filtrarEtapasRegistradas(&$etapas)
    {
        $etapas = array_filter($etapas, function($etapa) {
            return property_exists($etapa, 'aDisciplinas');
        });
    }

    private function comparaBases(array $etapasPorBase)
    {
        $keyBases = array_keys($etapasPorBase);
        $isBasesIguais = true;

        for ($i = 0; $i < count($keyBases) - 1; $i++) {
            for ($j = $i + 1; $j < count($keyBases); $j++) {
                if (!$this->comparaEtapasBases($etapasPorBase[$keyBases[$i]], $etapasPorBase[$keyBases[$j]])) {
                    $isBasesIguais = false;
                    break 2; // Quebra os dois níveis do loop
                }
            }
        }

        return $isBasesIguais;
    }

    private function comparaEtapasBases($baseA, $baseB)
    {
        if (count($baseA) != count($baseB)) {
            return false;
        }
        if (!empty(array_diff($baseA, $baseB)) || !empty(array_diff($baseB, $baseA))) {
            return false;
        }
        return true;
    }

    private function ajustarBases(&$etapasGradePorBase)
    {
        $freqEtapasPorBase = [];
        foreach ($etapasGradePorBase as $key => $baseGrade) {
            $freqEtapasPorBase[$key] = $this->calcularFrequencia($baseGrade);
        }

        $frequenciasCombinadas = [];
        foreach ($freqEtapasPorBase as $frequencias) {
            foreach ($frequencias as $codigo => $freq) {
                if (!isset($frequenciasCombinadas[$codigo])) {
                    $frequenciasCombinadas[$codigo] = 0;
                }
                $frequenciasCombinadas[$codigo] = max($frequenciasCombinadas[$codigo], $freq);
            }
        }

        // Ajustar as bases com base nas frequências combinadas
        foreach ($etapasGradePorBase as &$base) {
            // Calcular a frequência atual da base
            $frequenciaBaseAtual = $this->calcularFrequencia($base);

            foreach ($frequenciasCombinadas as $codigo => $frequenciaNecessaria) {
                if (!isset($frequenciaBaseAtual[$codigo])) {
                    $frequenciaBaseAtual[$codigo] = 0;
                }

                // Adicionar os códigos que estão faltando
                while ($frequenciaBaseAtual[$codigo] < $frequenciaNecessaria) {
                    foreach ($this->estadoOriginalTodasEtapas as $etapas) {
                        foreach ($etapas as $etapa) {
                            if ($etapa->iEtapa == $codigo) {
                                $base[] = $etapa;
                            }
                        }
                    }
                    $frequenciaBaseAtual[$codigo]++;
                }
            }
        }
    }

    private function calcularFrequencia($etapasGrade)
    {
        $frequencia = [];
        foreach ($etapasGrade as $etapa) {
            if (!isset($frequencia[$etapa->iEtapa])) {
                $frequencia[$etapa->iEtapa] = 0;
            }
            $frequencia[$etapa->iEtapa]++;
        }
        return $frequencia;
    }

    private function verificarEquivalenciaEnsino($ensinoA, $ensinoB)
    {
        if ($ensinoA->ensino->getCodigo() == 6 && $ensinoB->ensino->getCodigo() == 1) {
            return true;
        }
        if ($ensinoA->ensino->getCodigo() == 1 && $ensinoB->ensino->getCodigo() == 6) {
            return true;
        }
        return false;
    }

    /**
     * @param $etapaVerificar
     * @param $etapas
     * @return bool
     * @throws DBException
     */
    private function verificarExisteEquivalenteAdicionada($etapaVerificar, $etapas)
    {
        $etapa = new Etapa($etapaVerificar->iEtapa);
        $etapasEquivalentes = $etapa->buscaEtapaEquivalente();
        $existeEtapaEquivalente = false;

        foreach ($etapasEquivalentes as $etapaEquivalente) {
            foreach ($etapas as $etapaAdicionada) {
                if ($etapaEquivalente->getCodigo() === $etapaAdicionada->iEtapa) {
                    $existeEtapaEquivalente = true;
                }
            }
            if (array_key_exists($etapaEquivalente->getCodigo(), $etapas)) {
                $existeEtapaEquivalente = true;
            }
        }

        foreach ($etapas as $etapaAdicionada) {
            if ($etapaAdicionada->iEtapa === $etapaVerificar->iEtapa) {
                $existeEtapaEquivalente = true;
            }
        }

        return $existeEtapaEquivalente;
    }

    private function calcularAlturaGrade($etapas)
    {
        $disciplinas = [];
        foreach ($etapas as $etapa) {
            if (property_exists($etapa, 'aDisciplinas')) {
                foreach ($etapa->aDisciplinas as $disciplina) {
                    $disciplinas[] = $disciplina->iCadDisciplina;
                }
            }
        }
        $alturaLinha = RelatorioHistoricoEscolarRetrato::ALTURA_LINHA;
        $quantidadeDisciplinas = count(array_unique($disciplinas));
        return ($quantidadeDisciplinas + 2) * $alturaLinha;
    }

    private function sortArrayEtapasHelper($anoA, $anoB, $ordemA, $ordemB, $ordemEnsinoA, $ordemEnsinoB, $verificaEquivalencia = false)
    {
        if ($ordemEnsinoA == $ordemEnsinoB) {
            $anosComNumeros = preg_match("/^[0-9]+$/", $anoA) && preg_match("/^[0-9]+$/", $anoB);
            if ($anosComNumeros || $verificaEquivalencia) {
                if ($anoA == $anoB) {
                    return $ordemA > $ordemB;
                }
                return $anoA > $anoB;
            }
                return $ordemA > $ordemB;
        }
        return $ordemEnsinoA > $ordemEnsinoB;
    }

    private function buscaEtapasPorEnsino($ensinos)
    {

        $etapasPorEnsino = [];
        foreach ($ensinos as $key => $ensino) {
            $etapas = EtapaRepository::getEtapasEnsino($ensino);
            foreach ($etapas as $etapa) {
                $etapasPorEnsino[$key][$etapa->getCodigo()] = $etapa;
            }
        }
        $etapasPorTipoEnsino = [];
        foreach ($etapasPorEnsino as $ensino) {
            foreach ($ensino as $e) {
                $etapasPorTipoEnsino[$e->getEnsino()->getTipoEnsino()->getValue()][$e->getCodigo()] = $this->criarObjetoEtapa($e);
            }
        }
        return $etapasPorTipoEnsino;
    }

    /**
     * @throws DBException
     */
    private function sortArrayEtapasPorOrdem($grade)
    {
        foreach ($grade as $tipoEnsino) {
            foreach ($tipoEnsino->tiposBases as $codTipoBase => $etapas) {
                $listaEtapas = [];
                foreach ($etapas as $etapa) {
                    $listaEtapas[] = $etapa;
                }
                $this->ordenaEtapasPorEquivalenciaEOrdem($listaEtapas);
                $tipoEnsino->tiposBases[$codTipoBase] = $listaEtapas;
            }
        }
        return $grade;
    }
    
    // Ordena as etapas pelo atributo ordem e equivalência das etapas
    /**
     * @throws DBException
     */
    private function ordenaEtapasPorEquivalenciaEOrdem(&$listaEtapas)
    {
        // Esse loop serve para verificar as equivalências das etapas e pegar a ordem maior entre as etapas equivalentes
        foreach ($listaEtapas as $etapa) {
            $etapaVerificar = new Etapa($etapa->iEtapa);
            $etapasEquivalentes = $etapaVerificar->buscaEtapaEquivalente();

            if (count($etapasEquivalentes) > 0) {
                foreach ($etapasEquivalentes as $etapaEquivalente) {
                        if ($etapaEquivalente->getOrdem() > $etapa->ordem) {
                            $etapa->ordem = $etapaEquivalente->getOrdem();
                    }
                }
            }
        }

        usort($listaEtapas, function($a, $b) {
            $tipoEnsinoA = $a->ensino->getTipoEnsino()->getValue();
            $tipoEnsinoB = $b->ensino->getTipoEnsino()->getValue();

            if ($tipoEnsinoA < $tipoEnsinoB) {
                return -1;
            } else if ($tipoEnsinoA > $tipoEnsinoB) {
                return 1;
            } else if ($tipoEnsinoA == $tipoEnsinoB) {
                if ($a->ordem > $b->ordem) {
                    return 1;
                }
                if ($a->ordem < $b->ordem) {
                    return -1;
                }
                if ($a->ordem == $b->ordem) {
                    if ((property_exists($a, "aDisciplinas")) && (property_exists($b, "aDisciplinas"))) {
                        $disciplinaA = reset($a->aDisciplinas);
                        $disciplinaB = reset($b->aDisciplinas);
                        if ($disciplinaA->iAno > $disciplinaB->iAno) {
                            return 1;
                        } else if ($disciplinaA->iAno < $disciplinaB->iAno) {
                            return -1;
                        }
                        return 0;
                    }
                    return 0;
                }
            }
            return 0;
        });
    }

    private function trataValorParaNaoExibirZero($valor)
    {
        $valorTemp = trim($valor);
        if ($valorTemp <= 0) {return "";}
        return $valorTemp;
    }

    private function verificaBaseVazia(&$grade)
    {
        foreach ($grade as &$tipoEnsino) {
            $chavesParaRemover = [];

            foreach ($tipoEnsino->tiposBases as $key => &$etapas) {
                $isBaseVazia = true;
                foreach ($etapas as $etapa) {
                    if (property_exists($etapa, 'aDisciplinas')) {
                        $isBaseVazia = false;
                        break;
                    }
                }

                if ($isBaseVazia) {
                    $chavesParaRemover[] = $key;
                }
            }
            foreach ($chavesParaRemover as $chave) {
                unset($tipoEnsino->tiposBases[$chave]);
            }
        }
    }
}
