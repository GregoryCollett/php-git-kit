<?php

namespace Chroma\CodeQuality;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
* App Service Container
* Used to resolve services based on an config given :D
*/

class Container
{

  private $configPath = BASE_PATH;
  /**
  * Container for DI
  * @var ContainerBuilder
  */
  private $container;

  public function __construct()
  {
    $this->container = new ContainerBuilder();
    $this->getServiceConfiguration();
  }

  private function getServiceConfiguration()
  {
    $loader = new XmlFileLoader($this->container, new FileLocator($this->configPath));
    $loader->load($this->servicesFile);
  }

  public function get($service)
  {
    return $this->container->get($service);
  }
}
