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

class menuItem {
    
  private $descricao;
  private $acao;
  private $imagem;
  private $id;
  private $aItens = array();
  /**
   * Função construtora dos iten
   *
   * @param string $sDescricao label do menu
   * @param string $sAction  acao a ser executada
   * @param string $sId id do menu
   * @param string $sImage path da imagem 
   */
  function __construct($sDescricao, $sAction='', $sId='', $sImage = null) {
        
    if ($sImage != '') {
       $sImage = "<img src=\"$sImage\" border=\"0\">"; 
    }
    $this->descricao = $sDescricao;
    $this->acao      = $sAction;
    $this->id        = $sId;
    $this->imagem    = $sImage;
  }

  function getContent() {

    $sImgString= "";
    if ($this->imagem != "") {
       $sImgString = "{$this->imagem}";
    }
    if (count($this->aItens) > 0) {

      $sButton  = "<li id=\"{$this->id}\">{$sImgString} {$this->descricao}";
      $sButton .= "<ul>";      
      foreach ($this->aItens as $oMenuItem) {                
         $sButton .= $oMenuItem->getContent();
      }
      $sButton .= "</ul>\n";
      $sButton .= "</li>\n";
    } else {
      $sButton = "<li>{$sImgString} <a  id=\"{$this->id}\" href=\"{$this->acao}\">$this->descricao</a></li>"; 
    }
    return $sButton;
  }
    
  function addMenu(menuItem $oMenu){
    $this->aItens[] = $oMenu;
  }
}


class menuBar {
    
  private $menu;
  private $sId;
  private $aItens = array();
  function __construct($sName) {
    $this->sId  = $sName;
  }
    
  function addButton (menuButton $menuButton) {
    $this->aItens[] = $menuButton;
  }
   
  function createList() {
       
    $sMenuBar = "";
    if (count($this->aItens) > 0) {            
           
      $sMenuBar  = " <div class=\"menuBar\" style=\"width:100%;position:absolute;left:0px;top:0px\">";
      $sMenuBar .= "<ul id=\"$this->sId\" style=\"display: none\">";
      foreach ($this->aItens as $oMenuButton) {                
        $sMenuBar .= $oMenuButton->getContent();
      }
      $sMenuBar .= "";
      $sMenuBar .= "</ul></div>";            
    }        
    return $sMenuBar;
  }
   
  function show($lRender=false) {
       
    $sMenuBar = "";
    if ($lRender) {
       $sMenuBar .= $this->createList();
    }
    $sMenuBar .= "<script type='text/javascript' src='libs/src/hmenu.js'></script>\n";
    $sMenuBar .= "<script>DynarchMenu.setup('{$this->sId}',{ shadows: false,scrolling: true, lazy:true})</script>";
    echo $sMenuBar;
  }
}

class menuButton {
    
  private $menu;
  private $action = "";
  private $aItens = array();
  function __construct($sCaption, $sAction = "") {

    $this->menu   = $sCaption;
    $this->action = $sAction;
    
  }
    
  function addItem(menuItem $oMenuItem) {
    $this->aItens[] = $oMenuItem;
  }
    
  function getContent() {
        
    if ($this->action != "") {
      $sButton = "<li><a href=\"javascript:{$this->action}\">{$this->menu}</a>";
    } else {
      $sButton = "<li>{$this->menu}";
    }
    if (count($this->aItens) > 0) {
            
       $sButton .= "<ul>";
       foreach ($this->aItens as $oMenuItem) {                
         $sButton .= $oMenuItem->getContent();
       }
       $sButton .= "</ul>\n";            
    }
            
    $sButton .= "</li>\n";
    return $sButton; 
  }
}

?>