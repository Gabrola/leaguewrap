<?php
namespace LeagueWrap\Api;

use LeagueWrap\Dto\Champion as Champ;

class Champion extends AbstractApi {
	
	/**
	 * Do we want to only get the free champions?
	 *
	 * @param string
	 */
	protected $free = 'false';

	/**
	 * We keep all the champion results here.
	 *
	 * @param array
	 */
	protected $champions = [];

	/**
	 * Valid versions for this api call.
	 *
	 * @var array
	 */
	protected $versions = [
		'v1.2',
	];

	/**
	 * A list of all permitted regions for the Champion api call.
	 *
	 * @param array
	 */
	protected $permittedRegions = [
		'br',
		'eune',
		'euw',
		'lan',
		'las',
		'na',
		'oce',
		'ru',
		'tr',
		'kr',
	];

	/**
	 * The amount of time we intend to remember the response for.
	 *
	 * @var int
	 */
	protected $defaultRemember = 86400;

	/**
	 * Attempt to get a champion by key.
	 *
	 * @param string $key
	 * @return object|null
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

	/**
	 * Attempt to get a champion by key.
	 *
	 * @param string $key
	 * @return object|null
	 */
	public function get($key)
	{
		$key = strtolower($key);
		if (isset($this->champions[$key]))
		{
			return $this->champions[$key];
		}
		return null;
	}

	/**
	 * Gets all the champions in the given region.
	 *
	 * @return array
	 */
	public function all()
	{
		$params   = [
			'freeToPlay' => $this->free,
		];

		$array = $this->request('champion', $params);

		// set up the champions
		foreach ($array['champions'] as $info)
		{
			$id                   = intval($info['id']);
			$champion             = new Champ($info);
			$this->champions[$id] = $champion;
		}

		return $this->champions;
	}

	/**
	 * Gets the information for a single champion
	 *
	 * @param int $id
	 * @return Champ
	 */
	public function championById($id)
	{
		$info     = $this->request('champion/'.$id);
		$champion = new Champ($info);
		$this->champions[$champion->id] = $champion;

		return $champion;
	}

	/**
	 * Gets all the free champions for this week.
	 *
	 * @uses $this->all()
	 * @return array
	 */
	public function free()
	{
		$this->free = 'true';
		return $this->all();
	}
}
