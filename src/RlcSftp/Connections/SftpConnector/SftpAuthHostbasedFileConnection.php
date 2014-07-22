<?php

namespace RlcSftp\Connections\SftpConnector;

use \InvalidArgumentException;
use \RuntimeException;

class SftpAuthHostbaseFileConnection extends AbstractSftpConnection
{
    protected $pubKey        = null;
    protected $privKey       = null;
    protected $passphrase    = null;
    protected $localHost     = null;
    protected $localUsername = null;

    public function login(
        $hostname,
        $pubKey,
        $privKey,
        $passphrase = null,
        $localUser = null,
        $username = null
    ) {
        if (empty($this->connection)) {
            $this->connect();
        }
        
        $this->pubKey        = $pubKey;
        $this->privKey       = $privKey;
        $this->localHost     = $hostname;
        $this->passphrase    = $passphrase;
        $this->localUsername = $localUser;
        
        $u = $this->username;
        if (!empty($username)) {
            $u = $username;
        }

        if (empty($u)) {
            throw new InvalidArgumentException("Username is not set. Either pass it to " . __CLASS_ . "::setUsername or pass it to this function.");
        }

        ssh2_auth_hostbased_file(
            $this->connection,
            $u,
            $hostname,
            $pubKey,
            $privKey,
            $passphrase,
            $localUser
        );
        $this->setSftp();
    }
}
