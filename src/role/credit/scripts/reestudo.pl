#!/usr/bin/perl

#################################################################################
# pacote Temp: usado apenas para testes no linux, já q nao tem o modulo Win32::ODBC no esfiha
package Temp;
sub new{ return bless {}; }
sub Sql{ return 1; }
sub FetchRow{ return 1; }
sub Close{ return 1; }
sub Data{ return 1; }
sub RowCount{ return 1; }
#################################################################################

package main;

use funcoes;

$temp = shift;

if($temp){ # se estamos usando o pacote Temp
    print "using Temp...\n";
    $db = new Temp;
}else{
    eval 'use Win32::ODBC';
    # abre a conexao com o banco de dados
    eval "\$db = new Win32::ODBC(\"dsn=sbce;UID=sa;PWD=\")";
    if($@){ # se nao conseguiu abrir o BD, cai fora
	die "Nao consegui me conectar ao banco de dados: $DBI::errstr\n$@\n";
    }
}

&query("select i.id, i.idInform, inf.idRegion, inf.name from Inform inf join Importer i on i.idInform=inf.id where inf.state in (4,5)");
while($db->FetchRow){
    my ($idImporter, $idInform, $idRegion, $name) = $db->Data;
    my %h = (idImporter => $idImporter,
	     idInform => $idInform,
	     idRegion => $idRegion,
	     name => &trim($name));
    push @importers, { %h };
}

for $h (@importers){
    &query("select * from ChangeCredit where idImporter=$h->{idImporter} and credit is not null ".
	   "and id in (select max(id) from ChangeCredit where idImporter=$h->{idImporter} and credit is not null) ".
	   "and creditDate <= getdate() - 150"); # credito concedido ha mais de 5 meses
    if($db->FetchRow){
	# altera o status do inform
	&query("update Inform set state=2 where id=$h->{idInform}");

	# gera a notificacao
	$notification = "Importadores com crédito concedido há mais de 5 meses [$h->{name}]";
	&query("select * from NotificatioR where notification = '$notification' and state=1");
	if($db->FetchRow){ # se ja existe notificacao, nao cria outra
	    next;
	}
	sleep(1);
	$key = time();
	&query("INSERT INTO NotificationR (cookie, notification, idRegion) VALUES ('$key', '$notification', $h->{idRegion})");

        &query("SELECT max(id) FROM NotificationR WHERE cookie='$key'");
	$db->FetchRow;
        ($idNotification) = $db->Data;
        &query("UPDATE NotificationR SET link='../executive/Executive.php?comm=cinco_meses&idInform=$h->{idInform}&idNotification=$idNotification' WHERE id=$idNotification");
	if($h->{idRegion} == 0){
	    $roleNumber = 6;
	}else {
	    $roleNumber = 2;
	}
	&query("INSERT INTO RoleNotification (idRole, idNotification) VALUES ($roleNumber, $idNotification)");
    }
}

$db->Close();

############################################################
# executa uma query, devolve o numero de linhas resultantes
sub query{
    my $q = shift;

    print "$q\n";
    $db->Sql($q);
    return $db->RowCount;
}
