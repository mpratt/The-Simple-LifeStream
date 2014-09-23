<?php
/**
 * SimpleLifestream.php
 *
 * @package SimpleLifestream
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream;

/**
 * The main Class for this library
 */
class SimpleLifestream
{
    /** @var int The version of this library */
    const VERSION = '4.7.1';

    /** @var array Array with the loaded providers. */
    protected $providers = array();

    /** @var array Associative array wwith configuration directives */
    protected $config = array();

    /** @var array An array with all the caught errors */
    protected $errors = array();

    /**
     * Instantiates available services on construction.
     *
     * @param array $config  Associative array with configuration directives.
     * @return void
     */
    public function __construct(array $config = array())
    {
        $this->config = array_merge(array(
            'blacklist' => array(),
            'date_format' => 'Y-m-d H:i',
            'link_format' => '<a href="{url}">{text}</a>',
            'merge_strategy' => 'Ymd',
            'language' => 'English'
        ), $config);

        $this->config['blacklist'] = array_map('strtolower',
            array_flip($this->config['blacklist'])
        );
    }

    /**
     * Loads the streams
     *
     * @param array $providers Array with \SimpleLifestream\Stream objects
     * @return object Instance of this object
     */
    public function loadStreams(array $providers)
    {
        $providers = array_filter($providers, function ($p){
            return ($p instanceof Stream);
        });

        $result = array();
        $http = new \SimpleLifestream\HttpRequest($this->config);

        foreach ($providers as $p)
        {
            $p->registerHttpConsumer($http);
            $result[$p->getId()] = $p;
        }

        $this->providers = array_merge($this->providers, $result);
        return $this;
    }

    /**
     * Calls all available streams and gets the relevant data.
     * Returns an array with the results found sorted by date.
     *
     * @param int $limit The maximal amount of entries you want to get, 0 disables it
     * @return array
     */
    public function getLifestream($limit = 0)
    {
        $output = array();
        $cachePool  = new \SimpleLifestream\Cache\Pool($this->config);
        $cacheItems = $cachePool->getItems(array_keys($this->providers));

        foreach ($this->providers as $key => $provider)
        {
            if ($cacheItems[$key]->isHit())
                $response = $cacheItems[$key]->get();
            else
            {
                $response = $provider->getResponse();
                if (!$errors = $provider->getErrors())
                    $cacheItems[$key]->set($response);

                $this->errors = array_merge($this->errors, (array) $errors);
            }

            $output = array_merge($output, (array) $response);
        }

        $output = $this->translate(array_filter($output));
        $output = $this->deleteBlacklisted($output);

        usort($output, function ($a, $b) {
            return $a['stamp'] < $b['stamp'];
        });

        if ($limit > 0)
            $output = array_slice($output, 0, $limit);

        return $output;
    }

    /**
     * Tells the library which types should be ignored
     *
     * @param string $type     The type of event that should be ignored
     * @param string $service  When specified ignore only the $type on this service
     * @return void
     */
    public function ignore($type, $service = '') { $this->config['blacklist'][strtolower($type . $service)] = true;  }

    /**
     * Validates and translates the values returned by the
     * services.
     *
     * @param array $array
     * @return array
     */
    protected function translate(array $payload = array())
    {
        $result = array();
        $language = $this->loadLanguage();
        foreach ($payload as $data)
        {
            $id = md5($data['service'] . $data['type'] . $data['text'] . date($this->config['merge_strategy'], $data['stamp']));
            $link = str_replace(array_map(function ($n){
                return '{' . $n . '}';
            }, array_keys($data)), array_values($data), $this->config['link_format']);

            $rel = $this->getRelativeInterval($data['stamp']);
            $result[$id] = array_merge($data, array(
                'link' => $link,
                'date' => date($this->config['date_format'], (int) $data['stamp']),
                'date_relative' => $language->getRelativeTranslation(key($rel), (int) current($rel)),
                'html' => str_replace('{link}', $link, $language->get($data['type'])),
            ));
        }

        return $result;
    }

    /**
     * Calculates the interval between the dates and returns
     * an array with the valid time.
     *
     * @param string $from
     * @param string $to When null is given, uses the current date.
     * @return array
     */
    protected function getRelativeInterval($from, $to = null)
    {
        $fromTime = new \DateTime();
        $fromTime->setTimestamp($from);

        if (!$to) {
            $to = time();
        }

        $toTime = new \DateTime();
        $toTime->setTimestamp($to);

        $interval = $fromTime->diff($toTime);
        $units = array_filter(array(
            'years'   => (int) $interval->y,
            'months'  => (int) $interval->m,
            'days'    => (int) $interval->d,
            'hours'   => (int) $interval->h,
            'minutes' => (int) $interval->i,
        ));

        if (empty($units)) {
            return array('just_now' => 0);
        }

        return array_slice($units, 0, 1);
    }

    /**
     * Loads a new Language
     *
     * @return object
     */
    protected function loadLanguage()
    {
        $languages = array(
            '\SimpleLifestream\Languages\\' . ucfirst($this->config['language']),
            $this->config['language'],
        );

        foreach ($languages as $lang)
        {
            if (class_exists($lang))
                return new $lang();
        }

        $this->errors[] = 'Could not load the language ' . $this->config['language'];
        return new \SimpleLifestream\Languages\English();
    }

    /**
     * Removes blacklisted actions/services
     *
     * @param array $payload
     * @return array
     */
    protected function deleteBlacklisted(array $payload)
    {
        $blacklist = array_keys($this->config['blacklist']);
        return array_filter($payload, function($a) use ($blacklist) {
            // Check if at least one of the values is blacklisted. When none is found, keep the array
            return (count(array_intersect($blacklist, array($a['type'], $a['type'] . $a['service']))) === 0);
        });
    }

    /**
     * Returns true if there were any errors on execution.
     *
     * @return bool
     */
    public function hasErrors() { return (!empty($this->errors)); }

    /**
     * Returns an array with errors catched while executing the script.
     *
     * @return array
     */
    public function getErrors() { return $this->errors; }

    /**
     * Returns the last error
     *
     * @return string
     */
    public function getLastError() { return end($this->errors); }
}

?>
