<?php

namespace Plancke\HypixelPHP\fetch\impl;

use Plancke\HypixelPHP\exceptions\BadResponseCodeException;
use Plancke\HypixelPHP\exceptions\CurlException;
use Plancke\HypixelPHP\fetch\Fetcher;
use Plancke\HypixelPHP\fetch\Response;

/**
 * Class DefaultFetcher
 * @package Plancke\HypixelPHP\fetch\impl
 */
class DefaultCurlFetcher extends Fetcher {

    /**
     * @param string $url
     * @param array $options
     * @return Response
     * @throws BadResponseCodeException
     * @throws CurlException
     */
    public function getURLContents(string $url, $options = []): Response {
        $response = new Response();
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeOut);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->timeOut);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            if (array_key_exists('headers', $options)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
            }
            $responseHeaders = [];
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$responseHeaders) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;

                $responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
            });

            $curlOut = curl_exec($ch);

            $error = curl_error($ch);
            if ($error != null && $error != '') {
                throw new CurlException($error);
            }
            if ($curlOut === false) {
                return $response;
            }

            $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            if ($responseCode != '200') {
                throw new BadResponseCodeException(200, $responseCode);
            }

            $data = json_decode($curlOut, true);
            if (isset($data['success'])) {
                $response->setSuccessful($data['success']);
                unset($data['success']);
            } else {
                $response->setSuccessful(true);
            }
            $response->setData($data);
            $response->setHeaders($responseHeaders);
        } finally {
            curl_close($ch);
        }
        return $response;
    }

}