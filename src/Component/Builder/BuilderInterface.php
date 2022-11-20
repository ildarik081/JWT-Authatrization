<?php

namespace App\Component\Builder;

use App\Component\Exception\BuilderException;

interface BuilderInterface
{
    /**
     * Builds result
     *
     * @return BuilderInterface
     * @throws BuilderException
     */
    public function build(): BuilderInterface;

    /**
     * Resets builder
     *
     * @return BuilderInterface
     */
    public function reset(): BuilderInterface;

    /**
     * Returns builder result
     *
     * @return mixed
     */
    public function getResult(): mixed;
}
