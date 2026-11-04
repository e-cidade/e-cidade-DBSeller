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

namespace App\Domain\Financeiro\Orcamento\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int o206_sequencial
 * @property int o206_origem
 * @property int o206_numero
 * @property int o206_recurso
 * @property int o206_complementorecurso
 */
class OrigemComplementoRecurso extends Model
{
    const DESPESA = 1; // o206_numero = empenho empempenho.e60_numemp
    const EMISSAO_EMPENHO = 1; // o206_numero = empenho empempenho.e60_numemp
    const EMPENHO_RP = 10; // o206_numero = empenho empempenho.e60_numemp quando inscrito em RP
    const LANCAMENTO_CONTABIL = 2; // o206_numero = contabilidade.conlancam c70_codlan
    const AUTORIZACAO_EMPENHO = 100; // o206_numero) = autorizacao: empautoriza.e54_autori
    const PLANILHA_ARRECADACAO = 200; // k81_seqpla da tabela placaixarec... buscar o k81_seqpla pelo codigo da planilha
    const RECIBO = 300; // cornump k12_numpre
    const COMPLEMENTO_PADRAO = 400;

    protected $table = 'orcamento.origemcomplementorecurso';

    protected $primaryKey = 'o206_sequencial';
    public $timestamps = false;

    public $fillable = [
        'o206_sequencial',
        'o206_origem',
        'o206_numero',
        'o206_recurso',
        'o206_complementorecurso'
    ];
}
