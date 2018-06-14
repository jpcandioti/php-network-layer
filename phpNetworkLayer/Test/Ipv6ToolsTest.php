<?php

namespace phpNetworkLayer\Test;

use PHPUnit\Framework\TestCase;
use phpNetworkLayer\Tools\Ipv6Tools;

/**
 * Ipv6ToolsTest
 * 
 * Test of IPv4 tools.
 *
 *
 * @author  Juan Pablo Candioti (@JPCandioti)
 */
class Ipv6ToolsTest extends TestCase
{
    public function testCompressIp()
    {
        $ipv6_01 = Ipv6Tools::compressIp('2001:0db8:85a3:0000:0000:8a2e:0370:7334');
        $this->assertEquals('2001:db8:85a3::8a2e:370:7334', $ipv6_01);

        //$ipv6_02 = Ipv6Tools::compressIp('01000.200.002.10');
        //$this->assertFalse($ipv6_02);
    }

    public function testExpandIp()
    {
        $ipv6_01 = Ipv6Tools::expandIp('2001:db8:85a3::8a2e:370:7334');
        $this->assertEquals('2001:0db8:85a3:0000:0000:8a2e:0370:7334', $ipv6_01);

        //$ipv6_02 = Ipv6Tools::compressIp('01000.200.002.10');
        //$this->assertFalse($ipv6_02);
    }

    public function testGetIpv6ByIpv4Mapped()
    {
        $ipv6_01 = Ipv6Tools::getIpv6ByIpv4Mapped('192.168.1.15');
        $this->assertEquals('::ffff:192.168.1.15', $ipv6_01);

        //$ipv6_02 = Ipv6Tools::compressIp('01000.200.002.10');
        //$this->assertFalse($ipv6_02);
    }

    public function testGetIpv4Mapped()
    {
        $ipv6_01 = Ipv6Tools::getIpv4Mapped('::ffff:192.168.1.15');
        $this->assertEquals('::ffff:192.168.1.15', $ipv6_01);

        //$ipv6_02 = Ipv6Tools::compressIp('01000.200.002.10');
        //$this->assertFalse($ipv6_02);
    }

    public function testGetWildcardByMask()
    {
        $ipv6_01 = Ipv6Tools::getWildcardByMask('ffff:ffff:ffff:ffff::');
        $this->assertEquals('::ffff:ffff:ffff:ffff', $ipv6_01);

        $ipv6_02 = Ipv6Tools::getWildcardByMask('ffff:ffff:ffff:0000:0000:8a2e:0370:7334');
        $this->assertEquals('::ffff:ffff:75d1:fc8f:8ccb', $ipv6_02);
    }

    public function testGetEui64()
    {
        $ipv6_01 = Ipv6Tools::getEui64('2001:0db8:85a3:0000:0000:8a2e:0370:7334/64', 'FC:99:47:75:CE:E0');
        $this->assertEquals('2001:db8:85a3:0:fe99:47ff:fe75:cee0', $ipv6_01);
    }
}