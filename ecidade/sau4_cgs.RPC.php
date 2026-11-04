<?php
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

use ECidade\Core\Helpers\DataNascimentoHelper;
use ECidade\Saude\Ambulatorial\Repository\CgsAuditoriaRepository;
use ECidade\Saude\Ambulatorial\Service\CgsAuditoriaService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));


$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->iStatus = 1;
$oRetorno->sMessage = '';

db_putsession('DB_desativa_trigger_endereco', '1');
define("MSG_SAU4_CGSRPC", "saude.ambulatorial.sau4_cgsRPC.");

try {
    db_inicio_transacao();

    switch ($oParam->sExecucao) {
        case "buscarCgsUndPorCodigoCgsUnd":
            $dao = new cl_cgs_und();
            $postgresObject = db_query($dao->sql_query($oParam->cgsund));

            if (pg_num_rows($postgresObject) == 0) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . 'cgs_inexistente'));
            }
            $oRetorno->cgs_und = pg_fetch_assoc($postgresObject, 0);
            break;

        case "buscarNumeroCgsPorCns":
            $dao = new cl_cgs_cartaosus();
            $postgresObject = db_query(
                $dao->sql_query_file(null, 's115_i_cgs', null, "s115_c_cartaosus = '{$oParam->cns}'")
            );

            if (pg_num_rows($postgresObject) == 0) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . 'cns_inexistente'));
            }

            $oRetorno->numeroCgs = pg_fetch_row($postgresObject, 0)[0];

            break;

        case "buscarNumeroCgsPorCpf":
            $dao = new cl_cgs_und();
            $postgresObject = db_query(
                $dao->sql_query_etnia(null, 'z01_i_numcgs', null, "z01_v_cgccpf = '{$oParam->cpf}'")
            );

            if (pg_num_rows($postgresObject) == 0) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . 'cpf_inexistente'));
            }

            $oRetorno->numeroCgs = pg_fetch_row($postgresObject, 0)[0];

            break;

        case "buscarCgsPorCpfFiltrandoCgs":
            $dao = new cl_cgs_und();

            $cpf = str_replace('-', '', str_replace('.', '', $oParam->cpf));

            $filtro_cgs = $oParam->iCgs ? "and z01_i_numcgs <> '{$oParam->iCgs}'" : "";

            $postgresObject = db_query(
                $dao->sql_query_etnia(null, 'z01_i_numcgs', null, "z01_v_cgccpf = '{$cpf}' $filtro_cgs")
            );

            $oRetorno->cpfValido = pg_num_rows($postgresObject) == 0;

            break;

        case "buscarDadosCgs":

            if (!isset($oParam->iCgs) || empty($oParam->iCgs)) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . "informe_cgs"));
            }

            $oCgs = new Cgs($oParam->iCgs);
            $oDataAtual = new DBDate(date("Y-m-d"));
            $sIdadeCompleta = '';
            $sDataNascimento = '';
            $idadeEmAnos = '';

            if (isset($oParam->lValidaCgs) && $oCgs->getCodigo() == null) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . 'cgs_inexistente'));
            }

            if ($oCgs->getCodigo() != null and !empty($oCgs->getDataNascimento())) {
                $iQuantidadeDias = DBDate::calculaIntervaloEntreDatas($oDataAtual, $oCgs->getDataNascimento(), 'd');
                $sIdadeCompleta = urlencode(DBDate::getIdadeCompleta($iQuantidadeDias));
                $sDataNascimento = $oCgs->getDataNascimento()->convertTo(DBDate::DATA_PTBR);
                $idadeEmAnos = DBDate::calculaIntervaloEntreDatas($oDataAtual, $oCgs->getDataNascimento(), 'y');
            }

            $oRetorno->oCgs = new stdClass();
            $oRetorno->oCgs->iCgs = $oCgs->getCodigo();
            $oRetorno->oCgs->sNome = urlencode($oCgs->getNome());
            $oRetorno->oCgs->sSexo = urlencode($oCgs->getSexo());
            $oRetorno->oCgs->dtNascimento = $sDataNascimento;
            $oRetorno->oCgs->sIdadeCompleta = $sIdadeCompleta;
            $oRetorno->oCgs->sNomeMae = urlencode($oCgs->getNomeMae());
            $oRetorno->oCgs->sNomePai = urlencode($oCgs->getNomePai());
            $oRetorno->oCgs->sEndereco = urlencode($oCgs->getEndereco());
            $oRetorno->oCgs->iNumero = $oCgs->getNumero();
            $oRetorno->oCgs->sComplemento = urlencode($oCgs->getComplemento());
            $oRetorno->oCgs->sBairro = urlencode($oCgs->getBairro());
            $oRetorno->oCgs->sCep = urlencode($oCgs->getCep());
            $oRetorno->oCgs->sMunicipio = urlencode($oCgs->getMunicipio());
            $oRetorno->oCgs->sUF = urlencode($oCgs->getUF());
            $oRetorno->oCgs->sEstadoCivil = urlencode($oCgs->getEstadoCivil());
            $oRetorno->oCgs->sEmail = $oCgs->getEmail();
            $oRetorno->oCgs->sCelular = $oCgs->getCelular();
            $oRetorno->oCgs->sRaca = $oCgs->getRaca();
            $oRetorno->oCgs->sPis = $oCgs->getPis();
            $oRetorno->oCgs->iNaturalidade = $oCgs->getNaturalidade();
            $oRetorno->oCgs->iPaisOrigem = $oCgs->getPaisOrigem();
            $oRetorno->oCgs->sMunicipioNascimento = urlencode($oCgs->getMunicipioNascimento());
            $oRetorno->oCgs->sUfNascimento = $oCgs->getUfNascimento();
            $oRetorno->oCgs->sIbgeNascimento = $oCgs->getIbgeNascimento();
            $oRetorno->oCgs->iEscolaridade = $oCgs->getEscolaridade();
            $oRetorno->oCgs->sDesconheceMae = $oCgs->desconheceMae() ? 't' : 'f';
            $oRetorno->oCgs->iCodigoCartaoSus = '';
            $oRetorno->oCgs->sCartaoSus = '';
            $oRetorno->oCgs->cpf = $oCgs->getCpf();
            $oRetorno->oCgs->idadeEmAnos = $idadeEmAnos;

            foreach ($oCgs->getCartaoSus() as $oCartaoSus) {
                if ($oCartaoSus->sTipoCartaoSus != 'D') {
                    continue;
                }

                $oRetorno->oCgs->iCodigoCartaoSus = $oCartaoSus->iCodigo;
                $oRetorno->oCgs->sCartaoSus = $oCartaoSus->sCartaoSus;
            }

            break;

        case 'buscaExames':

            if (!isset($oParam->iCgs) || empty($oParam->iCgs)) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . "informe_cgs"));
            }

            $oCgs = CgsRepository::getByCodigo($oParam->iCgs);

            $aExames = array();
            foreach ($oCgs->getRequisicoesExame() as $oRequisicao) {
                foreach ($oRequisicao->getRequisicoesDeExames() as $oExamesRequisicao) {
                    $oDados = new stdClass();
                    $oDados->iRequisicao = $oRequisicao->getCodigo();
                    $oDados->iItem = $oExamesRequisicao->getCodigo();
                    $oDados->sExame = urlencode($oExamesRequisicao->getExame()->getNome());
                    $oDados->sSituacao = urlencode($oExamesRequisicao->getDescricaoSituacao());
                    $oDados->dtRequisicao = $oRequisicao->getData()->getDate();
                    $aExames[] = $oDados;
                }
            }

            $oRetorno->aExames = $aExames;

            break;

        /**
         * Retorna uma coleção com os documentos do CGS
         */
        case 'documentosCgs':

            if (empty($oParam->iCgs)) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . 'informe_cgs'));
            }
            $oRetorno->aDocumentos = getDocumentos($oParam->iCgs);
            break;

        /**
         * Realiza o vínculo do CGS com um documento e seus valores salvos
         */
        case 'vinculaCgsDocumento':

            if (empty($oParam->iCgs)) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . 'informe_cgs'));
            }

            if (empty($oParam->iDocumento)) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . 'documento_nao_informado'));
            }

            $oCgs = CgsRepository::getByCodigo($oParam->iCgs);
            $oCgs->salvarCgsDocumento($oParam->iDocumento);

            $oRetorno->sMessage = _M(MSG_SAU4_CGSRPC . 'documento_salvo');

            break;

        case 'getDadosCadastroAlteracao':

            if (empty($oParam->cgs)) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . 'informe_cgs'));
            }

            $campos = '
        s115_c_cartaosus, s115_i_codigo, z01_v_nome, z01_v_mae, z01_v_pai, z01_v_sexo, z01_c_raca, z01_d_nasc,
        z01_i_nacion, z01_c_naturalidade, z01_v_uf, z01_codigoibge, z01_i_fatorrh, z01_b_inativo,
        z01_i_tiposangue, z01_c_municipio, s201_etnia, s200_descricao, z01_i_paisorigem, z01_registromunicipio,
        z01_v_municnasc, z01_v_ufnasc, z01_codigoibgenasc, z01_d_falecimento,z01_b_faleceu, z01_v_email,
        z01_v_telef, z01_v_telcel, z01_v_fax, db76_sequencial, z01_o_oid, z01_c_foto, z01_numcgm, z01_nome,
        ed47_i_codigo, ed47_v_nome, ov02_sequencial, ov02_nome, rh70_sequencial, rh70_descr, z01_i_estciv,
        sd35_i_microarea, sd35_i_codigo, z01_c_nomeresp, z01_t_obs, z01_i_escolaridade, z01_c_bolsafamilia,
        z01_v_cgccpf, z01_d_ultalt, z01_d_cadast, z01_nome_social, z01_v_ident, z01_distritosanitario,sd114_unidadesorigem
      ';

            //plugin ESF operation#0 - adicionando campo equipe_id na query via plugin ESF

            $oDao = new cl_cgs_und();
            $sSqlDadosCGS = $oDao->sql_query_cadastro($oParam->cgs, $campos);

            $resultadoQuery = db_query($sSqlDadosCGS);

            $oCgsAud = CgsAuditoriaRepository::getUltimoRegistroByCgs($oParam->cgs);

            if (!$resultadoQuery) {
                throw new DBException("NÃ£o foi possivel recuperar os dados do CGS informado({$oParam->cgs})");
            }

            $oRetorno->informacoesCGS = (object)array(
                "dados_pessoais" => getDadosPessoais($resultadoQuery),
                "contato" => getDadosContato($resultadoQuery),                
                "biometria" => getDadosBiometricos($resultadoQuery),
                "outros_dados" => getOutrosDados($resultadoQuery),
                "dados_sistema" => getDadosSistema($oCgsAud),
            );
        case 'getDadosCadastroNovo':

            $oRetorno->informacoesPadrao = getDadosPadrao();
            break;
        case 'salvarCgs':
            $oParam = JSON::create()->parse(str_replace("\\", "", $_POST["json"]));

            new DataNascimentoHelper($oParam->dados_pessoais->dataNascimento);

            $oCgs = CgsRepository::getByCodigo($oParam->iCgs);

            $oCgs->salvar($oParam);

            $CgsAuditoriaService = new CgsAuditoriaService(
                $oCgs->getCodigo(),
                $oParam->iCgs
            );

            $CgsAuditoriaService->salvar();

            $oRetorno->iCgs = $oCgs->getCodigo();
            $oRetorno->sMessage = "Dados do <b><span style='font-size: 16px'>CGS " . $oCgs->getCodigo(
                ) . "</span></b> salvos com sucesso.";

            break;

        case 'excluirCgs':

            if (empty($oParam->iCgs)) {
                throw new ParameterException(_M(MSG_SAU4_CGSRPC . 'informe_cgs'));
            }

            $oCgs = CgsRepository::getByCodigo($oParam->iCgs);
            $oCgs->excluir();

            $oRetorno->sMessage = urlencode(_M(MSG_SAU4_CGSRPC . 'cgs_excluido'));

            break;

        case "validarCGS":

            $oRetorno->valido = Cgs::validar(
                new Cgs($oParam->cgs)
            );
            break;
    }

    db_fim_transacao(false);
} catch (Exception $eErro) {
    db_fim_transacao(true);
    $oRetorno->iStatus = 2;
    $oRetorno->sMessage = urlencode($eErro->getMessage());
}

