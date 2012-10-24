<?php
/**
 * Spanish.php
 * Translation to the english language
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Languages;

class Spanish extends \SimpleLifestream\Core\Lang
{
    protected $translation = array('pushEvent'   => 'actualizó el proyecto {link}.',
                                   'createEvent' => 'creó el proyecto {link}.',
                                   'createGist'  => 'creó el gist {link}',
                                   'updateGist'  => 'actualizó el gist {link}',
                                   'starred'     => 'está observando a {link}.',
                                   'favorited'   => 'agregó {link} a sus favoritos.',
                                   'followed'    => 'se suscribió a {link}.',
                                   'commented'   => 'comentó en "{link}".',
                                   'posted'      => 'escribió {link}.',
                                   'tweeted'     => 'twitteó "{link}".',
                                   'link'        => '{link}.',
                                   'answered'    => 'contestó la pregunta "{link}".',
                                   'asked'       => 'preguntó "{link}".',
                                   'acceptedAnswer' => 'aceptó una respuesta a "{link}".',
                                   'badgeWon'    => 'ganó la insignia {link}.'
                               );
}
?>
