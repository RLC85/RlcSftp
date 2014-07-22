<?php

namespace RlcSftp\Connections\SftpConnector {

    use RlcSftp\Tests\Connections\SftpConnector\AbstractSftpConnectionTest;
    
    function ssh2_connect()
    {
        return AbstractSftpConnectionTest::$ssh2_connect;
    }

    function opendir()
    {
        return AbstractSftpConnectionTest::$opendir;
    }

    function readdir()
    {
        if(AbstractSftpConnectionTest::$readdir) {
            AbstractSftpConnectionTest::$readdir = false;
            return "string";
        } else {
            return false;
        }
    }
}


namespace RlcSftp\Tests\Connections\SftpConnector {

    use \PHPUnit_Framework_TestCase;
    use RlcSftp\Connections\SftpConnector\AbstractSftpConnection;
    use RlcSftp\Connections\Connector\ConnectorInterface;
    use \RuntimeException;

    class AbstractSftpConnectionTest extends PHPUnit_Framework_TestCase
    {
        public static $ssh2_connect;
        public static $opendir;
        public static $readdir;

        public function setUp()
        {
    
        }

        public function testConnect()
        {
            $asftp = $this->getMockForAbstractClass(
                "RlcSftp\\Connections\\SftpConnector\\AbstractSftpConnection",
                array("BogusHost")
            );
            
            //Test when ssh2_connect returns false
            self::$ssh2_connect = false;
            $this->assertFalse($asftp->connect());

            //Test when ssh2_connect returns true;
            self::$ssh2_connect = true;
            $this->assertTrue($asftp->connect());
        }

        public function testFlist()
        {
            $asftp = $this->getMockForAbstractClass(
                "RlcSftp\\Connections\\SftpConnector\\AbstractSftpConnection", 
                array(),
                'AbstractSftpConnection',
                false,
                false,
                false,
                array(
                    'connect',
                    'disconnect',
                    'login',
                    'getSftp'
                )
            );
           
            $asftp->expects($this->once())
                ->method("connect")
                ->will($this->returnValue(true));

            $asftp->expects($this->once())
                ->method("login")
                ->will($this->returnValue(true));

            $asftp->expects($this->once())
                ->method("getSftp")
                ->will($this->returnValue("Resource"));

            $asftp->expects($this->once())
                ->method("disconnect")
                ->will($this->returnValue(true));

            //Test when the directory exists and there are entries to read.
            self::$opendir = true;
            self::$readdir = true;
            $this->assertCount(1,$asftp->flist("remoteDir"));
        }

        public function testFlistConnectionException()
        {
            $asftp = $this->getMockForAbstractClass(
                "RlcSftp\\Connections\\SftpConnector\\AbstractSftpConnection", 
                array(),
                'AbstractSftpConnection',
                false,
                false,
                false,
                array(
                    'connect',
                    'login',
                    'getSftp'
                )
            );
           
            $asftp->expects($this->once())
                ->method("connect")
                ->will($this->throwException(new RuntimeException));



            //Test when the directory exists and there are entries to read.
            self::$opendir = true;
            self::$readdir = true;
            $this->assertFalse($asftp->flist("remoteDir"));
        }

        public function testFlistInvalidDirectory()
        {
            $asftp = $this->getMockForAbstractClass(
                "RlcSftp\\Connections\\SftpConnector\\AbstractSftpConnection", 
                array(),
                'AbstractSftpConnection',
                false,
                false,
                false,
                array(
                    'connect',
                    'disconnect',
                    'login',
                    'getSftp'
                )
            );
           
            $asftp->expects($this->once())
                ->method("connect")
                ->will($this->returnValue(true));

            $asftp->expects($this->once())
                ->method("login")
                ->will($this->returnValue(true));

            $asftp->expects($this->once())
                ->method("getSftp")
                ->will($this->returnValue("Resource"));

            //Test when the directory exists and there are entries to read.
            self::$opendir = false;
            self::$readdir = true;
            $this->assertFalse($asftp->flist("remoteDir"));
        }

        public function testDisconnect()
        {
             $asftp = $this->getMockForAbstractClass(
                "RlcSftp\\Connections\\SftpConnector\\AbstractSftpConnection", 
                array(),
                'AbstractSftpConnection',
                false,
                false,
                false,
                array(
                    'getConnection',
                    'setConnection',
                    'exec'
                )
            );

            $asftp->expects($this->once())
                ->method("getConnection")
                ->will($this->returnValue("Resource #14"));

            $asftp->expects($this->once())
                ->method("exec")
                ->will($this->returnValue("Exiting"));

            $asftp->expects($this->once())
                ->method("setConnection")
                ->will($this->returnValue(true));

            $this->assertTrue($asftp->disconnect());
        }

        public function testDisconnectNoConnection() {
            $asftp = $this->getMockForAbstractClass(
                "RlcSftp\\Connections\\SftpConnector\\AbstractSftpConnection", 
                array(),
                'AbstractSftpConnection',
                false,
                false,
                false,
                array(
                    'getConnection',
                    'setConnection',
                    'exec'
                )
            );

            $asftp->expects($this->once())
                ->method("getConnection")
                ->will($this->returnValue(null));

            $asftp->expects($this->once())
                ->method("exec")
                ->will($this->returnValue("Exiting"));

            $asftp->expects($this->once())
                ->method("setConnection")
                ->will($this->returnValue(null));

            $this->assertTrue($asftp->disconnect());
        }
    }
}
