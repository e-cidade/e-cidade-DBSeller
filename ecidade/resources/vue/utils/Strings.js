export function toCpf(string)
{
    const cpf = string.replace(/[^\d]/g, "");
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}

export function toCnpj(string)
{
    const cnpj = string.replace(/[^\d]/g, "");
    return cnpj.replace(/^(\d{2})(\d{3})?(\d{3})?(\d{4})?(\d{2})?/, "$1 $2 $3/$4-$5")
}

export function formatCurrency(value)
{
    return Number(value).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
}

export function formatNumber(value)
{
    return Number(value).toLocaleString('pt-BR');
}

/**
 * @param {string} value data no formato ISO (Y-m-d)
 * @returns {Date}
 */
export function buildDate(value)
{
    return new Date(`${value}T00:00:00`);
}
/**
 * @param {string} value data no formato ISO (Y-m-d)
 * @returns {string}
 */
export function formateDate(value)
{
    return (buildDate(value)).toLocaleDateString('pt-BR');
}

/**
 * @param {Date} value
 * @returns {string}
 */
export function formateDateToBD(value)
{
    return value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'});
}


export function formatDateToBrazilian(dateString)
{
    if (dateString === null) {
        return;
    }
    const date = new Date(dateString);

    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    };

    return date.toLocaleString('pt-BR', options);
}
