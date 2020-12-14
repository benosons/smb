<?php
namespace Application\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* get helpermanager gan */
        $headScript = $container->get('ViewHelperManager')->get('headScript');

        return new \Application\Controller\UserController($headScript);
    }
}