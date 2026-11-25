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

    public function testArgumentsAndForget(): void
    {
        $this->assertSame('purple', retriever()->get('retriever::colors.shade', ['purple']));
        $this->assertSame('orange', retriever()->get('retriever::colors.shade', ['orange']));
        $this->assertNull(retriever()->get('retriever::unknown'));

        retriever()->forget('retriever::colors.shade', ['purple']);
        $this->assertSame('purple', retriever()->get('retriever::colors.shade', ['purple']));
    }

    public function testLoadingRetrieversWithoutNamespaceDoesNotEmitDeprecations(): void
    {
        $deprecations = [];
        set_error_handler(static function ($severity, $message) use (&$deprecations) {
            if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
                $deprecations[] = $message;
            }

            return false;
        });

        try {
            $manager = retriever();
            $result = $manager->loadRetrieversFrom(__DIR__.'/../Cache');
        } finally {
            restore_error_handler();
        }

        $this->assertSame($manager, $result);
        $this->assertSame([], $deprecations);
    }

    public function testExceptions()
    {

        $this->expectException(BadMethodCallException::class);
        retriever()->thisMethodReallyDoesntExistWhatsoever();

    }

}
