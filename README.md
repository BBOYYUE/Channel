#Channel 
bboyyue/channel 是一个基于workerMan的分频道websocket解决方案

1. 使用方法
2. 配置项:
    正确的使用配置,首先你需要在laravel项目的配置文件目录下新增一个 channel 配置文件
    1. status 配置
        - 字段名称
           - client_count
           - server_count
           - channel_count
           - equipment_list 设备列表
    2. connect 配置
        - server 服务端
            - ip ip地址
            - port 端口
            - proc_count 进程数
        - client 客户端
            - protocol
            - port
            - ip
            - proc_count 进程数
    3. container 容器 可以在容器注册一些方法,方法可以绑定事件,比如send需要执行内些事件
       - send  发送的方法
       - listen  监听
       - query  查询
       - close_listen 取消监听  
    4. event 事件,事件可以绑定操作
        - forBegin
        - forEnd
        - forTapType
    
 3. 功能说明:
    1. client 客户端
       - default 分配频道,每台设备上线的时候都会说明自己的设备号,服务器会默认以当前设备的设备号建立专用频道.
         没有说明自己设备号的设备将生成一个唯一的临时设备号. 连接成功之后会返回一个设备号.
       - send 发送消息, 向指定的频道发送消息.注意 send方法不会接收到发送成功的消息,或者返回值.
       - listen 监听指定的频道, 需要手动监听自己的设备号,才能收到自己的消息.
       - query 向服务器发送消息, 发送成功后会接受到返回的消息
       - close_listen 取消监听某个频道
    2. server 服务端
       - default 转发功能
    3. status 状态信息
       - client_count 当前连接的客户端数量
       - server_count 当前的服务进程数
       - channel_count 当前的频道数量
    4. log 日志记录
       - ip IP地址
       - equipment_number 设备号
       - count 连接次数
       - equipment_type 设备类型 
    5. 异常处理
       - configException 会写入到日志里面.
       - containerException 会返回给客户端.
    6. container 容器
       容器需要与 config 配合使用, 容有一个静态的 make 方法可以返回一个容器实例.
       容器实例的属性包含传入的消息内容,和当前消息对应的方法和事件.
       
 4. 自带的事件:
    1. 全局事件 
        1. debug 开启之后,会将所有消息转发到999频道.
        2. check equipment_number . 如果传入的数据中没有 equipment_number 那么会返回一个临时的设备编号 ,消息中的 tapType = 153
    2. 方法事件
        1. query 
            1. heart 心跳功能,默认心跳 tapType = 113 , 接受到来自 113 的消息会返回 消息 
        2. listen
            1. bind 将当前设备与当前连接绑定在一起
        3. send
            1. forward 转发功能
    
