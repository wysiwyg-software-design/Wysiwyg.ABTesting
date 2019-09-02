<?php

namespace Wysiwyg\ABTesting\Domain\Decider;

interface DeciderInterface
{
    public function decide(array $decisions);
}
