#!/usr/bin/perl

use IO::Socket::INET;

$sock = IO::Socket::INET->new(PeerAddr  => 'esfiha',
			      PeerPort  => 1654,
			      Proto     => 'tcp');

die "$!\n" unless $sock;

$n = $ARGV[0] || 10;
for $i (1..$n){
    print $sock "$i\n";
}

close $sock;
