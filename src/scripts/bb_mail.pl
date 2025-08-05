#!/usr/bin/perl

#use Mail::Sender;
use Mail::Mailer;

$dir = 'e:\projects\sbce\siex\src\download'; # mudar para o diretorio q vcs usam
$key = time().$$.'BB.mdb';
system "copy $dir\\BB.mdb $dir\\$key";
system "copy $dir\\BBTempl.mdb $dir\\BB.mdb";

&envia();

# envia o email
sub envia{
    my $link = "http://tdc144/siex/src/download/$key"; # mudar para o endereço de vcs

    my $msg =<<MSG;
Oi,

O arquivo esta em $link

Tchau
MSG


$mail = new Mail::Mailer 'smtp', Server => 'mailhost.rest';
$mail->open(
	     {
#	      To      => 'joaomaciel@bb.com.br,fabianolinden@bb.com.br,fred@sbce.com.br',
	      To      => 'eduardo@tendencies.com.br,eduardo@tendencies.com.br',
	      From    => 'Fred Stuckenbruck <fred@sbce.com.br>',
	      Subject => 'Sistema WEB SBCE-SIEx'
	     }
	    );

print $mail $msg;

$mail->close;

# Segundo
$mail = new Mail::Mailer 'smtp', Server => 'mailhost.rest';
$mail->open(
	     {
#	      To      => 'joaomaciel@bb.com.br,fabianolinden@bb.com.br,fred@sbce.com.br',
	      To      => 'eduardo@tendencies.com.br,eduardo@tendencies.com.br',
	      From    => 'Fred Stuckenbruck <fred@sbce.com.br>',
	      Subject => 'Sistema WEB SBCE-SIEx'
	     }
	    );

print $mail $msg;

$mail->close;

#Terceiro
$mail = new Mail::Mailer 'smtp', Server => 'mailhost.rest';
$mail->open(
	     {
#	      To      => 'joaomaciel@bb.com.br,fabianolinden@bb.com.br,fred@sbce.com.br',
	      To      => 'eduardo@tendencies.com.br,eduardo@tendencies.com.br',
	      From    => 'Fred Stuckenbruck <fred@sbce.com.br>',
	      Subject => 'Sistema WEB SBCE-SIEx'
	     }
	    );

print $mail $msg;

$mail->close;

}
