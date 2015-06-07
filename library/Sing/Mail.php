<?php

/**
 * Sing_Mail
 *
 * @category   Core
 * @package    Core
 * @copyright  Copyright 2014 MICROWAVE CO.,LTD. (http://www.micro-wave.net/)
 * @author     t.watanabe
 * @since      2014/07/26
 */
class Sing_Mail
{
    private $locales = array('en', 'ja');

    private $_mailContext = array();

    public function __construct($templateDir)
    {
        $this->_mailContext['TemplateDir'] = $templateDir;
    }

    public function setFromAddress($address)
    {
        $this->_mailContext['FromAddress'] = $address;
        return $this;
    }

    public function setFromName($name)
    {
        $this->_mailContext['FromName'] = $name;
        return $this;
    }

    private function isValid()
    {
        if (!isset($this->_mailContext['FromAddress'])) {
            throw new Zend_Mail_Exception('送信元メールアドレスが未設定です。');
        }

        if (!isset($this->_mailContext['FromName'])) {
            throw new Zend_Mail_Exception('送信元名称が未設定です。');
        }

        if (!isset($this->_mailContext['To'])) {
            throw new Zend_Mail_Exception('送信先メールアドレスが未設定です。');
        }

        if (!isset($this->_mailContext['TemplateName'])) {
            throw new Zend_Mail_Exception('メールのテンプレートが未設定です。');
        }

        $this->_mailContext['TemplateFullPath'] = $this->_mailContext['TemplateDir'] . DS. $this->_mailContext['TemplateName'];
        if (!file_exists($this->_mailContext['TemplateFullPath'])) {
            throw new Zend_Mail_Exception('メールのテンプレートが存在しません。 '. $this->_mailContext['TemplateFullPath']);
        }

        return true;
    }

    private function createSubjectAndBody()
    {
        $contentFile = file_get_contents($this->_mailContext['TemplateFullPath']);
        $arrayMail  = explode('[body]', $contentFile);

        $mailObject = new stdClass();

        $mailObject->subject = trim(str_replace('[subject]','',$arrayMail[0]));
        $mailObject->body = trim($arrayMail[1]);

        return array($mailObject->subject, $mailObject->body);
    }

    public function send($to, $templateName, $options = null)
    {
        $this->_mailContext['To']               = $to;
        $this->_mailContext['TemplateName']     = $templateName;
        $this->_mailContext['TemplateFullPath'] = null;

        try {
            if ($this->isValid()) {

                list($subject, $content) = $this->createSubjectAndBody();

                if ($options != null) {
                    foreach ($options as $key => $val) {
                        $subject = str_replace('<%'.$key.'%>', $val, $subject);
                        $content = str_replace('<%'.$key.'%>', $val, $content);
                    }
                }

                return $this->_send($this->_mailContext['FromAddress'],
                                $this->_mailContext['FromName'],
                                $this->_mailContext['To'],
                                $subject,
                                $content);
            }
        } catch (Exception $e) {
            Sing_Log::error($e->getMessage());
            throw new Zend_Mail_Exception('メール送信で例外が発生しています。');
        }

    }

    private function _send($from, $fromName, $to, $subject, $content)
    {
        $config = array();
        $config['port']     = Sing_Configure::read('mail_port');
        $config['auth']     = Sing_Configure::read('mail_auth');
        $config['username'] = Sing_Configure::read('mail_username');
        $config['password'] = Sing_Configure::read('mail_password');

        try {
            $transport = new Zend_Mail_Transport_Smtp(Sing_Configure::read('mail_host'), $config);

            $mail = new Zend_Mail("ISO-2022-JP");
            mb_language("japanese");
            mb_internal_encoding('UTF-8');

            $mail->setBodyText(mb_convert_encoding($content,'ISO-2022-JP','UTF-8'));
            //$mail->setBodyHtml(mb_convert_encoding($content,'ISO-2022-JP','UTF-8'));

            $mail->setFrom($from, mb_encode_mimeheader(
                    mb_convert_encoding($fromName,'ISO-2022-JP','UTF-8')));
            $mail->setSubject(mb_convert_encoding($subject,'ISO-2022-JP','UTF-8'));

            if (strpos($to, ',') !== FALSE) {
                $to_arr = explode(',', $to);
                $is_send = FALSE;
                foreach ($to_arr as $to_address) {
                    if (Sing_StringUtil::checkEmail($to_address)) {
                        $mail->addTo($to_address);
                        $is_send = TRUE;
                    } else {
                        Sing_Log::error(json_encode(array('method'=>__METHOD__,
                            'email'=>$to, 'subject' => $subject)));
                        throw new Zend_Mail_Exception(Mw_Const::EN_M125);
                    }
                }
                if (!$is_send) {
                    throw new Zend_Mail_Exception(Mw_Const::EN_M125);
                }
            } else {
                $mail->addTo($to);
            }
            return $mail->send($transport);

        } catch (Exception $e) {
            Sing_Log::error($e->getMessage());
            throw $e;
        }
    }
}