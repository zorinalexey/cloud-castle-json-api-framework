<?php

namespace CloudCastle\Core\Validator;

interface ValidatorInterface
{
    public function validate (mixed &$var, mixed $params = []): void;
}