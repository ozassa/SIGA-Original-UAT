#!/usr/bin/perl

use IO::Socket::INET;

$sock = IO::Socket::INET->new(LocalHost => 'esfiha',
			      LocalPort => 1654,
			      Proto     => 'tcp',
			      Listen    => 5,
			      Reuse     => 1
			      );

die "$!\n" unless $sock;

# fica esperando chegar confirmacao
while($new_sock = $sock->accept()){
    while(<$new_sock>){
	print;
    }
}

close $sock;
