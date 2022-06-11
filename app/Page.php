<?php

namespace Fortuna;

use Rain\Tpl;

class Page
{
    private $tpl;
    private $options  = [];
    private $defaults = [
        "header" => false,
        "footer" => false,
        "data" => []
    ];

    public function __construct($opts = array(), $tpl_dir = '/views/')
    {
        $this->options = array_merge($this->defaults, $opts);

        $config = array(
            "tpl_dir"   =>dirname(__DIR__).$tpl_dir,
            "cache_dir" =>dirname(__DIR__)."/views-cache/",
            "debug"     => false,
        );

        Tpl::configure( $config );

        $this->tpl = new Tpl;

        $this->setData($this->options['data']);

        if($this->options['header'] === true) $this->tpl->draw('header');
    }

    
    private function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->tpl->assign($key, $value);
        }
    }

    public function setTpl($name ,$data = array(), $returnHTML = false)
    {
        $this->setData($data);

        return $this->tpl->draw($name, $returnHTML);
    }

    public function __destruct()
    {
        if($this->options['footer'] === true) $this->tpl->draw("footer");
    }
}
