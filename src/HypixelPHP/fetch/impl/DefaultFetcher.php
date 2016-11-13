<?php

namespace Plancke\HypixelPHP\fetch\impl;

use Plancke\HypixelPHP\cache\CacheHandler;
use Plancke\HypixelPHP\fetch\Fetcher;
use Plancke\HypixelPHP\fetch\Response;

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
        if ($response->getData()['success'] == false) {
            $this->getHypixelPHP()->getLogger()->log('Fetch Failed! ' . $response);
            // If one fails, stop trying for that session
            $this->getHypixelPHP()->getCacheHandler()->setGlobalTime(CacheHandler::MAX_CACHE_TIME);
            $response->setSuccessful(false);
        } else {
            $this->getHypixelPHP()->getLogger()->log('Fetch successful!');
            $response->setSuccessful(true);
        }

        return $response;
    }

    public function getURLContents($url) {
        $response = new Response();
        if ($this->useCurl) {
            $ch = curl_init();
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

            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($curlOut === false) {
                return $response;
            }
            if ($status != '200') {
                return $response->addError('Status not 200');
            }

            return $response->setData(json_decode($curlOut, true));
        } else {
            $ctx = stream_context_create([
                'https' => ['timeout' => $this->timeOut / 1000]
            ]);

            $out = file_get_contents($url, 0, $ctx);
            if ($out === false) {
                return $response->addError('Failed to pull data!');
            }

            return $response->setData(json_decode($out, true));
        }
    }

}