<?php

namespace root4root\Reshaper;

interface ProcessorInterface
{
    public function requiredCol(array $rule);
    public function processCol(array $rule);
    public function setData(ReshaperData $data);
}
