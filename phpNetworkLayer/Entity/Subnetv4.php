<?php

namespace phpNetworkLayer\Entity;

use phpNetworkLayer\Tools\Ipv4Tools;

/**
 * Subnetv4
 *
 * $ip_address      => IP as string type.
 * $proper_address  => IP as integer type.
 *
 *
 * @author  Juan Pablo Candioti (@JPCandioti)
 */
class Subnetv4
{
    /**
     * Network address of Subnet.
     *
     * @var Ipv4
     */
    private $network_address;

    /**
     * Mask of Subnet.
     *
     * @var integer
     */
    private $mask;

    /**
     * Number of hosts.
     *
     * @var integer
     */
    protected $hosts;
    

    /**
     * __construct
     *
     * @param   string|IPv4 $subnet
     * @param   string      $mask
     */
    function __construct($subnet, $mask = null)
    {
        if (is_string($subnet)) {
            $parts = explode('/', $subnet);
            $proper_address = is_numeric($parts[0]) ? (int) $parts[0] : ip2long($parts[0]);
        }elseif (is_object($subnet) && get_class($subnet) == 'phpNetworkLayer\Entity\Ipv4') {
            $parts = array();
            $proper_address = $subnet->getProperAddress();
        }else {
            throw new \Exception('The $subnet parameter must be string or IPv4 object.');
        }

        if (is_null($mask)) {
            if (count($parts) == 0) {
                $prefix = $subnet->getPrefixByIpClass();
            }elseif (count($parts) == 1) {
                $ip_address = is_numeric($parts[0]) ? long2ip($proper_address) : $parts[0];
                $prefix = Ipv4Tools::getPrefixByIpClassFromIpAddress($ip_address);
            }else {
                $prefix = (int) $parts[1];
            }
            $this->mask = 4294967295 & (4294967295 << (32 - $prefix));
        }else {
            $this->mask = ip2long($mask);
        }

        $network_address = $proper_address & $this->mask;
        $this->network_address = ($proper_address == $network_address) ? $subnet : new Ipv4($network_address);
    }
    
    function __toString()
    {
        $subnet_address = $this->getSubnetAddress();
        return $subnet_address === false ? $this->getNetworkAddress()->getIpAddress() : $subnet_address;
    }
    
    
    public function getNetworkAddress()
    {
        return $this->network_address;
    }

    public function getMask()
    {
        return $this->mask;
    }

    public function getMaskAsString()
    {
        return long2ip($this->mask);
    }

    /**
     * getSubnetAddress
     *
     * Return the subnet address.
     *
     * @return  string|false                    Subnet address, or error by boolean false.
     */
    public function getSubnetAddress()
    {
        $prefix = $this->getMaskPrefix();
        return $prefix ? $this->getNetworkAddress()->getIpAddress() . '/' . $prefix : false;
    }

    /**
     * getMaskPrefix
     *
     * Return the mask subnet.
     *
     * @return  integer|false                   Bit-length prefix, or error by boolean false.
     */
    public function getMaskPrefix()
    {
        $prefix = false;

        $word = 4294967295;
        for ($i = 0; $i < 32; $i++) {
            if ($word == $this->mask) {
                $prefix = 32 - $i;
                break;
            }
            $word -= pow(2, $i);
        }

        return $prefix;
    }

    /**
     * getBroadcastAddress
     *
     * Return broadcast subnet address.
     *
     * @return  Ipv4
     */
    public function getBroadcastAddress()
	{
		return new Ipv4($this->network_address->getProperAddress() | ~$this->mask);
    }

    /**
     * isIpInSubnet
     *
     * Check IP string include in its.
     *
     * @return	boolean
     */
    public function isIpInSubnet(Ipv4 $ip)
    {
        $proper_net_address_by_ip = $ip->getProperAddress() & $this->mask;
    
        return $proper_net_address_by_ip == $this->network_address->getProperAddress();
    }

    /**
     * getNumberOfHosts
     *
     * Return number of hosts for that subnet.
     *
     * @return	integer
     */
    public function getNumberOfHosts()
    {
        if (is_null($this->hosts)) {
            $zeros = 0;
            for ($slit = 1; $slit <= 2147483648; $slit = $slit << 1) {
                if (($this->mask & $slit) == 0) {
                    $zeros++;
                }
            }

            $this->hosts = pow(2, $zeros) - 2;
        }

        return $this->hosts;
    }
}
