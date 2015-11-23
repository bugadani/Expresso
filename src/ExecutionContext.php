<?php

namespace Expresso;

class ExecutionContext extends \ArrayObject
{
    public function access($where, $what, $nullSafe = false)
    {
        if (is_array($where)) {
            if (!$nullSafe || isset($where[ $what ])) {
                return $where[ $what ];
            }
        }
    }
}