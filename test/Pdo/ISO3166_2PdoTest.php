<?php
use PHPUnit\Framework\TestCase;
use ISOCodes\ISO3166_2\Adapter\Pdo;
use ISOCodes\ISO3166_2\Model\ISO3166_2Interface;

class ISO3166_2PdoTest extends TestCase
{
    public function testExceptionIsRaisedForInvalidConstructorArguments()
    {
        new Pdo();
    }
    
    public function testGetAll()
    {
        $adapter = new Pdo();
        $all     = $adapter->getAll();
        $this->assertInternalType('array', $all);
        
        foreach($all as $current) {
            $this->assertInstanceOf(ISO3166_2Interface::class, $current);
        }
    }
    
    public function testGetAllParentCountry()
    {
        $adapter = new Pdo();
        $all     = $adapter->getAll('ES');
        $this->assertInternalType('array', $all);
        $this->assertEquals(19, count($all));
        
        $all2     = $adapter->getAll('es');
        $this->assertInternalType('array', $all2);
        $this->assertEquals(count($all), count($all2));
        
        foreach($all as $current) {
            $this->assertInstanceOf(ISO3166_2Interface::class, $current);
        }
    }
    
    public function testGetAllParentRegion()
    {
        $adapter = new Pdo();
        $all     = $adapter->getAll('ES-PV');
        $this->assertInternalType('array', $all);
        $this->assertEquals(3, count($all));
        
        $all2     = $adapter->getAll('ES-PV');
        $this->assertInternalType('array', $all2);
        $this->assertEquals(count($all), count($all2));
        
        foreach($all as $current) {
            $this->assertInstanceOf(ISO3166_2Interface::class, $current);
        }
    }
    
    public function testGetBI()
    {
        $adapter = new Pdo();
        $single  = $adapter->get('ES-BI');
        $this->assertInstanceOf(ISO3166_2Interface::class, $single);
        $this->assertEquals('Bizkaia', $single->name);
        $this->assertEquals('Province', $single->type);
        
        $single2  = $adapter->get('es-bi');
        $this->assertInstanceOf(ISO3166_2Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
        
        $this->assertEquals('Vizcaya', $single->getName('es'));
    }
    
    public function testHasCode()
    {
        $adapter = new Pdo();
        $has     = $adapter->has('ES-BI');
        $has2    = $adapter->has('es-bi');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has = $adapter->has('AA-00');
        $this->assertFalse($has);
    }
}