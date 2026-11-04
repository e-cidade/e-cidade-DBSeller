<?php

namespace App\Domain\Tributario\ISSQN\Repository\InscricaoMunicipal;

use App\Domain\Tributario\Cadastro\Models\DbSyscampo;
use App\Domain\Tributario\ISSQN\Model\HistRiscoInscri\HistRiscoInscricao;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use Illuminate\Support\Facades\DB;

class InscricaoMunicipalRepository
{
    /**
     * Busca uma lista de inscricoes municipais de acordo com os parametros passados
     *
     * @param string $limit
     * @param string $offset
     * @param string $idInscricaoMunicipal
     * @param string $nome
     * @param string $inscricaoAnterior
     * @param string $cgcpf
     * @param string $setorFiscal
     */
    public function getInscricoes(
        $porPagina,
        $inscricaoMunicipal,
        $nome,
        $inscricaoAnterior,
        $cgcpf,
        $setorFiscal
    ) {
        $where = " 1 = 1 ";

        if (trim($inscricaoMunicipal) != "") {
            $where .= " and q02_inscr = {$inscricaoMunicipal} ";
        }

        if (trim($nome) != "") {
            $where .= " and z01_nome ilike '%{$nome}%' ";
        }

        if (trim($inscricaoAnterior) != "") {
            $where .= " and q02_inscmu ilike '{$inscricaoAnterior}' ";
        }

        if (trim($cgcpf) != "") {
            $where .= " and z01_cgccpf ilike '{$cgcpf}' ";
        }

        if (trim($setorFiscal) != "") {
            $where .= " and q177_setorfiscal = {$setorFiscal} ";
        }

        return IssBase::join('cgm', 'cgm.z01_numcgm', '=', 'issbase.q02_numcgm')
            ->leftJoin('issveiculo', 'issveiculo.q172_issbase', '=', 'issbase.q02_inscr')
            ->leftJoin('isssetorfiscal', 'isssetorfiscal.q177_issbase', '=', 'issbase.q02_inscr')
            ->whereRaw($where)
            ->orderBy('z01_nome', 'ASC')
            ->paginate($porPagina);
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     *
     * @param array $nomeCampos
     * @return array
     */
    public function getLabelsListaImoveis($nomeCampos)
    {
        $labels = array_map(function ($nomeCampo) {
            return DbSyscampo::select("rotulo")->where('nomecam', '=', $nomeCampo)->first()->toArray();
        }, $nomeCampos);

        return $labels;
    }


    public function getHistRiscoInscr($q201_inscr)
    {
        return HistRiscoInscricao::select('*')
            ->where('q201_inscr', '=', $q201_inscr);
    }



    public function getInscrDispSalaoParc($inscricaoMunicipal)
    {
        $iAnoUso = db_getsession("DB_anousu");
    
        if (empty($iAnoUso)) {
            $iAnoUso = date('Y');
        }
    
        $sql = "
            SELECT
                issqn.issbasesalaoparceiro.q202_dtinicial,
                issqn.issbasesalaoparceiro.q202_dtfinal,
                protocolo.cgm.z01_nome,
                issqn.issbase.q02_inscr,
                issqn.issbase.q02_dtbaix,
                issqn.cnae.q71_estrutural,
                issqn.cnae.q71_descr,
                configuracoes.db_estruturavalor.db121_estrutural,
                configuracoes.db_estruturavalor.db121_descricao,
                issqn.issbase.q02_numcgm,
                issqn.issgruposervicoativid.q127_issgruposerviso,
                issqn.issconfiguracaogruposervico.q136_exercicio,
                case when issqn.issconfiguracaogruposervico.q136_salaoparceiro is null then false
                else issqn.issconfiguracaogruposervico.q136_salaoparceiro end as q136_salaoparceiro,
                issqn.isscadsimples.q38_categoria,
                issqn.issbasesalaoparceiro.q202_sequencial,
                issqn.tabativ.q07_datain
            FROM
                issqn.issbase
            LEFT JOIN
                issqn.tabativ ON issqn.tabativ.q07_inscr = issqn.issbase.q02_inscr
            LEFT JOIN
                issqn.ativid ON issqn.tabativ.q07_ativ = issqn.ativid.q03_ativ
                and issqn.ativid.q03_deducao = true
            LEFT JOIN
                issqn.atividcnae ON issqn.atividcnae.q74_ativid = issqn.ativid.q03_ativ
            LEFT JOIN
                issqn.cnaeanalitica ON issqn.atividcnae.q74_cnaeanalitica = issqn.cnaeanalitica.q72_sequencial
            LEFT JOIN
                issqn.cnae ON issqn.cnaeanalitica.q72_cnae = issqn.cnae.q71_sequencial
            LEFT JOIN
                issqn.issgruposervicoativid ON issqn.issgruposervicoativid.q127_ativid = issqn.ativid.q03_ativ
            LEFT JOIN
                issqn.issgruposervico ON 
                issqn.issgruposervico.q126_sequencial = issqn.issgruposervicoativid.q127_issgruposerviso
            LEFT JOIN
                issqn.issconfiguracaogruposervico ON 
                issqn.issconfiguracaogruposervico.q136_issgruposervico = issqn.issgruposervico.q126_sequencial
                AND issqn.issconfiguracaogruposervico.q136_exercicio = :iAnoUso
            LEFT JOIN
                issqn.issbasesalaoparceiro ON issqn.issbasesalaoparceiro.q202_inscr = issqn.issbase.q02_inscr
            LEFT JOIN
                configuracoes.db_estruturavalor ON 
                issqn.issgruposervico.q126_db_estruturavalor = configuracoes.db_estruturavalor.db121_sequencial
            LEFT JOIN
                protocolo.cgm ON protocolo.cgm.z01_numcgm = issqn.issbase.q02_numcgm
            LEFT JOIN
                issqn.isscadsimples ON issqn.isscadsimples.q38_inscr = issqn.issbase.q02_inscr
                AND issqn.isscadsimples.q38_categoria = 3
                AND issqn.isscadsimples.q38_sequencial NOT IN (
                    SELECT q39_sequencial
                    FROM issqn.isscadsimplesbaixa
                )
            WHERE
                (issqn.tabativ.q07_databx IS NULL OR issqn.tabativ.q07_datafi >= CURRENT_DATE)
                AND (issqn.issbase.q02_inscr = :inscricaoMunicipal OR :inscricaoMunicipal IS NULL)
            ORDER BY
                CASE 
                    WHEN issqn.issconfiguracaogruposervico.q136_salaoparceiro = true THEN 1
                    WHEN issqn.issconfiguracaogruposervico.q136_salaoparceiro = false THEN 2
                    ELSE 3
                END,
                issqn.tabativ.q07_datain ASC,
                issqn.issbasesalaoparceiro.q202_sequencial DESC
            LIMIT 1;
        ";
    
        return DB::select($sql, [
            'iAnoUso' => $iAnoUso,
            'inscricaoMunicipal' => $inscricaoMunicipal
        ]);
    }
    
    


    public function getDispParc($inscricaoMunicipal, $cgm = null)
    {
        $iAnoUso = db_getsession("DB_anousu");

        if (empty($iAnoUso)) {
            $iAnoUso = date('Y');
        }

        $query = DB::table('issqn.issbase')
            ->leftjoin(
                'protocolo.cgm',
                'protocolo.cgm.z01_numcgm',
                '=',
                'issqn.issbase.q02_numcgm'
            )
            ->leftjoin(
                'issqn.isscadsimples',
                function ($join) {
                    $join->on('issqn.isscadsimples.q38_inscr', '=', 'issqn.issbase.q02_inscr')
                        ->where('issqn.isscadsimples.q38_categoria', 3)
                        ->whereNotIn(
                            'issqn.isscadsimples.q38_sequencial',
                            function ($subQuery) {
                                $subQuery->select('q39_sequencial')
                                    ->from('issqn.isscadsimplesbaixa');
                            }
                        );
                }
            )
            ->select(
                'protocolo.cgm.z01_numcgm',
                'protocolo.cgm.z01_nome',
                'issqn.issbase.q02_inscr',
                'issqn.issbase.q02_dtbaix',
                'issqn.isscadsimples.q38_categoria'
            )
            ->distinct();

        $query->wherenull('issqn.issbase.q02_dtbaix');
        if (!empty($inscricaoMunicipal)) {
            $query->where('issqn.issbase.q02_inscr', $inscricaoMunicipal);
        }

        if (!empty($cgm)) {
            $query->where('issqn.issbase.q02_numcgm', $cgm);
        }

        return $query->get();
    }

    public function getSetorFiscal($dados)
    {
        $setoresFiscais = DB::table('setorfiscal')
        ->select(
            'setorfiscal.j90_codigo as sequencial',
            'setorfiscal.j90_descr as descricao',
            'setorfiscal.j90_valor as valor'
        )
        ->when($dados->sequencial, function ($query) use ($dados) {
            $query->whereRaw("CAST(setorfiscal.j90_codigo AS TEXT) like ?", ["%$dados->sequencial%"]);
        })
        ->when($dados->descricao, function ($query) use ($dados) {
            $query->where('setorfiscal.j90_descr', 'ilike', "%$dados->descricao%");
        })
        ->orderBy('setorfiscal.j90_codigo')
        ->get();

        return $setoresFiscais;
    }
}
