<?php

namespace phpNetworkLayer\Entity;

/**
 * Ipv4
 * 
 * $ip_address      => IP as string type.
 * $proper_address  => IP as integer type.
 *
 *
 * @author  Juan Pablo Candioti (@JPCandioti)
 */
class Ipv4
{
    /**
     * IP address.
     *
     * @var integer
     */
    private $proper_address;
    

    /**
     * __construct
     *
     * @param   integer|string  $ip
     */
    function __construct($ip)
    {
        if (is_numeric($ip)) {
            $this->proper_address = (int) $ip;
        }else {
            $octet = explode('.', $ip);
            $this->proper_address = ip2long(((int) $octet[0]) . '.' . ((int) $octet[1]) . '.' . ((int) $octet[2]) . '.' . ((int) $octet[3]));
        }
    }
    
    function __toString()
    {
        return $this->getIpAddress();
    }

    /**
     * getProperAddress
     *
     * Return IP as integer type.
     *
     * @return  integer
     */
    public function getProperAddress()
    {
        return $this->proper_address;
    }

    /**
     * getIpAddress
     *
     * Return IP as string type.
     *
     * @return  string
     */
    public function getIpAddress()
    {
        return long2ip($this->proper_address);
    }

    /**
     * getIpClass
     *
     * Return IP class as string.
     *
     * @param   string      $ip_address
     * @return  string
     */
    public function getClass()
    {
        if ($this->proper_address < 2147483648) {
            $class = 'A';
        }elseif ($this->proper_address < 3221225472) {
            $class = 'B';
        }elseif ($this->proper_address < 3758096384) {
            $class = 'C';
        }elseif ($this->proper_address < 4026531840) {
            $class = 'D';
        }else {
            $class = 'E';
        }
        
        return $class;
    }

    /**
     * getPrefixByIpClass
     *
     * Return prefix by IP class.
     *
     * @return  integer
     */
    public function getPrefixByIpClass()
    {
        $class = $this->getClass();

        if ($class == 'A') {
            $prefix = 8;
        }elseif ($class == 'B') {
            $prefix = 16;
        }elseif ($class == 'C') {
            $prefix = 24;
        }else {
            $prefix = 32;
        }

        return $prefix;
    }

    /**
     * getProperMaskByIpClass
     *
     * Return proper mask by IP class.
     *
     * @return  string
     */
    public function getProperMaskByIpClass()
    {
        $prefix = $this->getPrefixByIpClass();

        return 4294967295 & (-4294967296 >> $prefix);
    }

    /**
     * getMaskByIpClass
     *
     * Return mask by IP class.
     *
     * @return  string
     */
    public function getMaskByIpClass()
    {
        return long2ip($this->getProperMaskByIpClass());
    }
}
