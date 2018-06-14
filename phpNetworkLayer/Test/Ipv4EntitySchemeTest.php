<?php

namespace phpNetworkLayer\Tests;

use PHPUnit\Framework\TestCase;
use phpNetworkLayer\Entity\Ipv4;
use phpNetworkLayer\Entity\Subnetv4;

/**
 * Ipv4EntitySchemeTest
 * 
 * Test of IPv4 entity scheme.
 *
 *
 * @author  Juan Pablo Candioti (@JPCandioti)
 */
class Ipv4EntitySchemeTest extends TestCase
{
    public function testIpv4Entity()
    {
        $ipv4_01 = new Ipv4('010.200.002.10');
        $this->assertEquals('10.200.2.10',      $ipv4_01->getIpAddress());
        $this->assertEquals(180879882,          $ipv4_01->getProperAddress());
        $this->assertEquals('A',                $ipv4_01->getClass());
        $this->assertEquals(8,                  $ipv4_01->getPrefixByIpClass());
        $this->assertEquals(4278190080,         $ipv4_01->getProperMaskByIpClass());
        $this->assertEquals('255.0.0.0',        $ipv4_01->getMaskByIpClass());

        $ipv4_02 = new Ipv4(3232236995);
        $this->assertEquals('192.168.5.195',    $ipv4_02->getIpAddress());
        $this->assertEquals(3232236995,         $ipv4_02->getProperAddress());
        $this->assertEquals('C',                $ipv4_02->getClass());
        $this->assertEquals(24,                 $ipv4_02->getPrefixByIpClass());
        $this->assertEquals(4294967040,         $ipv4_02->getProperMaskByIpClass());
        $this->assertEquals('255.255.255.0',    $ipv4_02->getMaskByIpClass());
    }

    public function testSubnetv4Entity()
    {
        $subnetv4_01 = new Subnetv4('10.200.2.10');
        $this->assertEquals('10.0.0.0',         $subnetv4_01->getNetworkAddress()->getIpAddress());
        $this->assertEquals('255.0.0.0',        $subnetv4_01->getMaskAsString());
        $this->assertEquals(16777214,           $subnetv4_01->getNumberOfHosts());
        $this->assertEquals('10.0.0.0/8',       $subnetv4_01->getSubnetAddress());
        $this->assertEquals('10.255.255.255',   $subnetv4_01->getBroadcastAddress()->getIpAddress());
        $this->assertTrue($subnetv4_01->isIpInSubnet(new Ipv4('10.5.5.5')));
        $this->assertFalse($subnetv4_01->isIpInSubnet(new Ipv4('11.5.5.5')));

        $ipv4_02 = new Ipv4('192.168.5.195');
        $subnetv4_02 = new Subnetv4($ipv4_02);
        $this->assertEquals('192.168.5.0',      $subnetv4_02->getNetworkAddress()->getIpAddress());
    }
}