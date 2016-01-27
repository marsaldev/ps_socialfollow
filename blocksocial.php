<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Blocksocial extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'blocksocial';
        $this->tab = 'front_office_features';
        $this->version = '2.0';
        $this->author = 'PrestaShop';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Social networking block');
        $this->description = $this->l('Allows you to add information about your brand\'s social networking accounts.');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return (parent::install() and Configuration::updateValue('BLOCKSOCIAL_FACEBOOK', '') &&
            Configuration::updateValue('BLOCKSOCIAL_TWITTER', '') &&
            Configuration::updateValue('BLOCKSOCIAL_RSS', '') &&
            Configuration::updateValue('BLOCKSOCIAL_YOUTUBE', '') &&
            Configuration::updateValue('BLOCKSOCIAL_GOOGLE_PLUS', '') &&
            Configuration::updateValue('BLOCKSOCIAL_PINTEREST', '') &&
            Configuration::updateValue('BLOCKSOCIAL_VIMEO', '') &&
            Configuration::updateValue('BLOCKSOCIAL_INSTAGRAM', '') &&
            $this->registerHook('displayFooter'));
    }

    public function uninstall()
    {
        //Delete configuration
        return (Configuration::deleteByName('BLOCKSOCIAL_FACEBOOK') &&
            Configuration::deleteByName('BLOCKSOCIAL_TWITTER') &&
            Configuration::deleteByName('BLOCKSOCIAL_RSS') &&
            Configuration::deleteByName('BLOCKSOCIAL_YOUTUBE') &&
            Configuration::deleteByName('BLOCKSOCIAL_GOOGLE_PLUS') &&
            Configuration::deleteByName('BLOCKSOCIAL_PINTEREST') &&
            Configuration::deleteByName('BLOCKSOCIAL_VIMEO') &&
            Configuration::deleteByName('BLOCKSOCIAL_INSTAGRAM') &&
            parent::uninstall());
    }

    public function getContent()
    {
        // If we try to update the settings
        $output = '';
        if (Tools::isSubmit('submitModule')) {
            Configuration::updateValue('BLOCKSOCIAL_FACEBOOK', Tools::getValue('blocksocial_facebook', ''));
            Configuration::updateValue('BLOCKSOCIAL_TWITTER', Tools::getValue('blocksocial_twitter', ''));
            Configuration::updateValue('BLOCKSOCIAL_RSS', Tools::getValue('blocksocial_rss', ''));
            Configuration::updateValue('BLOCKSOCIAL_YOUTUBE', Tools::getValue('blocksocial_youtube', ''));
            Configuration::updateValue('BLOCKSOCIAL_GOOGLE_PLUS', Tools::getValue('blocksocial_google_plus', ''));
            Configuration::updateValue('BLOCKSOCIAL_PINTEREST', Tools::getValue('blocksocial_pinterest', ''));
            Configuration::updateValue('BLOCKSOCIAL_VIMEO', Tools::getValue('blocksocial_vimeo', ''));
            Configuration::updateValue('BLOCKSOCIAL_INSTAGRAM', Tools::getValue('blocksocial_instagram', ''));
            $this->_clearCache('blocksocial.tpl');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&conf=4&module_name='.$this->name);
        }

        return $output.$this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Facebook URL'),
                        'name' => 'blocksocial_facebook',
                        'desc' => $this->l('Your Facebook fan page.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Twitter URL'),
                        'name' => 'blocksocial_twitter',
                        'desc' => $this->l('Your official Twitter account.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('RSS URL'),
                        'name' => 'blocksocial_rss',
                        'desc' => $this->l('The RSS feed of your choice (your blog, your store, etc.).'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('YouTube URL'),
                        'name' => 'blocksocial_youtube',
                        'desc' => $this->l('Your official YouTube account.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google+ URL:'),
                        'name' => 'blocksocial_google_plus',
                        'desc' => $this->l('Your official Google+ page.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Pinterest URL:'),
                        'name' => 'blocksocial_pinterest',
                        'desc' => $this->l('Your official Pinterest account.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Vimeo URL:'),
                        'name' => 'blocksocial_vimeo',
                        'desc' => $this->l('Your official Vimeo account.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Instagram URL:'),
                        'name' => 'blocksocial_instagram',
                        'desc' => $this->l('Your official Instagram account.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $helper->submit_action = 'submitModule';
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
        );
        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'blocksocial_facebook' => Tools::getValue('blocksocial_facebook', Configuration::get('BLOCKSOCIAL_FACEBOOK')),
            'blocksocial_twitter' => Tools::getValue('blocksocial_twitter', Configuration::get('BLOCKSOCIAL_TWITTER')),
            'blocksocial_rss' => Tools::getValue('blocksocial_rss', Configuration::get('BLOCKSOCIAL_RSS')),
            'blocksocial_youtube' => Tools::getValue('blocksocial_youtube', Configuration::get('BLOCKSOCIAL_YOUTUBE')),
            'blocksocial_google_plus' => Tools::getValue('blocksocial_google_plus', Configuration::get('BLOCKSOCIAL_GOOGLE_PLUS')),
            'blocksocial_pinterest' => Tools::getValue('blocksocial_pinterest', Configuration::get('BLOCKSOCIAL_PINTEREST')),
            'blocksocial_vimeo' => Tools::getValue('blocksocial_vimeo', Configuration::get('BLOCKSOCIAL_VIMEO')),
            'blocksocial_instagram' => Tools::getValue('blocksocial_instagram', Configuration::get('BLOCKSOCIAL_INSTAGRAM')),
        );
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached('blocksocial.tpl', $this->getCacheId())) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->display(__FILE__, 'blocksocial.tpl', $this->getCacheId());
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $social_links = [];

        if (Configuration::get('BLOCKSOCIAL_FACEBOOK')) {
            $social_links['facebook'] = [
                'label' => $this->l('Facebook'),
                'class' => 'facebook',
                'url' => Configuration::get('BLOCKSOCIAL_FACEBOOK'),
            ];
        }

        if (Configuration::get('BLOCKSOCIAL_TWITTER')) {
            $social_links['twitter'] = [
                'label' => $this->l('Twitter'),
                'class' => 'twitter',
                'url' => Configuration::get('BLOCKSOCIAL_TWITTER'),
            ];
        }

        if (Configuration::get('BLOCKSOCIAL_RSS')) {
            $social_links['rss'] = [
                'label' => $this->l('Rss'),
                'class' => 'rss',
                'url' => Configuration::get('BLOCKSOCIAL_RSS'),
            ];
        }

        if (Configuration::get('BLOCKSOCIAL_YOUTUBE')) {
            $social_links['youtube'] = [
                'label' => $this->l('Youtube'),
                'class' => 'youtube',
                'url' => Configuration::get('BLOCKSOCIAL_YOUTUBE'),
            ];
        }

        if (Configuration::get('BLOCKSOCIAL_GOOGLE_PLUS')) {
            $social_links['googleplus'] = [
                'label' => $this->l('Google +'),
                'class' => 'googleplus',
                'url' => Configuration::get('BLOCKSOCIAL_GOOGLE_PLUS'),
            ];
        }

        if (Configuration::get('BLOCKSOCIAL_PINTEREST')) {
            $social_links['pinterest'] = [
                'label' => $this->l('Pinterest'),
                'class' => 'pinterest',
                'url' => Configuration::get('BLOCKSOCIAL_PINTEREST'),
            ];
        }

        if (Configuration::get('BLOCKSOCIAL_VIMEO')) {
            $social_links['vimeo'] = [
                'label' => $this->l('Vimeo'),
                'class' => 'vimeo',
                'url' => Configuration::get('BLOCKSOCIAL_VIMEO'),
            ];
        }

        if (Configuration::get('BLOCKSOCIAL_INSTAGRAM')) {
            $social_links['instagram'] = [
                'label' => $this->l('Instagram'),
                'class' => 'instagram',
                'url' => Configuration::get('BLOCKSOCIAL_INSTAGRAM'),
            ];
        }

        return [
            'social_links' => $social_links,
        ];
    }
}
