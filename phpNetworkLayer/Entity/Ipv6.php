<?php

namespace phpNetworkLayer\Entity;

/**
 * Ipv6
 * 
 * $address      	=> IP as string type.
 * $in_addr         => IP as binary type.
 *
 *
 * @author  Juan Pablo Candioti (@JPCandioti)
 */
class Ipv6
{
    /**
     * IP address.
     *
     * @var string
     */
    private $in_addr;
    

    /**
     * __construct
     *
     * @param   string      $address
     */
    function __construct($address)
    {
        $this->in_addr = inet_pton($address);
    }
    
    function __toString()
    {
        return inet_ntop($this->in_addr);
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
     * Return IP address to a human readable representation.
     *
     * @return  string
     */
    public function getIpAddress()
    {
        return inet_ntop($this->in_addr);
    }

    /**
     * getExpandIpAddress
     *
     * Expand a compressed IPv6 string.
     *
     * @return  string
     */
    public static function getExpandIpAddress()
	{
        $groups = unpack('H4group1/H4group2/H4group3/H4group4/H4group5/H4group6/H4group7/H4group8', $this->in_addr);
        return implode(':', $groups);
    }

}
