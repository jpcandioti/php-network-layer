<?php

namespace phpNetworkLayer\Tools;

/**
 * Ipv4Tools
 * 
 * Tools for IPv4 protocol.
 * 
 * $ip_address      => IP as string type.
 * $proper_address  => IP as integer type.
 *
 *
 * @author  Juan Pablo Candioti (@JPCandioti)
 */
class Ipv4Tools
{
    /**
     * normalizeIp
     *
     * Normalize IP string.
     *
     * @param   string          $ip_address     IP address as string type.
     * @return  string|false                    Normalize IP string, or error by boolean false.
     */
    public static function normalizeIp($ip_address)
    {
        $octet = explode('.', $ip_address);

        if (count($octet) == 4) {
            $normalize_ip_address = long2ip(ip2long(((int) $octet[0]) . '.' . ((int) $octet[1]) . '.' . ((int) $octet[2]) . '.' . ((int) $octet[3])));

            if ($normalize_ip_address == '0.0.0.0') {
                $normalize_ip_address = false;
            }
        }else {
            $normalize_ip_address = false;
        }
        
        return $normalize_ip_address;
    }

    /**
     * getMaskByPrefix
     *
     * Return the subnet mask by bit-length prefix.
     *
     * @param   integer         $prefix         Bit-length prefix.
     * @return  string|false                    Subnet mask as string type, or error by boolean false.
     */
    public static function getMaskByPrefix($prefix)
    {
        $mask = false;

        if (is_numeric($prefix)) {
            if ($prefix >= 0 && $prefix <= 32) {
                $mask = long2ip(4294967295 << (32 - $prefix));
            }
        }

        return $mask;
    }

    /**
     * getPrefixByMask
     *
     * Return the subnet prefix by mask.
     *
     * @param   string          $mask           Subnet mask as string type.
     * @return  integer|false                   Bit-length prefix, or error by boolean false.
     */
    public static function getPrefixByMask($mask)
    {
        $prefix         = false;
        $proper_mask    = ip2long($mask);

        $word = 4294967295;
        for ($i = 0; $i < 32; $i++) {
            if ($word == $proper_mask) {
                $prefix = 32 - $i;
                break;
            }
            $word -= pow(2, $i);
        }

        return $prefix;
    }

    /**
     * getWildcardByMask
     *
     * Return the wildcard mask by subnet mask.
     *
     * @param   string          $mask           Subnet mask as string type.
     * @return  string
     */
    public static function getWildcardByMask($mask)
    {
        return long2ip(~ip2long($mask));
    }

    /**
     * getIpClass
     *
     * Return IP class as string.
     *
     * @param   string      $ip_address
     * @return  string
     */
    public static function getIpClass($ip_address)
    {
        $proper_address = ip2long($ip_address);

        if ($proper_address < 2147483648) {
            $class = 'A';
        }elseif ($proper_address < 3221225472) {
            $class = 'B';
        }elseif ($proper_address < 3758096384) {
            $class = 'C';
        }elseif ($proper_address < 4026531840) {
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
     * @param   string      $class
     * @return  integer
     */
    public static function getPrefixByIpClass($class)
    {
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
     * getMaskByIpClass
     *
     * Return mask by IP class.
     *
     * @param   string      $class
     * @return  string
     */
    public static function getMaskByIpClass($class)
    {
        return self::getMaskByPrefix(self::getPrefixByIpClass($class));
    }

    /**
     * getPrefixByIpClassFromIpAddress
     *
     * Return prefix by IP class.
     *
     * @param   string      $ip_address
     * @return  string
     */
    public static function getPrefixByIpClassFromIpAddress($ip_address)
    {
        return self::getPrefixByIpClass(self::getIpClass($ip_address));
    }

    /**
     * getMaskByIpClassFromIpAddress
     *
     * Return mask by IP class from IP address.
     *
     * @param   string      $ip_address
     * @return  string
     */
    public static function getMaskByIpClassFromIpAddress($ip_address)
    {
        return self::getMaskByIpClass(self::getIpClass($ip_address));
    }

    /**
     * getNetworkAddress
     *
     * Return subnet address.
     *
     * @param   string      $subnet
     * @param   string      $mask
     * @return  string
     */
    public static function getNetworkAddress($subnet, $mask = null)
    {
        $parts = explode('/', $subnet);

		if (is_null($mask)) {
			if (count($parts) == 1) {
				$prefix = self::getPrefixByIpClassFromIpAddress($parts[0]);
			}else {
				$prefix = (int) $parts[1];
			}
            $mask = 4294967295 << (32 - $prefix);
		}else {
			$mask = ip2long($mask);
		}

		$net = ip2long($parts[0]) & $mask;
		
		return long2ip($net);
    }

    /**
     * getBroadcastAddress
     *
     * Return broadcast subnet address.
     *
     * @param   string		$subnet
     * @param   string		$mask
     * @return  string
     */
    public static function getBroadcastAddress($subnet, $mask = null)
	{
		$parts = explode('/', $subnet);
		
		if (is_null($mask)) {
			if (count($parts) == 1) {
				$prefix = self::getPrefixByIpClassFromIpAddress($parts[0]);
			}else {
				$prefix = (int) $parts[1];
			}
            $mask = 4294967295 << (32 - $prefix);
		}else {
			$mask = ip2long($mask);
		}

		$net = ip2long($parts[0]) | ~$mask;
		
		return long2ip($net);
    }

    /**
     * isIpInSubnet
     *
     * Check IP string include in net.
     *
     * @param	string		$ip_address
     * @param	string		$subnet
     * @param   string		$mask
     * @return	boolean
     */
    public static function isIpInSubnet($ip_address, $subnet, $mask = null)
    {
        $parts = explode('/', $subnet);

		if (is_null($mask)) {
			if (count($parts) == 1) {
				$prefix = self::getPrefixByIpClassFromIpAddress($parts[0]);
			}else {
				$prefix = (int) $parts[1];
			}
            $mask = 4294967295 << (32 - $prefix);
		}else {
			$mask = ip2long($mask);
		}
    
        $proper_net_address_by_ip = ip2long($ip_address) & $mask;
        $proper_net_address = ip2long($parts[0]) & $mask;
    
        return $proper_net_address_by_ip == $proper_net_address;
    }

    /**
     * getNumberOfHosts
     *
     * Return number of hosts for that mask.
     *
     * @param   string		$mask
     * @return	integer
     */
    public static function getNumberOfHosts($mask)
    {
        $mask = ip2long($mask);

        $zeros = 0;
        for ($slit = 1; $slit <= 2147483648; $slit = $slit << 1) {
            if (($mask & $slit) == 0) {
                $zeros++;
            }
        }

        return pow(2, $zeros) - 2;
    }
}
