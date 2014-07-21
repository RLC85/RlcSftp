<?php

namespace RlcSftp\Connections\Connector;

use \InvalidArgumentException;
use \RuntimeException;

interface ConnectorInterface
{
    const ASCII      = FTP_ASCII;
    const BINARY     = FTP_BINARY;
    const TEXT       = FTP_TEXT;
    const IMAGE      = FTP_IMAGE;
    const AUTORESUME = FTP_AUTORESUME;
    const FAILED     = FTP_FAILED;
    const FINISHED   = FTP_FINISHED;
    const MOREDATE   = FTP_MOREDATA;


    public function put($localFile, $remoteFile, $mode);

    public function get($remoteFile);

    public function list($remote);

    public function chdir($remote);

    public function connect();

    public function login();

    public function disconnect();
}