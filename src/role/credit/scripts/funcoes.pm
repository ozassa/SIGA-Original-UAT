# funcoes usadas no script extrai_dados.pl e suas variantes (extract.pl, extrai_etc...)
# nenhuma delas usa variaveis globais

############################################################
# extrai os dados de uma string
# recebe a string, e uma referencia para um vetor de comprimentos,
# representando o comprimento de cada dado,
# e devolve um array com estes dados.
# por exemplo, no trecho seguinte:
#
#  $string = 'abcdefghijklmnopq';
#  @v = &get_data($string, [ 2, 5, 3, 7 ]);
#
# teremos que @v = ('ab', 'cdefg', 'hij', 'klmnopq');

sub get_data{
    local $_ = shift;
    my @lengths = @{ $_[0] };
    my @result = ();
    my $pattern;
    my $length;

    for $length (@lengths){
	$pattern = "(.{$length})";
	if(s/^$pattern//){ # retira os primeiros $length caracteres da string e guarda em @result
	    push @result, &trim($1); # elimina espacos em branco no inicio e no fim da string
	}
    }
    return @result;
}

############################################################
# devolve um vetor de comprimentos de acordo com o section_number
# cada valor de section_number tem um significado:
#
# 0  - file identification
# 1  - policy identification
# 2  - buyer identification
# 3  - request
# 4  - request on secondary address
# 5  - guarantor recommended by the policy holder
# 6  - decision
# 7  - decision clause
# 8  - decision comments
# 9  - guarantor on the decision
# 10 - buyer complement
# 11 - guarantor complement(1) on decision
# 12 - in spite of our search
# 13 - information request
# 14 - balance sheet request
# 15 - policy broker
# 16 - guarantor complement(1) on request
# 17 - guarantor complement(2) on request
# 18 - guarantor complement(2) on decision
# 19 - KZU-NZM
# 20 - decision in the future
# 21 - decision clause in the future
# 22 - decision in the future comments
# 23 - guarantor on decision in the future
# 24 - guarantor complement(1)
# 25 - guarantor complement(2)
# 26 - @rating
# 27 - previous address on FO RECTIF
# 28 - SCRL report