$oRetorno->erro = $oRetorno->iStatus == 2;
echo $oJson->encode($oRetorno);

/**
 * Funções
 */
function getDadosPessoais($rsResultaoCGS)
{
    return db_utils::makeFromRecord($rsResultaoCGS, function ($oResultado) {
        $oRetorno = new stdClass();
        $oResultado = DBString::utf8_encode_all($oResultado);

        $oRetorno->codigo_cartao_sus = trim($oResultado->s115_i_codigo);
        $oRetorno->cadastroInativo = $oResultado->z01_b_inativo;
        $oRetorno->cns = trim($oResultado->s115_c_cartaosus);
        $oRetorno->nome = trim($oResultado->z01_v_nome);
        $oRetorno->nome_social = trim($oResultado->z01_nome_social);
        $oRetorno->nome_mae = trim($oResultado->z01_v_mae);
        $oRetorno->nome_pai = trim($oResultado->z01_v_pai);
        $oRetorno->sexo = trim($oResultado->z01_v_sexo);
        $oRetorno->raca = trim($oResultado->z01_c_raca);
        $oRetorno->codigo_etnia = trim($oResultado->s201_etnia);
        $oRetorno->label_etnia = trim($oResultado->s200_descricao);
        $oRetorno->fator_rh = trim($oResultado->z01_i_fatorrh);
        $oRetorno->tipo_sanguineo = trim($oResultado->z01_i_tiposangue);
        $oRetorno->data_nascimento = trim($oResultado->z01_d_nasc);
        $oRetorno->nacionalidade = trim($oResultado->z01_i_nacion);
        $oRetorno->paisOrigem = trim($oResultado->z01_i_paisorigem);
        $oRetorno->cgsMunicipio = $oResultado->z01_registromunicipio;

        $oRetorno->municipio_nascimento = trim($oResultado->z01_v_municnasc);
        $oRetorno->uf_nascimento = trim($oResultado->z01_v_ufnasc);
        $oRetorno->codigo_ibge_nascimento = $oResultado->z01_codigoibgenasc;
        $oRetorno->cpf = $oResultado->z01_v_cgccpf;
        $oRetorno->rg = $oResultado->z01_v_ident;
        $oRetorno->distritoSanitario = $oResultado->z01_distritosanitario;

        $oRetorno->informacao_obito = $oResultado->z01_b_faleceu;
        $oRetorno->data_obito = trim($oResultado->z01_d_falecimento);

        $oRetorno->data_cadastro = trim($oResultado->z01_d_cadast);
        $oRetorno->data_alteracao = trim($oResultado->z01_d_ultalt);
        $oRetorno->unidade_origem = trim($oResultado->sd114_unidadesorigem);

        return $oRetorno;
    }, 0);
}

