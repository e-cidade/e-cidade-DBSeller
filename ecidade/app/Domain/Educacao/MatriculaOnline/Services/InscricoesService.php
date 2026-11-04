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

namespace App\Domain\Educacao\MatriculaOnline\Services;

use App\Domain\Educacao\MatriculaOnline\Models\Candidato;
use App\Domain\Educacao\MatriculaOnline\Models\Candidatura;
use ECidade\Educacao\MatriculaOnline\Service\CriterioDesignacaoEnsinoService;
use Ensino;
use Exception;
use Illuminate\Support\Facades\DB;
use ReflectionException;

class InscricoesService
{
    /**
     * Critérios Designação
     * @array
     */
    private $criteriosDesignacao = array();

    /**
     * @throws Exception
     */
    public function getDados($parametros)
    {
        $candidatosPorFase = $this->getCandidatosPorFase($parametros);
        foreach ($parametros as $fase) {
            $this->buscaCriteriosDesignacao($fase);
        }
        $this->dadosAlocacao($candidatosPorFase);

        return $candidatosPorFase;
    }

    public function getCandidatosPorFase($fases)
    {
        $candidatosPorFase = [];
        foreach ($fases as $fase) {
            $candidatosPorFase[$fase['descricao']] =
                DB::table('plugins.mobase')
                    ->distinct()
                    ->select(
                        'plugins.mobase.mo01_codigo AS mobase',
                        DB::raw('TRIM(plugins.mobase.mo01_nome) AS nome_candidato'),
                        DB::raw('TRIM(plugins.mobase.mo01_ender) AS rua'),
                        DB::raw('plugins.mobase.mo01_bairro AS cod_bairro'),
                        DB::raw('TRIM(bairro.j13_descr) AS bairro'),
                        'plugins.mobase.mo01_numero AS numero',
                        DB::raw('TRIM(plugins.mobase.mo01_compl) AS complemento'),
                        DB::raw('plugins.mobase.mo01_cep AS cep'),
                        DB::raw('TRIM(plugins.mobase.mo01_uf) AS uf'),
                        DB::raw('TRIM(plugins.mobase.mo01_municip) AS municipio'),
                        DB::raw('TRIM(zonas_residencia.ed194_descriicao) AS zona_residencia'),
                        DB::raw('TRIM(pais.ed228_c_descr) AS pais_nascimento'),
                        DB::raw("CASE
                                            WHEN TRIM(mobase.mo01_nacion) = '1' THEN 'BRASILEIRA'
                                            WHEN TRIM(mobase.mo01_nacion) = '2' THEN 'BRAS. NASC. EXTERIOR'
                                            WHEN TRIM(mobase.mo01_nacion) = '3' THEN 'ESTRANGEIRA'
                                       END AS nacionalidade"),
                        DB::raw('TRIM(plugins.mobase.mo01_telef) AS telefone'),
                        DB::raw('TRIM(plugins.mobase.mo01_ident) AS identidade'),
                        DB::raw('TRIM(plugins.mobase.mo01_orgident) AS orgao_identidade'),
                        DB::raw("CASE
                                            WHEN mobase.mo01_tiporesp = 1 THEN 'FILIAÇÃO 1'
                                            WHEN mobase.mo01_tiporesp = 2 THEN 'FILIAÇÃO 2'
                                            WHEN mobase.mo01_tiporesp = 4 THEN 'OUTRO'
                                       END AS tipo_responsavel"),
                        DB::raw('TRIM(plugins.mobase.mo01_nomeresp) AS nome_responsavel'),
                        DB::raw('TRIM(plugins.mobase.mo01_telresp) AS telefone_responsavel'),
                        DB::raw('TRIM(plugins.mobase.mo01_identresp) AS identidade_responsavel'),
                        DB::raw('TRIM(plugins.mobase.mo01_orgidresp) AS orgao_identidade_responsavel'),
                        DB::raw('TRIM(plugins.mobase.mo01_cpfresp) AS cpf_responsavel'),
                        DB::raw('TRIM(plugins.mobase.mo01_emailresp) AS email_responsavel'),
                        DB::raw('TRIM(plugins.mobase.mo01_vistoresponsavel) AS visto_responsavel'),
                        DB::raw('TRIM(plugins.mobase.mo01_rnmresponsavel) AS rne_responsavel'),
                        DB::raw("CASE
                                            WHEN mobase.mo01_certidaotipo = 1 THEN 'NASCIMENTO'
                                            WHEN mobase.mo01_certidaotipo = 2 THEN 'CASAMENTO'
                                       END AS tipo_certidao"),
                        DB::raw('TRIM(plugins.mobase.mo01_certidaonum) AS termo_certidao'),
                        DB::raw('TRIM(plugins.mobase.mo01_certidaolivro) AS livro_certidao'),
                        DB::raw('TRIM(plugins.mobase.mo01_certidaofolha) AS folha_certidao'),
                        DB::raw('TRIM(plugins.mobase.mo01_certidaocart) AS cartorio_certidao'),
                        DB::raw('TRIM(plugins.mobase.mo01_certidaomatricula) AS matricula_certidao'),
                        DB::raw('TRIM(certidaoestado.ed260_c_nome) AS estado_certidao'),
                        DB::raw('TRIM(certidaomunicipio.ed261_c_nome) AS municipio_certidao'),
                        DB::raw('TO_CHAR(plugins.mobase.mo01_certidaodata, \'dd/mm/yyyy\') AS data_certidao'),
                        DB::raw('TO_CHAR(plugins.mobase.mo01_dtnasc, \'dd/mm/yyyy\') AS data_nascimento'),
                        DB::raw('TRIM(nascimentoestado.ed260_c_nome) AS estado_nascimento'),
                        DB::raw('TRIM(nascimentomunicipio.ed261_c_nome) AS municipio_nascimento'),
                        DB::raw("CASE
                                            WHEN mobase.mo01_estciv = 1 THEN 'SOLTEIRO'
                                            WHEN mobase.mo01_estciv = 2 THEN 'CASADO'
                                            WHEN mobase.mo01_estciv = 3 THEN 'VIÚVO'
                                            WHEN mobase.mo01_estciv = 4 THEN 'DIVORCIADO'
                                       END AS estado_civil"),
                        DB::raw('TRIM(plugins.mobase.mo01_cpf) AS cpf_candidato'),
                        DB::raw('TRIM(plugins.mobase.mo01_email) AS email_candidato'),
                        DB::raw('TRIM(plugins.mobase.mo01_pai) AS filiacao_um'),
                        DB::raw('TRIM(plugins.mobase.mo01_cpf_filiacao_um) AS cpf_filiacao_um'),
                        DB::raw('TRIM(plugins.mobase.mo01_mae) AS filiacao_dois'),
                        DB::raw('TRIM(plugins.mobase.mo01_cpf_filiacao_dois) AS cpf_filiacao_dois'),
                        DB::raw("CASE
                                            WHEN TRIM(mobase.mo01_sexo) = 'F' THEN 'FEMININO'
                                            WHEN TRIM(mobase.mo01_sexo) = 'M' THEN 'MASCULINO'
                                       END AS sexo"),
                        DB::raw('TRIM(plugins.mobase.mo01_telcel) AS telefone_adicional'),
                        DB::raw('serie.ed11_i_ensino AS ensino_pretendido'),
                        DB::raw('TRIM(serie.ed11_c_descr) AS etapa_pretendida'),
                        DB::raw('TRIM(redeorigem.mo05_descr) AS rede_origem'),
                        DB::raw("CASE
                                            WHEN mobase.mo01_bolsafamilia = true THEN 'SIM'
                                            WHEN mobase.mo01_bolsafamilia = false THEN 'NÃO'
                                       END AS bolsa_familia"),
                        DB::raw('TRIM(plugins.mobase.mo01_cartaobolsafamilia) AS numero_bolsa_familia'),
                        DB::raw("CASE
                                            WHEN mobase.mo01_responsaveltrabalhador = true THEN 'SIM'
                                            WHEN mobase.mo01_responsaveltrabalhador = false THEN 'NÃO'
                                       END AS responsavel_trabalhador"),
                        DB::raw('TRIM(mo25_faixa) AS renda_familiar'),
                        DB::raw('TRIM(plugins.mobase.mo01_cartaosus) AS cartao_sus'),
                        DB::raw("CASE
                                            WHEN mobase.mo01_temirmaogemeo = true THEN 'SIM'
                                            WHEN mobase.mo01_temirmaogemeo = false THEN 'NÃO'
                                       END AS irmao_gemeo"),
                        DB::raw('TRIM(plugins.mobase.mo01_visto) AS visto_candidato'),
                        DB::raw('TRIM(plugins.mobase.mo01_rnm) AS rne_candidato'),
                        DB::raw('TRIM(plugins.mobase.mo01_raca) AS raca_candidato'),
                        DB::raw('TO_CHAR(plugins.mobase.mo01_datacad, \'dd/mm/yyyy\') AS data_cadastro'),
                        DB::raw('TRIM(plugins.mobase.mo01_horainscricao) AS hora_cadastro'),
                        DB::raw('plugins.mobase.mo01_datahoramanutencao AS data_hora_manutencao'),
                        DB::raw("CASE
                                            WHEN mobase.mo01_cadeirante = true THEN 'SIM'
                                            WHEN mobase.mo01_cadeirante = false THEN 'NÃO'
                                            WHEN mobase.mo01_cadeirante IS NULL THEN 'N/I'
                                       END AS cadeirante"),
                        DB::raw("(SELECT ARRAY_TO_STRING( ARRAY_ACCUM(ed48_c_descr), ',' )
                                        FROM plugins.basenecess
                                        JOIN necessidade ON mo11_necess = ed48_i_codigo
                                        WHERE mo11_base = mo01_codigo ) AS necessidades_especiais"),
                        DB::raw("CASE
                                            WHEN mobase.mo01_mae_vitima = true THEN 'SIM'
                                            WHEN mobase.mo01_mae_vitima = false THEN 'NÃO'
                                       END AS mae_vitima_violencia"),
                        DB::raw('basefase.mo12_protocolo AS protocolo'),
                        DB::raw('basefase.mo12_fase AS fase'),
                        DB::raw('basefase.mo12_base AS base'),
                        DB::raw("CASE
                                            WHEN mo13_base = mo01_codigo
                                                THEN mo27_descricao
                                                ELSE 'NÃO ALOCADO'
                                            END AS situacao_alocado"),
                        DB::raw('mo53_codigo AS codigo_escola_alocado'),
                        DB::raw('TRIM(mo53_nome) AS escola_alocado'),
                        DB::raw("CASE
                                            WHEN mo13_base = mo01_codigo
                                                THEN mo02_temirmaoescola
                                                ELSE false
                                            END AS irmao_escola_alocado")
                    )
                    ->join('plugins.basefase', 'mo01_codigo', '=', 'mo12_base')
                    ->join('plugins.fase', 'mo12_fase', '=', 'mo04_codigo')
                    ->join('bairro', 'j13_codi', '=', 'mo01_bairro')
                    ->join('serie', 'mo01_serie', '=', 'ed11_i_codigo')
                    ->join('redeorigem', 'mo05_codigo', '=', 'mo01_redeorigem')
                    ->join('zonas_residencia', 'ed194_id', '=', 'mo01_zonaresidencia')
                    ->join('censouf AS nascimentoestado', 'mo01_ufnasc', '=', 'nascimentoestado.ed260_i_codigo')
                    ->join(
                        'censomunic AS nascimentomunicipio',
                        'mo01_munnasc',
                        '=',
                        'nascimentomunicipio.ed261_i_codigo'
                    )
                    ->leftJoin('escola.pais', 'ed228_i_codigo', '=', 'mo01_paisnascimento')
                    ->leftJoin('censouf AS certidaoestado', 'mo01_ufcartcert', '=', 'certidaoestado.ed260_i_codigo')
                    ->leftJoin(
                        'censomunic AS certidaomunicipio',
                        'mo01_munnasc',
                        '=',
                        'certidaomunicipio.ed261_i_codigo'
                    )
                    ->leftJoin('matriculaonline.renda_familiar', 'mo25_id', '=', 'mo01_renda_familiar')
                    ->leftJoin('plugins.alocados', 'mo13_base', '=', 'mo01_codigo')
                    ->leftJoin('plugins.alocadossituacao', 'mo28_alocado', '=', 'mo13_codigo')
                    ->leftJoin('plugins.situacaoinscricao', 'mo27_sequencial', '=', 'mo28_situacao')
                    ->leftJoin('plugins.baseescturno', 'mo03_codigo', '=', 'mo13_baseescturno')
                    ->leftJoin('plugins.baseescola', 'mo02_codigo', '=', 'mo03_baseescola')
                    ->leftJoin('plugins.escolas', 'mo53_codigo', '=', 'mo02_escola')
                    ->where('mo04_codigo', $fase['codigo'])
                    ->get();
        }
        return $candidatosPorFase;
    }

    private function dadosAlocacao(array $candidatosPorFase)
    {
        // Loop para buscar as opções da candidatura e demais atributos
        foreach ($candidatosPorFase as $candidatos) {
            foreach ($candidatos as $candidato) {
                if ($candidato->situacao_alocado !== 'NÃO ALOCADO') {
                    if (in_array('DEFICIÊNCIA', $this->criteriosDesignacao[$candidato->ensino_pretendido])
                        && $candidato->necessidades_especiais !== ''
                    ) {
                        $candidato->criteriosAlocacao[] = 'DEFICIENCIA';
                    }
                    if (in_array('BOLSA FAMÍLIA', $this->criteriosDesignacao[$candidato->ensino_pretendido])
                        && $candidato->bolsa_familia !== 'NÃO'
                    ) {
                        $candidato->criteriosAlocacao[] = 'BOLSA FAMILIA';
                    }
                    if (in_array('RESPONSÁVEL TRABALHADOR', $this->criteriosDesignacao[$candidato->ensino_pretendido])
                        && $candidato->responsavel_trabalhador !== 'NÃO'
                    ) {
                        $candidato->criteriosAlocacao[] = 'RESPONSAVEL TRABALHADOR';
                    }
                    if (in_array('REDE DE ORIGEM', $this->criteriosDesignacao[$candidato->ensino_pretendido])) {
                        $candidato->criteriosAlocacao[] = 'REDE DE ORIGEM';
                    }
                    if (in_array(
                        'SITUAÇÃO DE VIOLÊNCIA DOMÉSTICA/FAMILIAR',
                        $this->criteriosDesignacao[$candidato->ensino_pretendido]
                    )
                        && $candidato->mae_vitima_violencia !== 'NÃO'
                    ) {
                        $candidato->criteriosAlocacao[] = 'SITUACAO DE VIOLENCIA DOMESTICA/FAMILIAR';
                    }
                    if (in_array(
                        'RESIDÊNCIA PRÓXIMA DA ESCOLA',
                        $this->criteriosDesignacao[$candidato->ensino_pretendido]
                    )) {
                        $result = DB::select(
                            "SELECT EXISTS (
                                    SELECT 1 FROM plugins.escbairro
                                    WHERE mo08_escola = $candidato->codigo_escola_alocado
                                    AND mo08_bairro = $candidato->cod_bairro
                                ) as proximo_escola;"
                        );
                        if ($result[0]->proximo_escola) {
                            $candidato->criteriosAlocacao[] = 'RESIDENCIA PROXIMA';
                        }
                    }
                    if (in_array(
                        'IRMÃO NA MESMA INSTITUIÇÃO',
                        $this->criteriosDesignacao[$candidato->ensino_pretendido]
                    )
                        && $candidato->irmao_escola_alocado
                    ) {
                        $candidato->criteriosAlocacao[] = 'IRMAO NA MESMA ESCOLA';
                    }
                    /*
                     * Se o candidato foi alocado (Esse IF) eu busco as opções na baseescturno
                     * */
                    $candidato->opcoes = DB::table('plugins.mobase')
                        ->distinct()
                        ->select(
                            DB::raw('TRIM(mo53_nome) AS escola'),
                            DB::raw('\'-\' AS classificacao'),
                            'mo03_opcao'
                        )
                        ->join('plugins.basefase', 'mo12_base', '=', 'mo01_codigo')
                        ->join('plugins.baseescola', 'mo02_base', '=', 'mo01_codigo')
                        ->join('plugins.baseescturno', 'mo03_baseescola', '=', 'mo02_codigo')
                        ->join('plugins.escolas', 'mo53_codigo', '=', 'mo02_escola')
                        ->where('mo01_codigo', $candidato->mobase)
                        ->orderBy('mo03_opcao', 'ASC')
                        ->get();
                } else {
                    /*
                     * Caso o candidato não tenha sido alocado, busco o candidato na lista de espera
                     * junto com a sua classificação
                     * */
                    $candidato->opcoes = DB::table('plugins.baseescola')
                        ->distinct()
                        ->select(
                            DB::raw('TRIM(mo53_nome) AS escola'),
                            'mo18_classificacao as classificacao',
                            'mo03_opcao'
                        )
                        ->join('plugins.baseescturno', 'baseescturno.mo03_baseescola', '=', 'baseescola.mo02_codigo')
                        ->join('escola.turno', 'turno.ed15_i_codigo', '=', 'baseescturno.mo03_turno')
                        ->join('plugins.escolas', 'escolas.mo53_codigo', '=', 'baseescola.mo02_escola')
                        ->leftJoin('plugins.listaespera', function ($join) {
                            $join->on('baseescola.mo02_base', '=', 'listaespera.mo18_base')
                                 ->on('baseescola.mo02_escola', '=', 'listaespera.mo18_escola');
                        })
                        ->where('mo18_base', $candidato->mobase)
                        ->orderBy('mo03_opcao', 'ASC')
                        ->get();
                }
            }
        }
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    private function buscaCriteriosDesignacao($fase)
    {
        $criteriosPorEnsino = [];

        foreach ($fase['ciclo']['ensinos'] as $ensino) {
            $oEnsino = new Ensino($ensino['codigo']);
            $criteriosPorEnsino[$oEnsino->getCodigo()] = new CriterioDesignacaoEnsinoService($oEnsino);
            $criteriosPorEnsino[$oEnsino->getCodigo()]->carregarCriterios();
        }

        foreach ($criteriosPorEnsino as $codEnsino => $criteriosEnsino) {
            foreach ($criteriosEnsino->getCriteriosPontuacao() as $criterio) {
                $this->criteriosDesignacao[$codEnsino][] = $criterio->getCriterioDesignacao()->getDescricao();
            }
        }
    }

    public function getDadosDemandaReprimida($fase, $etapa, $escola)
    {
        $query = DB::table('plugins.mobase')
            ->distinct()
            ->select(
                'basefase.mo12_protocolo AS protocolo',
                DB::raw("TO_CHAR(mobase.mo01_dtnasc, 'DD-MM-YYYY') AS data_nascimento"),
                DB::raw("
                TO_CHAR(mobase.mo01_datacad, 'DD-MM-YYYY') || ' - ' || mobase.mo01_horainscricao AS data_inscricao"),
                DB::raw('CASE WHEN mo13_codigo IS NULL THEN \'LISTA ESPERA\' ELSE mo27_descricao END AS situacao')
            )
            ->join('plugins.basefase', 'basefase.mo12_base', '=', 'mobase.mo01_codigo')
            ->join('plugins.baseescola', 'baseescola.mo02_base', '=', 'mobase.mo01_codigo')
            ->join('plugins.baseescturno', 'baseescturno.mo03_baseescola', '=', 'baseescola.mo02_codigo')
            ->join('plugins.escolas', 'escolas.mo53_codigo', '=', 'baseescola.mo02_escola')
            ->leftJoin('plugins.alocados', 'alocados.mo13_baseescturno', '=', 'baseescturno.mo03_codigo')
            ->leftJoin('plugins.alocadossituacao', 'alocadossituacao.mo28_alocado', '=', 'alocados.mo13_codigo')
            ->leftJoin(
                'plugins.situacaoinscricao',
                'situacaoinscricao.mo27_sequencial',
                '=',
                'alocadossituacao.mo28_situacao'
            )
            ->where('basefase.mo12_fase', $fase);

        $query->when($escola !== '', function ($q) use ($escola) {
            return $q->where('escolas.mo53_codigo', $escola);
        });

        $query->when($etapa !== '', function ($q) use ($etapa) {
            return $q->where('mobase.mo01_serie', $etapa);
        })
        ->where(function ($q) {
            $q->whereNotNull('mo13_codigo')
            ->orWhereExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('plugins.listaespera')
                    ->whereColumn('mo18_base', 'mobase.mo01_codigo')
                    ->where('mo18_status', 1);
            });
        });

        return $query->get();
    }
}
