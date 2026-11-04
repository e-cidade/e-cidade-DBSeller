export class Utils {
	static removeCaracteres(valor) {
		if (valor === null || valor === 'null' || valor === '' || valor === undefined) {
			return
		}
		return valor.match(/\d/g).join("");
	}

	static validaTamanhoCampoMatriculaCertidao(valor) {
		let value = this.removeCaracteres(valor)
		return value.length === 32
	}

	static validaTamanhoCampoTelefone(valor) {
		let value = this.removeCaracteres(valor)
		return value.length === 11
	}

	static validaTamanhoCampoCEP(valor) {
		let value = this.removeCaracteres(valor)
		return value.length === 8
	}

	static validaTamanhoCampoIdentidade(valor) {
		if (valor === '' || valor === null || valor === 'null') {
			return
		}
		let value = this.removeCaracteres(valor)
		return value.length === 10
	}

	static mapperOpcoesEscola(opcoes) {
		return opcoes.map((opcao, chave) => {
			return {
				irmao_escola: opcao.temIrmao,
				escola: opcao.value.codigo,
				nomeEscola: opcao.value.nome,
				turno: opcao.value.turno.codigo,
				escola_longe: opcao.value.escolaLonge,
				nome_irmao: opcao.nomeIrmao
			}
		})
	}

	static validaCPF(val) {
		if (val === '' || val === null) {
			return true
		}

		var Soma = 0
		var Resto

		var strCPF = String(val).replace(/[^\d]/g, '')

		if (strCPF.length !== 11)
			return false

		if ([
			'00000000000',
			'11111111111',
			'22222222222',
			'33333333333',
			'44444444444',
			'55555555555',
			'66666666666',
			'77777777777',
			'88888888888',
			'99999999999',
		].indexOf(strCPF) !== -1)
			return false

		for (let i=1; i<=9; i++)
			Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);

		Resto = (Soma * 10) % 11

		if ((Resto == 10) || (Resto == 11))
			Resto = 0

		if (Resto != parseInt(strCPF.substring(9, 10)) )
			return false

		Soma = 0

		for (let i = 1; i <= 10; i++)
			Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i)

		Resto = (Soma * 10) % 11

		if ((Resto == 10) || (Resto == 11))
			Resto = 0

		if (Resto != parseInt(strCPF.substring(10, 11) ) )
			return false

		return true
	}

	static formataTelefone (string) {
		if (string.charAt(0)) {
			string = string.slice(1)
		}
		string = string.replace(/\D/g,'')
		string = string.replace(/(\d{2})(\d)/,"($1) $2")
		string = string.replace(/(\d)(\d{4})$/,"$1-$2")
		return string
	}

    static somaDiasUteis (now, dias) {
        now = new Date(Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate()));
        if (dias == 0) {
            return now;
        }
        now.setDate(now.getDate() + 1);
        if (![0, 6].includes(now.getDay())) { dias--; }
        return this.somaDiasUteis(now, dias);
    }
}
