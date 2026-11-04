<?php

namespace App\Domain\Tributario\Cadastro\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Model table view proprietario
 *
 * @property int z01_numcgm
 * @property int $j01_matric
 * @property string $z01_cgccpf
 * @property string $proprietario
 * @property string $z01_nome_original
 * @property string $z01_nome
 * @property string $z01_nomecompleto_original
 * @property string $z01_nomecompleto
 * @property string $z01_ender
 * @property string $z01_munic
 * @property string $z01_bairro
 * @property string $z01_cep
 * @property string $z01_uf
 * @property int $z01_numero
 * @property string $z01_compl
 * @property int $codpri
 * @property string $nomepri
 * @property string $tipopri
 * @property int $j39_numero
 * @property string $j39_compl
 * @property string $j34_setor
 * @property string $j34_quadra
 * @property string $j34_lote
 * @property int $j34_zona
 * @property int $j34_bairro
 * @property string $j40_refant
 * @property string $j40_registrocartografico
 * @property int $j01_idbql
 * @property int $j14_codigo
 * @property string $j14_nome
 * @property string $j14_tipo
 * @property int $j13_codi
 * @property string $j13_descr
 * @property string $j01_baixa
 * @property double $j34_area
 * @property double $j34_areal
 * @property int $j44_numcgm
 * @property int $j41_numcgm
 * @property int $j43_matric
 * @property string $j01_datacad
 * @property string $j43_munic
 * @property string $j43_ender
 * @property string $j43_cep
 * @property string $j43_uf
 * @property string $j43_dest
 * @property int $j43_numimo
 * @property string $j43_cxpost
 * @property string $j43_comple
 * @property string $j01_tipoimp
 * @property int $j01_codave
 * @property int $j37_zona
 * @property int $z01_cgmpri
 * @property int $j39_pavim
 * @property string $j05_codigoproprio
 * @property int $j06_setorloc
 * @property string $j06_quadraloc
 * @property string $j06_lote
 * @property string $j05_descr
 * @property string $pql_localizacao
 * @property string $z01_cgccpfpropri
 * @property string $z01_nomecomplepri
 * @property string $z01_enderpri
 * @property string $z01_municpri
 * @property string $z01_bairropri
 * @property string $z01_ceppri
 * @property string $z01_ufpri
 * @property int $z01_numeropri
 * @property string $z01_complpri
 * @property int $j01_tipoproprietario
 * @property string $tipopro
 * @property string $j163_abreviatura
 * @property string $j164_abreviatura
 */
class Proprietario extends Model
{
    protected $table = "cadastro.proprietario";

    protected $fillable = [
        'z01_numcgm',
        'j01_matric',
        'z01_cgccpf',
        'proprietario',
        'z01_nome_original',
        'z01_nome',
        'z01_nomecompleto_original',
        'z01_nomecompleto',
        'z01_ender',
        'z01_munic',
        'z01_bairro',
        'z01_cep',
        'z01_uf',
        'z01_numero',
        'z01_compl',
        'codpri',
        'nomepri',
        'tipopri',
        'j39_numero',
        'j39_compl',
        'j34_setor',
        'j34_quadra',
        'j34_lote',
        'j34_zona',
        'j34_bairro',
        'j40_refant',
        'j40_registrocartografico',
        'j01_idbql',
        'j14_codigo',
        'j14_nome',
        'j14_tipo',
        'j13_codi',
        'j13_descr',
        'j01_baixa',
        'j34_area',
        'j34_areal',
        'j44_numcgm',
        'j41_numcgm',
        'j43_matric',
        'j01_datacad',
        'j43_munic',
        'j43_ender',
        'j43_cep',
        'j43_uf',
        'j43_dest',
        'j43_numimo',
        'j43_cxpost',
        'j43_comple',
        'j01_tipoimp',
        'j01_codave',
        'j37_zona',
        'z01_cgmpri',
        'j39_pavim',
        'j05_codigoproprio',
        'j06_setorloc',
        'j06_quadraloc',
        'j06_lote',
        'j05_descr',
        'pql_localizacao',
        'z01_cgccpfpropri',
        'z01_nomecomplepri',
        'z01_enderpri',
        'z01_municpri',
        'z01_bairropri',
        'z01_ceppri',
        'z01_ufpri',
        'z01_numeropri',
        'z01_complpri',
        'j01_tipoproprietario',
        'tipopro',
        'j163_abreviatura',
        'j164_abreviatura'
    ];

    public function save(array $options = [])
    {
        throw new Exception('Operaчуo invalida!');
    }
}
