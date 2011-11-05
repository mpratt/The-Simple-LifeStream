<?php
/**
 * SimpleLifestream.php
 * @author	Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

require(dirname(__FILE__) . '/SimpleLifestreamAdapter.php');
class SimpleLifestream
{
	protected $services = array();

	/**
	 * Instantiates available services on construction.
	 *
	 * @param mixed $config An array with all the data needed to call the services. When Empty we use load the data from config.ini
	 * @return void
	 */
	public function __construct($config = array())
	{
		$config = (is_array($config) && !empty($config) ? $config : parse_ini_file(dirname(__FILE__) . '/config.ini', true));
		foreach ($config as $serviceName => $values)
		{
			$serviceName .= 'Service';
			if (!is_readable(dirname(__FILE__) . '/Services/' . $serviceName . '.php') || !isset($values['username']))
				throw new Exception('The service ' . $serviceName . ' does not exist or has no username variable!');

			require_once(dirname(__FILE__) . '/Services/' . $serviceName . '.php');
			$serviceObject = new $serviceName();
			$serviceObject->setConfig($values);
			$this->services[] = $serviceObject;
		}
	}

	/**
	 * Calls all available Services and gets all the
	 * Api data and returns an array with the service name, date stamp and html for outputting.
	 *
	 * @param int $limit The maximal amount of entries you want to get.
	 * @return array
	 */
	public function getLifestream($limit = 50)
	{
		$output = array();
		foreach ($this->services as $service)
			$output[] = $service->getApiData();

		$output = $this->flattenArray($output);
		usort($output, array($this, 'orderByDate'));

		if ($limit > 0 && count($output) > $limit)
			$output = array_slice($output, 0, $limit);

		return $output;
	}

	/**
	 * To avoid warnings of strtotime() relying on the system's timezone settings
	 * you can set the timezone on the library itself.
	 *
	 * @param string $timezone The timezone.
	 * @return void
	 */
	public function setTimezone($timezone) { date_default_timezone_set($timezone); }

	/**
	 * flattens a multidimensional array
	 *
	 * @param array $array
	 * @return array
	 */
	protected function flattenArray($array)
	{
		$result = array();
		if (!is_array($array) || empty($array))
			return $result;

		foreach ($array as $value)
			$result = array_merge($result, $value);

		return array_filter($result);
	}

	/**
	 * Callback method that organizes the stream by most recent
	 *
	 * @param array $a
	 * @param array $b
	 * @return bool
	 */
	protected function orderByDate($a, $b) { return $a['date'] < $b['date']; }
}
?>