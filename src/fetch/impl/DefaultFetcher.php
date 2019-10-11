<?php

namespace Plancke\HypixelPHP\fetch\impl;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\exceptions\BadResponseCodeException;
use Plancke\HypixelPHP\exceptions\CurlException;
use Plancke\HypixelPHP\exceptions\FileGetContentsException;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\Fetcher;
use Plancke\HypixelPHP\fetch\Response;

/**
 * Class DefaultFetcher
 * @package Plancke\HypixelPHP\fetch\impl
 */
class DefaultFetcher extends Fetcher {

    protected $useCurl = true;

    /**
     * @param boolean $useCurl
     * @return $this
     */
    public function setUseCurl($useCurl) {
        $this->useCurl = $useCurl;
        return $this;
    }

    /**
     * @param string $fetch
     * @param array $keyValues
     * @return Response
     * @throws HypixelPHPException
     */
    public function fetch($fetch, $keyValues = []) {
        $requestURL = Fetcher::BASE_URL . $fetch;

        $debug = $fetch;
        if (is_array($keyValues) && sizeof($keyValues) > 0) {
            $requestURL .= '?';
            $debug .= $fetch;
            foreach ($keyValues as $key => $value) {
                $value = urlencode(trim($value));
                $requestURL .= '&' . $key . '=' . $value;
                $debug .= '&' . $key . '=' . $value;
            }
        }

        $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Starting Fetch: ' . $debug);

        $response = $this->getURLContents($requestURL);
        if (!$response->wasSuccessful()) {
            $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Fetch Failed! ' . var_export($response, true));

            // If one fails, stop trying for that session
            // ideally also have a cached check before
            $this->getHypixelPHP()->getCacheHandler()->setGlobalTime(CacheHandler::MAX_CACHE_TIME);
        } else {
            $this->getHypixelPHP()->getLogger()->log(LOG_DEBUG, 'Fetch successful!');
        }

        return $this->getResponseAdapter()->adaptResponse($fetch, $keyValues, $response);
    }

    /**
     * @param string $url
     * @return Response
     * @throws HypixelPHPException
     */
    public function getURLContents($url) {
        $response = new Response();
        if ($this->useCurl) {
            $ch = curl_init();
            try {
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeOut);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->timeOut);
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                $curlOut = curl_exec($ch);

                $error = curl_error($ch);
                if ($error != null && $error != '') {
                    throw new CurlException($error);
                }
                if ($curlOut === false) {
                    return $response;
                }

                $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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
            } finally {
                curl_close($ch);
            }
        } else {
            $ctx = stream_context_create([
                'http' => ['timeout' => $this->timeOut / 1000]
            ]);

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
        }
        return $response;
    }

}