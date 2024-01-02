<?php
namespace Delorius\Application;

interface IController
{
    const SUFFIX_ACTION = 'Action';
    const SUFFIX_PARTIAL = 'Partial';

    function execute($method,array $params = null);
}
