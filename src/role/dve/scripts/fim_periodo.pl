#!/usr/bin/perl
# Verifica se o final do período da DVE já se esgotou

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

use Date::Manip;
@argv = &process(@ARGV);

$dias = 15; # qtos dias depois do fim do periodo pode gerar notificacao

#print "Entrei...\n";
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
#    print "Abri o banco...\n";
}
#print "Pronto...\n";

#&query("select distinct idInform from DVE");
# &query("select id from Inform WHERE state = 10 and codProd = 1");
# while($db->FetchRow){
#     ($aux) = $db->Data;
#     push @idInforms, $aux;
# }
# $num_dves = 12; # ********** numero de dves, pode mudar a qualquer momento!! **********
# for $idInform (@idInforms){
#     &query("select startValidity, endValidity from Inform where id=$idInform");
#     $db->FetchRow;
#     ($start, $end) = $db->Data;
#     &query("select count(*) from DVE where idInform=$idInform");
#     $db->FetchRow;
#     ($aux) = $db->Data;
#     if($aux != $num_dves){ # nao tem todas as dves, criar as que faltam
# 	for $num (1..$num_dves){
# 	    &query("select count(*) from DVE where idInform=$idInform and num=$num");
# 	    $db->FetchRow;
# 	    ($aux) = $db->Data;
# 	    if($aux == 0){ # se nao existe, cria
# 		if($num == 1){
# 		    $inicio = $start;
# 		}else{
# 		    $inicio = &dmy2ymd(&getEndDate($idInform, &ymd2dmy($start), $num - 1, 1));
# 		}
# 		&query("insert into DVE (idInform, state, inicio, num) values ($idInform, 1, '$inicio', $num)");
# 	    }
# 	}
#     }
# }

#&query("select id from DVE where state <> 2"); # DVEs q nao foram enviadas
&query("SELECT d.id, d.idInform, d.state, d.AR, d.total2, d.total3,
        d.nCompr2, d.nCompr3, d.inicio, d.periodo, d.num,
        d.bornDate, d.sentDate, d.gerDate, d.recDate, i.name
        FROM DVE d join Inform i on d.idInform=i.id
        WHERE d.state <> 2 and i.codProd = 1");
while($db->FetchRow){
    my @aux = $db->Data;
    push @ids, [ @aux ];
}

for $aux (@ids){
    # nem sei se vao usar todos os campos deste SELECT
#     &query("SELECT d.idInform, d.state, d.AR, d.total2, d.total3,
#             d.nCompr2, d.nCompr3, d.inicio, d.periodo, d.num, d.bornDate,
#             d.sentDate, d.gerDate, d.recDate, i.name FROM
#             DVE d join Inform i on d.idInform=i.id where d.id=$idDVE");
    ($idDVE, $idInform, $state, $ar_code,
     $total2, $total3, $nCompr2,
     $nCompr3, $inicio, $periodo,
     $num, $bornDate, $sentDate,
     $gerDate, $recDate, $name) = @{ $aux };
    if(&se_passaram_n_dias($dias, $inicio, $periodo, $idInform)){ # se já se passaram n dias, gera notificacao
	&query("select count(*) from NotificationR where state=1 and notification = 'Comitê de Cancelamento [$name]'");
	$db->FetchRow;
	($count) = $db->Data;
	if($count == 0){ # se a notificacao ainda nao existe, gera a maldita...
	    &query("insert into NotificationR (cookie, state, notification) values ('', 1, 'Comitê de Cancelamento [$name]')");
	    &query("select max(id) from NotificationR");
	    $db->FetchRow;
	    ($idNotification) = $db->Data;
	    &query("update NotificationR set link='../dve/Dve.php?comm=comite&idDVE=$idDVE&idNotification=$idNotification'
                    where id=$idNotification");
	    # gera notificacao pro funcionario com role dve
	    &query("insert into RoleNotification (idRole, idNotification) values (16, $idNotification)");
	}
    }
}

############################################################
# executa uma query, devolve o numero de linhas resultantes
sub query{
    my ($q, $verbose) = @_;

    print "$q<br>\n" if $verbose;
    $db->Sql($q); # if($q =~ m/^select/i);
    return $db->RowCount;
}

############################################################
# mostra os resultados de uma query
sub show_results{
    while ($db->FetchRow) {
	for my $val ($db->Data){
	    print "$val ";
	}
	print "\n";
    }
}

################################
# transforma a data de 2002-09-10 para 10/09/2002
sub ymd2dmy{
    my $date = shift;
    my ($a, $m, $d) = $date =~ /(\d\d\d\d)-(\d\d?)-(\d\d?)/;
    return "$d/$m/$a";
}

sub process{ # processa os argumentos
    my @v;
    for (@_){
	if(/^-temp$/){
	    $temp = 1; # indica que vai usar o pacote Temp
	}elsif(/^-help$/){
	    &show_help();
	}else{
	    push @v, $_;
	}
    }
    return @v;
}

sub show_help{
    print <<HELP;
Uso: $0 [-temp]
HELP
    exit;
}

sub dmy2ymd{
    my $date = shift;
    my ($d, $m, $a) = $date =~ m[(\d{1,2})/(\d{1,2})/(\d\d\d\d)];
    return "$a-$m-$d 00:00:00.000";
}

sub getEndDate{
    my ($idInform, $d, $n) = @_;
    if($d =~ m[(\d\d?)/(\d\d?)/(\d\d\d\d)]){
	$dia = $1;
	$mes = $2;
	$ano = $3;
    }
    if($dia == 1){
      $num_dves = 12;
    }else{
      $num_dves = 13;
    }

    if($n != $num_dves){
	return &ultimo_dia($mes, $ano). "/$mes/$ano";
    }else{
	&query("select endValidity from Inform where id=$idInform");
	$db->FetchRow;
	($end) = $db->Data;
	return &ymd2dmy($end);
    }
}

sub get_mes{
    my $n = shift;
    if($n <= 12){
	return $n;
    }
    return $n % 12;
}

sub ultimo_dia{
    my ($m, $a) = @_;
    if($m == 1 || $m == 3|| $m == 5 || $m == 7 || $m == 8 || $m == 10 || $m == 12){
	return 31;
    }elsif($m == 2){
	if($a % 4 == 0){
	    return 29;
	}else{
	    return 28;
	}
    }else{
	return 30;
    }
}

# verifica se ja se passaram $dias dias do final do periodo
sub se_passaram_n_dias{
    my ($dias, $inicio, $periodo, $idInform) = @_;
    my $fim = &getEndDate($idInform, &ymd2dmy($inicio), $num);
    my ($d, $m, $a) = $fim =~ m[(\d\d?)/(\d\d?)/(\d\d\d\d)];
    #my $time_stamp = &Date_SecsSince1970($m, $d + $dias + 1, $a, 0, 0, 0); # fim do periodo mais $dias dias
    my $time_stamp = &Date_SecsSince1970($m, $d , $a, 0, 0, 0) + ($dias * 24 * 3600); # fim do periodo mais $dias dias

    my ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) = localtime(time);
    my $today = &Date_SecsSince1970($mon + 1, $mday, $year + 1900, 0, 0, 0);
    #print "idInform=$idInform, fim=$d/$m/$a  today=$today, timestamp=$time_stamp";

    if($today >= $time_stamp){
	#print "    *** passou! <br>";
	return 1;
    }
#    print "<br>";
    return 0;
}
