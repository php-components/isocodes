<?php
use PHPUnit\Framework\TestCase;
use ISOCodes\ISO639_2\Adapter\Pdo;
use ISOCodes\ISO639_2\Model\ISO639_2Interface;

class ISO639_2PdoTest extends TestCase
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
            $this->assertInstanceOf(ISO639_2Interface::class, $current);
        }
    }
    
    public function testGetAlpha2()
    {
        $adapter = new Pdo();
        $single = $adapter->get('af');
        $this->assertInstanceOf(ISO639_2Interface::class, $single);
        $this->assertEquals('Afrikaans', $single->name);
        
        $single2 = $adapter->get('AF');
        $this->assertInstanceOf(ISO639_2Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testGetAlpha3()
    {
        $adapter = new Pdo();
        $single = $adapter->get('ada');
        $this->assertInstanceOf(ISO639_2Interface::class, $single);
        $this->assertEquals('Adangme', $single->name);
        
        $single2 = $adapter->get('ADA');
        $this->assertInstanceOf(ISO639_2Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testGetBibliographic()
    {
        $adapter = new Pdo();
        $single = $adapter->getBibliographic('TIB');
        $this->assertInstanceOf(ISO639_2Interface::class, $single);
        $this->assertEquals('Tibetan', $single->name);
        
        $single2 = $adapter->getBibliographic('tib');
        $this->assertInstanceOf(ISO639_2Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testHasAlpha2()
    {
        $adapter = new Pdo();
        $has     = $adapter->has('AF');
        $has2    = $adapter->has('af');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->has('ZZ');
        $this->assertFalse($has);
    }
    
    public function testHasAlpha3()
    {
        $adapter = new Pdo();
        $has     = $adapter->has('ADA');
        $has2    = $adapter->has('ada');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->has('AAA');
        $this->assertFalse($has);
    }
    
    public function testHasBibliographic()
    {
        $adapter = new Pdo();
        $has     = $adapter->hasBibliographic('TIB');
        $has2    = $adapter->hasBibliographic('tib');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->hasBibliographic('AAA');
        $this->assertFalse($has);
    }
}