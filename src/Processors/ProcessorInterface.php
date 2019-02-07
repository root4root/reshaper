<?php
namespace Root4root\Reshaper\Processors;

use Root4root\Reshaper\ReshaperData;

interface ProcessorInterface
{
    public function requiredCol(array $rule);
    public function processCol(array $rule);
    public function setData(ReshaperData $data);
}
