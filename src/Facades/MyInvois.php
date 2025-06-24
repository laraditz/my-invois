<?php

namespace Laraditz\MyInvois\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Laraditz\MyInvois\Skeleton\SkeletonClass
 */
class MyInvois extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'myinvois';
    }
}
