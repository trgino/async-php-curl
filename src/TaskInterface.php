<?php
namespace trgino\Http;


interface TaskInterface
{
    /**
     * get created curl resource
     * @return resource curl
     */
    public function getCurl();

    /**
     * create curl resource
     * @return mixed
     */
    public function createCurl();
}