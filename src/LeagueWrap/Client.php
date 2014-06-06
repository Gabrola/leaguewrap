<?php
namespace LeagueWrap;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ServerException;

class Client implements ClientInterface {

	protected $guzzle;
    public $log = [];

	/**
	 * Sets the base url to be used for future requests.
	 *
	 * @param string $url
	 * @return void
	 */
	public function baseUrl($url)
	{
		$this->guzzle = new Guzzle(['base_url' => $url]);
	}

	/**
	 * Attempts to do a request of the given path.
	 *
	 * @param string $path
	 * @param array $params
	 * @return string
	 * @throws BaseUrlException
	 */
	public function request($path, array $params = [])
	{
		if ( ! $this->guzzle instanceof Guzzle)
		{
			throw new BaseUrlException('BaseUrl was never set. Please call baseUrl($url).');
		}

		$uri      = $path.'?'.http_build_query($params);

        $mt = microtime(true);
        try
        {
            $response = $this->guzzle
                             ->get($uri);
        }
        catch(ServerException $e)
        {
            if($e->getCode() == 503)
            {
                $dif = microtime(true) - $mt;
                $this->log[] = [ "error" => '503', "url" => $this->guzzle->getBaseUrl() . $uri, "time" => $dif ];
                return $this->request($path, $params);
            }
            else
                throw $e;
        }

        $dif = microtime(true) - $mt;
        $this->log[] = [ "url" => $this->guzzle->getBaseUrl() . $uri, "time" => $dif ];
		
		$body = $response->getBody();
		$body->seek(0);
		return $body->read($body->getSize());
	}
}
