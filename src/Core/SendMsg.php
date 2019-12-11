<?php
namespace Myfcomic\Core;

/**
 * Interface SendMsg
 * @package Myfcomic\Core
 */
interface SendMsg {

    /**
     * @param array $msg_body
     * @return mixed
     */
    public function send(array $msg_body);
}