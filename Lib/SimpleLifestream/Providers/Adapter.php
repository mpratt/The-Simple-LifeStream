<?php
/**
 * Adapter.php
 *
 * @package Providers
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Providers;

/**
 * This class provides a minor logic for every service and
 * it acts as a "Interface" for them.
 *
 * Every provider should extend this class.
 *
 * @abstract
 */
abstract class Adapter
{
    /** @var Array Associative array with settings for the current service */
    protected $settings = array(
        'resource' => null,
        'callback' => null,
    );

    /** @var object Instance of \SimpleLifestream\HttpRequest */
    protected $http;

    /** @var string The api url for the current service */
    protected $url;

    /** @var array Array with callbacks for the response */
    protected $callbacks = array();

    /**
     * Gets the data API response and returns an array
     * with all the information.
     *
     * @return array
     * @throws Exception When an error happened, no matter what kind.
     */
    abstract public function getApiData();

    /**
     * Constructor
     *
     * @param array $settings
     * @return void
     */
    public function __construct(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);
        foreach ((array) $this->settings['callback'] as $func) {
            $this->addCallback($func);
        }

        unset($this->settings['callback']);
    }

    /**
     * Returns an instance of the current provider
     *
     * @return void
     */
    public function registerHttpConsumer(\SimpleLifestream\HttpRequest $http)
    {
        $this->http = $http;
    }

    /**
     * Gets the API url of the current service
     *
     * @return string
     */
    public function getApiUrl()
    {
        if (!empty($this->url)) {
            return sprintf($this->url, $this->settings['resource']);
        }

        return $this->settings['resource'];
    }

    /**
     * Applies callback functions to the given value
     *
     * @param array $value
     * @return array
     */
    protected function applyCallbacks($value)
    {
        $return = array();
        foreach ($this->callbacks as $func) {
            $return = (array) $func((!empty($return) ? $return : $value));
        }

        return (array) $return;
    }

    /**
     * Adds a new callback to the stream provider
     *
     * @return void
     * @throws Exception when $callback is not callable
     *
     * TODO: When the PHP requirements for this library are >= 5.4
     *       we can use the `Callable` typehint!
     */
    public function addCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \Exception('The addCallback method expects a callable function');
        }

        $this->callbacks[] = $callback;
    }
}
?>
