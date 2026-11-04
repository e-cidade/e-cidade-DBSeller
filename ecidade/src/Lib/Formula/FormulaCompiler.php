<?php
/**
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

namespace ECidade\Lib\Formula;

use FormulaInterpreter\Compiler;
use Exception;

/**
 * Class FormulaCompiler
 * @package ECidade\Lib\Formula
 */
abstract class FormulaCompiler
{
    /**
     * @var string
     */
    private $formula;

    /**
     * @var Compiler
     */
    private $compiler;

    /**
     * @var array
     */
    private $variaveis = array();

    /**
     * FormulaCompiler constructor.
     * @param string $formula
     */
    public function __construct($formula)
    {
        $this->formula = $formula;
        $this->compiler = new Compiler();
    }

    /**
     * @param $chave
     * @param $valor
     */
    protected function addVariavelValor($chave, $valor)
    {
        if (!isset($this->variaveis[$chave])) {
            $this->variaveis[$chave] = $valor;
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected function execute()
    {
        if (empty($this->formula)) {
            throw new Exception('Fórmula não informada.');
        }

        if ($this->formula[0] == '(' && $this->formula[strlen($this->formula) -1] === ')') {
            $this->formula = substr($this->formula, 1, -1);
        }

        $executavel = $this->compiler->compile($this->formula);

        return $executavel->run($this->variaveis);
    }

    /**
     * @return int
     */
    protected function totalVariaveis()
    {
        return count($this->variaveis);
    }
}
