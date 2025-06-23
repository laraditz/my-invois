<?php

namespace Laraditz\MyInvois;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Laraditz\MyInvois\Skeleton\SkeletonClass
 */
class MyInvoisFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'my-invois';
    }
}
