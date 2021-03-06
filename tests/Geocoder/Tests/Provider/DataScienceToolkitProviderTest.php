<?php

namespace Geocoder\Tests\Provider;

use Geocoder\Tests\TestCase;
use Geocoder\Provider\DataScienceToolkitProvider;

class DataScienceToolkitProviderTest extends TestCase
{
    public function testGetName()
    {
        $provider = new DataScienceToolkitProvider($this->getMockAdapter($this->never()));
        $this->assertEquals('data_science_toolkit', $provider->getName());
    }

    /**
     * @expectedException \Geocoder\Exception\UnsupportedOperation
     * @expectedExceptionMessage The DataScienceToolkit provider does not support empty addresses.
     */
    public function testGeocodeWithNull()
    {
        $provider = new DataScienceToolkitProvider($this->getMockAdapter($this->never()));
        $provider->geocode(null);
    }

    /**
     * @expectedException \Geocoder\Exception\UnsupportedOperation
     * @expectedExceptionMessage The DataScienceToolkit provider does not support empty addresses.
     */
    public function testGeocodeWithEmpty()
    {
        $provider = new DataScienceToolkitProvider($this->getMockAdapter($this->never()));
        $provider->geocode('');
    }

    /**
     * @expectedException \Geocoder\Exception\NoResult
     * @expectedExceptionMessage Could not execute query http://www.datasciencetoolkit.org/street2coordinates/10+rue+de+baraban+lyon
     */
    public function testGeocodeWithFrenchAddress()
    {
        $provider = new DataScienceToolkitProvider($this->getMockAdapterReturns(null));
        $provider->geocode('10 rue de baraban lyon');
    }

    public function testGeocodeWithLocalhostIPv4()
    {
        $provider = new DataScienceToolkitProvider($this->getMockAdapter($this->never()));
        $result   = $provider->geocode('127.0.0.1');

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $result = $result[0];
        $this->assertInternalType('array', $result);
        $this->assertArrayNotHasKey('latitude', $result);
        $this->assertArrayNotHasKey('longitude', $result);
        $this->assertArrayNotHasKey('zipcode', $result);
        $this->assertArrayNotHasKey('timezone', $result);
        $this->assertArrayNotHasKey('city', $result);
        $this->assertArrayNotHasKey('region', $result);
        $this->assertArrayNotHasKey('county', $result);

        $this->assertEquals('localhost', $result['locality']);
        $this->assertEquals('localhost', $result['country']);
    }

    /**
     * @expectedException \Geocoder\Exception\NoResult
     * @expectedExceptionMessage Could not execute query http://www.datasciencetoolkit.org/ip2coordinates/81.220.239.218
     */
    public function testGeocodeWithRealIPv4GetsNullContent()
    {
        $provider = new DataScienceToolkitProvider($this->getMockAdapterReturns(null));
        $provider->geocode('81.220.239.218');
    }

    /**
     * @expectedException \Geocoder\Exception\NoResult
     * @expectedExceptionMessage Could not execute query http://www.datasciencetoolkit.org/ip2coordinates/81.220.239.218
     */
    public function testGeocodeWithRealIPv4GetsEmptyContent()
    {
        $provider = new DataScienceToolkitProvider($this->getMockAdapterReturns(''));
        $provider->geocode('81.220.239.218');
    }

    /**
     * @expectedException \Geocoder\Exception\UnsupportedOperation
     * @expectedExceptionMessage The DataScienceToolkit provider does not support IPv6 addresses.
     */
    public function testGeocodeWithRealIPv6()
    {
        $provider = new DataScienceToolkitProvider($this->getAdapter());
        $result = $provider->geocode('::ffff:88.188.221.14');
    }

    public function testGeocodeWithRealIPv4()
    {
        $provider = new DataScienceToolkitProvider($this->getAdapter());
        $result   = $provider->geocode('81.220.239.218');

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $result = $result[0];
        $this->assertInternalType('array', $result);
        $this->assertEquals(45.75, $result['latitude'], '', 0.01);
        $this->assertEquals(4.8499999046326, $result['longitude'], '', 0.01);
        $this->assertEquals('Lyon', $result['city']);
        $this->assertEquals('France', $result['country']);
        $this->assertEquals('FR', $result['countryCode']);
    }

    public function testGeocodeWithRealAdress()
    {
        $provider = new DataScienceToolkitProvider($this->getAdapter());
        $result   = $provider->geocode('2543 Graystone Place, Simi Valley, CA 93065');

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $result = $result[0];
        $this->assertInternalType('array', $result);
        $this->assertEquals(34.280874, $result['latitude'], '', 0.01);
        $this->assertEquals(-118.766282, $result['longitude'], '', 0.01);
        $this->assertEquals('Simi Valley', $result['city']);
        $this->assertEquals('United States', $result['country']);
        $this->assertEquals('US', $result['countryCode']);
    }

    /**
     * @expectedException \Geocoder\Exception\UnsupportedOperation
     * @expectedExceptionMessage The DataScienceToolkit provider is not able to do reverse geocoding.
     */
    public function testReverse()
    {
        $provider = new DataScienceToolkitProvider($this->getMockAdapter($this->never()));
        $provider->reverse(1, 2);
    }
}
