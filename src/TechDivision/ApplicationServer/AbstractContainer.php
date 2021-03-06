<?php

/**
 * TechDivision\ApplicationServer\AbstractContainer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 * @author Johann Zelger <jz@techdivision.com>
 */
abstract class AbstractContainer extends \Stackable implements ContainerInterface
{

    /**
     * Path to the container's receiver configuration.
     * 
     * @var string
     */
    const XPATH_CONFIGURATION_RECEIVER = '/container/receiver';

    /**
     * Path to the receiver's worker.
     * 
     * @var string
     */
    const XPATH_CONFIGURATION_WORKER = '/container/receiver/worker';

    /**
     * Path to the receiver's worker.
     * 
     * @var string
     */
    const XPATH_CONFIGURATION_THREAD = '/container/receiver/thread';

    /**
     * Array with deployed applications.
     * 
     * @var array
     */
    protected $applications = array();

    /**
     * The container configuration.
     * 
     * @var \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration
     */
    protected $configuration;

    /**
     * The server instance.
     * 
     * @var \TechDivision\ApplicationServer\Server
     */
    protected $server;
    
    /**
     * TRUE if the container has been started, else FALSE.
     * @var boolean
     */
    protected $started = false;

    /**
     * Initializes the server instance with the configuration.
     *
     * @param \TechDivision\ApplicationServer\Server $server
     *            The server instance
     * @param \TechDivision\ApplicationServer\Configuration $configuration
     *            The container configuration
     * @todo Application deployment only works this way because of Thread compatibilty
     * @return void
     */
    public function __construct($initialContext, $configuration, $applications)
    {
        $this->initialContext = $initialContext;
        $this->setConfiguration($configuration);
        $this->setApplications($applications);
    }

    /**
     *
     * @see \Stackable::run()
     */
    public function run()
    {
        $this->setStarted($this->getReceiver()->start());
    }

    /**
     *
     * @see \TechDivision\ApplicationServer\Interfaces\ContainerInterface::getReceiver()
     */
    public function getReceiver()
    {
        return $this->newInstance($this->getReceiverType(), array(
            $this->initialContext,
            $this
        ));
    }

    /**
     * Sets an array with the deployed applications.
     *
     * @param array $applications
     *            Array with the deployed applications
     * @return \TechDivision\ServletContainer\Container The container instance itself
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;
        return $this;
    }

    /**
     * Returns an array with the deployed applications.
     *
     * @return array The array with applications
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Sets the passed container configuration.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration $configuration
     *            The configuration for the container
     * @return \TechDivision\ServletContainer\Container The container instance itself
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Returns the actual container configuration.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration The actual container configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Return's the path to the container's receiver configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The receiver configuration instance
     */
    public function getReceiverConfiguration()
    {
        return current($this->getConfiguration()->getChilds(self::XPATH_CONFIGURATION_RECEIVER));
    }

    /**
     * Return's the class name of the container's receiver type.
     *
     * @return string The class name of the container's receiver type
     */
    public function getReceiverType()
    {
        return $this->getReceiverConfiguration()->getType();
    }

    /**
     * Return's the class name of the receiver's worker type.
     *
     * @return string The class name of the receiver's worker type
     */
    public function getWorkerType()
    {
        return current($this->getConfiguration()->getChilds(self::XPATH_CONFIGURATION_WORKER))->getType();
    }

    /**
     * Return's the class name of the receiver's thread type.
     *
     * @return string The class name of the receiver's thread type
     */
    public function getThreadType()
    {
        return current($this->getConfiguration()->getChilds(self::XPATH_CONFIGURATION_THREAD))->getType();
    }

    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     *
     * @param string $className
     *            The class name to create the instance of
     * @param array $args
     *            The parameters to pass to the constructor
     * @return object The created instance
     */
    public function newInstance($className, array $args = array())
    {
        return $this->initialContext->newInstance($className, $args);
    }

    /**
     * Returns the inital context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }
    
    /**
     * Marks the container as started.
     * 
     * @return void
     */
    public function setStarted()
    {
        $this->started = true;
    }
    
    /**
     * Returns TRUE if the container has been started, else FALSE.
     * 
     * @return boolean TRUE if the container has been started, else FALSE
     */
    public function isStarted()
    {
        return $this->started;
    }
}