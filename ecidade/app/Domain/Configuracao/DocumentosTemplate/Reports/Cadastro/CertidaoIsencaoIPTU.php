<?php

namespace App\Domain\Configuracao\DocumentosTemplate\Reports\Cadastro;

use App\Domain\Configuracao\DocumentosTemplate\ProcessaDocumentoTemplate;
use Illuminate\Database\Capsule\Manager as DB;

class CertidaoIsencaoIPTU extends ProcessaDocumentoTemplate
{
    private $matricula;
    private $codigoIsencao;
    const GRUPOTEMPLATE = 44;

    public function __construct($matricula, $codigoIsencao, $codigoDocumento)
    {
        parent::__construct(self::GRUPOTEMPLATE, $codigoDocumento);
        $this->setNomeDocumento('CertidaoDeIsencaoDeIptu');

        if (empty($matricula) || !is_numeric($matricula)) {
            throw new \BusinessException("Matricula inválida.");
        }

        if (empty($codigoIsencao) || !is_numeric($codigoIsencao)) {
            throw new \BusinessException("Código de Isenção inválido.");
        }

        $this->matricula = $matricula;
        $this->codigoIsencao = $codigoIsencao;
    }

    /**
     * @return array
     */
    public function configuraDadosVariaveis()
    {
        $query = DB::select("
            SELECT
                proprietario.z01_numcgm as cgm_contribuinte,
                proprietario.z01_cgccpf as cpfcnpj_contribuinte,
                proprietario.z01_nome as nome_contribuinte,
                cgm_propri.z01_numcgm as cgm_proprietario,
                cgm_propri.z01_nome as nome_proprietario,
                cgm_propri.z01_cgccpf as cpfcnpj_proprietario,
                cgm_propri.z01_ender as rua_proprietario,
                cgm_propri.z01_numero as numero_proprietario,
                cgm_propri.z01_compl as complemento_proprietario,
                cgm_propri.z01_bairro as bairro_proprietario,
                cgm_propri.z01_munic as cidade_proprietario,
                cgm_propri.z01_uf as uf_proprietario,
                cgm_propri.z01_cep as cep_proprietario,
                cgm_propri.z01_cxpostal as cxpostal_proprietario,
                cgm_propri.z01_telef as telefone_proprietario,
                cgm_propri.z01_telcel as celular_proprietario,
                cgm_propri.z01_email as email_proprietario,
                cgm.z01_numcgm as cgm_promitente,
                cgm.z01_nome as nome_promitente,
                cgm.z01_cgccpf as cpfcnpj_promitente,
                cgm.z01_ender as rua_promitente,
                cgm.z01_numero as numero_promitente,
                cgm.z01_compl as complemento_promitente,
                cgm.z01_bairro as bairro_promitente,
                cgm.z01_munic as cidade_promitente,
                cgm.z01_uf as uf_promitente,
                cgm.z01_cep as cep_promitente,
                cgm.z01_cxpostal as cxpostal_promitente,
                cgm.z01_telef as telefone_promitente,
                cgm.z01_telcel as celular_promitente,
                cgm.z01_email as email_promitente,
                proprietario.j01_matric as matricula_imovel,
                proprietario.j34_setor as setor_imovel,
                proprietario.j34_quadra as quadra_imovel,
                proprietario.j34_lote as lote_imovel,
                proprietario.j34_bairro as bairro_imovel_codigo,
                proprietario.j13_descr as bairro_imovel
                ,proprietario.nomepri as rua_imovel,
                proprietario.j39_numero as numero_imovel,
                proprietario.j39_compl as complemento_imovel,
                proprietario.j06_setorloc as setorlocalizacao_imovel,
                proprietario.j06_quadraloc as quadralocalizacao_imovel,
                proprietario.j06_lote as lotelocalizacao_imovel,
                iptuisen.j46_codigo as codigo_isencao,
                iptuisen.j46_matric as matricula_isencao,
                iptuisen.j46_tipo as tipo_isencao,
                tipoisen.j45_descr as descricao_tipo_isencao,
                tipoisen.j45_obscertidao as observacao_tipo_isencao,
                to_char(iptuisen.j46_dtini,'DD/MM/YYYY') as data_inicial_isencao,
                to_char(iptuisen.j46_dtfim,'DD/MM/YYYY') as data_final_isencao,
                concat(protprocesso.p58_numero,'/',protprocesso.p58_ano) as processo_isencao,
                extract(year from j46_dtini) as ano_inicial_isencao,
                extract(year from j46_dtfim) as ano_final_isencao,
                iptuisen.j46_perc as percentual_iptu_isencao,
                isentaxa.j56_perc as percentual_taxas_isencao,
                tabrec.k02_descr as descricao_taxa_isencao,iptuisen.j46_hist as historico_isencao,
                fc_dataextenso(current_date) as db_data_atual_extenso,
                (select munic from db_config where codigo = 1) as db_municinst
            FROM
                iptuisen
                inner join tipoisen on tipoisen.j45_tipo = iptuisen.j46_tipo
                inner join proprietario on proprietario.j01_matric = iptuisen.j46_matric
                left join isenproc on isenproc.j61_codigo = iptuisen.j46_codigo
                left join protprocesso on protprocesso.p58_codproc = isenproc.j61_codproc
                left join isentaxa on isentaxa.j56_codigo = iptuisen.j46_codigo
                left join tabrec on tabrec.k02_codigo = isentaxa.j56_receit
                left join promitente on promitente.j41_matric = iptuisen.j46_matric and promitente.j41_tipopro is true
                left join cgm on promitente.j41_numcgm = cgm.z01_numcgm
                left join cgm as cgm_propri on proprietario.z01_numcgm = cgm_propri.z01_numcgm
            WHERE
                j46_matric = {$this->matricula} and j46_codigo = {$this->codigoIsencao};
        ");

        if (empty($query)) {
            throw new \BusinessException("Não foi possível obter dados da insenção de IPTU.");
        }

        $dados = (array) $query[0];
        return $dados;
    }
}
