<?php
/**
 * Stream.php
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
class Stream
{
    /** @var array Mapping of Provider -> Class relation */
    protected $providers = array(
        'facebookpages' => '\SimpleLifestream\Providers\FacebookPages',
        'feed' => '\SimpleLifestream\Providers\Feed',
        'atom' => '\SimpleLifestream\Providers\Feed',
        'rss'  => '\SimpleLifestream\Providers\Feed',
        'github' => '\SimpleLifestream\Providers\Github',
        'reddit' => '\SimpleLifestream\Providers\Reddit',
        'twitter' => '\SimpleLifestream\Providers\Twitter',
        'youtube' => '\SimpleLifestream\Providers\Youtube',
        'stackexchange' => '\SimpleLifestream\Providers\StackExchange',
        'stackoverflow' => '\SimpleLifestream\Providers\StackExchange',
    );

    /** @var object Instance of the current provider */
    protected $provider;

    /** @var array An array with all the caught errors */
    protected $errors = array();

    /**
     * Construct
     *
     * @param string $providerName
     * @param mixed $providerConfig
     * @return void
     *
     * @throws InvalidArgumentException when the provider was not found
     */
    public function __construct($providerName, $providerConfig)
    {
        $providerName = strtolower($providerName);
        if (!isset($this->providers[$providerName]))
        {
            throw new \InvalidArgumentException(
                sprintf('%s is not a valid provider. Allowed Providers are: %s',
                    $providerName, implode(', ', array_keys($this->providers)))
            );
        }

        if (!is_array($providerConfig))
        {
            $providerConfig = array(
                'resource' => $providerConfig,
            );
        }

        $this->provider = new $this->providers[$providerName]($providerConfig);
    }

    /**
     * Returns an instance of the current provider
     *
     * @return void
     */
    public function registerHttpConsumer(\SimpleLifestream\HttpRequest $http) { $this->provider->registerHttpConsumer($http); }

    /**
     * Gets the actual response from the provider and
     * returns an associative array with the relevant
     * information.
     *
     * @return array
     */
    public function getResponse()
    {
        try {
            $response = $this->provider->getApiData();
            if (!is_array($response))
                throw new \Exception(sprintf('Invalid/Empty answer from the url: %s', $this->provider->getApiUrl()));

            return $this->normalizeResponse(array_filter($response));
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            // $this->errors[] = $e->getTraceAsString();
        }

        return array();
    }

    /**
     * Cleans and normalizes the provider's response
     *
     * @param array $data
     * @return array
     */
    protected function normalizeResponse(array $data = array())
    {
        return array_map(function ($data) {
            return array_merge($data, array(
                'service'  => strtolower($data['service']),
                'type'     => strtolower($data['type']),
                'stamp'    => (int) $data['stamp'],
                'text'     => htmlspecialchars($data['text'], ENT_QUOTES, 'UTF-8', false),
                'url'      => htmlspecialchars($data['url'], ENT_QUOTES, 'UTF-8', false),
                'resource' => $data['resource'],
            ));
        }, $data);
    }

    /**
     * Returns an array with errors catched while executing the script.
     *
     * @return array
     */
    public function getErrors() { return $this->errors; }

    /**
     * Returns a unique identifier for the current provider
     *
     * @return string
     */
    public function getId() { return md5($this->provider->getApiUrl()); }
}

?>
