
/**
 * Valida se o departamento logado é uma UPS
 * @param  {HTMLFormElement} oForm         Object form
 * @param  {boolean}         lBloquearForm
 * @return {boolean}
 */
function validarDepartamentoUPS( oForm, lBloquearForm ) {

  var lRetorno = true;
  var oAjax    = new AjaxRequest('amb4_saude.RPC.php ', {exec: 'validaDepartamentoUPS'}, function (oRetorno, lErro) {

    if (lErro) {

      if (lBloquearForm) {
        setFormReadOnly(oForm, true);
      }
      alert(oRetorno.sMessage.urlDecode());
      lRetorno = false;
    }

  });
  oAjax.asynchronous(false);
  oAjax.execute();

  return lRetorno;
};

/**
 * Pesquisa e exibe a idade do paciente no HTML
 * @param {int} cgs cgs a ser utilizado na requisição AJAX
 * @param {string} idHTML id do elemento HTML onde a idade será inserida
 */
function pesquisaIdadeCompletaDoPaciente(cgs,idHTML){
  if(cgs != ''){
    let diretrizes = {'sExecucao':'buscarDadosCgs','iCgs': cgs};
    let sIdadeCompleta = '';
  
    let oAjaxRequest = new AjaxRequest('sau4_cgs.RPC.php', diretrizes,(oRetorno)=>{
      sIdadeCompleta =  oRetorno.oCgs.sIdadeCompleta.urlDecode();
      exibeIdadeCompletaNoHTML(idHTML,sIdadeCompleta);
    })
    oAjaxRequest.asynchronous(false);
    oAjaxRequest.execute();
  }
}

function exibeIdadeCompletaNoHTML(idHTML,idadeCompleta){
  
  $(idHTML).innerHTML = ``;
  if(idadeCompleta != ""){
    $(idHTML).innerHTML = `<p>Idade: <span style="color: red;">${idadeCompleta}</span>`;
  } 
}