<?php
use PHPUnit\Framework\TestCase;
use ISOCodes\ISO15924\Adapter\Json;
use ISOCodes\ISO15924\Model\ISO15924Interface;

class ISO15924JsonTest extends TestCase
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
            $this->assertInstanceOf(ISO15924Interface::class, $current);
        }
    }
    
    public function testGetAlpha4()
    {
        $adapter = new Json();
        $single = $adapter->get('Adlm');
        $this->assertInstanceOf(ISO15924Interface::class, $single);
        $this->assertEquals('Adlam', $single->name);
        
        $single2 = $adapter->get('ADLM');
        $this->assertInstanceOf(ISO15924Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testGetNumeric()
    {
        $adapter = new Json();
        $single = $adapter->get('230');
        $this->assertInstanceOf(ISO15924Interface::class, $single);
        $this->assertEquals('Armenian', $single->name);
    
        $single2 = $adapter->get(230);
        $this->assertInstanceOf(ISO15924Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testHasAlpha4()
    {
        $adapter = new Json();
        $has     = $adapter->has('Adlm');
        $has2    = $adapter->has('ADLM');

        $this->assertTrue($has);
        $this->assertTrue($has2);
        
        $has     = $adapter->has('AAAA');
        $this->assertFalse($has);
    }
    
    public function testHasNumeric()
    {
        $adapter = new Json();
        $has     = $adapter->has('230');
        $has2    = $adapter->has(230);

        $this->assertTrue($has);
        $this->assertTrue($has2);
        
        $has     = $adapter->has(0);
        $this->assertFalse($has);
    }
}