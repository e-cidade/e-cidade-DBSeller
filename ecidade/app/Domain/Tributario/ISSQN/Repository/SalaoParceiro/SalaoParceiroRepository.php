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

namespace App\Domain\Tributario\ISSQN\Repository\SalaoParceiro;

use App\Domain\Tributario\Cadastro\Models\DbSyscampo;
use App\Domain\Tributario\ISSQN\Model\SalaoParceiro\IssBaseProfissionalParceiro;
use App\Domain\Tributario\ISSQN\Model\SalaoParceiro\IssBaseSalaoParceiro;
use App\Domain\Tributario\ISSQN\Model\SalaoParceiro\IssBaseDeducaoSalao;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class SalaoParceiroRepository
{




    public function getParceiros($q202_inscr)
    {
        return IssBaseProfissionalParceiro::where('q203_inscrsalao', $q202_inscr);
    }

    public function getSalao($q202_inscr)
    {
        return IssBaseSalaoParceiro::selectRaw('max(q202_sequencial) as q202_sequencial, 
        q202_inscr, q202_dtinicial, q202_dtfinal, q202_obs,
            (select z01_nome from protocolo.cgm 
                inner join issqn.issbase 
                on z01_numcgm = q02_numcgm 
                where q02_inscr = issqn.issbasesalaoparceiro.q202_inscr) as cgm_salao')
            ->where('q202_inscr', $q202_inscr)
            ->groupBy('q202_inscr', 'q202_dtinicial', 'q202_dtfinal', 'q202_obs', 'cgm_salao')
            ->orderBy('q202_sequencial', 'DESC')
            ->first();
    }
    public function getAll($q202_inscr)
    {
        $data = IssBaseSalaoParceiro::where('q202_inscr', $q202_inscr)
            ->leftjoin(
                'issqn.issbaseprofissionalparceiro',
                'issqn.issbasesalaoparceiro.q202_sequencial',
                '=',
                'issqn.issbaseprofissionalparceiro.q202_sequencial'
            )
            ->join('protocolo.cgm', 'issqn.issbaseprofissionalparceiro.q203_numcgm', '=', 'cgm.z01_numcgm')
            ->select(
                'issqn.issbasesalaoparceiro.*',
                'issqn.issbaseprofissionalparceiro.*',
                'protocolo.cgm.z01_nome as cgm_parceiro',
                DB::raw('(select z01_nome from protocolo.cgm 
                inner join issqn.issbase 
                on z01_numcgm = q02_numcgm 
                where q02_inscr = issqn.issbasesalaoparceiro.q202_inscr) as cgm_salao')
            )
            ->orderBy('issqn.issbasesalaoparceiro.q202_sequencial', 'DESC')
            ->get()
            ->groupBy('q202_sequencial');
        $result = [];
        foreach ($data as $q202_sequencial => $inscri) {
            $inscriData = $inscri->first();
            $result[] = [
                'q202_sequencial' => $inscriData->q202_sequencial,
                'q202_inscr' => $inscriData->q202_inscr,
                'q202_dtinicial' =>
                $inscriData->q202_dtinicial ? Carbon::parse($inscriData->q202_dtinicial)->format('d/m/Y') : '',
                'q202_dtfinal' =>
                $inscriData->q202_dtfinal ? Carbon::parse($inscriData->q202_dtfinal)->format('d/m/Y') : '',
                'q202_obs' => $inscriData->q202_obs,
                'cgm_salao' => $inscriData->cgm_salao,
                'parceiros' => $inscri->map(function ($parceiro) {
                    return [
                        'q203_sequencial' => $parceiro->q203_sequencial,
                        'q203_inscrsalao' => $parceiro->q203_inscrsalao,
                        'q203_inscrparc' => $parceiro->q203_inscrparc,
                        'q203_numcgm' => $parceiro->q203_numcgm,
                        'q203_dtinicial' =>
                        $parceiro->q203_dtinicial ? Carbon::parse($parceiro->q203_dtinicial)->format('d/m/Y') : '',
                        'q203_dtfinal' =>
                        $parceiro->q203_dtfinal ? Carbon::parse($parceiro->q203_dtfinal)->format('d/m/Y') : '',
                        'q203_obs' => $parceiro->q203_obs,
                        'cgm_parceiro' => $parceiro->cgm_parceiro,
                    ];
                })->values()->toArray()
            ];
        }
        
        return $result;
    }

    public function getAtividadesSalao($q202_inscr)
    {
        $iAnoUso = db_getsession("DB_anousu");

        if (empty($iAnoUso)) {
            $iAnoUso = date('Y');
        }

        return DB::table('issqn.issbase')
            ->select(
                'issqn.issbase.q02_inscr',
                'issqn.issbasesalaoparceiro.q202_sequencial',
                'issqn.issbasesalaoparceiro.q202_inscr',
                'issqn.cnae.q71_estrutural',
                'issqn.cnae.q71_descr',
                'issqn.issconfiguracaogruposervico.q136_salaoparceiro',
                'issqn.issbasededucaosalao.q204_sequencial',
                'issqn.issbasededucaosalao.q204_inscr',
                'issqn.issbasededucaosalao.q204_estrutural',
                'issqn.issbasededucaosalao.q204_deducao',
                DB::raw(
                    '(SELECT z01_nome 
                     FROM protocolo.cgm 
                     INNER JOIN issqn.issbase ON q02_numcgm = z01_numcgm 
                     WHERE q02_inscr = issqn.issbasesalaoparceiro.q202_inscr) AS cgm_salao'
                )
            )
            ->leftjoin('issqn.issbasesalaoparceiro', 'q202_inscr', '=', 'q02_inscr')
            ->join('issqn.tabativ', 'q07_inscr', '=', 'q02_inscr')
            ->join('issqn.ativid', 'q07_ativ', '=', 'q03_ativ')
            ->join('issqn.atividcnae', 'q74_ativid', '=', 'q03_ativ')
            ->leftJoin('issqn.cnaeanalitica', 'q74_cnaeanalitica', '=', 'q72_sequencial')
            ->join('issqn.cnae', 'q72_cnae', '=', 'q71_sequencial')
            ->join('issqn.issgruposervicoativid', 'q127_ativid', '=', 'q03_ativ')
            ->join('issqn.issconfiguracaogruposervico', 'q127_issgruposerviso', '=', 'q136_issgruposervico')
            ->join('issqn.issgruposervico', 'q126_sequencial', '=', 'q136_issgruposervico')
            ->join('configuracoes.db_estruturavalor', 'q126_db_estruturavalor', '=', 'db121_sequencial')
            ->leftJoin('issqn.issbasededucaosalao', function ($join) {
                $join->on('q204_inscr', '=', 'q202_inscr')
                     ->on('q204_estrutural', '=', 'q71_estrutural');
            })
            ->where('q136_salaoparceiro', true)
            ->whereNull('q07_databx')
            ->where('q136_exercicio', $iAnoUso)
            ->where('q02_inscr', $q202_inscr)
            ->get();
    }

    

    public function verificaParceiros($q203_inscrsalao, $q203_inscrparc)
    {
        return IssBaseProfissionalParceiro::where('q203_inscrsalao', $q203_inscrsalao)
            ->where('q203_inscrparc', $q203_inscrparc)
            ->whereNotNull('q203_dtinicial')
            ->whereNull('q203_dtfinal')
            ->exists();
    }

    public function verificaSalao($q202_inscr)
    {
        return IssBaseSalaoParceiro::where('q202_inscr', $q202_inscr)
            ->whereNotNull('q202_dtinicial')
            ->whereNull('q202_dtfinal')
            ->exists();
    }

    public function verificaDataSalao($q202_inscr, $q202_dtinicial)
    {

        return IssBaseSalaoParceiro::where('q202_inscr', $q202_inscr)
            ->where('q202_dtfinal', '>=', $q202_dtinicial)
            ->max('q202_sequencial');
    }

    public function verificaDataParceiro($q203_inscrparc, $q203_dtinicial)
    {
        return IssBaseProfissionalParceiro::selectRaw('max(q203_sequencial)')
            ->join(
                'issqn.issbasesalaoparceiro as i',
                'i.q202_sequencial',
                '=',
                'issqn.issbaseprofissionalparceiro.q202_sequencial'
            )
            ->where('q203_inscrparc', $q203_inscrparc)
            ->where('q203_dtfinal', '>=', $q203_dtinicial)
            ->where('q202_dtinicial', '<=', $q203_dtinicial)
            ->max('q203_sequencial');
    }
    public function getSequencialSalao($q202_inscr)
    {
        $sequencial = IssBaseSalaoParceiro::where('q202_inscr', $q202_inscr)
            ->max('q202_sequencial');
        return $sequencial;
    }

    public function saveParceiros(
        $q202_inscr,
        $q202_dtinicial,
        $q202_obs,
        $q203_inscrparc,
        $q203_numcgm,
        $q203_dtinicial,
        $q203_obs
    ) {
        // Verifica se já existe salão cadastrado sem uma data final
        if (!$this->verificaSalao($q202_inscr)) {
            if (!$this->verificaDataSalao($q202_inscr, $q202_dtinicial)) {
                $atividades = $this->getAtividadesSalao($q202_inscr);
                 $deducoes = [];
                foreach ($atividades as $atividade) {
                    if ($atividade->q204_sequencial == null) {
                        $deducoes[] = [
                            'q204_sequencial' => $atividade->q204_sequencial,
                            'q202_inscr' => $atividade->q02_inscr,
                            'q204_estrutural' => $atividade->q71_estrutural,
                            'q204_deducao' => $atividade->q204_deducao,
                        ];
                    }
                }
                $salao = new IssBaseSalaoParceiro();
                $salao->q202_inscr = $q202_inscr;
                $salao->q202_dtinicial = $q202_dtinicial;
                $salao->q202_obs = $q202_obs;

                try {
                    $salao->save();
                    $this->saveAtividadesSalao($deducoes);
                } catch (\Exception $e) {
                    return 'erro';
                }
            } else {
                return 'dataSalaoInvalida';
            }
        }

        // Verifica se já existe parceiro cadastrado sem uma data final
        if (!$this->verificaParceiros($q202_inscr, $q203_inscrparc)) {
            if (!$this->verificaDataParceiro($q203_inscrparc, $q203_dtinicial)) {
                $parceiro = new IssBaseProfissionalParceiro();
                $parceiro->q203_inscrsalao = $q202_inscr;
                $parceiro->q203_inscrparc = $q203_inscrparc;
                $parceiro->q203_numcgm = $q203_numcgm;
                $parceiro->q203_dtinicial = $q203_dtinicial;
                $parceiro->q203_obs = $q203_obs;
                $parceiro->q202_sequencial = $this->getSequencialSalao($q202_inscr);

                try {
                    $parceiro->save();
                    return 'inserido';
                } catch (\Exception $e) {
                    return 'erro';
                }
            } else {
                return 'dataParceiroInvalida';
            }
        } else {
            return 'existe';
        }
    }


    public function updateSalao($q202_sequencial, $q202_dtinicial, $q202_dtfinal, $q202_obs)
    {
        try {
            $salao = IssBaseSalaoParceiro::where('q202_sequencial', $q202_sequencial)->first();
            if ($salao) {
                $salao->q202_dtinicial = $q202_dtinicial;
                $salao->q202_dtfinal = $q202_dtfinal;
                $salao->q202_obs = $q202_obs;
                $salao->save();
                $this->updateDataFimParceiro($q202_sequencial, isset($q202_dtfinal) ? $q202_dtfinal : null);
                return 'salaoAtualizado';
            } else {
                return 'salaoNaoEncontrado';
            }
        } catch (\Exception $e) {
            return $e;
        }
    }


    public function updateDataFimParceiro($q202_sequencial, $q203_dtfinal = null)
    {
        try {
            IssBaseProfissionalParceiro::where('q202_sequencial', $q202_sequencial)
                ->whereNull('q203_dtfinal')
                ->update(['q203_dtfinal' => $q203_dtfinal]);
            return 'dataFimParcAtualizada';
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function updateParceiro($q203_sequencial, $q203_dtinicial, $q203_dtfinal, $q203_obs)
    {
        
        try {
            $parceiro = IssBaseProfissionalParceiro::where('q203_sequencial', $q203_sequencial)->first();
            if ($parceiro) {
                $parceiro->q203_dtinicial = $q203_dtinicial;
                $parceiro->q203_dtfinal = $q203_dtfinal;
                $parceiro->q203_obs = $q203_obs;
                $parceiro->save();
                return 'parceiroAtualizado';
            } else {
                return 'parceiroNaoEncontrado';
            }
        } catch (\Exception $e) {
            return 'erroParceiro';
        }
    }


    public function updateAtividadeSalao($q204_sequencial, $q204_estrutural, $q204_inscr, $q204_deducao)
    {

        $atividade = IssBaseDeducaoSalao::where('q204_sequencial', $q204_sequencial)->first();
        if ($atividade) {
            $atividade->q204_estrutural = $q204_estrutural;
            $atividade->q204_inscr = $q204_inscr;
            $atividade->q204_deducao = $q204_deducao;
            $atividade->save();
            return 'atividadeAtualizada';
        } else {
            return 'atividadeNaoEncontrada';
        }
    }

    public function saveAtividadesSalao($deducoes)
    {
        try {
            foreach ($deducoes as $atividades) {
                if (empty($atividades['q204_sequencial'])) {
                    $deducao = new IssBaseDeducaoSalao();
                    $deducao->q204_inscr = $atividades['q202_inscr'];
                    $deducao->q204_estrutural = $atividades['q204_estrutural'];
                    $deducao->q204_deducao = empty($atividades['q204_deducao']) ? null : $atividades['q204_deducao'];
                    $deducao->save();
                } else {
                    $deducao = $this->updateAtividadeSalao(
                        $atividades['q204_sequencial'],
                        $atividades['q204_estrutural'],
                        $atividades['q202_inscr'],
                        empty($atividades['q204_deducao']) ? null : $atividades['q204_deducao']
                    );
                }
            }
            return 'salvo';
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getInscricoesDialog(
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
            $where .= " and z01_nome ilike '{$nome}' ";
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
            ->join('issqn.issbasesalaoparceiro', 'issbasesalaoparceiro.q202_inscr', '=', 'issbase.q02_inscr')
            ->whereRaw($where)
            ->orderBy('z01_nome', 'ASC')
            ->paginate($porPagina);
    }
}
