<?php

namespace Simqel;

/**
 * Class Settings
 * @package Simqel
 */
class Settings
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $driver;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $database;

    /**
     * Settings constructor.
     * @param string|null $dsn
     */
    public function __construct($dsn = null)
    {
        if ($dsn) {
            $matches = array();

            $location = parse_url($dsn);
            if ($location) {
                $this->setDriver($location['scheme']);
                $this->setHost(isset($location['host']) ? $location['host'] : null);
                $this->setUsername(isset($location['user']) ? $location['user'] : null);
                $this->setDatabase(isset($location['path']) ? ltrim($location['path'], '/') : null);
                $this->setPort(isset($location['port']) ? $location['port'] : null);
                $this->setPassword(isset($location['pass']) ? urldecode($location['pass']) : null);
            } elseif (preg_match('/^(.*):\/\/\/(.*)$/', $dsn, $matches)) {
                list($this->dsn, $this->driver, $this->database) = $matches;
            }
        }
    }

    /**
     * @param $driver
     * @return $this
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param $database
     * @return $this
     */
    public function setDatabase($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearPassword()
    {
        $this->password = 'Hidden for security reasons!';
        return $this;
    }
}
