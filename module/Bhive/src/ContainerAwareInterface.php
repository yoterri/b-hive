<?php

namespace Bhive;
use Interop\Container\ContainerInterface;

interface ContainerAwareInterface
{

    function setContainer(ContainerInterface $container);
    
    function getContainer();
    
}