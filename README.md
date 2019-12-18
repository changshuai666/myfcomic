---------------------------------------------使用文档---------------------------------------

注：仅适用于PHP-Laravel框架，其他语言或框架不兼容！

Demo:

     $web_hook = "https://oapi.dingtalk.com";

        $message = [
            'project' => '漫番',
            'title' => '漫番异常测试',
            'message' => 'syntaerro, unexpected \'$validate\' (T_VARIABLE)'
        ];
        $result = Myfcomic\Client::create()->setHost($web_hook)->build()->send($message);



方法指南：

    1、create()            加载Myfcomic Client，不需传值
    
    2、setHost(string)     钉钉webhook url

    3、build(int)          选择发送信息渠道 并加载发送信息类（目前不需传值，默认为钉钉渠道发送）

    4、setCache(bool)      是否延时发送（默认延时5min发送）
    
    5、send(arry)          发送信息 

        1）keyName1   project   webhook 验签关键字

        2）keyName2   title     消息标题

        3）keyName3   message   消息主体

