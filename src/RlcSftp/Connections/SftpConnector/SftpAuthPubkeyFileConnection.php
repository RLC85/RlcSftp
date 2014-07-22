<?php

namespace RlcSftp\Connections\SftpConnector;

use \InvalidArgumentException;
use \RuntimeException;

class SftpAuthPubkeyFileConnection extends AbstractSftpConnection
{
    protected $pubKey        = null;
    protected $privKey       = null;
    protected $passphrase    = null;

    public function login(
        $pubKey,
        $privKey,
        $passphrase = null
    ) {
        if (empty($this->connection)) {
            $this->connect();
        }
        
        $this->pubKey        = $pubKey;
        $this->privKey       = $privKey;
        $this->passphrase    = $passphrase;
        
        $u = $this->username;
        if (!empty($username)) {
            $u = $username;
        }

        if (empty($u)) {
            throw new InvalidArgumentException("Username is not set. Either pass it to " . __CLASS_ . "::setUsername or pass it to this function.");
        }

        ssh2_auth_pubkey_file(
            $this->connection,
            $u,
            $pubKey,
            $privKey,
            $passphrase
        );
        $this->setSftp();
    }
}
