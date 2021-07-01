<?php

namespace Nocs\Retriever\Tests\Cache;

class Colors {

    public function get()
    {
        return $this->red();
    }

    public function red()
    {
        return 'red';
    }

    public function green()
    {
        return 'green';
    }

    public function blue()
    {
        return 'blue';
    }

}
