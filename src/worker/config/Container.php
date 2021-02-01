<?php


namespace Bboyyue\Channel\worker\config;



trait Container
{
    /**
     * @param string $name
     * @return $this
     * 动态设置容器配置
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
}