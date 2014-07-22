<?php

namespace RlcSftp\Connections\SftpConnector;

use \InvalidArgumentException;
use \RuntimeException;

class SftpAuthNoneConnection extends AbstractSftpConnection
{
    public function login($username = null)
    {
        if (empty($this->connection)) {
            $this->connect();
        }
        
        $u = $this->username;

        if (!empty($username)) {
            $u = $username;
        }

        if (empty($u)) {
            throw new InvalidArgumentException("Username is not set. Either pass it to " . __CLASS_ . "::setUsername or pass it to this function.");
        }
        
        ssh2_auth_agent($this->connection, $u);
        $this->setSftp();
    }
}
