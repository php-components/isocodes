<?php
use PHPUnit\Framework\TestCase;
use ISOCodes\ISO3166_3\Adapter\Pdo;
use ISOCodes\ISO3166_3\Model\ISO3166_3Interface;

class ISO3166_3PdoTest extends TestCase
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
            $this->assertInstanceOf(ISO3166_3Interface::class, $current);
        }
    }
    
    public function testGetAlpha3()
    {
        $adapter = new Pdo();
        $single  = $adapter->get('AFI');
        $this->assertInstanceOf(ISO3166_3Interface::class, $single);
        $this->assertEquals('French Afars and Issas', $single->name);
        
        $single2 = $adapter->get('afi');
        $this->assertInstanceOf(ISO3166_3Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testGetAlpha4()
    {
        $adapter = new Pdo();
        $single  = $adapter->get('AIDJ');
        $this->assertInstanceOf(ISO3166_3Interface::class, $single);
        $this->assertEquals('French Afars and Issas', $single->name);
        
        $single2 = $adapter->get('aidj');
        $this->assertInstanceOf(ISO3166_3Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testGetNumeric()
    {
        $adapter = new Pdo();
        $single  = $adapter->get('262');
        $this->assertInstanceOf(ISO3166_3Interface::class, $single);
        $this->assertEquals('French Afars and Issas', $single->name);
        
        $adapter = new Pdo();
        $single2 = $adapter->get(262);
        $this->assertInstanceOf(ISO3166_3Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testHasAlpha3()
    {
        $adapter = new Pdo();
        $has     = $adapter->has('AFI');
        $has2    = $adapter->has('afi');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->has('AAA');
        $this->assertFalse($has);
    }
    
    public function testHasAlpha4()
    {
        $adapter = new Pdo();
        $has     = $adapter->has('AIDJ');
        $has2    = $adapter->has('aidj');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->has('AAAA');
        $this->assertFalse($has);
    }
    
    public function testHasNumeric()
    {
        $adapter = new Pdo();
        $has     = $adapter->has('262');
        $has2    = $adapter->has(262);
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->has(0);
        $this->assertFalse($has);
    }
    
}