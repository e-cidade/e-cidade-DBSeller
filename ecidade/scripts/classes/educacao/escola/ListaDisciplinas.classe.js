require_once("scripts/arrays.js");
require_once("scripts/classes/educacao/DBViewFormularioEducacao.classe.js");

const MSG_LISTA_DISCIPLINAS = "educacao.escola.ListaDisciplinas.";
DBViewFormularioEducacao.ListaDisciplinas = function () {

  /**
   * RPC para buscar as regências
   * @type {string}
   */
  this.sRPC = "edu4_turmas.RPC.php";

  /**
   * Define um ação a ser executada após o carregamento dos dados
   * @type {function}
   */
  this.fCallBackLoad = function () {
    return true;
  };

  /**
   * Controla se devem ser retornadas somente disciplinas globais
   * @type {Boolean}
   */
  this.lSomenteDisciplinasGlobais = false;

    /**
     * Array com as disciplinas
     * @type {Array}
     */
  this.regencias = [];

  /**
   * Instância de ToggleList para disciplina
   * @type {DBToggleList}
   */
  this.oToggleRegencia = new DBToggleList([{'sId' : "sRegencia", 'sLabel' : "Regência"}]);
  this.oToggleRegencia.closeOrderButtons();
};

/**
 * Define a função para ser executado após o carregamento dos dados
 * @param fFunction
 */
DBViewFormularioEducacao.ListaDisciplinas.prototype.setCallBackLoad = function (fFunction) {
  this.fCallBackLoad = fFunction;
};

/**
 * Busca as disciplinas da turma para etapa selecionada
 * @param iTurma           código da turma
 * @param iEtapa           código da etapa
 * @param lProfessorLogado se deve as disciplinas do professor logado
 */
DBViewFormularioEducacao.ListaDisciplinas.prototype.getDisciplinas = async function ( iTurma, iEtapa, lProfessorLogado ) {
    const route = `v4/api/educacao/escola/turma/${iTurma}/etapas/${iEtapa}/regencias?validaUsuario`;
    const regencias = (await CurrentWindow.axios.get(route)).data.data

    this.regencias = regencias.map(regencia => {
        return {
            iRegencia: regencia.codigo,
            iDisciplina: regencia.disciplina.codigo,
            sDisciplina: regencia.disciplina.disciplina.nome,
            lTemGradeHorario: regencia.temGradeHorario,
            iProcedimentoAvaliacao: regencia.procedimentoAvaliacao.codigo,
            sProcedimentoAvaliacao: regencia.procedimentoAvaliacao.nome
        }
    })

    this.oToggleRegencia.clearAll();
    regencias.forEach(regencia => {
        let option = {
            iRegencia: regencia.codigo,
            iDisciplina: regencia.disciplina.codigo,
            sRegencia: regencia.disciplina.disciplina.nome,
            lTemGradeHorario: regencia.temGradeHorario
        }
        this.oToggleRegencia.addSelect(option)
    })
    this.oToggleRegencia.renderRows();
    this.fCallBackLoad();
};

/**
 * Retorna um array com as regencias selecionadas
 * @returns {Array}
 */
DBViewFormularioEducacao.ListaDisciplinas.prototype.getSelecionados = function () {
  return this.oToggleRegencia.getSelected();
};

/**
 * Limpa
 */
DBViewFormularioEducacao.ListaDisciplinas.prototype.clear = function () {
  this.oToggleRegencia.clearAll();
};

/**
 * Seta se devem ser trazidas somente disciplinas globais
 * @param  {Boolean} lSomenteDisciplinasGlobais
 */
DBViewFormularioEducacao.ListaDisciplinas.prototype.setSomenteDisciplinasGlobais = function( lSomenteDisciplinasGlobais ) {
  this.lSomenteDisciplinasGlobais = lSomenteDisciplinasGlobais;
}

/**
 * Renderiza o toggle em um elemento HTML
 * @param oElement {HTMLElement}
 */
DBViewFormularioEducacao.ListaDisciplinas.prototype.show = function( oElement ) {
  this.oToggleRegencia.show(oElement );
};
