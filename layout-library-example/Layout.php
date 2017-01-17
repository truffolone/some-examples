<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * The idea here is allowing the controller dictate the layout block creation. You can setup external javascripts to load as well as css.
  * There is also the possibility to load some partial js inside the $(document).ready(); in order to avoid multiple statements widhout loading unnecessary data
 */
class Layout {
    private $obj;
    private $layout_view;
    private $title = '';
    private $description = '';
    private $keywords = '';
    private $bodyClass = "";
    private $css_list = array(), $js_list = array(), $jsOnLoad = array();
    private $ogmetas = array();
    private $metas = array();
    private $public = "public/";
    private $bodySearch = "woman";
    private $_lmv = false;
    private $_lmvData = array();

    function Layout() {
        $this->obj =& get_instance();
        $this->layout_view = "layout/default.php";
        // Grab layout from called controller
        if (isset($this->obj->layout_view)) $this->layout_view = $this->obj->layout_view;

        //loading lang
        $this->obj->lang->load("global", $this->obj->user->get('lang'));

        //public path
        if(MOBILE) {
            $this->public .= "";
        }
    }

    function view($view, $data = null, $return = false) {
        $this->obj->session->set_userdata('referred_from', current_url());
        // Render template
        $data['content_for_layout'] = $this->obj->load->view($view, $data, true);
        $data['title_for_layout'] = $this->title == '' ? $this->obj->lang->line("meta.home.title") : $this->title . " | " . $this->obj->lang->line("meta.home.title");
        $data['description_for_layout'] = $this->description == '' ? $this->obj->lang->line("meta.home.description") : $this->description;
        $data['keywords_for_layout'] = $this->keywords;
        $data['bodyClass'] = $this->bodyClass;
        $data['ogmetas'] = $this->ogmetas;
        $data['metas'] = $this->metas;
        $data['publicpath'] = $this->public;
        $data['sale'] = $this->obj->core_model->getASale();

        //loading flashdata for messages
        $data['successMessage'] = $this->obj->session->flashdata("success") ? $this->obj->session->flashdata("success") : NULL;
        $data['alertMessage'] = $this->obj->session->flashdata("alert") ? $this->obj->session->flashdata("alert") : NULL;
        $data['warningMessage'] = $this->obj->session->flashdata("warning") ? $this->obj->session->flashdata("warning") : NULL;
        $data['bodySearch'] = $this->bodySearch;
        $data['leftMenuView'] = !$this->_lmv ? $this->obj->load->view("menu/menu_mobile_" . $this->obj->user->get('lang'), array(), true) : $this->obj->load->view($this->_lmv, $this->_lmvData, true);
        
        // Render resources
        $data['js_for_layout'] = '';
        foreach ($this->js_list as $v)
            $data['js_for_layout'] .= sprintf('<script type="text/javascript" src="' . base_url() . $this->public . '%s"></script>', $v);

        $data['css_for_layout'] = '';
        foreach ($this->css_list as $v)
            $data['css_for_layout'] .= sprintf('<link rel="stylesheet" type="text/css"  href="' . base_url() . $this->public . '%s" />', $v);

        $data['js_on_load'] = '';
        foreach ($this->jsOnLoad as $v)
            $data['js_on_load'] .= file_get_contents(base_url() . $this->public . $v) . "\n";

        $output = $this->obj->load->view($this->layout_view, $data, $return);

        return $output;
    }

    public function leftMenuView($lmv, $data = array()) {
        $this->_lmv = $lmv;
        $this->_lmvData = $data;
    }

    public function bodySearch($val) {
        $this->bodySearch = $val;
    }

    /** 
     * Set Layout
     */
    public function setLayout($layout) {
        $this->layout_view = $layout;
    }

    /**
     * Set page meta
     *
     * @param $meta, $content
     */
    function meta($meta, $content) {
        $this->metas[$meta] = $content;
    }

    /**
     * Set page og meta
     *
     * @param $meta, $content
     */
    function ogmeta($meta, $content) {
        $this->ogmetas[$meta] = $content;
    }

    /**
     * Set page body class
     *
     * @param $bodyClass
     */
    function bodyClass($bodyClass) {
        $this->bodyClass = $bodyClass;
    }

    /**
     * Set page title
     *
     * @param $title
     */
    function title($title) {
        $this->title = $title;
    }

    /**
     * Set page description
     *
     * @param $description
     */
    function description($description) {
        $this->description = $description;
    }

    /**
     * Set page keywords
     *
     * @param $keywords
     */
    function keywords($keywords) {
        $this->keywords = $keywords;
    }

    /**
     * Adds Javascript to add to the $(document).load(function(){}); resource to current page
     * @param $item
     */
    function jsOnLoad($item) {
        $this->jsOnLoad[] = $item;
    }

    /**
     * Adds Javascript resource to current page
     * @param $item
     */
    function js($item) {
        $this->js_list[] = $item;
    }

    /**
     * Adds CSS resource to current page
     * @param $item
     */
    function css($item) {
        $this->css_list[] = $item;
    }

}