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

namespace App\Domain\Educacao\MatriculaOnline\Models;

use App\Domain\Educacao\CentralMatriculas\Models\CBOOcupacao;
use App\Domain\Educacao\CentralMatriculas\Models\Escola;
use App\Domain\Educacao\MatriculaOnline\Models\AtestadoNecessidadeEspecial;
use App\Domain\Educacao\Escola\Models\CensoEstado;
use App\Domain\Educacao\Escola\Models\CensoMunicipio;
use App\Domain\Educacao\Escola\Models\Etapa;
use App\Domain\Educacao\Escola\Models\Pais;
use App\Domain\Educacao\Secretaria\Models\ZonaResidencia;
use App\Domain\Tributario\Cadastro\Models\Bairro;
use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    protected $table = 'plugins.mobase';
    public $timestamps = false;
    protected $primaryKey = 'mo01_codigo';
    protected $dates = ["mo01_datacad", "mo01_dtnasc"];
    protected $appends = ["opcoesEscola"];
    public function candidaturas()
    {
        return $this->hasMany(Candidatura::class, 'mo12_base', 'mo01_codigo');
    }

    public function atestadoNecessidadeEspecial()
    {
        return $this->hasMany(AtestadoNecessidadeEspecial::class, 'mo31_base', 'mo01_codigo');
    }


    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'mo01_serie', 'ed11_i_codigo');
    }

    public function basesEscola()
    {
        return $this->hasMany(OpcaoEscola::class, 'mo02_base', 'mo01_codigo');
    }

    public function getOpcoesEscolaAttribute()
    {
        $opcoes = [];
        foreach ($this->basesEscola as $baseEscola) {
            $classificacao = ListaEspera::query()
            ->where('mo18_base', $baseEscola->mo02_base)
            ->where('mo18_escola', $baseEscola->mo02_escola)
            ->where('mo18_turno', $baseEscola->opcaoTurno->turno->ed15_i_codigo)
            ->first(['mo18_classificacao', 'mo18_peso']);

            $opcoes[$baseEscola->opcaoTurno->mo03_opcao] = (object) [
                "opcao" => $baseEscola->opcaoTurno->mo03_opcao,
                "codEscola" => $baseEscola->escola->mo53_codigo,
                "escola" => trim($baseEscola->escola->mo53_nome),
                "turno" => trim($baseEscola->opcaoTurno->turno->ed15_c_nome),
                "classificacao" => !empty($classificacao) ? $classificacao->mo18_classificacao : null,
                "peso" => !empty($classificacao) ? $classificacao->mo18_peso : null,
                "irmaoNaEscola" => $baseEscola->mo02_temirmaoescola ? 'SIM' : 'NÃO'
            ];
        }

        ksort($opcoes);
        return array_values($opcoes);
    }

    public function listasEspera()
    {
        return $this->hasMany(ListaEspera::class, 'mo18_base', 'mo01_codigo');
    }

    public function redeOrigem()
    {
        return $this->belongsTo(RedeOrigem::class, 'mo01_redeorigem', 'mo05_codigo');
    }

    public function escolaRedeOrigem()
    {
        return $this->belongsTo(Escola::class, 'mo01_escolaredeorigem', 'mo53_codigo');
    }

    public function estadoCertidao()
    {
        return $this->belongsTo(CensoEstado::class, 'mo01_ufcartcert', 'ed260_i_codigo');
    }

    public function estadoNascimento()
    {
        return $this->belongsTo(CensoEstado::class, 'mo01_ufnasc', 'ed260_i_codigo');
    }

    public function municipioCertidao()
    {
        return $this->belongsTo(CensoMunicipio::class, 'mo01_muncartcert', 'ed261_i_codigo');
    }

    public function municipioNascimento()
    {
        return $this->belongsTo(CensoMunicipio::class, 'mo01_munnasc', 'ed261_i_codigo');
    }

    public function paisNascimento()
    {
        return $this->belongsTo(Pais::class, 'mo01_paisnascimento', 'ed228_i_codigo');
    }

    public function bairro()
    {
        return $this->belongsTo(Bairro::class, 'mo01_bairro', 'j13_codi');
    }

    public function zonaResidencia()
    {
        return $this->belongsTo(ZonaResidencia::class, 'mo01_zonaresidencia', 'ed194_id');
    }

    public function rendaFamiliar()
    {
        return $this->belongsTo(RendaFamiliar::class, 'mo01_renda_familiar', 'mo25_id');
    }

    public function ocupacaoPai()
    {
        return $this->belongsTo(CBOOcupacao::class, 'mo01_ocupacaopai', 'mo304_codigo');
    }

    public function ocupacaoMae()
    {
        return $this->belongsTo(CBOOcupacao::class, 'mo01_ocupacaomae', 'mo304_codigo');
    }

    public function ocupacaoResponsavel()
    {
        return $this->belongsTo(CBOOcupacao::class, 'mo01_ocupacaoresp', 'mo304_codigo');
    }

    public function candidatoNecessidades()
    {
        return $this->hasMany(CandidatoNecessidades::class, 'mo11_base', 'mo01_codigo');
    }
}
