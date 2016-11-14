<?php

namespace Plancke\HypixelPHP\fetch\impl;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\fetch\Fetcher;
use Plancke\HypixelPHP\fetch\Response;

/**
 * Class DefaultFetcher
 * @package Plancke\HypixelPHP\fetch\impl
 */
class DefaultFetcher extends Fetcher {

    protected $useCurl;

    /**
     * @return boolean
     */
    public function useCurl() {
        return $this->useCurl;
    }

    /**
     * @param boolean $useCurl
     * @return $this
     */
    public function setUseCurl($useCurl) {
        $this->useCurl = $useCurl;
        return $this;
    }

    public function fetch($fetch, $keyValues = []) {
        $requestURL = Fetcher::BASE_URL . $fetch . '?key=' . $this->getHypixelPHP()->getAPIKey();
        $debug = $fetch;
        foreach ($keyValues as $key => $value) {
            $value = trim($value);
            $value = str_replace(' ', '%20', $value);
            $requestURL .= '&' . $key . '=' . $value;
            $debug .= '?' . $key . '=' . $value;
        }
        $this->getHypixelPHP()->getLogger()->log('Starting Fetch: ' . $debug);

        $response = $this->getURLContents($requestURL);
        if (!$response->wasSuccessful()) {
            $this->getHypixelPHP()->getLogger()->log('Fetch Failed! ' . $response);

            // If one fails, stop trying for that session
            // ideally also have a cached check before
            $this->getHypixelPHP()->getCacheHandler()->setGlobalTime(CacheHandler::MAX_CACHE_TIME);
        } else {
            $this->getHypixelPHP()->getLogger()->log('Fetch successful!');
        }

        return $this->getResponseAdapter()->adaptResponse($fetch, $response);
    }

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
                if ($error != null) {
                    return $response->addError($error);
                }
                if ($curlOut === false) {
                    return $response;
                }
                if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != '200') {
                    return $response->addError('Status not 200');
                }

                $data = json_decode($curlOut, true);
                if (isset($data['success'])) {
                    $response->setSuccessful($data['success']);
                    unset($data['success']);
                }
                $response->setData($data);
                return $response;
            } finally {
                curl_close($ch);
            }
        } else {
            $ctx = stream_context_create([
                'https' => ['timeout' => $this->timeOut / 1000]
            ]);

            $out = file_get_contents($url, 0, $ctx);
            if ($out === false) {
                return $response->addError('Failed to pull data!');
            }

            $data = json_decode($out, true);
            if (isset($data['success'])) {
                $response->setSuccessful($data['success']);
                unset($data['success']);
            }
            $response->setData($data);
            return $response;
        }
    }

}