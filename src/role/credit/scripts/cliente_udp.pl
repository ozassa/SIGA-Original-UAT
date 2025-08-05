#!/usr/bin/perl

use IO::Socket::INET;

$sock = IO::Socket::INET->new(PeerPort  => 1654,
			      PeerAddr  => 'localhost',
			      Proto => 'udp');

die "$!\n" unless $sock;

$n = $ARGV[0] || 10;
for $i (1..$n){
    $sock->send("$i\n");
}

close $sock;
