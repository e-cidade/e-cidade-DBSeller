<?php

namespace ECidade\RecursosHumanos\ESocial\Transformer;

use ECidade\RecursosHumanos\ESocial\Model\Configuracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocialEnvio;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocialEnvioStatus;
use ECidade\RecursosHumanos\ESocial\Integracao\Recurso;
use ECidade\V3\Extension\Registry;
use ECidade\RecursosHumanos\ESocial\Mapeadores\Tabelas\CategoriaCNH;

/**
 * Class S2205
 * @package ECidade\RecursosHumanos\ESocial\Transformer
 */
class S2205
{
    /**
     * @var string $matricula
     */
    private $matricula;

    /**
     * @var mixed $dados
     */
    private $dados;

    /**
     * @var integer $idEvento
     */
    private $idEvento;

    /**
     * S2205 constructor.
     * @param $matricula
     */
    public function __construct($matricula)
    {
        $this->matricula = $matricula;
    }

    /**
     * seta idEvento
     *
     * @param $idEvento
     */
    public function setIdEvento($idEvento)
    {
        $this->idEvento = $idEvento;
    }

    /**
     * Retorna idEvento
     *
     * @return int
     */
    public function getIdEvento()
    {
        return $this->idEvento;
    }

    /**
     * @throws \ECidade\RecursosHumanos\ESocial\Integracao\ESocialContextExceptionException
     */
    public function buscarDados()
    {

        $oESocial    = new ESocial(Registry::get('app.config'), Recurso::CONSULTA_RECIBO);
        $oEmpregador = $this->getEmpregador();

        $params = new \stdClass();

        $params->idEvento            = $this->getIdEvento();
        $params->idReferencia        = $this->matricula;
        $params->inscricaoEmpregador = $oEmpregador->cnpj;

        $oESocial->setDados($params);
        $dadosRequest = $oESocial->request("GET");

        if (!empty($dadosRequest[0])) {
            $this->dados = $dadosRequest[0];
        }

        if (empty($this->dados->recibo)) {
            $msg = "Recibo não encontrado, para a matricula {$this->matricula}, consulte a situação do envio S-2200.";
            throw new \Exception($msg);
        }
    }

    /**
     * Busca o empregador
     *
     * @return string
     */
    private function getEmpregador()
    {

        $codigoInstituicao = \InstituicaoRepository::getInstituicaoSessao()->getCodigo();

        $anoFolha = \DBPessoal::getAnoFolha();
        $mesFolha = \DBPessoal::getMesFolha();

        $sqlCgm = "
            SELECT DISTINCT
              z01_numcgm                      AS cgm,
              z01_cgccpf                      AS cnpj
            FROM rhlota
              INNER JOIN cgm ON rhlota.r70_numcgm = cgm.z01_numcgm
              inner join rhpessoalmov on  rh02_lota = r70_codigo
            WHERE r70_instit = {$codigoInstituicao}  and  rh02_regist = {$this->matricula}
            and rh02_anousu = {$anoFolha} and rh02_mesusu = {$mesFolha}
            ORDER BY z01_numcgm
        ";

        $resultadoSqlCgm = db_query($sqlCgm);

        if (!$resultadoSqlCgm) {
            $msg = "Ocorreu um erro ao consultar os CGM vinculado a lotação da matrícula {$this->matricula}.";
            throw new DBException($msg);
        }

        if (pg_num_rows($resultadoSqlCgm) == 0) {
            throw new DBException("Não há empregadores cadastrados para essa matrícula {$this->matricula}.");
        }

        $aEmpregador = \db_utils::getCollectionByRecord($resultadoSqlCgm);

        return $aEmpregador[0];
    }

    /**
     * Verifica se o servidor possui preenchimento
     * @return bool
     * @throws Exception
     */
    protected function possuiPreenchimento()
    {
        $dao = new \cl_avaliacaogruporespostarhpessoalalteracao();
        $rs  = db_query($dao->sql_query_file(null, 1, null, "eso17_rhpessoal = {$this->matricula}"));

        if (!$rs) {
            throw new Exception("Erro ao verificar se a matrícula possui preenchimento.");
        }

        return pg_num_rows($rs) > 0;
    }

