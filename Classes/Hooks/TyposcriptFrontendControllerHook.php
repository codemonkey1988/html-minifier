<?php
namespace Codemonkey1988\HtmlMinifier\Hooks;

/***************************************************************
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use zz\Html\HTMLMinify;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class TyposcriptFrontendControllerHook
 *
 * @package ACodemonkey1988\HtmlMinifier\Hooks
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class TyposcriptFrontendControllerHook
{
    /**
     * Hook for minifing HTML content that is generated for pages with COA_/USER_INT objects.
     *
     * @param array $params
     * @return void
     */
    public function contentPostProcOutput(&$params)
    {
        if ($this->isTypoScriptFrontendInstance($params['pObj']) && $params['pObj']->isINTincScript() === true) {
            $this->minifyHtml($params['pObj']);
        }
    }

    /**
     * Hook for minifing HTML content that is generated for pages without COA_/USER_INT objects.
     *
     * @param array $params
     * @return void
     */
    public function contentPostProcAll(&$params)
    {
        if ($this->isTypoScriptFrontendInstance($params['pObj']) && $params['pObj']->isINTincScript() === false) {
            $this->minifyHtml($params['pObj']);
        }
    }

    /**
     * Check if the parameter is of type \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     *
     * @param mixed $tsfe
     * @return boolean
     */
    protected function isTypoScriptFrontendInstance(&$tsfe)
    {
        return is_object($tsfe) && $tsfe instanceof TypoScriptFrontendController;
    }

    /**
     * @param TypoScriptFrontendController $tsfe
     * @return void
     */
    protected function minifyHtml(&$tsfe)
    {
        if (!class_exists('zz\Html\HTMLMinify')) {
            return;
        } elseif (!isset($tsfe->tmpl->setup['plugin.']['tx_htmlminifier.']['settings.']['enable'])) {
            return;
        } elseif ((int)$tsfe->tmpl->setup['plugin.']['tx_htmlminifier.']['settings.']['enable'] !== 1) {
            return;
        } elseif ($tsfe->no_cache === true) {
            return;
        }

        $minifier      = new HTMLMinify($tsfe->content, $this->prepareMinifierOptions($tsfe));
        $tsfe->content = $minifier->process();
    }

    /**
     * @param TypoScriptFrontendController $tsfe
     * @return array
     */
    protected function prepareMinifierOptions(&$tsfe)
    {
        $options   = [];
        $extConfig = $tsfe->tmpl->setup['plugin.']['tx_htmlminifier.']['settings.'];

        if (isset($tsfe->tmpl->setup['config.']['doctype'])) {
            switch ($tsfe->tmpl->setup['config.']['doctype']) {
                case 'xhtml_trans':
                case 'xhtml_frames':
                case 'xhtml_strict':
                case 'xhtml_basic':
                case 'xhtml_11':
                    $options['doctype'] = HTMLMinify::DOCTYPE_XHTML1;
                    break;
                default:
                    $options['doctype'] = HTMLMinify::DOCTYPE_HTML5;
                    break;
            }
        } else {
            $options['doctype'] = HTMLMinify::DOCTYPE_HTML5;
        }

        $options['removeComment']     = ((int)$extConfig['remove_comments'] === 1);
        $options['optimizationLevel'] = ((int)$extConfig['remove_all_whitespaces'] === 1) ? HTMLMinify::OPTIMIZATION_ADVANCED : HTMLMinify::OPTIMIZATION_SIMPLE;
        $options['excludeComment']    = [];

        if ((int)$extConfig['remove_comments'] === 1 && (int)$extConfig['keep_typo3_header_comment'] === 1) {
            $options['excludeComment'][] = '/<!--\s*This website is powered by TYPO3.*-->/s';
        }

        return $options;
    }
}