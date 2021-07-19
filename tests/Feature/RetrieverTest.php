<?php

namespace Nocs\Retriever\Tests\Feature;

use BadMethodCallException;
use Nocs\Retriever\Tests\TestCase;

class RetrieverTest extends TestCase
{

    /**
     * @todo: Test get
     *
     * @return void
     */
    public function testRetrieve()
    {

        $this->assertEquals('red', retriever('retriever::colors'));

    }

    public function testCamels()
    {

        $this->assertContains('dog', retriever('retriever::animals_of_the_world.mammals'));

    }

    public function testExceptions()
    {

        $this->expectException(BadMethodCallException::class);
        retriever()->thisMethodReallyDoesntExistWhatsoever();

    }

}
