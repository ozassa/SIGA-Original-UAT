use Win32::ODBC;

# abre a conexao com o banco de dados
$db = new Win32::ODBC("dsn=sbce;UID=sa;PWD=aerdna4705") or die "Nao consegui me conectar ao banco de dados: $DBI::errstr\n";

srand(time ^ $$);

# notificacoes q terminaram há mais de 3 meses...
#&query("select id, name, idRegion from Inform where state=10 and day(endValidity) = day(getdate() + 91)
#        and month(endValidity) = month(getdate() + 91) and year(endValidity) = year(getdate() + 91)");
&query("select id, name, idRegion from Inform where state = 10 and endValidity < getdate() + 90");

while($db->FetchRow){
    ($id, $name, $region) = $db->Data;
    %f = (id => $id, name => $name, region => $region);
    push @inform, { %f };
}

for $h (@inform){
    ($id, $name, $region) = ($h->{id}, $h->{name}, $h->{region});
    # cria a notificacao
    $cookie = int(rand(10000)). time();
    &query("insert into NotificationR (cookie, notification, idRegion) values ('$cookie', 'Início do período de renovação [$name]', $region)");
    &query("select max(id) from NotificationR where cookie='$cookie'");
    $db->FetchRow;
    ($idNotification) = $db->Data;
    &query("update NotificationR
          set link='../executive/Executive.php?comm=renovacao&idInform=$id&idNotification=$idNotification'
          where id=$idNotification");
   # &query("insert into RoleNotification (idRole, idNotification) values (2, $idNotification)");
    &query("insert into RoleNotification (idRole, idNotification) values (11, $idNotification)");
}

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year += 1900;

# cessoes q nao foram aceitas depois de 30 dias
# &query("select id, idInform, codigo from CDBB where dateBackoffice is null and dateClient < getdate() - 30");
# while($db->FetchRow){
#    ($id, $idInform, $codigo) = $db->Data;
#    %h = (id => $id, idInform => $idInform, codigo => $codigo);
#    push @c, { %h };
#}
#for $h (@c){
#    # cessao perde a validade e libera os importadores
#    ($id, $idInform, $codigo) = ($h->{id}, $h->{idInform}, $h->{codigo});
#    &query("update CDBB set status=3 where id=$id");

    # cria a notificacao
#    $cookie = int(rand(10000)). time();
#    &query("select name from Inform where id=$idInform");
#    $db->FetchRow;
#    ($name) = $db->Data;

#    &query("INSERT INTO NotificationR (state, cookie, notification) VALUES (1, '$cookie', '($codigo/$year) Cessão de Direito sem Valor [$name]')");
#    &query("select max(id) from NotificationR where cookie='$cookie'");
#    $db->FetchRow;
#    ($idNotification) = $db->Data;

#    &query("UPDATE NotificationR
#            SET link ='../cessao/Cessao.php?comm=view&idInform=$idInform&idCDBB=$id&idNotification=$idNotification'
#            WHERE id = $idNotification");
#    &query("INSERT INTO RoleNotification (idRole, idNotification) VALUES (7, $idNotification)");
#}

sub query{
    my $q = shift;

    print "$q\n";
    $db->Sql($q);
    return $db->RowCount;
}
