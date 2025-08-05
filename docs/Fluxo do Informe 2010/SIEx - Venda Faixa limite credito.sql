--Rotina de consulta
select 
	VFLC.i_Venda_Faixa,
	Case
		When VFLC.v_Faixa_Inicial = 0 Then 'Até ' + M.Sigla + ' ' + Replace(Cast(VFLC.v_Faixa_Final as varchar), '.', ',')
		When VFLC.v_Faixa_Final = 0 Then 'Acima de ' + M.Sigla + ' ' + Replace(Cast(VFLC.v_Faixa_Inicial as varchar), '.', ',')
		Else 'De ' + M.Sigla + ' ' + Replace(Cast(VFLC.v_Faixa_Inicial as varchar), '.', ',') + ' até ' + M.Sigla + ' ' + Replace(Cast(VFLC.v_Faixa_Final as varchar), '.', ',')
	End,
	IsNull(IVFLC.n_Clientes, 0) as n_Clientes,
	IsNull(IVFLC.v_Valor, 0) as v_Valor
From 
	Venda_Faixa_Limite_Credito VFLC
Inner Join Moeda M On
	M.i_Moeda = 2
Left Join Inform_Venda_Faixa_Limite_Credito IVFLC On
	IVFLC.i_Venda_Faixa = VFLC.i_Venda_Faixa
	And IVFLC.i_Inform = "coluna id da Tabela Inform"
Order By
	VFLC.i_Venda_Faixa



--Rotina de inclusão
Insert Into Inform_Venda_Faixa_Limite_Credito Values ("coluna id da tabela Inform", "coluna i_Venda_Faixa da tabela Venda_Faixa_Limite_Credito", "Campo Nº Clientes", "Campo Contas a Receber")