<?php

namespace Fortuna;

use Fortuna\Model\Consumidor;
use Rain\Tpl;
use Fortuna\Model\Usuario;

class Page
{
    private $tpl;
    private $options  = [];
    private $defaults = [
        "header" => true,
        "footer" => true,
        "data"   => []
    ];

    public function __construct($opts = array(), $tpl_dir = '/views/')
    {
        $opts['data']['consumidor_logado'] = Consumidor::checklogin();
        $opts['data']['consumidor_nome']   = Consumidor::getFromSession('nome');

        $this->options = array_merge($this->defaults, $opts);

        $config = array(
            "tpl_dir"   => BASE_DIR . $tpl_dir,
            "cache_dir" => BASE_DIR . "/views-cache/",
            "debug"     => false,
        );

        Tpl::configure($config);

        $this->tpl = new Tpl;

        $this->setData($this->options['data']);

        if ($this->options['header'] === true) $this->tpl->draw('header');
    }


    private function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->tpl->assign($key, $value);
        }
    }

    public function setTpl($name, $data = array(), $returnHTML = false)
    {
        $this->setData($data);

        return $this->tpl->draw($name, $returnHTML);
    }

    public function __destruct()
    {
        if ($this->options['footer'] === true) $this->tpl->draw("footer");
    }
}
