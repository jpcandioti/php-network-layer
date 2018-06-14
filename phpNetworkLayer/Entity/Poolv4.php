<?php

namespace phpNetworkLayer\Entity;

use phpNetworkLayer\Exception\Ipv4NetLayerException;

/**
 * Poolv4
 *
 * $ip_address      => IP as string type.
 * $proper_address  => IP as integer type.
 *
 *
 * @author  Juan Pablo Candioti (@JPCandioti)
 */
class Poolv4
{
    /**
     * Subnet of pool.
     *
     * @var Subnetv4
     */
    private $subnet;

    /**
     * Network address of Subnet.
     *
     * @var integer
     */
    private $first_pos;

    /**
     * Network address of Subnet.
     *
     * @var integer
     */
    private $last_pos;

    /**
     * Network address of Subnet.
     *
     * @var integer
     */
    private $current_pos;
    
    /**
     * Struct of mask.
     *
     * @var array
     */
    protected $mask_struct;

    /**
     * __construct
     *
     * @param   Subnetv4    $subnet
     * @param   Ipv4        $first_ip
     * @param   Ipv4        $last_ip
     */
    function __construct(Subnetv4 $subnet, Ipv4 $first_ip = null, Ipv4 $last_ip = null)
    {
        if ($subnet->getNumberOfHosts() <= 0) {
            throw new Ipv4NetLayerException("The subnet $subnet won't have hosts.", 301);
        }

        $this->subnet = $subnet;
        $this->analyzingMask();

        $this->setFirstIp($first_ip);
        $this->setLastIp($last_ip);
    }
    
    function __toString()
    {
        return $this->getIpAddress();
    }

    /**
     * setFirstIp
     *
     * @param   Ipv4        $first_ip
     * @return  Poolv4
     */
    public function setFirstIp($first_ip = null)
    {
        if (is_null($first_ip)) {
            $first_pos = 1;
        }elseif ($this->subnet->isIpInSubnet($first_ip)) {
            $first_pos = getPositionByIp($first_ip);

            if ($first_pos == 0) {
                throw new Ipv4NetLayerException("The IP address $first_ip is the network address of {$this->subnet}.", 301);
            }elseif (!is_null($this->last_pos) && $first_pos >= $this->last_pos) {
                throw new Ipv4NetLayerException("The IP address $first_ip is equals or greatest as last IP address.", 301);
            }
        }else {
            throw new Ipv4NetLayerException("The IP address $first_ip isn't include in {$this->subnet}.", 301);
        }

        $this->first_pos = $first_pos;

        return $this;
    }
    
    public function getFirstIp()
    {
        return $this->getIpByPosition($this->first_pos);
    }

    /**
     * setLastIp
     *
     * @param   Ipv4        $last_ip
     * @return  Poolv4
     */
    public function setLastIp($last_ip = null)
    {
        if (is_null($last_ip)) {
            $last_pos = $this->subnet->getNumberOfHosts();
        }elseif ($this->subnet->isIpInSubnet($last_ip)) {
            $last_pos = getPositionByIp($last_ip);

            if ($last_pos == $this->subnet->getNumberOfHosts() + 1) {
                throw new Ipv4NetLayerException("The IP address $last_ip is the broadcast address of {$this->subnet}.", 301);
            }elseif ($last_pos <= $this->first_pos) {
                throw new Ipv4NetLayerException("The IP address $last_ip is equals or greatest at last IP address.", 301);
            }
        }else {
            throw new Ipv4NetLayerException("The IP address $last_ip isn't include in {$this->subnet}.", 301);
        }

        $this->last_pos = $last_pos;

        return $this;
    }

    public function getLastIp()
    {
        return $this->getIpByPosition($this->last_pos);
    }
    
    public function setCurrentPos($position = null)
    {
        if (is_null($position)) {
            $this->current_pos = $position;
        }else {
            $this->current_pos = $position;
        }

        return $this;
    }
    
    public function getCurrentPos()
    {
        return $this->current_pos;
    }
    
    /**
     * analyzingMask
     *
     * Return the first host of subnet.
     */
    private function analyzingMask()
    {
        $this->mask_struct = array();
        
        $mask = $this->subnet->getMask();
        
        $step = 1;
        for ($slit = 1; $slit <= 2147483648; $slit = $slit << 1) {
            if (($mask & $slit) == 0) {
                $this->mask_struct[] = $step;
            }

            $step *= 2;
        }
    }
    
    private function getIpByPosition($position)
    {
        $host = 0;
        $rest = $position;
        for ($index = count($this->mask_struct) - 1; $index >= 0; $index--) {
            $exp = pow(2, $index);
            if ($rest >= $exp) {
                $host += $this->mask_struct[$index];
                $rest -= $exp;
            }
        }

        if ($rest > 0) {
            throw new Ipv4NetLayerException("The position $position isn't include in $subnet.", 301);
        }

        $proper_ip = $this->subnet->getNetworkAddress()->getProperAddress() | $host;
        
        return new Ipv4($proper_ip);
    }

    private function getPositionByIp($ip)
    {
        $host = $ip->getProperAddress() & ~$this->subnet->getMask();

        $position = 0;
        for ($index = count($this->mask_struct) - 1; $index >= 0; $index--) {
            if ($host >= $this->mask_struct[$index]) {
                $position += pow(2, $index);
                $host -= $this->mask_struct[$index];
            }
        }

        return $position;
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
        return $this->last_pos - $this->first_pos;
    }
}
