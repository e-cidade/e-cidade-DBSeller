<?php

namespace App\Domain\Saude\ESF\Models;

use DateTime;
use ECidade\Enum\Common\FaixaEtariaEnum;
use ECidade\Enum\Saude\ESF\SituacaoCondicaoPacienteEnum;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property integer $psf5_id
 * @property integer $psf5_profissional_cgm
 * @property integer $psf5_profissional_unidade
 * @property integer $psf5_profissional_equipe
 * @property string $psf5_profissional_microarea
 * @property DateTime $psf5_profissional_data
 * @property integer $psf5_codcgs
 * @property string $psf5_cns
 * @property integer $psf5_cod_cns
 * @property boolean $psf5_responsavel
 * @property integer $psf5_codcgsresp
 * @property string $psf5_cns_responsavel
 * @property string $psf5_cod_cns_responsavel
 * @property DateTime $psf5_nascimento_responsavel
 * @property string $psf5_nome_social
 * @property integer $psf5_relacao_parentesco
 * @property boolean $psf5_frequenta_escola
 * @property string $psf5_ocupacao
 * @property integer $psf5_ocupacao_id
 * @property integer $psf5_situacao_trabalho
 * @property integer $psf5_criancas
 * @property boolean $psf5_cuidador
 * @property boolean $psf5_grupo
 * @property boolean $psf5_plano_saude
 * @property boolean $psf5_comunidade
 * @property string $psf5_comunidade_qual
 * @property boolean $psf5_info_sexual
 * @property integer $psf5_orientacao_sexual
 * @property boolean $psf5_tem_deficiencia
 * @property boolean $psf5_deficiencia_auditiva
 * @property boolean $psf5_deficiencia_visual
 * @property boolean $psf5_deficiencia_intelectual
 * @property boolean $psf5_deficiencia_fisica
 * @property boolean $psf5_deficiencia_outra
 * @property integer $psf5_saida_cidadao
 * @property boolean $psf5_gestante
 * @property integer $psf5_peso
 * @property boolean $psf5_fumante
 * @property boolean $psf5_alcool
 * @property boolean $psf5_drogas
 * @property boolean $psf5_hipertensao
 * @property boolean $psf5_diabetes
 * @property boolean $psf5_avc
 * @property boolean $psf5_infarto
 * @property boolean $psf5_cardiaca
 * @property boolean $psf5_cardiaca_isuficiencia
 * @property boolean $psf5_cardiaca_outro
 * @property boolean $psf5_cardiaca_nao
 * @property boolean $psf5_rins
 * @property boolean $psf5_rins_isuficiencia
 * @property boolean $psf5_rins_outro
 * @property boolean $psf5_rins_nao
 * @property string $psf5_maternidade
 * @property boolean $psf5_respiratoria
 * @property boolean $psf5_respiratoria_asma
 * @property boolean $psf5_respiratoria_dpoc
 * @property boolean $psf5_respiratoria_outro
 * @property boolean $psf5_respiratoria_nao
 * @property boolean $psf5_hanseniase
 * @property boolean $psf5_tuberculose
 * @property boolean $psf5_cancer
 * @property boolean $psf5_internacao
 * @property string $psf5_internacao_causa
 * @property boolean $psf5_psiquiatria
 * @property boolean $psf5_acamado
 * @property boolean $psf5_domiciliado
 * @property boolean $psf5_plantas
 * @property string $psf5_plantas_qual
 * @property boolean $psf5_praticas
 * @property string $psf5_outras_condicoes_1
 * @property string $psf5_outras_condicoes_2
 * @property string $psf5_outras_condicoes_3
 * @property boolean $psf5_situacao_rua
 * @property integer $psf5_tempo_situacao
 * @property boolean $psf5_beneficio
 * @property boolean $psf5_ref_familiar
 * @property integer $psf5_alimentacao
 * @property boolean $psf5_alimentacao_popular
 * @property boolean $psf5_alimentacao_doacaorelig
 * @property boolean $psf5_alimentacao_doacaorest
 * @property boolean $psf5_alimentacao_doacaopop
 * @property boolean $psf5_alimentacao_outro
 * @property boolean $psf5_acompanhado_inst
 * @property string $psf5_instituicao_qual
 * @property boolean $psf5_visita_fam
 * @property string $psf5_grau_parentesco
 * @property boolean $psf5_higiene
 * @property boolean $psf5_higiene_banho
 * @property boolean $psf5_higiene_sanitario
 * @property boolean $psf5_higiene_bucal
 * @property boolean $psf5_higiene_outros
 * @property boolean $psf5_recusou
 * @property integer $psf5_profissional_cbo
 * @property integer $psf5_etnia
 * @property string $psf5_nome_pai
 * @property string $psf5_portaria_naturalizacao
 * @property DateTime $psf5_data_naturalizacao
 * @property DateTime $psf5_data_entrada
 * @property DateTime $psf5_data_obito
 * @property string $psf5_numerodo
 * @property boolean $psf5_info_identidade_genero
 * @property integer $psf5_identidade_genero
 *
 * @method CadastroIndividual unidade(integer $idUnidade = null)
 * @method CadastroIndividual equipe(integer $idEquipe = null)
 * @method CadastroIndividual paciente(integer $idPaciente = null)
 * @method CadastroIndividual microarea(integer $idMicroarea = null)
 * @method CadastroIndividual data(DateTime $data)
 * @method CadastroIndividual condicao(SituacaoCondicaoPacienteEnum $enum = null)
 * @method CadastroIndividual sexo(string $sexo)
 * @method CadastroIndividual faixaEtaria(FaixaEtariaEnum $enum)
 * @method CadastroIndividual ativo()
 */
