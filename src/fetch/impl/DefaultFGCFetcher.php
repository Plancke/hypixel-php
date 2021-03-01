<?php

namespace Plancke\HypixelPHP\fetch\impl;

use Plancke\HypixelPHP\exceptions\BadResponseCodeException;
use Plancke\HypixelPHP\exceptions\CurlException;
use Plancke\HypixelPHP\exceptions\FileGetContentsException;
use Plancke\HypixelPHP\fetch\Fetcher;
use Plancke\HypixelPHP\fetch\Response;

/**
 * Class DefaultFetcher
 * @package Plancke\HypixelPHP\fetch\impl
 */
class DefaultFGCFetcher extends Fetcher {

    /**
     * @param string $url
     * @param array $options
     * @return Response
     * @throws BadResponseCodeException
     * @throws CurlException
     * @throws FileGetContentsException
     */
    public function getURLContents(string $url, $options = []): Response {
        $response = new Response();
        $stream_options = [
            'http' => [
                'timeout' => $this->timeOut / 1000
            ]
        ];
        if (array_key_exists('headers', $options)) {
            $stream_options['http']['header'] = implode("\r\n", $options['headers']);
        }

        $ctx = stream_context_create($stream_options);

        $out = file_get_contents($url, 0, $ctx);
        if ($out === false) {
            throw new FileGetContentsException($url);
        }

        $data = json_decode($out, true);
        if (isset($data['success'])) {
            $response->setSuccessful($data['success']);
            unset($data['success']);
        } else {
            $response->setSuccessful(true);
        }
        $response->setData($data);
        return $response;
    }

}