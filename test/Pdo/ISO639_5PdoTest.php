<?php
use PHPUnit\Framework\TestCase;
use ISOCodes\ISO639_5\Adapter\Pdo;
use ISOCodes\ISO639_5\Model\ISO639_5Interface;

class ISO639_5PdoTest extends TestCase
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
            $this->assertInstanceOf(ISO639_5Interface::class, $current);
        }
    }
    
    public function testGetAlpha3()
    {
        $adapter = new Pdo();
        $single = $adapter->get('apa');
        $this->assertInstanceOf(ISO639_5Interface::class, $single);
        $this->assertEquals('Apache languages', $single->name);
        
        $single2 = $adapter->get('APA');
        $this->assertInstanceOf(ISO639_5Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testHasAlpha3()
    {
        $adapter = new Pdo();
        $has     = $adapter->has('APA');
        $has2    = $adapter->has('apa');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->has('ZZZ');
        $this->assertFalse($has);
    }
}