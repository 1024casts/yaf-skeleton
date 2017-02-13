<?php
namespace Core\Events;

interface SubscriberInterface
{
    /**
     * 获取订阅的事件
     * @attention copy from symfony/event-dispatcher
     *
     * 如:
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents();
}