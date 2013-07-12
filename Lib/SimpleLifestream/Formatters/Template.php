<?php
/**
 * Template.php
 *
 * @package SimpleLifestream
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Formatters;

/**
 * A formatter that acts as a Decorator for the main library.
 * It outputs the lifestream data as a custom string.
 */
class Template
{

    /** @var object Instance of \SimpleLifestream\SimpleLifestream */
    protected $lifestream;

    /** @var string The template with placeholders to be replaces */
    protected $template;

    /** @var string String to be set after the template */
    protected $afterTemplate = '';

    /** @var string String to be set before the template */
    protected $beforeTemplate = '';

    /**
     * Constructor
     *
     * @param object $lifestream Instance of \SimpleLifestream\SimpleLifestream
     * @return void
     */
    public function __construct(\SimpleLifestream\SimpleLifestream $lifestream) { $this->lifestream = $lifestream; }

    /**
     * Sets the Template to be outputted
     *
     * @param string $template
     * @return void
     */
    public function setTemplate($template) { $this->template = $template; }

    /**
     * Data to be set before the Template
     *
     * @param string $before
     * @return void
     */
    public function beforeTemplate($before) { $this->beforeTemplate = $before; }

    /**
     * Data to be set after the Template
     *
     * @param string $after
     * @return void
     */
    public function afterTemplate($after) { $this->afterTemplate = $after; }

    /**
     * Gets the lifestream data and returns
     * a string with the template
     *
     * @param int $count
     * @return string
     */
    public function getLifestream($count = 0)
    {
        $return = $this->lifestream->getLifestream($count);
        return $this->processTemplate($return);
    }

    /**
     * Process the data and constructs the template, replacing
     * the found placeholders wirh actual values.
     *
     * @param array $data
     * @return string
     */
    protected function processTemplate(array $data = array())
    {
        if (empty($data) || empty($this->template))
            return ;

        $return = $this->beforeTemplate;
        foreach ($data as $d)
        {
            $return .= str_replace(array_map(function ($n){
                return '{' . $n . '}';
            }, array_keys($d)), array_values($d), $this->template);
        }

        return $return . $this->afterTemplate;
    }

    /**
     * Truly decorate the lifestream object
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     *
     * @throws InvalidArgumentException when a method was not found
     */
    public function __call($method, $args)
    {
        if (is_callable(array($this->lifestream, $method)))
            return call_user_func_array(array($this->lifestream, $method), $args);

        throw new \InvalidArgumentException('No method ' . $method . ' was found');
    }
}

?>
