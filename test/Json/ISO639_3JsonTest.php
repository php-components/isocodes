<?php
use PHPUnit\Framework\TestCase;
use ISOCodes\ISO639_3\Adapter\Json;
use ISOCodes\ISO639_3\Model\ISO639_3Interface;

class ISO639_3JsonTest extends TestCase
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
            $this->assertInstanceOf(ISO639_3Interface::class, $current);
        }
    }
    
    public function testGetAllScope()
    {
        $adapter = new Json();
        $all     = $adapter->getAll('I');
        $this->assertInternalType('array', $all);
    
        $all2     = $adapter->getAll('i');
        $this->assertInternalType('array', $all2);
        
        $this->assertEquals(count($all), count($all2));
        
        foreach($all as $current) {
            $this->assertInstanceOf(ISO639_3Interface::class, $current);
            $this->assertEquals('I', $current->scope);
        }
    }
    
    public function testGetAllType()
    {
        $adapter = new Json();
        $all     = $adapter->getAll(null, 'L');
        $this->assertInternalType('array', $all);
    
        $all2     = $adapter->getAll(null, 'l');
        $this->assertInternalType('array', $all2);
    
        $this->assertEquals(count($all), count($all2));
    
        foreach($all as $current) {
            $this->assertInstanceOf(ISO639_3Interface::class, $current);
            $this->assertEquals('L', $current->type);
        }
    }
    
    public function testGetAlpha2()
    {
        $adapter = new Json();
        $single = $adapter->get('ab');
        $this->assertInstanceOf(ISO639_3Interface::class, $single);
        $this->assertEquals('Abkhazian', $single->name);
        
        $single2 = $adapter->get('AB');
        $this->assertInstanceOf(ISO639_3Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testGetAlpha3()
    {
        $adapter = new Json();
        $single = $adapter->get('abd');
        $this->assertInstanceOf(ISO639_3Interface::class, $single);
        $this->assertEquals('Manide', $single->name);
        
        $single2 = $adapter->get('ABD');
        $this->assertInstanceOf(ISO639_3Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testGetBibliographic()
    {
        $adapter = new Json();
        $single = $adapter->getBibliographic('TIB');
        $this->assertInstanceOf(ISO639_3Interface::class, $single);
        $this->assertEquals('Tibetan', $single->name);
        
        $single2 = $adapter->getBibliographic('tib');
        $this->assertInstanceOf(ISO639_3Interface::class, $single2);
        $this->assertEquals($single->name, $single2->name);
    }
    
    public function testHasAlpha2()
    {
        $adapter = new Json();
        $has     = $adapter->has('AB');
        $has2    = $adapter->has('ab');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->has('ZZ');
        $this->assertFalse($has);
    }
    
    public function testHasAlpha3()
    {
        $adapter = new Json();
        $has     = $adapter->has('ABD');
        $has2    = $adapter->has('abd');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->has('ZZZ');
        $this->assertFalse($has);
    }
    
    public function testHasBibliographic()
    {
        $adapter = new Json();
        $has     = $adapter->hasBibliographic('TIB');
        $has2    = $adapter->hasBibliographic('tib');
        $this->assertTrue($has);
        $this->assertTrue($has2);
    
        $has     = $adapter->hasBibliographic('AAA');
        $this->assertFalse($has);
    }
}