function getDadosContato($rsResultaoCGS)
{
    return db_utils::makeFromRecord($rsResultaoCGS, function ($oResultado) {
        $oRetorno = new stdClass();
        $oRetorno->email = trim($oResultado->z01_v_email);
        $oRetorno->telefone_fixo = trim($oResultado->z01_v_telef);
        $oRetorno->telefone_celular = trim($oResultado->z01_v_telcel);
        $oRetorno->fax = trim($oResultado->z01_v_fax);
        $oRetorno->endereco = $oResultado->db76_sequencial;
        return $oRetorno;
    }, 0);
}

function getDadosBiometricos($rsResultaoCGS)
{
    return db_utils::makeFromRecord($rsResultaoCGS, function ($oResultado) {
        $oRetorno = new stdClass();
        $oRetorno->foto_oid = trim($oResultado->z01_o_oid);
        $oRetorno->foto_caminho = trim($oResultado->z01_c_foto);
        return $oRetorno;
    }, 0);
}

function getOutrosDados($rsResultaoCGS)
{
    return db_utils::makeFromRecord($rsResultaoCGS, function ($oResultado) {
        $oRetorno = new stdClass();
        $oRetorno->codigo_cgm = trim($oResultado->z01_numcgm);
        $oRetorno->label_cgm = urlencode(trim($oResultado->z01_nome));
        $oRetorno->codigo_aluno = trim($oResultado->ed47_i_codigo);
        $oRetorno->label_aluno = urlencode(trim($oResultado->ed47_v_nome));
        $oRetorno->codigo_cidadao = trim($oResultado->ov02_sequencial);
        $oRetorno->label_cidadao = urlencode(trim($oResultado->ov02_nome));
        $oRetorno->codigo_ocupacao = trim($oResultado->rh70_sequencial);
        $oRetorno->label_ocupacao = urlencode(trim($oResultado->rh70_descr));
        $oRetorno->estado_civil = trim($oResultado->z01_i_estciv);
        $oRetorno->microarea = trim($oResultado->sd35_i_microarea);
        $oRetorno->familia = trim($oResultado->sd35_i_codigo);
        $oRetorno->responsavel = trim($oResultado->z01_c_nomeresp);
        $oRetorno->observacoes = trim($oResultado->z01_t_obs);
        $oRetorno->escolaridade = trim($oResultado->z01_i_escolaridade);
        $oRetorno->bolsa_familia = trim($oResultado->z01_c_bolsafamilia);
        //plugin ESF operation#2 - adicionando novo atributo equipe ao objeto via plugin ESF
        return $oRetorno;
    }, 0);
}