    /**
     * Realiza o parser dos dados da api
     *
     * @return \stdClass|void
     */
    public function parse()
    {
        if ($this->possuiPreenchimento()) {
            return null;
        }

        $this->buscarDados();
        if (empty($this->dados)) {
            return;
        }

        $oReturn = new  \stdClass();
        $oEvento = json_decode($this->dados->evento);

        if (empty($oEvento)) {
            return;
        }

        if (!empty($oEvento->trabalhador)) {
            // dados pessoais
            $oReturn->cpfTrab = db_formatar($oEvento->trabalhador->cpfTrab, 'CPF');

            if (!empty($oEvento->trabalhador->sexo)) {
                $oReturn->sexo->option = "sexo_" . strtolower($oEvento->trabalhador->sexo);
            }

            $oReturn->nmTrab   = $oEvento->trabalhador->nmTrab;
            $oReturn->nisTrab  = $oEvento->trabalhador->nisTrab;

            if (!empty($oEvento->trabalhador->racaCor)) {
                $oReturn->racaCor->option = "racaCor_" . $oEvento->trabalhador->racaCor;
            }

            if (!empty($oEvento->trabalhador->estCiv)) {
                $oReturn->estCiv->option = "estCiv_" . $oEvento->trabalhador->estCiv;
            }

            if (!empty($oEvento->trabalhador->grauInstr)) {
                $oReturn->grauInstr->option = "grauInstr_" . $oEvento->trabalhador->grauInstr;
            }

            // contato
            $oReturn->fonePrinc     = $oEvento->trabalhador->contato->fonePrinc;
            $oReturn->emailPrinc    = $oEvento->trabalhador->contato->emailPrinc;
            $oReturn->foneAlternat  = (!empty($oEvento->trabalhador->contato->foneAlternat)
                ? $oEvento->trabalhador->contato->foneAlternat : '');
            $oReturn->emailAlternat = (!empty($oEvento->trabalhador->contato->emailAlternat)
                ? $oEvento->trabalhador->contato->emailAlternat : '');

            // deficiecia
            if (!empty($oEvento->trabalhador->infoDeficiencia)) {
                $oReturn->infoCota->option       = "infoCota_" . $oEvento->trabalhador->infoDeficiencia->infoCota;
                $oReturn->defFisica->option      = "defFisica_" . $oEvento->trabalhador->infoDeficiencia->defFisica;
                $oReturn->defMental->option      = "defMental_" . $oEvento->trabalhador->infoDeficiencia->defMental;
                $oReturn->defVisual->option      = "defVisual_" . $oEvento->trabalhador->infoDeficiencia->defVisual;
                $oReturn->reabReadap->option     = "reabReadap_" . $oEvento->trabalhador->infoDeficiencia->reabReadap;
                $oReturn->defAuditiva->option    = "defAuditiva_" . $oEvento->trabalhador->infoDeficiencia->defAuditiva;
                $oReturn->defIntelectual->option = "defIntelectual_"
                    . $oEvento->trabalhador->infoDeficiencia->defIntelectual;
            }

            // depedentes
            foreach ($oEvento->trabalhador->dependente as $dep => $dependente) {
                $dep = $dep + 1;

                $oReturn->{'tpDep_' . $dep}    = $dependente->tpDep;
                $oReturn->{'depSF_' . $dep}    = $dependente->depSF;
                $oReturn->{'nmDep_' . $dep}    = $dependente->nmDep;
                $oReturn->{'cpfDep_' . $dep}   = db_formatar($dependente->cpfDep, 'cpf');
                $oReturn->{'depIRRF_' . $dep}  = $dependente->depIRRF;
                $oReturn->{'incTrab_' . $dep}  = $dependente->incTrab;
                $oReturn->{'dtNascto_' . $dep} = db_formatar($dependente->dtNascto, 'd');
            }
        }
        return $oReturn;
    }

    /**
     * Retorna os campos que necessitam controle de
     * alteração para reenvio do evento
     *
     * @return array
     */
    public static function getCamposControleAlteracao()
    {
        $campos = [
            //Dados Pessoais
            'z01_cpf', 'z01_ident', 'z01_numcgm', 'z01_nome', 'rh01_nome',
            'z01_nomecomple', 'z01_pai', 'z01_mae', 'z01_nasc',
            'z01_dtfalecimento', 'z01_estciv', 'rh01_sexo',
            'rh01_raca', 'rh01_estciv', 'rh01_instru', 'rh01_nacion',
            'z04_nomesocial', 'rh01_natura', 'z01_sexo', 'z01_estciv',
            //Dados Endereço
            'z01_ender', 'z01_numero', 'z01_compl', 'z01_bairro', 'z01_cep', 'z01_munic', 'z01_uf',
            //Dados Endereço Exterior
            'z19_pais', 'z19_logradouro', 'z19_numero', 'z19_complemento', 'z19_bairro',
            'z19_cidade', 'z19_codigopostal',
            //Dados Trabalhador Imigrante
            'rh252_residencia', 'rh252_condicao',
            //Dados Deficiência
            'rh253_fisica', 'rh253_visual', 'rh253_auditiva', 'rh253_mental', 'rh253_intelectual',
            'rh253_reabilitado', 'rh253_cota', 'rh253_observacao',
            //Dados Dependentes
            'rh31_tipoparentesco', 'rh31_nome', 'rh31_dtnasc', 'dp01_cpf', 'dp01_sexo', 'rh31_irf',
            'rh31_depend', 'rh31_especi', 'rh31_descricaoparentesco',
            //Dados Contato
            'z01_telef', 'z01_email'
        ];

        return $campos;
    }
}
