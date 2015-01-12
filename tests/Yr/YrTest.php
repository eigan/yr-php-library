<?php

class YrTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp", 10, "english");

        $this->assertInstanceOf("Yr\Location", $yr);
    }

    public function testCreateFresh()
    {
        $cache_dir = "/tmp/phpyr".time();
        mkdir($cache_dir);

        $yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", $cache_dir, 10, "english");
        $this->assertInstanceOf("Yr\Location", $yr);
    }

    public function testCreateNorwegian()
    {
        $yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp", 10, "norwegian");
        $this->assertInstanceOf("Yr\Location", $yr);
    }

    public function testCreateNewNorwegian()
    {
        $yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp", 10, "newnorwegian");
        $this->assertInstanceOf("Yr\Location", $yr);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidLocationArgument()
    {
        Yr\Yr::create("", "/tmp", 10, null);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateInvalidLocation2()
    {
        Yr\Yr::create("5855/invalid", "/tmp", 10, null);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateInvalidLocation()
    {
        Yr\Yr::create("Norway/Vestfold/nocity/Nocity", "/tmp", 10, null);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateNotWriteableCache()
    {
        Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/this/dir/does/not/exist/", 10, null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidCachePath()
    {
        Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "", 10, null);
    }
}