function getDadosSistema($oCgsAud)
{
    $oRetorno = new stdClass();

    if ($oCgsAud === null) {
        $oRetorno->usuario = '';
        return $oRetorno;
    }

    $oRetorno->usuario = trim($oCgsAud->getUsuario());
    return $oRetorno;
}

function getDocumentos($iCodigoCGS)
{
    $aDocumentos = array();
    $oCgs = CgsRepository::getByCodigo($iCodigoCGS);

    foreach ($oCgs->getDocumentos() as $oDocumentoBase) {
        $oDadosDocumento = new stdClass();
        $oDadosDocumento->codigoDocumento = $oDocumentoBase->getCodigo();
        $oDadosDocumento->documento = utf8_encode($oDocumentoBase->getDescricao());
        $oDadosDocumento->documentoValor = $oDocumentoBase->getDocumento();

        $aDocumentos[] = $oDadosDocumento;
    }

    return $aDocumentos;
}

function getDadosPadrao()
{
    /**
     * Dados da microarea e familia
     */
    $oDaoFamiliaMicroArea = new cl_familiamicroarea();
    $sSqlDadosFamilia = $oDaoFamiliaMicroArea->sql_query(null, "*", "sd34_v_descricao asc, sd33_v_descricao asc");
    $rsDadosFamilias = db_query($sSqlDadosFamilia);

    if (!$rsDadosFamilias) {
        throw new DBException(_M(MSG_SAU4_CGSRPC . 'erro_buscar_dados_familia'));
    }

    $aMicroAreas = array();
    $aFamilias = array();

    /**
     * Separa a familia da microarea
     */
    db_utils::makeCollectionFromRecord($rsDadosFamilias, function ($oDados) use (&$aMicroAreas, &$aFamilias) {
        $aMicroAreas[$oDados->sd34_i_codigo] = array(
            "codigo_microarea" => $oDados->sd34_i_codigo,
            "label_microarea" => utf8_encode($oDados->sd34_v_descricao)
        );

        $aFamilias[$oDados->sd35_i_codigo] = array(
            "codigo_microarea" => $oDados->sd34_i_codigo,
            "codigo_familia" => trim($oDados->sd35_i_codigo),
            "label_familia" => urlencode($oDados->sd33_v_descricao),
        );
    });

    foreach ($aFamilias as $oFamilia) {
        $aMicroAreas[$oFamilia["codigo_microarea"]]["familias"][] = $oFamilia;
    }

    /**
     * Paises de origem
     */
    $oDaoPais = new cl_pais();
    $sCamposPais = "ed228_i_codigo, ed228_c_descr";
    $sSqlPais = $oDaoPais->sql_query_file("", $sCamposPais, "ed228_c_descr");
    $rsPais = db_query($sSqlPais);

    if (!is_resource($rsPais)) {
        throw new DBException(_M(MSG_SAU4_CGSRPC . 'erro_buscar_pais_origem'));
    }

    $aPaises = db_utils::makeCollectionFromRecord($rsPais, function ($oResultado) {
        $oRetorno = new stdClass();
        $oRetorno->codigo_pais = $oResultado->ed228_i_codigo;
        $oRetorno->label_pais = trim($oResultado->ed228_c_descr);

        return $oRetorno;
    });

    /**
     * Distritos sanitários
     */
    $daoDistritosSanitarios = new cl_sau_distritosanitario;
    $sqlCampos = 's153_i_codigo, s153_c_descr';
    $sqlWhere = 's153_ativo = true';
    $sqlOrdem = 's153_c_descr ASC';
    $sqlDistritos = $daoDistritosSanitarios->sql_query_file(null, $sqlCampos, $sqlOrdem, $sqlWhere);
    $rsSqlDistritos = db_query($sqlDistritos);
    $distritosSanitarios = [];

    if (pg_num_rows($rsSqlDistritos) > 0) {
        $distritosSanitarios = db_utils::makeCollectionFromRecord($rsSqlDistritos, function ($distrito) {
            $retorno = new stdClass();
            $retorno->codigo = $distrito->s153_i_codigo;
            $retorno->descricao = $distrito->s153_c_descr;
            return $retorno;
        });
    }

    sort($aMicroAreas);
    sort($aFamilias);

    return (object)array(
        'microareas' => $aMicroAreas,
        'paisOrigem' => $aPaises,
        'distritosSanitarios' => $distritosSanitarios
    );
}
