<?php

namespace Varimax\Aliyun;

require_once(dirname(__DIR__).'/lib/aliyun-php-sdk-core/Config.php');

use ClientException;
use Dm\Request\V20151123 as Dm;
use ServerException;

class Mail{
    /**
     * 配置选项
     * @var array
     */
    protected $config = [
        'accountName'=>'',//管理控制台中配置的发信地址
        'regionId'=>'cn-hangzhou',
        'accessKeyId'=>'',
        'accessSecret'=>'',
        'addressType'=>0,//地址类型 0：为随机账号 1：为发信地址
        'replyToAddress'=>'',//使用管理控制台中配置的回信地址（状态必须是验证通过）。
        'clickTrace'=>0,//0（默认）：为关闭数据跟踪功能。 1：为打开数据跟踪功能
    ];

    /**
     * 发信人昵称，长度小于15个字符。
     * 例如:发信人昵称设置为”小红”，发信地址为 test@example.com，收信人看到的发信地址为"小红"<test@example.com>。
     * @var mixed
     */
    protected $from;

    /**
     * 邮件标签
     * @var mixed
     */
    protected $tags;

    /**
     * 邮件 html 正文，限制28K
     * @var mixed
     */
    protected $body;

    /**
     * 邮件列表名称
     * 预先创建且上传了收件人的收件人列表名称。
     * @var mixed
     */
    protected $list;

    /**
     * 邮件模板名称
     * 控制台预先创建且通过审核的模板名称。
     * @var mixed
     */
    protected $template;

    /**
     * 邮件主题，建议填写
     */
    protected $subject;



    /**
     * 配置邮件
     * @param mixed $key 
     * @param mixed|null $value 
     * @return $this 
     */
    public function config($key = null, $value = null)
    {
        if(!$key) {
            return $this->config;

        }else if(is_array($key)){
            $this->config = array_merge($this->config, $key);
            
        }else if($value){
            $this->config[$key] = $value;
        }else{
            return isset($this->config[$key]) ? $this->config[$key] : null;
        }
        return $this;
    }


    public function to(...$mail)
    {
        return $this->mail($mail);
    }

    /**
     * set email address
     * @param mixed $email 
     * @return $this 
     */
    public function mail(...$mail)
    {
        $this->mail = $mail;
        return $this;
    }


    /**
     * 邮件标签s
     * @param mixed $tags 
     * @return $this 
     */
    public function tags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * mail body
     * 
     * @param mixed $content 
     * @return $this 
     */
    public function body($content)
    {
        $this->body = $content;
        return $this;
    }


    /**
     * 地址发送邮件
     * @return void 
     */
    public function send(...$email)
    {
        foreach ($email as $key => $value)
        {
            if (is_array($value))
            {
                $email += array_merge(...array_values($value));
            }
            else
            {
                $email[$key] = $value;
            }
        }
        return $this->singleSend(join(',', $email));
    }

    /**
     * 发送邮件列表
     * @param mixed $list 邮件列表名称
     * @param mixed $template 模板名称
     * @return void 
     */
    public function list($list, $template)
    {
        return $this->batchSend($list, $template);
    }

    /**
     * 单邮件发送
     * @return mixed 
     * @throws ClientException 
     * @throws ServerException 
     */
    protected function singleSend($toAddress)
    {
        $client = new \DefaultAcsClient(\DefaultProfile::getProfile($this->config('regionId'), $this->config('accessKeyId'), $this->config('accessSecret')));
        $request = new Dm\SingleSendMailRequest();
        $request->setAccountName($this->config('accountName'));
        $request->setReplyToAddress($this->config('replyToAddress'));
        $request->setAddressType($this->config('addressType'));
        $request->setClickTrace($this->config('clickTrace'));

        $request->setFromAlias($this->from);
        $request->setToAddress($toAddress);
        $request->setTagName($this->tags);
        $request->setHtmlBody($this->body);
        $request->setSubject($this->subject);

        return $client->getAcsResponse($request);
    }

    /**
     * 批量邮件发磅
     * @return mixed 
     * @throws ClientException 
     * @throws ServerException 
     */
    protected function batchSend($list, $template)
    {
        $client = new \DefaultAcsClient(\DefaultProfile::getProfile($this->config('regionId'), $this->config('accessKeyId'), $this->config('accessSecret')));
        $request = new Dm\BatchSendMailRequest();
        $request->setTemplateName($template);
        $request->setReceiversName($list);
        $request->setTagName($this->tags);
        $request->setAccountName($this->config('accountName'));
        $request->setAddressType($this->config('addressType'));
        $request->setClickTrace($this->config('clickTrace'));

        return $client->getAcsResponse($request);
    }
}