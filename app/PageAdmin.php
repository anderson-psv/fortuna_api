<?php

namespace Fortuna;

use Rain\Tpl;
use Fortuna\Model\UsuarioAdmin;

class PageAdmin
{
    private $tpl;
    private $options  = [];
    private $defaults = [
        "header"  => true,
        "footer"  => true,
        'sub_res' => false,
        "data" => []
    ];

    public function __construct($opts = array(), $tpl_dir = '/views/admin/')
    {
        $opts['data']['admin_nome'] = UsuarioAdmin::getFromSession('nome');

        if ($opts['sub_res']) {
            $opts['data']['res_path'] = './../../res/admin/';
        }
        else {
            $opts['data']['res_path']   = './../res/admin/';
        }

        $this->options = array_merge($this->defaults, $opts);

        $config = array(
            "tpl_dir"   => BASE_DIR . $tpl_dir,
            "cache_dir" => BASE_DIR . "/views-cache/admin/",
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
