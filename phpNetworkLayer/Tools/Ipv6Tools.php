<?php

namespace phpNetworkLayer\Tools;

/**
 * Ipv6Tools
 *
 * Tools for IPv6 protocol.
 * 
 * $address      	=> IP as string type.
 * $in_addr         => IP as binary type.
 *
 *
 * @author  Juan Pablo Candioti (@JPCandioti)
 */
class Ipv6Tools
{
    /**
     * compressIp
     *
     * Compress a expanded IPv6 string.
     *
     * @param   string      $address
     * @return  string
     */
    public static function compressIp($address)
	{
        return inet_ntop(inet_pton($address));
    }

    /**
     * expandIp
     *
     * Expand a compressed IPv6 string.
     *
     * @param   string      $address
     * @return  string
     */
    public static function expandIp($address)
	{
        $groups = unpack('H4group1/H4group2/H4group3/H4group4/H4group5/H4group6/H4group7/H4group8', inet_pton($address));
        return implode(':', $groups);
    }

    /**
     * getIpv6ByIpv4Mapped
     *
     * Normalize IP string.
     *
     * @param   string      $ip_address
     * @return  string
     */
    public static function getIpv6ByIpv4Mapped($ip_address)
	{
		$proper_address = ip2long($ip_address);
		$in_addr = pack('NNssN', 0, 0, 0, -1, $proper_address);

        return inet_ntop($in_addr);
    }

    /**
     * getIpv4Mapped
     *
     * Normalize IP string.
     *
     * @param   string      $ip_address
     * @return  string
     */
    public static function getIpv4Mapped($ip_address)
	{
        $groups = unpack('H4group1/H4group2/H4group3/H4group4/H4group5/H4group6/C4oct', inet_pton($ip_address));
        //var_dump($groups);die();
        $parts = array_chunk($groups, 6);
        return implode(':', $parts[0]) . ':' . implode('.', $parts[1]);
    }

    /**
     * getMaskByPrefix
     *
     * Return the subnet mask by bit-length prefix.
     *
     * @param   integer         $prefix         Bit-length prefix.
     * @return  string|false                    Subnet mask as string type, or error by boolean false.
     */
    private static function _getMaskByPrefix($prefix)
    {
        $mask = false;

        if (is_numeric($prefix)) {
			$num = array(-1, -1, -1, -1);
			if ($prefix < 32) {
				$num[0] = $num[0] << (32 - $prefix);
				$num[1] = $num[2] = $num[3] = 0;
			}elseif ($prefix < 64) {
				$num[1] = $num[1] << (64 - $prefix);
				$num[2] = $num[3] = 0;
			}elseif ($prefix < 96) {
				$num[2] = $num[2] << (96 - $prefix);
				$num[3] = 0;
			}else {
				$num[3] = $num[3] << (128 - $prefix);
			}
			
			$mask = pack('NNNN', $num[0], $num[1], $num[2], $num[3]);
        }

        return $mask;
    }
	
	public static function getEui64($net, $mac)
	{
		$parts = explode('/', $net);
		
		$macbin = hex2bin(str_pad(str_replace(':', '', $mac), 12, '0', STR_PAD_LEFT));
		
		$host = str_pad(implode(array(($macbin[0] ^ chr(2)), $macbin[1], $macbin[2], chr(255), chr(254), $macbin[3], $macbin[4], $macbin[5])), 16, chr(0), STR_PAD_LEFT);
		
		$mask = self::_getMaskByPrefix($parts[1]);
		//die(inet_ntop($mask));
		$in_addr = (inet_pton($parts[0]) & $mask) | $host;
		return inet_ntop($in_addr);
	}

    /**
     * getWildcardByMask
     *
     * Return the wildcard mask by subnet mask.
     *
     * @param   string		$mask
     * @return  string
     */
    public static function getWildcardByMask($mask)
	{
        return inet_ntop(~inet_pton($mask));
    }
	
	public static function getType($ip_address)
	{
		$in_addr = inet_pton($ip_address);

		$type = '';
		//var_dump(ord($in_addr[0]));die();
		if (($in_addr[0] & chr(254)) == chr(252)) {
			$type = 'unique-local';
		}elseif ($in_addr[0] == chr(254) && ($in_addr[1] & chr(192)) == chr(128)) {
			$type = 'link-local';
		}elseif ($in_addr == inet_pton('::1')) {
			$type = 'loopback';
		}

		return $type;
	}
}
