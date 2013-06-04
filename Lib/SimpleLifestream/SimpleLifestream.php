<?php
/**
 * SimpleLifestream.php
 * The main class of this library.
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

use \SimpleLifestream\FileCache,
    \SimpleLifestream\Languages\English,
    \SimpleLifestream\Languages\Spanish,
    \SimpleLifestream\HttpRequest;

class SimpleLifestream
{
    protected $services   = array();
    protected $config     = array();
    protected $errors     = array();
    protected $blacklist  = array();
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $linkTemplate = '<a href="{url}">{text}</a>';
    protected $mergeConsecutive = false;
    protected $http = null;

    /**
     * Instantiates available services on construction.
     *
     * @param array $config  An array with the service name and resource. A resource could be a username or a resource
     *                       and it depends on each service.
     * @return void
     */
    public function __construct(array $services = array(), array $config = array())
    {
        $this->config = array_merge(
            array(
                'lang' => new English(),
                'cache' => true,
                'cache_dir' => __DIR__ . '/Cache',
                'cache_ttl' => (60*10),
                'cache_prefix' => 'SimpleLifestreamFileCache',
            ),
            $config
        );

        $this->http = new HttpRequest($this->config, new FileCache($this->config));
        if (!empty($services))
        {
            foreach ($services as $name => $resource)
                $this->loadService($name, $resource);
        }
    }

    /**
     * Instantiates and initializes a service object!
     * It stores the object in the services array property.
     *
     * @param string $service
     * @param string $resource The resource related to the service, like a username or url
     * @return void
     */
    public function loadService($service, $resource)
    {
        $class = '\SimpleLifestream\Services\\' . $service;
        $this->services[] = new $class($this->http, $resource);
    }

    /**
     * Calls all available Services and fetches its data.
     * Returns an array with the results found sorted by date.
     *
     * @param int $limit The maximal amount of entries you want to get, 0 shows everything fetched
     * @return array
     */
    public function getLifestream($limit = 0)
    {
        if (empty($this->services))
            return array();

        $output = array();
        foreach ($this->services as $service)
        {
            try {
                $output = array_filter(array_merge($output, $service->getApiData()));
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        $output = $this->translate($output);
        usort($output, function ($a, $b) {
            return $a['stamp'] < $b['stamp'];
        });

        if ($limit > 0 && count($output) > $limit)
            $output = array_slice($output, 0, $limit);

        return $output;
    }

    /**
     * Changes the way a link is displayed by the library.
     *
     * @param string $template The template with a {url} and {text} placeholder
     * @return void
     */
    public function setLinkTemplate($template) { $this->linkTemplate = $template; }

    /**
     * Returns an array with errors catched while executing the script.
     *
     * @param string $type     The type of event that should be ignored
     * @param string $service  When specified ignore only the $type on this service
     * @return void
     */
    public function ignoreType($type, $service = '') { $this->blacklist[lcfirst($type)] = strtolower($service); }

    /**
     * Sets the date format for each event.
     *
     * @param string A format supported by php's date function
     * @return void
     */
    public function setDateFormat($format) { $this->dateFormat = $format; }

    /**
     * When set to true, the library merges consecutive actions that
     * are equal.
     *
     * @param bool $merge
     * @return void
     */
    public function mergeConsecutive($merge) { $this->mergeConsecutive = (bool) $merge; }

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
    public function getLastError()
    {
        if (!empty($this->errors))
            return end($this->errors);

        return ;
    }

    /**
     * Validates and translates the values returned by the
     * services.
     *
     * @param array $array
     * @return array
     */
    protected function translate(array $payload)
    {
        if (empty($payload))
            return array();

        $defaultValues = array(
            'stamp' => 0,
            'type'  => 'unknown',
            'service' => 'unknown',
            'url'  => '',
            'text' => '',
            'date' => '',
            'resource' => '',
        );

        $i = 0;
        $result = array();
        foreach ($payload as $v)
        {
            $v = array_merge($defaultValues, $v);
            $v = array(
                'type'  => lcfirst($v['type']),
                'stamp' => (int) $v['stamp'],
                'date'  => date($this->dateFormat, $v['stamp']),
                'text'  => htmlspecialchars($v['text'], ENT_QUOTES, 'UTF-8', false),
                'url'   => htmlspecialchars($v['url'], ENT_QUOTES, 'UTF-8', false),
                'service' => strtolower($v['service']),
                'resource' => $v['resource'],
            );

            $link = str_replace(array_map(function ($n){
                return '{' . $n . '}';
            }, array_keys($v)), array_values($v), $this->linkTemplate);

            if ($this->mergeConsecutive)
                $id = md5($v['service'] . $v['type'] . $v['text']);
            else
                $id = $i;

            $result[$id] = array_merge($v, array(
                    'link' => $link,
                    'html' =>  str_replace('{link}', $link, $this->config['lang']->get($v['type']))
                )
            );

            $i++;
        }

        return $this->deleteBlacklisted($result);
    }

    /**
     * Removes blacklisted actions
     *
     * @param array $payload
     * @return array
     *
     */
    protected function deleteBlacklisted(array $payload)
    {
        $blacklist = $this->blacklist;
        return array_filter($payload, function($a) use ($blacklist) {
            if (isset($blacklist[$a['type']]))
                return (!empty($blacklist[$a['type']]) && $blacklist[$a['type']] != $a['service']);

            return true;
        });
    }
}

?>
