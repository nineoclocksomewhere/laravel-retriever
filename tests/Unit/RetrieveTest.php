<?php

namespace Nocs\Retriever\Tests\Unit;

use Nocs\Retriever\Tests\TestCase;

class RetrieveTest extends TestCase
{

    /**
     * @todo: Test get
     *
     * @return void
     */
    public function testRetrieve()
    {

        $this->assertEquals('red', retriever('retriever::colors.red'));
        $this->assertEquals('green', retriever('retriever::colors.green'));
        $this->assertEquals('blue', retriever('retriever::colors.blue'));

    }

}
