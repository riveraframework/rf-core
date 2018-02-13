<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Html;

/**
 * Class Navigation
 *
 * @since 1.0
 *
 * @package Rf\Core\Html
 *
 * @TODO: Register navigation system
 */
class Navigation {

    /**
     * @var array $leftMenu
     * @since 1.0
     */
    public static $leftMenu;
    
    /**
     * Cette fonction permet de définir un menu sur la gauche de la page.
     * Le tableau passé en paramètre doit être de la forme :
     * array($tplname => array('linkname' => $linkName, 'linkurl' => $linkUrl[, 'class' => $class])[,array(...)[,...]])
     *
     * @since 1.0
     *
     * @param array $array
     * @return void
     */
    public static function setLeftMenu($array) {
        self::$leftMenu = $array;
    }

    /**
     * Cette fonction permet d'ajouter des éléments uns à uns dans le menu gauche
     *
     * @since 1.0
     *
     * @param string $tplName
     * @param string $linkName
     * @param string $linkUrl
     * @return void
     */
    public static function addLeftMenuItem($tplName, $linkName, $linkUrl) {
        self::$leftMenu[$tplName] = array('linkname' => $linkName, 'linkurl' => $linkUrl);
    }

    /**
     * Cette fonction retourne le menu de gauche s'il est défini et renvoi NULL sinon.
     *
     * @since 1.0
     *
     * @return array 
     */
    public static function getLeftMenu() {
        return self::$leftMenu;
    }

    /**
     * Get HTML pagination links
     *
     * @since 1.0
     *
     * @param string $url
     * @param int $nbPages
     * @param int $currentPage
     * @return string
     */
    public static function pagination($url, $nbPages, $currentPage) {
        //@TODO: à faire mieux
        $range = 4;
        $pagination = '';
        $urlparambase = strstr($url, '?') === false ? '?pagenb=' : '&pagenb=';
        if(is_numeric($nbPages) && $nbPages > 1){
            $pagination .= '<ul class="pagination">';
            if($currentPage > 1){
                $i = $currentPage - 1;
                $urlparam = $i == 1 ? '' : $urlparambase.$i;
                $pagination .= '<li><a href="'.$url.$urlparam.'">&laquo; Précédent</a></li>';
            } else {
                $pagination .= '<li>&laquo; Précédent</li>';
            }
            
            for($i = 1; $i <= $nbPages; $i++){
                if($i == $currentPage) {
                    $pagination .= '<li>'.$i.'</li>';
                } elseif($i == 1 || $i == $nbPages){
                    $urlparam = $i == 1 ? '' : $urlparambase.$i;
                    $pagination .= '<li><a href="'.$url.$urlparam.'">'.$i.'</a></li>';
                } elseif($i == $currentPage - $range || $i == $currentPage + $range){
                    $pagination .= '<li>...</li>';
                } elseif($i > $currentPage - $range && $i < $currentPage + $range){
                    $urlparam = $i == 1 ? '' : $urlparambase.$i;
                    $pagination .= '<li><a href="'.$url.$urlparam.'">'.$i.' </a></li>';
                } else {
                    continue;
                }
            }
            
            if($currentPage < $nbPages){
                $pagination .= '<li><a href="'.$url.$urlparambase.($currentPage+1).'">Suivant &raquo;</a></li>';
            } else {
                $pagination .= '<li>Suivant &raquo;</li>';
            }
            $pagination .= '</ul>';
        }
        return $pagination;
    }
}