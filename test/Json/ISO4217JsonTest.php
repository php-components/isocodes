<?php
use PHPUnit\Framework\TestCase;
use ISOCodes\ISO4217\Adapter\Json;
use ISOCodes\ISO4217\Model\ISO4217Interface;

class ISO4217JsonTest extends TestCase
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
            $this->assertInstanceOf(ISO4217Interface::class, $current);
        }
    }
    
    public function testGetAlpha3()
    {
        $adapter = new Json();
        $single = $adapter->get('EUR');
        $this->assertInstanceOf(ISO4217Interface::class, $single);
        $this->assertEquals('Euro', $single->name);
        
        $single2 = $adapter->get('eur');
        $this->assertInstanceOf(ISO4217Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testGetNumeric()
    {
        $adapter = new Json();
        $single = $adapter->get('840');
        $this->assertInstanceOf(ISO4217Interface::class, $single);
        $this->assertEquals('US Dollar', $single->name);
    
        $single2 = $adapter->get(840);
        $this->assertInstanceOf(ISO4217Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testHasAlpha3()
    {
        $adapter = new Json();
        $has     = $adapter->has('EUR');
        $has2    = $adapter->has('eur');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->has('AAA');
        $this->assertFalse($has);
    }
    
    public function testHasNumeric()
    {
        $adapter = new Json();
        $has     = $adapter->has('840');
        $has2    = $adapter->has(840);
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->has(0);
        $this->assertFalse($has);
    }
}