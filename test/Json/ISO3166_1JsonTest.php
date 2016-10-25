<?php
use PHPUnit\Framework\TestCase;
use ISOCodes\ISO3166_1\Adapter\Json;
use ISOCodes\ISO3166_1\Model\ISO3166_1Interface;

class ISO3166_1JsonTest extends TestCase
{
    public function testExceptionIsRaisedForInvalidConstructorArguments()
    {
        new Json();
    }
    
    public function testGetAll()
    {
        $adapter = new Json();
        $all     = $adapter->getAll();
        $this->assertInternalType('array', $all);
        
        foreach($all as $current) {
            $this->assertInstanceOf(ISO3166_1Interface::class, $current);
        }
    }
    
    public function testGetAlpha2()
    {
        $adapter = new Json();
        $single = $adapter->get('es');
        $this->assertInstanceOf(ISO3166_1Interface::class, $single);
        $this->assertEquals('Spain', $single->name);
        
        $single2 = $adapter->get('ES');
        $this->assertInstanceOf(ISO3166_1Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testGetAlpha3()
    {
        $adapter = new Json();
        $single = $adapter->get('esp');
        $this->assertInstanceOf(ISO3166_1Interface::class, $single);
        $this->assertEquals('Spain', $single->name);
        
        $single2 = $adapter->get('ESP');
        $this->assertInstanceOf(ISO3166_1Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testGetNumeric()
    {
        $adapter = new Json();
        $single = $adapter->get(724);
        $this->assertInstanceOf(ISO3166_1Interface::class, $single);
        $this->assertEquals('Spain', $single->name);
        
        $single2 = $adapter->get("724");
        $this->assertInstanceOf(ISO3166_1Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testHasAlpha2()
    {
        $adapter = new Json();
        $has     = $adapter->has('ES');
        $has2   = $adapter->has('ES');
        $this->assertEquals($has, $has2);
        $this->assertEquals($has, true);
        
        $has     = $adapter->has('AA');
        $this->assertFalse($has);
    }
    
    public function testHasAlpha3()
    {
        $adapter = new Json();
        $has     = $adapter->has('esp');
        $has2   = $adapter->has('ESP');
        $this->assertEquals($has, $has2);
        $this->assertEquals($has, true);
        
        $has     = $adapter->has('AAA');
        $this->assertFalse($has);
    }
    
    public function testHasNumeric()
    {
        $adapter = new Json();
        $has     = $adapter->has(724);
        $has2    = $adapter->has("724");
        $this->assertTrue($has);
        $this->assertTrue($has2);
        
        $has     = $adapter->has(0);
        $this->assertFalse($has);
    }
}