class CadastroIndividual extends Model
{
    const OBITO = 135;
    const MUDANCA_TERRITORIO = 136;

    public $timestamps = false;
    protected $table = 'plugins.psf_individual';
    protected $primaryKey = 'psf5_id';

    protected $casts = [
        'psf5_profissional_data' => 'datetime',
        'psf5_nascimento_responsavel' => 'datetime',
        'psf5_data_naturalizacao' => 'datetime',
        'psf5_data_entrada' => 'datetime',
        'psf5_data_obito' => 'datetime'
    ];

    /**
     * @param Builder $query
     * @param integer|null $idUnidade
     */
    public function scopeUnidade(Builder $query, $idUnidade = null)
    {
        $query->join('configuracoes.db_depart', 'psf5_profissional_unidade', 'coddepto');
        if ($idUnidade) {
            $query->where('psf5_profissional_unidade', $idUnidade);
        }
    }

    /**
     * @param Builder $query
     * @param integer|null $idEquipe
     */
    public function scopeEquipe(Builder $query, $idEquipe = null)
    {
        $query->join('plugins.psf_equipe', 'psf5_profissional_equipe', 'psf_id');
        if ($idEquipe) {
            $query->where('psf5_profissional_equipe', $idEquipe);
        }
    }

    /**
     * @param Builder $query
     * @param integer|null $idPaciente
     */
    public function scopePaciente(Builder $query, $idPaciente = null)
    {
        $query->join('ambulatorial.cgs_und', 'psf5_codcgs', 'z01_i_cgsund');
        if ($idPaciente) {
            $query->where('z01_i_cgsund', $idPaciente);
        }
    }

    /**
     * @param Builder $query
     * @param integer|null $idMicroarea
     */
    public function scopeMicroarea(Builder $query, $idMicroarea = null)
    {
        $query->join('ambulatorial.microarea', 'psf5_profissional_microarea', DB::raw('sd34_i_codigo::VARCHAR'));
        if ($idMicroarea) {
            $query->where('psf5_profissional_microarea', $idMicroarea);
        }
    }

    /**
     * @param Builder $query
     * @param DateTime $data
     */
    public function scopeData(Builder $query, DateTime $data)
    {
        $query->where('psf5_profissional_data', '<=', $data->format('Y-m-d'));
    }

    /**
     * @param Builder $query
     * @param SituacaoCondicaoPacienteEnum|null $enum
     * @throws Exception
     */
    public function scopeCondicao(Builder $query, $enum = null)
    {
        if ($enum instanceof SituacaoCondicaoPacienteEnum) {
            $coluna = $enum->column();
            $query->where($coluna, true);
        } else {
            $condicoes = SituacaoCondicaoPacienteEnum::toArray();
            foreach ($condicoes as $condicao) {
                $coluna = (new SituacaoCondicaoPacienteEnum($condicao))->column();
                $query->whereRaw($coluna . ' is not true');
            }
        }
    }

    /**
     * @param Builder $query
     * @param string $sexo
     */
    public function scopeSexo(Builder $query, $sexo)
    {
        $query->where('z01_v_sexo', $sexo);
    }

    /**
     * @param Builder $query
     * @param FaixaEtariaEnum $enum
     * @throws Exception
     */
    public function scopeFaixaEtaria(Builder $query, FaixaEtariaEnum $enum)
    {
        $query->whereBetween(
            DB::raw('EXTRACT(YEAR FROM age(CURRENT_DATE, z01_d_nasc))'),
            $enum->getFaixaEtaria()
        );
    }

    /**
     * @param Builder $query
     */
    public function scopeAtivo(Builder $query)
    {
        $query->whereNull('psf5_saida_cidadao');
    }
}
