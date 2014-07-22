<?php

namespace RlcSftp\Tests\Connections\FtpConnector;

use \PHPUnit_Framework_TestCase;
use RlcSftp\Connections\FtpConnector\FtpConnection;
use RlcSftp\Connections\Connector\ConnectorInterface;

/**
 * In order to run these tests you must have a global $ftp_credentials variable with the following keys.
 *
 * $ftp_credentials['hostname'];
 * $ftp_credentials['username'];
 * $ftp_credentials['password'];
 * 
 * This is to allow the Tests to verify they work with your server.
 */
class FtpConnectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        global $ftp_credentials;
        if (!isset($ftp_credentials)) {
            throw new RuntimeException("In order to run these tests, you must include a global \$ftp_credentials array. with keys, hostname, username, password.");
        }
        $this->ftp_credentials = $ftp_credentials;
        parent::setUp();
    }
    
    public function testConnectDefaults()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $this->assertTrue($ftp->connect());
    }

    public function testConnectBadHost()
    {
        $ftp = new FtpConnection("Foobar");
        $this->assertFalse($ftp->connect());
    }

    public function testConnectBadPort()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname'], "badPort");
        $this->assertFalse($ftp->connect());
    }

    /**
     * @depends testConnectDefaults
     */
    public function testDisconnectGoodConnect()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->connect();
        $this->assertTrue($ftp->disconnect());
    }

    public function testDisconnectNoConnect()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $this->assertTrue($ftp->disconnect());
    }
    /**
     * @depends testConnectBadHost
     * @depends testConnectBadPort
     */
    public function testDisconnectBadConnect()
    {
        $ftp = new FtpConnection("FooBar");
        $ftp->connect();
        $this->assertTrue($ftp->disconnect());

        $ftp = null;
        $ftp = new FtpConnection($this->ftp_credentials['hostname'], "BadPort");
        $ftp->connect();
        $this->assertTrue($ftp->disconnect());
    }

    public function testSetUsername()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $username = "Username";
        $this->assertEquals($username, $ftp->setUsername($username));
    }
    
    public function testSetPassword()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $password = "password";
        $this->assertEquals($password, $ftp->setUsername($password));
    }

    /**
     * @depends testConnectDefaults
     * @depends testSetUsername
     * @depends testSetPassword
     */
    public function testLoginSuccess()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->setUsername($this->ftp_credentials['username']);
        $ftp->setPassword($this->ftp_credentials['password']);
        $ftp->connect();
        $this->assertTrue($ftp->login());
    }

    /**
     * @depends testConnectDefaults
     * @depends testSetUsername
     * @depends testSetPassword
     */
    public function testLoginSuccessWithUsername()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->setUsername($this->ftp_credentials['username']);
        $ftp->setPassword($this->ftp_credentials['password']);
        $ftp->connect();
        $this->assertTrue($ftp->login($this->ftp_credentials['username']));
    }

    /**
     * @depends testConnectDefaults
     * @depends testSetUsername
     * @depends testSetPassword
     */
    public function testLoginSuccessWithUsernameandPassword()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->setUsername($this->ftp_credentials['username']);
        $ftp->setPassword($this->ftp_credentials['password']);
        $ftp->connect();
        $this->assertTrue($ftp->login($this->ftp_credentials['username'], $this->ftp_credentials['password']));
    }

    /**
     * @depends testConnectDefaults
     * @depends testSetUsername
     * @depends testSetPassword
     */
    public function testLoginFailure()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->setUsername("MakeSureThisIsNotAUser");
        $ftp->setPassword("AnythingGoesWithPlayDoh");
        $ftp->connect();
        $this->assertFalse($ftp->login());
        unset($ftp);
    }

    /**
     * @depends testConnectDefaults
     * @depends testSetUsername
     * @depends testSetPassword
     * @expectedException RuntimeException
     */
    public function testLoginNoConnection()
    {
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->setUsername("MakeSureThisIsNotAUser");
        $ftp->setPassword("AnythingGoesWithPlayDoh");
        $this->assertFalse($ftp->login());
        unset($ftp);
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     * @expectedException RuntimeException
     */
    public function testPutLocalFileNotExists()
    {
        $localfile = "Nothing Here";
        $remoteDir = "./TestDir/";
        $filename   = "Foobar.txt";
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->setUsername($this->ftp_credentials['username']);
        $ftp->setPassword($this->ftp_credentials['password']);
        $ftp->connect();
        $ftp->login();
        $ftp->put($localfile, $remoteDir, $filename);
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     * @expectedException RuntimeException
     */
    public function testPutRemoteDirNotExists()
    {
        $localfile = sys_get_temp_dir() . "Foobar.txt";
        touch($localfile);
        if (file_exists($localfile)) {
            $remoteDir = "/MakeBelieveDirectory";
            $filename   = "Foobar.txt";
            $ftp = new FtpConnection($this->ftp_credentials['hostname']);
            $ftp->setUsername($this->ftp_credentials['username']);
            $ftp->setPassword($this->ftp_credentials['password']);
            $ftp->connect();
            $ftp->login();
            $ftp->put($localfile, $remoteDir, $filename);
            
        } else {
            print("Local file could not be touched");
        }
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     * @expectedException RuntimeException
     */
    public function testPutNoLogin()
    {
        $localfile = sys_get_temp_dir() . "Foobar.txt";
        touch($localfile);
        if (file_exists($localfile)) {
            $remoteDir = "./TestDir/";
            $filename   = "Foobar.txt";
            $ftp = new FtpConnection($this->ftp_credentials['hostname']);
            $ftp->connect();
            $ftp->put($localfile, $remoteDir, $filename);
        } else {
            print("Local file could not be touched");
        }
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     */
    public function testPutNoConnection()
    {
        $localfile = sys_get_temp_dir() . "Foobar.txt";
        touch($localfile);
        if (file_exists($localfile)) {
            $remoteDir = "./TestDir/";
            $filename   = "Foobar.txt";
            $ftp = new FtpConnection($this->ftp_credentials['hostname']);
            $ftp->setUsername($this->ftp_credentials['username']);
            $ftp->setPassword($this->ftp_credentials['password']);
            $ftp->disconnect();
            $ftp->put($localfile, $remoteDir, $filename);
        } else {
            print("Local file could not be touched");
        }
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     * @expectedException RuntimeException
     */
    public function testPutNoConnectionUnableToConnect()
    {
        $localfile = sys_get_temp_dir() . "Foobar.txt";
        touch($localfile);
        if (file_exists($localfile)) {
            $remoteDir = "./TestDir/";
            $filename   = "Foobar.txt";
            $ftp = new FtpConnection("BadHostName");
            $ftp->setUsername($this->ftp_credentials['username']);
            $ftp->setPassword($this->ftp_credentials['password']);
            $ftp->disconnect();
            $ftp->put($localfile, $remoteDir, $filename);
        } else {
            print("Local file could not be touched");
        }
    }

     /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     */
    public function testPutSuccess()
    {
        $localfile = sys_get_temp_dir() . "Foobar.txt";
        touch($localfile);
        if (file_exists($localfile)) {
            $remoteDir = "./TestDir/";
            $filename   = "Foobar.txt";
            $ftp = new FtpConnection($this->ftp_credentials['hostname']);
            $ftp->setUsername($this->ftp_credentials['username']);
            $ftp->setPassword($this->ftp_credentials['password']);
            $ftp->connect();
            $ftp->login();
            $this->assertTrue($ftp->put($localfile, $remoteDir, $filename));
        } else {
            print("Local file could not be touched");
        }
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     * @expectedException RuntimeException
     */
    public function testFlistNoConnection()
    {
        $remoteDir = "./TestDir/";
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->flist($remoteDir);
    }

   /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     * @expectedException RuntimeException
     */
    public function testFlistNoLogin()
    {
        $remoteDir = "./TestDir/";
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->connect();
        $ftp->flist($remoteDir);
    }

   /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     */
    public function testFlistSuccess()
    {
        $remoteDir = "./TestDir/";
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->connect();
        $ftp->setUsername($this->ftp_credentials['username']);
        $ftp->setPassword($this->ftp_credentials['password']);
        $this->assertTrue(false !== $ftp->flist($remoteDir));
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     */
    public function testGetLocalFileNotExists()
    {
        $localfile = "Foobar.txt";
        $remoteDir = "./TestDir/";
        $filename   = "Foobar.txt";
        $ftp = new FtpConnection($this->ftp_credentials['hostname']);
        $ftp->setUsername($this->ftp_credentials['username']);
        $ftp->setPassword($this->ftp_credentials['password']);
        $ftp->connect();
        $ftp->login();
        $ftp->get($localfile, $remoteDir, $filename);

    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     * @expectedException RuntimeException
     */
    public function testGetRemoteDirNotExists()
    {
        $localfile = sys_get_temp_dir() . "Foobar.txt";
        touch($localfile);
        if (file_exists($localfile)) {
            $remoteDir = "/MakeBelieveDirectory";
            $filename   = "Foobar.txt";
            $ftp = new FtpConnection($this->ftp_credentials['hostname']);
            $ftp->setUsername($this->ftp_credentials['username']);
            $ftp->setPassword($this->ftp_credentials['password']);
            $ftp->connect();
            $ftp->login();
            $ftp->get($localfile, $remoteDir, $filename);
            
        } else {
            print("Local file could not be touched");
        }
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     * @expectedException RuntimeException
     */
    public function testGetNoLogin()
    {
        $localfile = sys_get_temp_dir() . "Foobar.txt";
        touch($localfile);
        if (file_exists($localfile)) {
            $remoteDir = "./TestDir/";
            $filename   = "Foobar.txt";
            $ftp = new FtpConnection($this->ftp_credentials['hostname']);
            $ftp->connect();
            $ftp->get($localfile, $remoteDir, $filename);
        } else {
            print("Local file could not be touched");
        }
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     */
    public function testGetNoConnection()
    {
        $localfile = sys_get_temp_dir() . "Foobar.txt";
        touch($localfile);
        if (file_exists($localfile)) {
            $remoteDir = "./TestDir/";
            $filename   = "Foobar.txt";
            $ftp = new FtpConnection($this->ftp_credentials['hostname']);
            $ftp->setUsername($this->ftp_credentials['username']);
            $ftp->setPassword($this->ftp_credentials['password']);
            $ftp->disconnect();
            $ftp->get($localfile, $remoteDir, $filename);
        } else {
            print("Local file could not be touched");
        }
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     * @expectedException RuntimeException
     */
    public function testGetNoConnectionUnableToConnect()
    {
        $localfile = sys_get_temp_dir() . "Foobar.txt";
        touch($localfile);
        if (file_exists($localfile)) {
            $remoteDir = "./TestDir/";
            $filename   = "Foobar.txt";
            $ftp = new FtpConnection("BadHostName");
            $ftp->setUsername($this->ftp_credentials['username']);
            $ftp->setPassword($this->ftp_credentials['password']);
            $ftp->disconnect();
            $ftp->get($localfile, $remoteDir, $filename);
        } else {
            print("Local file could not be touched");
        }
    }

    /**
     * @depends testSetUsername
     * @depends testSetPassword
     * @depends testConnectDefaults
     * @depends testLoginSuccess
     */
    public function testGetSuccess()
    {
        $localfile = sys_get_temp_dir() . "Foobar.txt";
        touch($localfile);
        if (file_exists($localfile)) {
            $remoteDir = "./TestDir/";
            $filename   = "Foobar.txt";
            $ftp = new FtpConnection($this->ftp_credentials['hostname']);
            $ftp->setUsername($this->ftp_credentials['username']);
            $ftp->setPassword($this->ftp_credentials['password']);
            $ftp->connect();
            $ftp->get($localfile, $remoteDir, $filename);
        } else {
            print("Local file could not be touched");
        }
    }
}
