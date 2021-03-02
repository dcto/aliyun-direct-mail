<?php

namespace Varimax\Aliyun;

require_once('../lib/aliyun-php-sdk-core/Config.php');

use Dm\Request\V20151123 as Dm; 

class Mail{
    protected $regionId;
    protected $accessKeyId;
    protected $accessSecret;

    public function __construct($regionId, $accessKeyId, $accessSecret)
    {
        $this->regionId = $regionId;
        $this->accessKeyId = $accessKeyId;
        $this->accessSecret = $accessSecret;
    }

    /**
     * 发送单条邮件
     * @param string $accountName 管理控制台中配置的发信地址
     * @param string $fromAlias 发信人昵称，长度小于15个字符。
     * 例如:发信人昵称设置为”小红”，发信地址为 test@example.com，收信人看到的发信地址为"小红"<test@example.com>。
     * @param string $toAddress 目标地址，多个 email 地址可以用逗号分隔，最多100个地址。
     * @param string $subject 邮件主题，建议填写
     * @param string $htmlBody 邮件 html 正文，限制28K
     * @param int $addressType 地址类型 0：为随机账号 1：为发信地址
     * @param bool $replyToAddress 使用管理控制台中配置的回信地址（状态必须是验证通过）。
     * @param int $clickTrace 0（默认）：为关闭数据跟踪功能。 1：为打开数据跟踪功能
     * @return mixed|\SimpleXMLElement
     */
    public function singleSend(
        $accountName,
        $fromAlias,
        $toAddress,
        $subject,
        $htmlBody,
        $tagName,
        $addressType = 1,
        $replyToAddress = false,
        $clickTrace = 1)
    {
        $profile = \DefaultProfile::getProfile($this->regionId, $this->accessKeyId, $this->accessSecret);
        $client = new \DefaultAcsClient($profile);
        $request = new Dm\SingleSendMailRequest();
        $request->setAccountName($accountName);
        $request->setFromAlias($fromAlias);
        $request->setAddressType($addressType);
        $request->setClickTrace($clickTrace);
        $request->setTagName($tagName);
        $request->setReplyToAddress($replyToAddress);
        $request->setToAddress($toAddress);
        $request->setSubject($subject);
        $request->setHtmlBody($htmlBody);
        return $client->getAcsResponse($request);
    }

    /**
     * 批量发送邮件
     * @param string $accountName 管理控制台中配置的发信地址。
     * @param string $templateName 预先创建且通过审核的模板名称。
     * @param string $receiversName 预先创建且上传了收件人的收件人列表名称。
     * @param string $tagName 邮件标签名称。
     * @param int $addressType 地址类型 0：为随机账号 1：为发信地址
     * @param int $clickTrace 0（默认）：为关闭数据跟踪功能。 1：为打开数据跟踪功能
     * @return mixed|\SimpleXMLElement
     */
    public function batchSend($accountName, $templateName, $receiversName, $tagName, $addressType = 1, $clickTrace = 1)
    {
        $profile = \DefaultProfile::getProfile($this->regionId, $this->accessKeyId, $this->accessSecret);
        $client = new \DefaultAcsClient($profile);
        $request = new Dm\BatchSendMailRequest();
        // 模板名称
        $request->setTemplateName($templateName);
        // 收件人列表名称
        $request->setReceiversName($receiversName);
        $request->setTagName($tagName);
        $request->setAccountName($accountName);
        $request->setAddressType($addressType);
        $request->setClickTrace($clickTrace);
        return $client->getAcsResponse($request);
    }
}