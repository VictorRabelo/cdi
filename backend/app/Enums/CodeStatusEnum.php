<?php

namespace App\Enums;

final class CodeStatusEnum
{
    const OK = 200;
    const CREATED = 201;
    const UNAUTHORIZED = 401;
    const NOT_FOUND = 404;
    const ERROR_SERVER = 500;
}