# alem disso, seta um vetor com os nomes dos respectivos campos q serao retirados
sub get_correct_lengths{
    my ($section_number, $keys) = @_;

    if($section_number == 0){
	@{ $keys } = ("File Identifiant", "Number of Records", "Address Name",
		      "File's Type Stock or Flux", "Filler");
	return (3, 6, 20, 1, 234);
    }

    if($section_number == 1){
	@{ $keys } = ("Policy Type", "Policy Holder Trade Name", "Policy Holder City",
		      "Policy Holder Area Code", "Policy Holder Address Additions",
		      "Policy Holder Address", "Policy Status", "Region Code",
		      "Notification Receiver", "Policy Status Date", "First Effect Date",
		      "Commercial Credit Rate", "Policy Country Number", "Commercial Sector",
		      "Filler");
	return (5, 32, 32, 5, 32, 32, 2, 2, 1, 8, 8, 4, 10, 3, 2, 9, 3, 3, 71);
    }

    if($section_number == 2){
	@{ $keys } = ("Buyer/Policy Holder Reference", "First Code", "Second Code",
		      "Buyer Ilkey 1", "Buyer Ilkey 2", "Buyer Ilkey 3",
		      "Trade Name 1", "Country Name", "Buyer Area Code",
		      "Trade Name 2", "Address 1", "Address 2");
	return (14, 15, 15, 5, 5, 5, 40, 30, 15, 40, 40, 40);
    }

    if($section_number == 3){
	@{ $keys } = ("Date of Request (it date)", "Amount of Request in Currency", "Currency",
		      "Currency Exchange Rate", "Currency Coefficient", "Amount of Request in Thousands",
		      "Risk's Type", "Receipt Date of the Request", "Policy Holder Date of the Request",
		      "Order Number", "Status Code", "Request's Type", "Keyboarding Agent Code",
		      "Keyboarding Agent User Id", "Effect Date", "Filler");
	return (8, 14, 3, 11, 2, 14, 1, 8, 8, 2, 1, 1, 3, 8, 8, 172);
    }

    if($section_number == 4){
	@{ $keys } = ("Address 1 Request On", "Trade Name 1 Request On", "Address 2 Request On",
		      "Trade Name 2 Request On", "City Request On", "Area Code Request On",
		      "Country Request On", "Address Type", "Country Number", "Filler");
	return (40, 40, 40, 40, 30, 15, 20, 1, 3, 35);
    }

    if($section_number == 5){
	@{ $keys } = ("Guarantor Country Ref. Request 1", "Guarantor Buyer Ref. Request 1",
		      "Guarantor Country Ref. Request 2", "Guarantor Buyer Ref. Request 2",
		      "Guarantor Country Ref. Request 3", "Guarantor Buyer Ref. Request 3",
		      "Filler");
	return (3, 6, 3, 6, 3, 6, 237);
    }

    if ($section_number == 6 or $section_number == 20){
	@{ $keys } = ("Decision Type", "Decision Reference Number", "Decision Date",
		      "Decision Amount (in thousands french francs)",
		      "Overtrade Amount (if existing) in Thousands French Francs",
		      "Date of Overtrade End", "Amount of the Decision in Currency",
		      "Overtrade Amount (if existing) in Currency", "Currency",
		      "Currency Exchange Rate", "Currency Coefficient",
		      "Effect Date of the Decision", "Name of the Underwriter",
		      "Phone Number of the Underwriter", "Buyer Prevous Ref.(transfer)",
		      "Filler", "Duplicata", "Automatic Decision Code",
		      "Keyboarding Agent Name", "Time of Decision", "Insured Percentage",
		      "Payment Conditions", "Inquiry Coast", "Order Number",
		      "Policy Previous Ref.(transfer)", "Decision Ilkey 1",
		      "Decision Ilkey 2", "Decision Ilkey 3", "Underwriter Code",
		      "Payment Conditions Code", "Filler");
	return (2, 6, 8, 14, 14, 8, 14, 14, 3, 11, 2, 8, 30, 11, 9,
		2, 1, 1, 30, 6, 3, 4, 1, 2, 12, 5, 5, 5, 3, 2, 28);
    }

    if ($section_number == 7 or $section_number == 21){
	@{ $keys } = ("Code of Special Clause of Decision", "Special Clause of Decision in Text",
		      "Additional Text Indication Code", "Filler");
	return (4, 210, 1, 49);
    }

    if ($section_number == 8 or $section_number == 22){
	@{ $keys } = ("Underwriter's Comments", "Additional Comments Indic. Code", "Filler");
	return (210, 1, 53);
    }

    if ($section_number == 9 or $section_number == 23){
	@{ $keys } = ("Country Code First Guarantor", "Buyer Code First Guarantor",
		      "Country Code Second Guarantor", "Buyer Code Second Guarantor",
		      "Country Code Third Guarantor", "Buyer Code Third Guarantor",
		      "Filler");
	return (3, 6, 3, 6, 3, 6, 237);
    }

    if($section_number == 10){
	@{ $keys } = ("Buyer Phone Number", "Buyer Telex", "Buyer Fax", "Buyer Legal Form",
		      "Buyer Registration Date", "Buyer Statute", "Buyer Nace Code",
		      "Buyer Country Name", "First Code", "Second Code", "Third Code",
		      "County", "Quotation Agent Name", "Country Number",
		      "Buyer Number", "Filler");
	return (15, 15, 15, 20, 8, 1, 4, 16, 35, 35, 35, 25, 30, 3, 6, 1);
    }

    if ($section_number == 11 or $section_number == 24){
	@{ $keys } = ("Buyer/Policy Holder Reference", "First Code", "Second Code",
		      "Third Code", "Trade Name 1", "Country Name", "Buyer Area Code",
		      "Trade Name 2", "Address 1", "Address 2");
	return (14, 15, 15, 15, 40, 30, 15, 40, 40, 40);
    }

    if ($section_number == 12 or $section_number == 13){
	@{ $keys } = ("Information Type", "Language Code", "Address", "Name",
		      "Payment Terms", "Diverse", "To the Attention of", "Phone",
		      "Signature", "Reference", "Duplicata", "Date of the Request",
		      "Amount of the Request", "Request Coefficient", "Coefficient",
		      "Currency", "Filler");
	return (1, 2, 1, 1, 1, 1, 25, 8, 30, 14, 1, 8, 13, 1, 2, 3, 152);
    }

    if($section_number == 14){
	@{ $keys } = ("Type", "Language Code", "To the Attention of",
		      "Signature", "Date of the Letter",
		      "Ic Bur.", "Comments 1", "Comments 2", "Filler");
	return (1, 2, 25, 30, 8, 1, 69, 69, 58);
    }

    if($section_number == 15){
	@{ $keys } = ("Broker Name", "Broker Address 1", "Broker Address 2",
		      "Broker Address 3", "Broker Area Code",
		      "Broker City", "Broker Key", "Broker Country Number",
		      "Filler");
	return (32, 32, 32, 32, 5, 26, 9, 3, 93);
    }

    if($section_number == 16){
	@{ $keys } = ("Buyer/Policy Holder Reference", "First Code", "Second Code",
		      "Third Code", "Trade Name 1", "Country Name", "Buyer Area Code",
		      "Trade Name 2", "Address 1", "Address 2");
	return (14, 15, 15, 15, 40, 30, 15, 40, 40, 40);
    }

    if ($section_number == 17 or $section_number == 18 or $section_number == 25){
	@{ $keys } = ("Buyer Phone Number", "Buyer Telex", "Buyer Fax",
		      "Buyer Legal Form", "Buyer Registration Date", "Buyer Statute",
		      "Buyer Nace Code", "Buyer Country Name", "First Code", "Second Code",
		      "Third Code", "County", "Quotation Agent Name", "Country Number",
		      "Buyer Number", "Filler");
	return (15, 15, 15, 20, 8, 1, 4, 16, 35, 35, 35, 25, 30, 3, 6, 1);
    }

    if($section_number == 19){
	@{ $keys } = ("Type", "Creation Date", "Request Date", "Sequence Number",
		      "Total Amount", "Currency of Decision", "Request Underwriter(code)",
		      "Decision Code", "Last Modification Date", "Decreased Limit Amount",
		      "Answer Date", "Comment 1 on Decision", "Comment 2 on Decision",
		      "Flag Allgemeine Inkasso", "State of KZU or NZM", "Medium",
		      "Old Buyer Country Number", "Old Buyer Sequential Number",
		      "Old Policy Number", "Old Sequence Number", "Decision Underwriter (code)",
		      "Decision Underwriter (name)", "Filler");
	return (1, 8, 8, 3, 12, 3, 3, 1, 8, 12, 8, 60, 60, 1, 1, 1, 3, 6, 12, 3, 3, 30, 17);
    }

    if($section_number == 26){
	@{ $keys } = ("Decision Type", "Rating", "Last Update Date/Time", "First Creation Date/Time",
		      "Type of Rating", "Fax Notification", "Policy Transfert", "Solicited Rating Flag",
		      "Guarantee Flag", "Status Rating Flag", "Filler");
	return (2, 3, 14, 14, 1, 1, 12, 1, 1, 1, 214);
    }

    if($section_number == 27){
	@{ $keys } = ("Address 1", "Trade Name 1", "Address 2", "Trade Name 2",
		      "City", "Area Code", "Country Request", "Address Type",
		      "Country Number", "Filler");
	return (40, 40, 40, 40, 30, 15, 20, 1, 3, 35);
    }

    if($section_number == 28){
	@{ $keys } = ("Delivery Date", "Type of Report", "Country Number",
		      "Order Number", "Trade Name", "Filler");
	return (8, 2, 4, 12, 50, 188);
    }

    # se nao for nenhum desses, devolve um array vazio
    return ();
}

############################################################
# pega os valores do hash referenciado por $fields
# o segundo parametro (opcional) referencia um vetor
# com as chaves do hash cujos valores serao retornados,
# se nao for fornecido, todos os valores sao retornadas em um vetor
sub get_fields{
    my ($fields, $fields_to_show) = @_;
    my $i;
    my @result = ();

    @{ $fields_to_show } = keys %{ $fields } unless $fields_to_show;
    for $i (@{ $fields_to_show }){
	if(exists $fields->{$i}){
	    push @result, $fields->{$i};
	}
    }
    return @result;
}

############################################################
sub get_line{
    my ($s, $n) = @_; # pega a enesima linha de $s
    my @row;

    for my $i (1..$n){
	@row = $s->fetchrow;
    }
    return @row;
}

############################################################
sub help{
    print <<HELP;
Uso: $_[0] <arquivo(s) de dados>
HELP
    exit;
}

############################################################
# elimina espacos em branco no comeco e no final da string
sub trim{
    my $s = shift;
    $s =~ s/^\s*//;
    $s =~ s/\s*$//;
    return $s;
}

1;
