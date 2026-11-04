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

/**
 * Class Check
 */
class Check
{

  private function __construct()
  {
  }
  private function __clone()
  {
  }

  /**
   * Verfica se o valor está entre o valor mínimo e máximo
   * @param $iValorProcurado
   * @param $iValorMinimo
   * @param $iValorMaximo
   * @return bool
   */
  public static function between($iValorProcurado, $iValorMinimo, $iValorMaximo)
  {
    return ($iValorProcurado >= $iValorMinimo && $iValorProcurado <= $iValorMaximo);
  }

  /**
   * Verifica se o valor informado é inteiro ou compatível com inteiro.
   * @param $iValor
   *
   * @return bool
   */
  public static function isInt($iValor)
  {
    return filter_var($iValor, FILTER_VALIDATE_INT, array('flags' => FILTER_NULL_ON_FAILURE)) !== null;
  }

  /**
   * Verifica se o valor informadi é float ou compatível com float.
   * @param $nValor
   *
   * @return bool
   */
  public static function isFloat($nValor)
  {
    return filter_var($nValor, FILTER_VALIDATE_FLOAT, array('flags' => FILTER_NULL_ON_FAILURE)) !== null;
  }

  /**
   * Verifica se o valor informado é boolean ou compatível com boolean.
   * @param $nValor
   *
   * @return bool
   */
  public static function isBoolean($nValor)
  {

    if (is_bool($nValor)) {
      return true;
    }
    return filter_var($nValor, FILTER_VALIDATE_BOOLEAN, array('flags' => FILTER_NULL_ON_FAILURE)) !== null;
  }

  /*Verifica se é numeric e  tamanho
   *Exemplo:
   *[
   *  [entrada,numeric/null,tamanho/null],
   *  [entrada],numeric/null,tamanho/null,[caracteres nao permitidos na string]/null]
   *  [[entrada,entrada],numeric/null,tamanho/null]
   * ]
   * retorna false se passou no teste ou mensagem com o erro.
   */
  public static function VaidacaoDados($dados)
  {
    $msg = false;
    foreach ($dados as $key => $array) {
      if (is_array($array[0])) {
        foreach ($array[0] as $key => $value) {
          if ($array[1] == 'numeric' && !is_numeric($value)) {
            $msg = $value . ' Não numérico';
          }
          if (isset($array[2]) && strlen($array[0]) > $value) {
            $msg = $value . ' Maior que ' . $array[2];
          }
          if (isset($array[3])) {
            if (is_array($array[3])) {
              foreach ($array[3] as $key => $caract) {
                if (db_indexOf($value, $caract)) {
                  $msg = 'Não permitido o caracter ' . $caract;
                }
              }
            } else {
              if (db_indexOf($value, $array[3])) {
                $msg = 'Não permitido o caracter ' . $array[3];
              }
            }
          }
        }
      } else {
        if ($array[1] == 'numeric' && !is_numeric($array[0])) {
          $msg = $array[0] . ' Não numérico';
        }
        if (isset($array[2]) && strlen($array[0]) > $array[2]) {
          $msg =  $array[0] . ' Maior que ' . $array[2];
        }
        if (isset($array[3])) {
          if (is_array($array[3])) {
            foreach ($array[3] as $key => $caract) {
              if (db_indexOf($array[0], $caract)) {
                $msg = 'Não permitido o caracter ' . $caract;
              }
            }
          } else {
            if (db_indexOf($array[0], $array[3])) {
              $msg = 'Não permitido o caracter ' . $array[3];
            }
          }
        }
      }
    }
    return $msg;
  }
}
