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

namespace App\Domain\Tributario\Cadastro\Repositories\Zoneamento;

use App\Domain\Tributario\Cadastro\Models\Zoneamento\Zonasdesenvolvimento;
use App\Domain\Tributario\Cadastro\Models\Zoneamento\Macrozonas;
use App\Domain\Tributario\Cadastro\Models\DbSyscampo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class ZoneamentoRepository
{

    public function getNextSeqMacrozona()
    {
        $sequence = DB::select("select last_value + 1 AS sequence
                                FROM pg_sequences
                                WHERE schemaname = 'cadastro' 
                                AND sequencename = 'macrozonas_j224_sequencial_seq';");
                                
        if ($sequence[0]->sequence == null) {
            return 1;
        }
        return $sequence[0]->sequence;
    }

    public function getNextSeqZona()
    {
        $sequence = DB::select("select last_value + 1 AS sequence
                                FROM pg_sequences
                                WHERE schemaname = 'cadastro' 
                                AND sequencename = 'zonasdesenvolvimento_j225_sequencial_seq';");
                                
        if ($sequence[0]->sequence == null) {
            return 1;
        }
        return $sequence[0]->sequence;
    }


    public function saveMacroZona($sigla, $localizacao, $descricao)
    {
        try {
            $macrozona = new Macrozonas();
            $macrozona->j224_sigla = $sigla;
            $macrozona->j224_localizacao = $localizacao;
            $macrozona->j224_descricao = $descricao;
            $macrozona->save();

            return ['sucesso' => $macrozona->j224_sequencial];
        } catch (Exception $e) {
            return $e;
        }
    }


    public function getMacrozonasDialog(
        $porPagina,
        $sequencial,
        $sigla,
        $localizacao,
        $descricao
    ) {
        $where = " 1 = 1 ";

        if (trim($sequencial) != "") {
            $where .= " and j224_sequencial = {$sequencial} ";
        }

        if (trim($sigla) != "") {
            $where .= " and j224_sigla ilike '{$sigla}' ";
        }

        if (trim($localizacao) != "") {
            $where .= " and j224_localizacao ilike '{$localizacao}' ";
        }

        if (trim($descricao) != "") {
            $where .= " and j224_descricao ilike '{$descricao}' ";
        }

        return Macrozonas::query()
            ->whereRaw($where)
            ->orderBy('j224_sequencial', 'ASC')
            ->paginate($porPagina);
    }

    public function getZonasDialog(
        $porPagina,
        $sequencial,
        $sigla,
        $localizacao,
        $descricao
    ) {
        $where = " 1 = 1 ";
    
        if (trim($sequencial) != "") {
            $where .= " and z.j225_sequencial = {$sequencial} ";
        }
    
        if (trim($sigla) != "") {
            $where .= " and z.j225_sigla ilike '{$sigla}' ";
        }
    
        if (trim($localizacao) != "") {
            $where .= " and m.j224_localizacao ilike '{$localizacao}' ";
        }
    
        if (trim($descricao) != "") {
            $where .= " and z.j225_descricao ilike '{$descricao}' ";
        }
    
        return Zonasdesenvolvimento::query()
            ->from('zonasdesenvolvimento as z')
            ->selectRaw("z.j225_sequencial, 
                        z.j225_sigla, 
                        z.j225_descricao, 
                        m.j224_localizacao, 
                        m.j224_sigla, 
                        m.j224_sequencial
            ")
            ->join('macrozonas as m', 'm.j224_sequencial', '=', 'z.j224_sequencial')
            ->whereRaw($where)
            ->orderBy('z.j225_sequencial', 'ASC')
            ->paginate($porPagina);
    }
    

    public function getMacrozonaById($sequencial)
    {
        $result = Macrozonas::where('j224_sequencial', $sequencial)->first();
        
        if (empty($result)) {
            return 'semResultado';
        }
        
        return $result;
    }


    public function saveZona(
        $j224_sequencial,
        $j225_sigla,
        $j225_descricao
    ) {
        try {
            $zona = new Zonasdesenvolvimento;
            $zona->j224_sequencial = $j224_sequencial;
            $zona->j225_sigla = $j225_sigla;
            $zona->j225_descricao = $j225_descricao;
            $zona->save();
            return ['sucesso'=>$zona->j225_sequencial];
        } catch (Exception $e) {
            return $e;
        }
    }

    public function updateMacrozona($j224_sequencial, $j224_sigla, $j224_localizacao, $j224_descricao)
    {
        try {
            $macrozona = Macrozonas::where('j224_sequencial', $j224_sequencial)->first();
            $macrozona->j224_sigla = $j224_sigla;
            $macrozona->j224_localizacao = $j224_localizacao;
            $macrozona->j224_descricao = $j224_descricao;
            $macrozona->save();
            return ['sucesso'=>$macrozona->j224_sequencial];
        } catch (Exception $e) {
            return $e;
        }
    }

    public function updateZona($j225_sequencial, $j224_sequencial, $j225_sigla, $j225_descricao)
    {
        try {
            $zona = Zonasdesenvolvimento::where('j225_sequencial', $j225_sequencial)->first();
            $zona->j224_sequencial = $j224_sequencial;
            $zona->j225_sigla = $j225_sigla;
            $zona->j225_descricao = $j225_descricao;
            $zona->save();
            return ['sucesso'=>$zona->j225_sequencial];
        } catch (Exception $e) {
            return $e;
        }
    }
}
