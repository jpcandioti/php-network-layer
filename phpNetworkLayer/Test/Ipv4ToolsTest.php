<?php

namespace phpNetworkLayer\Test;

use PHPUnit\Framework\TestCase;
use phpNetworkLayer\Tools\Ipv4Tools;

/**
 * Ipv4ToolsTest
 * 
 * Test of IPv4 tools.
 *
 *
 * @author  Juan Pablo Candioti (@JPCandioti)
 */
class Ipv4ToolsTest extends TestCase
{
    public function testNormalizeIp()
    {
        $ipv4_01 = Ipv4Tools::normalizeIp('010.200.002.10');
        $this->assertEquals('10.200.2.10', $ipv4_01);

        $ipv4_02 = Ipv4Tools::normalizeIp('01000.200.002.10');
        $this->assertFalse($ipv4_02);

        $mask_03 = Ipv4Tools::normalizeIp(-1);
        $this->assertFalse($mask_03);

        $mask_04 = Ipv4Tools::normalizeIp(33);
        $this->assertFalse($mask_04);
    }

    public function testGetMaskByPrefix()
    {
        $mask_01 = Ipv4Tools::getMaskByPrefix(0);
        $this->assertEquals('0.0.0.0', $mask_01);

        $mask_02 = Ipv4Tools::getMaskByPrefix(32);
        $this->assertEquals('255.255.255.255', $mask_02);

        $mask_05 = Ipv4Tools::getMaskByPrefix(24.5);
        $this->assertEquals('255.255.255.128', $mask_05);

        $mask_06 = Ipv4Tools::getMaskByPrefix('25');
        $this->assertEquals('255.255.255.128', $mask_06);

        $mask_07 = Ipv4Tools::getMaskByPrefix(25);
        $this->assertEquals('255.255.255.128', $mask_07);
    }

    public function testGetWildcardByMask()
    {
        $wildcard_01 = Ipv4Tools::getWildcardByMask('0.0.0.0');
        $this->assertEquals('255.255.255.255', $wildcard_01);

        $wildcard_02 = Ipv4Tools::getWildcardByMask('255.255.255.255');
        $this->assertEquals('0.0.0.0', $wildcard_02);

        $wildcard_03 = Ipv4Tools::getWildcardByMask('255.255.255.128');
        $this->assertEquals('0.0.0.127', $wildcard_03);
    }

    public function testGetIpClass()
    {
        $class01 = Ipv4Tools::getIpClass('102.5.4.3');
        $this->assertEquals('A', $class01);

        $class02 = Ipv4Tools::getIpClass('127.255.255.255');
        $this->assertEquals('A', $class02);

        $class03 = Ipv4Tools::getIpClass('128.0.0.0');
        $this->assertEquals('B', $class03);

        $class04 = Ipv4Tools::getIpClass('191.255.255.255');
        $this->assertEquals('B', $class04);

        $class05 = Ipv4Tools::getIpClass('192.0.0.0');
        $this->assertEquals('C', $class05);

        $class05 = Ipv4Tools::getIpClass('223.255.255.255');
        $this->assertEquals('C', $class05);

        $class05 = Ipv4Tools::getIpClass('224.0.0.0');
        $this->assertEquals('D', $class05);

        $class05 = Ipv4Tools::getIpClass('239.255.255.255');
        $this->assertEquals('D', $class05);

        $class05 = Ipv4Tools::getIpClass('240.0.0.0');
        $this->assertEquals('E', $class05);
    }

    public function testGetPrefixByIpClass()
    {
        $prefix01 = Ipv4Tools::getPrefixByIpClass('A');
        $this->assertEquals(8, $prefix01);

        $prefix02 = Ipv4Tools::getPrefixByIpClass('B');
        $this->assertEquals(16, $prefix02);

        $prefix03 = Ipv4Tools::getPrefixByIpClass('C');
        $this->assertEquals(24, $prefix03);

        $prefix04 = Ipv4Tools::getPrefixByIpClass('D');
        $this->assertEquals(32, $prefix04);

        $prefix05 = Ipv4Tools::getPrefixByIpClass('E');
        $this->assertEquals(32, $prefix05);
    }

    public function testGetNetworkAddress()
    {
        $net01 = Ipv4Tools::getNetworkAddress('192.168.5.195');
        $this->assertEquals('192.168.5.0', $net01);

        $net02 = Ipv4Tools::getNetworkAddress('192.168.5.195/25');
        $this->assertEquals('192.168.5.128', $net02);

        $net03 = Ipv4Tools::getNetworkAddress('192.168.5.195', '255.255.255.192');
        $this->assertEquals('192.168.5.192', $net03);
    }

    public function testGetBroadcastAddress()
    {
        $net_01 = Ipv4Tools::getBroadcastAddress('192.168.5.60');
        $this->assertEquals('192.168.5.255', $net_01);

        $net_02 = Ipv4Tools::getBroadcastAddress('192.168.5.60/25');
        $this->assertEquals('192.168.5.127', $net_02);

        $net_03 = Ipv4Tools::getBroadcastAddress('192.168.5.60', '255.255.255.192');
        $this->assertEquals('192.168.5.63', $net_03);
    }

    public function testIsIpInSubnet()
    {
        $this->assertTrue(Ipv4Tools::isIpInSubnet('192.168.5.61', '192.168.5.60'));

        $this->assertTrue(Ipv4Tools::isIpInSubnet('192.168.5.61', '192.168.5.60/25'));

        $this->assertTrue(Ipv4Tools::isIpInSubnet('192.168.5.61', '192.168.5.60', '255.255.255.192'));

        $this->assertFalse(Ipv4Tools::isIpInSubnet('192.168.4.61', '192.168.5.60'));

        $this->assertFalse(Ipv4Tools::isIpInSubnet('192.168.5.135', '192.168.5.60/25'));

        $this->assertFalse(Ipv4Tools::isIpInSubnet('192.168.5.69', '192.168.5.60', '255.255.255.192'));
    }

    public function testGetNumberOfHosts()
    {
        $hosts_01 = Ipv4Tools::getNumberOfHosts('0.0.0.0');
        $this->assertEquals(4294967294, $hosts_01);

        $hosts_02 = Ipv4Tools::getNumberOfHosts('255.255.255.255');
        $this->assertEquals(-1, $hosts_02);

        $hosts_03 = Ipv4Tools::getNumberOfHosts('255.255.255.254');
        $this->assertEquals(0, $hosts_03);

        $hosts_04 = Ipv4Tools::getNumberOfHosts('255.255.0.255');
        $this->assertEquals(254, $hosts_04);
    }
}