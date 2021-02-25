# Websocket服务 说明文档

## 用例分析
为了方便大家对 Websocket服务有一个基础的概念. 我们对项目进行分解来进行说明. 主要从以下三种元素入手:
1. 角色
2. 信息
3. 动作

### 1.角色
Websocket 服务角色有以下几种:
1. 服务器端. 服务器端建立服务,所有的客户端通过服务器端建立websocket连接,来间接的和另外一台设备建立websocket连接.
2. 客户端. 通过与服务端建立websocket连接,与其他和服务端建立了websocket连接的客户端通信

客户端可以分为:
1. 同别人进行双向通信的客户端,即一边接收消息,一边发送消息的客户端
2. 同别人进行单向通信的客户端,即只发送或者只接受的客户端.

### 2.信息
主要信息有:
1. 服务信息. 
    1. 频道,
    2. 转发服务器地址: 为了实现接收和转发异步, 所以有两个不同功能的服务服务器端,一个负责转发一个负责与客户端建立连接
    3. 转发服务器端口
    4. Websocket 服务器地址: 负责与客户端的Websocket连接的服务器 
    5. Websocket 服务器端口
    6. Websocket 服务器占用的进程数 websocketProcessCount 
    6. 设备号列表 当前在线的设备列表
    
2. 客户端信息.
    1. 连接ID
    2. 设备号
    3. 单向接收设备列表,
    4. 单向发送设备列表,
    5. 双向通讯列表.
    6. 上次发送消息的时间
    
3. 消息信息.
    1. 设备号.
    2. 请求方法
    3. 消息类型
    4. 消息正文
    
4. 消息类型


### 动作
角色和信息之间的互动称之为『动作』，动作主要由以下几个:
1. 服务端的动作,称为事件
    1. 服务启动
    2. 服务关闭
    3. 客户端建立连接, 
    4. 客户端连接关闭
    5. 接收到客户端发送的消息
    6. 打印服务端状态信息
2. 客户端的动作,称为方法
    1. 查询, 向服务端查询客户端信息
    2. 发送消息, 向其他客户端发送消息
    3. 监听, 监听某个客户端发出的消息
    4. 关闭连接,
    5. 打印客户端信息
    
## 用例分析

1. 服务端用例
    1. 服务端可以启动服务. 
    2. 服务端可以关闭服务.
    3. 服务端可以打印当前在线的设备列表
    4. 服务端可以展示当前转发服务器信息
    5. 服务端可以展示当前websocket服务器信息
    
2. 客户端用例
    1. 通过目标客户端设备号,向指定客户端发送消息.
    2. 通过目标客户端设备号,监听当前设备号的消息.
    3. 客户端可以主动断开与服务端的连接.
    4. 客户端可以获取当前连接的ID监听的设备号.
    5. 客户端可以获取所有监听当前设备号的连接ID.
    6. 客户端可以获取所有已发送消息的连接ID.
    7. 客户端可以获取同当前连接建立了双向连接的连接ID.
    8. 客户端可以获取上次发送消息的时间, 
    9. 客户端可以在长时间不发送消息的情况下自动断开连接

