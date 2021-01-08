<?php


namespace Bboyyue\Channel\worker;


class config
{
//  connect 中的一些配置
    protected $server;
    protected $client;
//  container 中的一些配置.
    protected $send;
    protected $listen;
    protected $query;
    protected $close_listen;
//  event 中的一些配置
    protected $onBegin;
    protected $onEnd;
    protected $forTapType;

// 容器中的操作列表
    protected $method_list;
// 事件列表
    protected $event_list;
// 错误消息
    private $errorMsg;
// 当前进行的操作
    protected $name;


    function __construct()
    {
        // 获取容器方法
        $this->method_list = array_keys(config("channel.container"));
        // 获取事件方法
        $this->event_list = array_keys(config("channel.event"));

        // 初始化配置项
        $this->init_connect();
        $this->init_container();
        $this->init_event();
    }

    protected function init_connect()
    {
        $this->server = config("channel.connect.server");
        $this->client = config("channel.connect.client");
    }

    /**
     * 将配置项申明为类属性
     */
    protected function init_container()
    {
        try {
            foreach ($this->method_list as $method ) {
                $this->{$method} = config("channel.container.".$method);
            }
        } catch (\Exception $e) {
            $this->errorMsg = "容器初始化失败: ".$e->getMessage();
        }

    }

    /**
     * 将配置项申明为类属性
     */
    protected function init_event()
    {
        try {
            foreach ($this->event_list as $event) {
                $this->{$event} = config("channel.event.".$event);
            }
        } catch (\Exception $e) {
            $this->errorMsg = "事件初始化失败: ".$e->getMessage();
        }
    }

    /**
     * @param string $name
     * @return $this
     * 调用容器方法时,需要先设置当前操作的容器方法类型
     */
    public function setMethod(string $name)
    {
        try {
            if (in_array($name,$this->method_list)) {
                $this->name = $name;
            } else {
                throw new \Exception('请求的方法不存在');
            }
        } catch (\Exception $e) {
            $this->errorMsg = "处理传入的 method 的时候出现了问题: ".$e->getMessage();
        }
        return $this;
    }

    /**
     * @param string $method
     * @return mixed
     */
    public function checkMethod(string $method){
        if(in_array($method,$this->method_list)) return true;
        else return false;
    }

    public function getServerPort()
    {
        return $this->server['port'];
    }

    public function getServerContent()
    {
        return $this->server['content'];
    }

    public function getClientLink()
    {
        return $this->client['protocol'] . "://" . $this->client['ip'] . ':' . $this->client['port'];
    }
    public function getClientIsSsl()
    {
        return $this->client['ssl'];
    }
    public function getClientContext()
    {
        return $this->client['context'];
    }

    public function getClientProcCount()
    {
        return $this->client['proc_count'];
    }

    /**
     * @return array
     * 这里的$name指的是容器方法名称,通过容器方法名找到事件方法列表
     */
    public function getOnBegin(): array
    {
        $onBegin = array();
        $name = $this->name;
        foreach ($this->onBegin as $key => $val){
            if(in_array($key,$this->{$name})){
                $onBegin[] = $val;
            }
        }
        if($this->check()) return $onBegin;
        else return [];
    }

    public function getOnEnd(): array
    {
        $onEnd = array();
        $name = $this->name;
        foreach ($this->onEnd as $key => $val){
            if(in_array($key,$this->{$name})){
                $onEnd[] = $val;
            }
        }
        if($this->check()) return $onEnd;
        else return [];
    }

    // 注入指定对应的方法
    public function getForTapType(): array
    {
        return $this->forTapType;
    }

    protected function check():bool
    {
        if($this->errorMsg) return false;
        else return true;
    }
    public function setError($error){
        $this->errorMsg = $error;
    }
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
    function __set($name, $value)
    {
        $this->errorMsg = "程序配置文件出现异常 : 配置项 ".$name." - ".$value." 不存在";
    }
}
