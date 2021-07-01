<?php

namespace Nocs\Retriever\Tests\Feature;

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

}
