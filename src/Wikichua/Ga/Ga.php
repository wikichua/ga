<?php namespace Wikichua\Ga;
use Config;

use Google_Client,
	Google_Service_Analytics,
	Google_Auth_AssertionCredentials;

class Ga {
	
	protected 	$client,
				$service,
				$siteIDs;

	public function __construct() {
		$this->client = new Google_Client(
				array(
					'oauth2_client_id' => Config::get('ga::ga.CLIENTID'),
				)
			);
		$this->client->setAccessType('online');
		$cred = new Google_Auth_AssertionCredentials(
		  Config::get('ga::ga.EMAILADDRESS'),
		  array('https://www.googleapis.com/auth/analytics.readonly'),
		  file_get_contents(Config::get('ga::ga.SECURITYCERT'))
		);
		$this->client->setAssertionCredentials($cred);
		$this->service = new Google_Service_Analytics($this->client);
	}

	public function all() {
		$metrics = $this->metrics == ''? 
			'ga:'.str_replace('ga:', '', Config::get('ga::ga.metrics', '')):$this->metrics;
		if(!isset($this->others['dimensions']))
		{
			$this->others['dimensions'] = 
				'ga:'.str_replace('ga:', '', Config::get('ga::ga.dimensions', ''));
		}
		return $this->service->data_ga->get($this->profileID, $this->from, $this->to, $metrics, $this->others);
	}

	public function get()
	{
		$rows = $this->all();
		return isset($rows['rows'])? $rows['rows']:[];
	}

	private $profileID = 0,
			$to = 'yesterday',
			$from = '7daysAgo',
			$metrics = '',
			$others = [];

	public function __call($method, $args) {
		if(!in_array($method, array('make','to','from')))
		{
			$value = $this->args($args);			
			if($method == 'metrics')
			{
				$this->{$method} .= ','.$value;
				$this->{$method} = trim($this->{$method}, ',');
			}else{
				$this->others[$method] = isset($this->others[$method])? $this->others[$method]:'';
				$this->others[$method] .= ','.$value;
				$this->others[$method] = trim($this->others[$method], ',');
			}
		}else{
			$value = $args[0];
			if($method == 'make')
				$this->profileID = 'ga:'.str_replace('ga:', '', $value);
			else
				$this->{$method} = $value;
		}

		return $this;
	}

	public function range($from,$to='yesterday')
	{
		$this->from = $from;
		$this->to = $to;

		return $this;
	}

	private function args($args)
	{
		$str = '';

		foreach ($args as $value) {
			$str .= 'ga:'.str_replace('ga:', '', $value) . ',';
		}

		return trim($str,',');
	}

	public function getSegments() {
		return $this->service->management_segments;
	}

	public function getAccounts() {
		return $this->service->management_accounts;
	}

	public function getGoals() {
		return $this->service->management_goals;
	}

	public function getProfiles() {
		return $this->service->management_profiles;
	}

	public function getWebProperties() {
		return $this->service->management_webproperties;
	}

	public function getAllSitesIds() {
		if (empty($this->siteIDs)) {
			$sites = $this->getProfiles()->listManagementProfiles("~all", "~all");
			foreach($sites['items'] as $site) {
				$this->siteIDs[$site['websiteUrl']] = 'ga:' . $site['id'];
			}
		}

		return $this->siteIDs;
	}

	public function getSiteIdByUrl($url) {
		if (!isset($this->siteIDs[$url])) {
			$this->getAllSitesIds();
		}

		if (isset($this->siteIDs[$url])) {
			return $this->siteIDs[$url];
		}

		throw new \Exception("Site $url is not valid or permission denied in your Analytics account.");
	}

}