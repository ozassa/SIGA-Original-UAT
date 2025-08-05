#!/usr/bin/perl

use IO::Socket::INET;

$sock = IO::Socket::INET->new(LocalPort => 1654,
			      Proto     => 'udp');

die "$!\n" unless $sock;

# fica esperando chegar confirmacao
while($sock->recv($data, 1024)){
    print "$data";
}

close $sock;
