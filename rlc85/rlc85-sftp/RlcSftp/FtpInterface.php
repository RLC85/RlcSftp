<?php

namespace RlcSftp;

interface FtpInterface
{
	public function connect($host, $port = 22, $timeout = 90);

	public function disconnect();
}