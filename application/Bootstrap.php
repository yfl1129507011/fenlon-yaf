<?php
class Bootstrap extends Yaf\Bootstrap_Abstract {

    public function _initConfig() {
        //把配置保存起来
        $arrConfig = Yaf\Application::app()->getConfig();
        /*echo '<pre>';
        print_r($arrConfig);die;*/
        Yaf\Registry::set('config', $arrConfig);
    }

    public function _initPlugin(Yaf\Dispatcher $dispatcher) {
        //注册一个插件
//        $objSamplePlugin = new SamplePlugin();
//        $dispatcher->registerPlugin($objSamplePlugin);
    }

    public function _initRoute(Yaf\Dispatcher $dispatcher) {
        //在这里注册自己的路由协议,默认使用简单路由
        $router = $dispatcher->getInstance()->getRouter();
        $router->addConfig(Yaf\Registry::get('config')->routes);
    }

    public function _initView(Yaf\Dispatcher $dispatcher) {
        //在这里注册自己的view控制器，例如smarty,firekylin
    }
}