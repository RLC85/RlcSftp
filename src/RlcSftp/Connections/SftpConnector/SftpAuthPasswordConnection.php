<?php

namespace RlcSftp\Connections\SftpConnector;

use \InvalidArgumentException;
use \RuntimeException;

class SftpAuthPasswordConnection extends AbstractSftpConnection
{
    public function login($username = null, $password = null)
    {
        if (empty($this->connection)) {
            $this->connect();
        }
        
        $u = $this->username;
        $p = $this->password;

        if (!empty($username)) {
            $u = $username;
        }

        if (!empty($password)) {
            $p = $password;
        }

        if (empty($u)) {
            throw new InvalidArgumentException("Username is not set. Either pass it to " . __CLASS_ . "::setUsername or pass it to this function.");
        }

        if (empty($p)) {
            throw new InvalidArgumentException("Password is not set. Either pass it to " . __CLASS_ . "::setPassword or pass it to this function.");
        }
        
        ssh2_auth_password($this->connection, $u, $p);
        $this->setSftp();
    }
}
