<?php
namespace Delorius\Core;


class Framework {

    const NAME = 'Delorius Framework',
        VERSION = '2.0',
        REVISION = '';

    /** @var bool set to TRUE if your host has disabled function ini_set */
    public static $iAmUsingBadHost = FALSE;

}