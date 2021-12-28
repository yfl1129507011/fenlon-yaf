<?php
class Bootstrap extends Yaf\Bootstrap_Abstract {

    public function _initConfig() {
        //把配置保存起来
        $arrConfig = Yaf\Application::app()->getConfig();
        Yaf\Registry::set('config', $arrConfig);

        // 加载数据库配置文件
        $dbConfig = new Yaf\Config\Ini(ROOT_PATH . '/conf/database.ini');
        Yaf\Registry::set('dbConfig', $dbConfig);
    }

    public function _initPlugin(Yaf\Dispatcher $dispatcher) {
        //注册一个插件
        /*$objSamplePlugin = new SamplePlugin();
        $dispatcher->registerPlugin($objSamplePlugin);*/
    }

    public function _initLoader(Yaf\Dispatcher $dispatcher) {
        // 通过php.ini来指定全局类的绝对路径
        // yaf.library = "/home/wwwroot/fenlon.com/application/library"
        // 通过调用Yaf_Loader的registerLocalNamespace方法来指定本地目录加载
        $namespace = array('src');
        Yaf\Loader::getInstance(APP_PATH)->registerLocalNamespace($namespace);
    }

    public function _initRoute(Yaf\Dispatcher $dispatcher) {
        //在这里注册自己的路由协议,默认使用简单路由
        $router = $dispatcher->getInstance()->getRouter();
        // 加载路由配置文件
        $routeConfig = new Yaf\Config\Ini(ROOT_PATH . '/conf/route.ini');
        $router->addConfig($routeConfig->get('routes'));
    }

    public function _initView(Yaf\Dispatcher $dispatcher) {
        //在这里注册自己的view控制器，例如smarty,firekylin
    }

    public function _initAutoload(Yaf\Dispatcher $dispatcher) {
        // 自动加载
         require ROOT_PATH . '/vendor/autoload.php';
    }
}