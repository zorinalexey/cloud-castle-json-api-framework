<?php

namespace CloudCastle\Core\Model;

enum RelationType: string
{
    case HAS_MANY = 'has_many';
    case HAS_ONE = 'has_one';
}
