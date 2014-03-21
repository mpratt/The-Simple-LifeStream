<?php
/**
 * Spanish.php
 *
 * @package Languages
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Languages;

/**
 * Translation to the spanish language
 */
class Spanish extends Adapter
{
    /** inline {@inheritdoc} */
    protected $translation = array(
        'link' => '{link}.',
        'posted' => 'escribió {link}.',
        'starred' => 'esta observando a {link}.',
        'followed' => 'se suscribió a {link}.',
        'commented' => 'comentó en: {link}.',
        'answered' => 'contestó la pregunta: {link}.',
        'accepted' => 'aceptó una respuesta para {link}.',
        'asked' => 'preguntó {link}.',
        'badge' => 'ganó la insignia {link}.',
        'repo-released' => 'liberó la version {link}',
        'repo-created' => 'creó el proyecto {link}.',
        'repo-pushed' => 'actualizó el proyecto {link}.',
        'repo-pull-opened' => 'creó un pull request para {link}',
        'repo-issue-created' => 'participó en el reporte {link}',
        'tweeted' => 'trinó {link}.',
        'favorited' => 'agregó {link} a sus favoritos.',
        'bookmarked' => 'guardó {link}.',
        'uploaded-video'  => 'subió el video {link}.',
        'listened' => 'escuchó a {link}.',
        'created' => 'creó {link}.',
        'took-picture' => 'tomó una foto de {link}.',
        'posted-picture' => 'posteó una foto de {link}.',
        'loved' => 'se enamoró de {link}.',
        'ranted' => 'hizo una pataleta {link}.',
        'liked' => 'le gusta {link}.',
        'uploaded' => 'subió {link}.',
        'time_just_now' => 'justo ahora',
        'time_seconds' => 'hace %d segundos',
        'time_seconds_singular' => 'hace %d segundo',
        'time_minutes' => 'hace %d minutos',
        'time_minutes_singular' => 'hace %d minuto',
        'time_hours' => 'hace %d horas',
        'time_hours_singular' => 'hace %d hora',
        'time_days' => 'hace %d dias',
        'time_days_singular' => 'hace %d dia',
        'time_months' => 'hace %d meses',
        'time_months_singular' => 'hace %d mes',
        'time_years' => 'hace %d años',
        'time_years_singular' => 'hace %d año',
    );
}
?>
