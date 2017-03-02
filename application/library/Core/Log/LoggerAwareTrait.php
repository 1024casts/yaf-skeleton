<?php
namespace Core\Log;

/**
 * Class LoggerAwareTrait
 *
 * @property \Psr\Log\LoggerInterface $logger
 */
trait LoggerAwareTrait
{
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }
}
