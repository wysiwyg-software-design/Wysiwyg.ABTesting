<?php

namespace Wysiwyg\ABTesting\ViewHelpers;

use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

class ClassNameViewHelper extends AbstractViewHelper
{
    /**
     * @param $fullClassName
     * @return bool|string
     */
    public function render($fullClassName)
    {
        $lastSlashPosition = strrpos($fullClassName, '\\');
        $classOnlyName = substr($fullClassName, $lastSlashPosition + 1);

        return $classOnlyName;
    }
}
