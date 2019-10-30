<?php
namespace Myself;
/**
 * SendMail 发送邮件类
 * @package Util
 */
class SendMail
{
    /**
	 * 邮件优先级 (1 = 高, 3 = 普通, 5 = 低).
     * @var int $priority
     */
    public $priority          = 3;

    /**
     * 邮件字符编码设置
     * @var string $charSet
     */
    public $charSet           = 'utf-8';

    /**
     * 设置邮件的内容类型
     * @var string $contentType
     */
     public $contentType        = "text/html";

    /**
     * 设置邮件信息的编码 选项是有以下这些 "8bit",
     * "7bit", "binary", "base64", 和 "quoted-printable".
     * @var string  $encoding
     */
    public $encoding          = "8bit";

    /**
     * 保存新的发邮件的错误信息
     * @var string $errorInfo
     */
    public $errorInfo         = '';

    /**
     * 设置发邮件者的Email.
     * @var string $from
     */
    public $from               = "root@localhost";

    /**
     * 设置发邮件者的名字.
     * @var string $fromName
     */
    public $fromName           = "Root User";

    /**
     * 设置邮件发送器  If not empty,
     * will be sent via -f to sendmail or as 'MAIL FROM' in smtp mode.
     * @var string $sender
     */
    public $sender            = '';

    /**
     * 邮件主题
     * @var string $subject
     */
    public $subject           = '';

    /**
     * 邮件内容.
     * @var string $body
     */
    public $body               = '';

    /**
     * 设置文本类型的邮件内容信息
	 * @var string $altBody
     */
    public $altBody           = '';

    /**
     * 设置信息体换行的字符数
     * @var int $wordWrap
     */
    public $wordWrap          = 0;

    /**
     * 发送邮件的方法("mail", "sendmail", "qmail", or "smtp").
     * @var string　$mailer
     */
    public $mailer            = "mail";

    /**
     * 设置sendmail命令的路径.
     * @var string $sendmail
     */
    public $sendmail          = "/usr/sbin/sendmail";
    
    /**
     * 插件的路径
     * @var string　$pluginDir
     */
    public $pluginDir         = '';

    /**
     *  版本号
     *  @var string　$version
     */
    public $version           = "1.0";

    /**
     * Sets the email address that a reading confirmation will be sent.
     * @var string $confirmReadingTo
     */
    public $confirmReadingTo  = '';

    /**
     *  发送邮件的主机名
     *  @var string　$hostname
     */
    public $hostname          = '';

    /**
     *  smtp　的主机地址
     *  @var string $host
     */
    public $host        = "localhost";

    /**
     *  smtp　服务器默认端口
     *  @var int $port
     */
    public $port        = 25;

    /**
     *  SMTP HELO(默认为$hostname).
     *  @var string $helo
     */
    public $helo        = '';

    /**
     *  设置 SMTP 认证，使用用户名和密码.
     *  @var bool $SMTPAuth
     */
    public $SMTPAuth     = false;

    /**
     *  SMTP用户名.
     *  @var string $username
     */
    public $username     = '';

    /**
     *  SMTP 密码.
     *  @var string $password
     */
    public $password     = '';

    /**
     *  设置SMTP服务器超时间，单位秒
     *  @var int $timeout
     */
    public $timeout      = 10;

    /**
     *  是否打开SMTP调试
     *  @var bool　$SMTPDebug
     */
    public $SMTPDebug    = false;

    /**
     * 保持SMTP的连接为活动状态
     * @var bool $SMTPKeepAlive
     */
    public $SMTPKeepAlive = false;

    /**#@+
     * @access private
     */
    private $smtp            = NULL;
    private $to              = array();
    private $cc              = array();
    private $bcc             = array();
    private $replyTo         = array();
    private $attachment      = array();
    private $customHeader    = array();
    private $messageType    = '';
    private $boundary        = array();
    private $language        = array();
    private $errorCount     = 0;
    private $LE              = "\n";
    /**#@-*/
    
    /**
     * 设置信息类型为 HTML.  
     * @param bool $bool
     * @return void
     */
    function isHTML($bool) {
        if($bool == true)
            $this->contentType = "text/html";
        else
            $this->contentType = "text/plain";
    }

    /**
     * 设置使用SMTP发送邮件.
     * @return void
     */
    function isSMTP() {
        $this->mailer = "smtp";
    }

    /**
     * 设置使用mail() 函数发送邮件.
     * @return void
     */
    function isMail() {
        $this->mailer = "mail";
    }

    /**
     * 设置使用sendmail 命令发送邮件.
     * @return void
     */
    function isSendmail() {
        $this->mailer = "sendmail";
    }

    /**
     * 设置使用qmail命令发送邮件.
     * @return void
     */
    function isQmail() {
        $this->sendmail = "/var/qmail/bin/sendmail";
        $this->mailer = "sendmail";
    }

    /**
     * 添加邮件接收者
     * @param string $address
     * @param string $name
     * @return void
     */
    function addAddress($address, $name = '') {
        $cur = count($this->to);
        $this->to[$cur][0] = trim($address);
        $this->to[$cur][1] = $name;
    }

    /**
     * 添加抄送地址
     * @param string $address
     * @param string $name
     * @return void
    */
    function addCC($address, $name = '') {
        $cur = count($this->cc);
        $this->cc[$cur][0] = trim($address);
        $this->cc[$cur][1] = $name;
    }

    /**
     * 添加密送地址
     * @param string $address
     * @param string $name
     * @return void
     */
    function addBCC($address, $name = '') {
        $cur = count($this->bcc);
        $this->bcc[$cur][0] = trim($address);
        $this->bcc[$cur][1] = $name;
    }

    /**
     * 添加回复地址
     * @param string $address
     * @param string $name
     * @return void
     */
    function addReplyTo($address, $name = '') {
        $cur = count($this->replyTo);
        $this->replyTo[$cur][0] = trim($address);
        $this->replyTo[$cur][1] = $name;
    }


    /**
     * 发送邮件
     * @return bool
     */
    function send() {
        $header = '';
        $body = '';
        $result = true;

        if((count($this->to) + count($this->cc) + count($this->bcc)) < 1)
        {
            $this->setError($this->lang("provide_address"));
            return false;
        }

        // Set whether the message is multipart/alternative
        if(!empty($this->altBody))
            $this->contentType = "multipart/alternative";

        $this->errorCount = 0; // reset errors
        $this->setMessageType();
        $header .= $this->createHeader();
        $body = $this->createBody();

        if($body == '') { return false; }
        // Choose the mailer
        switch($this->mailer)
        {
            case "sendmail":
                $result = $this->sendmailSend($header, $body);
                break;
            case "mail":
                $result = $this->mailSend($header, $body);
                break;
            case "smtp":
                $result = $this->smtpSend($header, $body);
                break;
            default:
            $this->setError($this->mailer . $this->lang("mailer_not_supported"));
                $result = false;
                break;
        }

        return $result;
    }
    
    /**
     * 使用Sendmail 命令发送邮件.  
     * @access private
     * @return bool
     */
    function sendmailSend($header, $body) {
        if ($this->sender != '')
            $sendmail = sprintf("%s -oi -f %s -t", $this->sendmail, $this->sender);
        else
            $sendmail = sprintf("%s -oi -t", $this->sendmail);

        if(!@$mail = popen($sendmail, "w"))
        {
            $this->setError($this->lang("execute") . $this->sendmail);
            return false;
        }

        fputs($mail, $header);
        fputs($mail, $body);
        
        $result = pclose($mail) >> 8 & 0xFF;
        if($result != 0)
        {
            $this->setError($this->lang("execute") . $this->sendmail);
            return false;
        }

        return true;
    }

    /**
     * 使用　mail()函数发送邮件.  
     * @access private
     * @return bool
     */
    function mailSend($header, $body) {
        $to = '';
        for($i = 0; $i < count($this->to); $i++)
        {
            if($i != 0) { $to .= ", "; }
            $to .= $this->to[$i][0];
        }

        if ($this->sender != '' && strlen(ini_get("safe_mode"))< 1)
        {
            $old_from = ini_get("sendmail_from");
            ini_set("sendmail_from", $this->sender);
            $params = sprintf("-oi -f %s", $this->sender);
            $rt = @mail($to, $this->encodeHeader($this->subject), $body, 
                        $header, $params);
        }
        else
            $rt = @mail($to, $this->encodeHeader($this->subject), $body, $header);

        if (isset($old_from))
            ini_set("sendmail_from", $old_from);

        if(!$rt)
        {
            $this->setError($this->lang("instantiate"));
            return false;
        }

        return true;
    }

    /**
     * 使用smtp发送邮件
     * @access private
     * @return bool
     */
    function smtpSend($header, $body) {
        include_once($this->pluginDir . "SMTP.class.php");
        $error = '';
        $bad_rcpt = array();

        if(!$this->smtpConnect())
            return false;

        $smtp_from = ($this->sender == '') ? $this->from : $this->sender;
        if(!$this->smtp->Mail($smtp_from))
        {
            $error = $this->lang("from_failed") . $smtp_from;
            $this->setError($error);
            $this->smtp->Reset();
            return false;
        }

        // Attempt to send attach all recipients
        for($i = 0; $i < count($this->to); $i++)
        {
            if(!$this->smtp->Recipient($this->to[$i][0]))
                $bad_rcpt[] = $this->to[$i][0];
        }
        for($i = 0; $i < count($this->cc); $i++)
        {
            if(!$this->smtp->Recipient($this->cc[$i][0]))
                $bad_rcpt[] = $this->cc[$i][0];
        }
        for($i = 0; $i < count($this->bcc); $i++)
        {
            if(!$this->smtp->Recipient($this->bcc[$i][0]))
                $bad_rcpt[] = $this->bcc[$i][0];
        }

        if(count($bad_rcpt) > 0) // Create error message
        {
            for($i = 0; $i < count($bad_rcpt); $i++)
            {
                if($i != 0) { $error .= ", "; }
                $error .= $bad_rcpt[$i];
            }
            $error = $this->lang("recipients_failed") . $error;
            $this->setError($error);
            $this->smtp->Reset();
            return false;
        }

        if(!$this->smtp->Data($header . $body))
        {
            $this->setError($this->lang("data_not_accepted"));
            $this->smtp->Reset();
            return false;
        }
        if($this->SMTPKeepAlive == true)
            $this->smtp->Reset();
        else
            $this->smtpClose();

        return true;
    }

    /**
     * 初始化一个smtp连接
     * @access private
     * @return bool
     */
    function smtpConnect() {
        if($this->smtp == NULL) { $this->smtp = new SMTP(); }

        $this->smtp->do_debug = $this->SMTPDebug;
        $hosts = explode(";", $this->host);
        $index = 0;
        $connection = ($this->smtp->Connected()); 

        // Retry while there is no connection
        while($index < count($hosts) && $connection == false)
        {
            if(strstr($hosts[$index], ":"))
                list($host, $port) = explode(":", $hosts[$index]);
            else
            {
                $host = $hosts[$index];
                $port = $this->port;
            }

            if($this->smtp->Connect($host, $port, $this->timeout))
            {
                if ($this->helo != '')
                    $this->smtp->Hello($this->helo);
                else
                    $this->smtp->Hello($this->serverHostname());
        
                if($this->SMTPAuth)
                {
                    if(!$this->smtp->Authenticate($this->username, 
                                                  $this->password))
                    {
                        $this->setError($this->lang("authenticate"));
                        $this->smtp->Reset();
                        $connection = false;
                    }
                }
                $connection = true;
            }
            $index++;
        }
        if(!$connection)
            $this->setError($this->lang("connect_host"));

        return $connection;
    }

    /**
     * 结束SMTP会话.
     * @return void
     */
    function smtpClose() {
        if($this->smtp != NULL)
        {
            if($this->smtp->Connected())
            {
                $this->smtp->Quit();
                $this->smtp->Close();
            }
        }
    }

    /**
     * 设置所有错误信息的语言
     * @param string $lang_type Type of language (e.g. Portuguese: "br")
     * @param string $lang_path Path to the language file directory
     * @access public
     * @return bool
     */
    function setLanguage($lang_type, $lang_path = "language/") {
        /*if(file_exists($lang_path.'phpmailer.lang-'.$lang_type.'.php'))
            include($lang_path.'phpmailer.lang-'.$lang_type.'.php');
        else if(file_exists($lang_path.'phpmailer.lang-en.php'))
            include($lang_path.'phpmailer.lang-en.php');
        else
        {
            $this->setError("Could not load language file");
            return false;
        }*/
		/*$PHPMAILER_LANG = array();

		$PHPMAILER_LANG["provide_address"] = 'You must provide at least one ' .
											 'recipient email address.';
		$PHPMAILER_LANG["mailer_not_supported"] = ' mailer is not supported.';
		$PHPMAILER_LANG["execute"] = 'Could not execute: ';
		$PHPMAILER_LANG["instantiate"] = 'Could not instantiate mail function.';
		$PHPMAILER_LANG["authenticate"] = 'SMTP Error: Could not authenticate.';
		$PHPMAILER_LANG["from_failed"] = 'The following From address failed: ';
		$PHPMAILER_LANG["recipients_failed"] = 'SMTP Error: The following ' .
											   'recipients failed: ';
		$PHPMAILER_LANG["data_not_accepted"] = 'SMTP Error: Data not accepted.';
		$PHPMAILER_LANG["connect_host"] = 'SMTP Error: Could not connect to SMTP host.';
		$PHPMAILER_LANG["file_access"] = 'Could not access file: ';
		$PHPMAILER_LANG["file_open"] = 'File Error: Could not open file: ';
		$PHPMAILER_LANG["encoding"] = 'Unknown encoding: ';*/
		$PHPMAILER_LANG = array();

		$PHPMAILER_LANG["provide_address"] = '您必须提供至少一个电子邮件地址。';
		$PHPMAILER_LANG["mailer_not_supported"] = ' 不支持邮件客户端。';
		$PHPMAILER_LANG["execute"] = '不能运行: ';
		$PHPMAILER_LANG["instantiate"] = '不能实例化mail函数。';
		$PHPMAILER_LANG["authenticate"] = 'SMTP 错误: 不能通过认证。';
		$PHPMAILER_LANG["from_failed"] = '下列地址错误: ';
		$PHPMAILER_LANG["recipients_failed"] = 'SMTP 错误: 下列收件人错误: ';
		$PHPMAILER_LANG["data_not_accepted"] = 'SMTP 错误: 数据不被接收。';
		$PHPMAILER_LANG["connect_host"] = 'SMTP 错误: 不能连接到SMTP主机。';
		$PHPMAILER_LANG["file_access"] = '不能访问文件: ';
		$PHPMAILER_LANG["file_open"] = '文件错误: 不能打开文件: ';
		$PHPMAILER_LANG["encoding"] = '未知编码: ';
        $this->language = $PHPMAILER_LANG;
    
        return true;
    }

    /**
     * 创建一个信息头容器
     * @access private
     * @return string
     */
    function addrAppend($type, $addr) {
        $addr_str = $type . ": ";
        $addr_str .= $this->addrFormat($addr[0]);
        if(count($addr) > 1)
        {
            for($i = 1; $i < count($addr); $i++)
                $addr_str .= ", " . $this->addrFormat($addr[$i]);
        }
        $addr_str .= $this->LE;

        return $addr_str;
    }
    
    /**
     * 格式化一个正确的地址. 
     * @access private
     * @return string
     */
    function addrFormat($addr) {
        if(empty($addr[1]))
            $formatted = $addr[0];
        else
        {
            $formatted = $this->encodeHeader($addr[1], 'phrase') . " <" . 
                         $addr[0] . ">";
        }

        return $formatted;
    }

    /**
     * 将文本换行
     * @access private
     * @return string
     */
    function wrapText($message, $length, $qp_mode = false) {
        $soft_break = ($qp_mode) ? sprintf(" =%s", $this->LE) : $this->LE;

        $message = $this->fixEOL($message);
        if (substr($message, -1) == $this->LE)
            $message = substr($message, 0, -1);

        $line = explode($this->LE, $message);
        $message = '';
        for ($i=0 ;$i < count($line); $i++)
        {
          $line_part = explode(" ", $line[$i]);
          $buf = '';
          for ($e = 0; $e<count($line_part); $e++)
          {
              $word = $line_part[$e];
              if ($qp_mode and (strlen($word) > $length))
              {
                $space_left = $length - strlen($buf) - 1;
                if ($e != 0)
                {
                    if ($space_left > 20)
                    {
                        $len = $space_left;
                        if (substr($word, $len - 1, 1) == "=")
                          $len--;
                        elseif (substr($word, $len - 2, 1) == "=")
                          $len -= 2;
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);
                        $buf .= " " . $part;
                        $message .= $buf . sprintf("=%s", $this->LE);
                    }
                    else
                    {
                        $message .= $buf . $soft_break;
                    }
                    $buf = '';
                }
                while (strlen($word) > 0)
                {
                    $len = $length;
                    if (substr($word, $len - 1, 1) == "=")
                        $len--;
                    elseif (substr($word, $len - 2, 1) == "=")
                        $len -= 2;
                    $part = substr($word, 0, $len);
                    $word = substr($word, $len);

                    if (strlen($word) > 0)
                        $message .= $part . sprintf("=%s", $this->LE);
                    else
                        $buf = $part;
                }
              }
              else
              {
                $buf_o = $buf;
                $buf .= ($e == 0) ? $word : (" " . $word); 

                if (strlen($buf) > $length and $buf_o != '')
                {
                    $message .= $buf_o . $soft_break;
                    $buf = $word;
                }
              }
          }
          $message .= $buf . $this->LE;
        }

        return $message;
    }
    
    /**
     * 设置主体字符换行
     * @access private
     * @return void
     */
    function setWordWrap() {
        if($this->wordWrap < 1)
            return;
            
        switch($this->messageType)
        {
           case "alt":
              // fall through
           case "alt_attachment":
              $this->altBody = $this->wrapText($this->altBody, $this->wordWrap);
              break;
           default:
              $this->body = $this->wrapText($this->body, $this->wordWrap);
              break;
        }
    }

    /**
     * 创建信息头
     * @access private
     * @return string
     */
    function createHeader() {
        $result = '';
        
        // Set the boundaries
        $uniq_id = md5(uniqid(time()));
        $this->boundary[1] = "b1_" . $uniq_id;
        $this->boundary[2] = "b2_" . $uniq_id;

        $result .= $this->headerLine("Date", $this->RFCDate());
        if($this->sender == '')
            $result .= $this->headerLine("Return-Path", trim($this->from));
        else
            $result .= $this->headerLine("Return-Path", trim($this->sender));
        
        // To be created automatically by mail()
        if($this->mailer != "mail")
        {
            if(count($this->to) > 0)
                $result .= $this->addrAppend("To", $this->to);
            else if (count($this->cc) == 0)
                $result .= $this->headerLine("To", "undisclosed-recipients:;");
            if(count($this->cc) > 0)
                $result .= $this->addrAppend("Cc", $this->cc);
        }

        $from = array();
        $from[0][0] = trim($this->from);
        $from[0][1] = $this->fromName;
        $result .= $this->addrAppend("From", $from); 

        // sendmail and mail() extract Bcc from the header before sending
        if((($this->mailer == "sendmail") || ($this->mailer == "mail")) && (count($this->bcc) > 0))
            $result .= $this->addrAppend("Bcc", $this->bcc);

        if(count($this->replyTo) > 0)
            $result .= $this->addrAppend("Reply-to", $this->replyTo);

        // mail() sets the subject itself
        if($this->mailer != "mail")
            $result .= $this->headerLine("Subject", $this->encodeHeader(trim($this->subject)));

        $result .= sprintf("Message-ID: <%s@%s>%s", $uniq_id, $this->serverHostname(), $this->LE);
        $result .= $this->headerLine("X-Priority", $this->priority);
        $result .= $this->headerLine("X-Mailer", "MyOIS mailer [version " . $this->version . "]");
        
        if($this->confirmReadingTo != '')
        {
            $result .= $this->headerLine("Disposition-Notification-To", 
                       "<" . trim($this->confirmReadingTo) . ">");
        }

        // Add custom headers
        for($index = 0; $index < count($this->customHeader); $index++)
        {
            $result .= $this->headerLine(trim($this->customHeader[$index][0]), 
                       $this->encodeHeader(trim($this->customHeader[$index][1])));
        }
        $result .= $this->headerLine("MIME-Version", "1.0");

        switch($this->messageType)
        {
            case "plain":
                $result .= $this->headerLine("Content-Transfer-Encoding", $this->encoding);
                $result .= sprintf("Content-Type: %s; charset=\"%s\"",
                                    $this->contentType, $this->charSet);
                break;
            case "attachments":
                // fall through
            case "alt_attachments":
                if($this->inlineImageExists())
                {
                    $result .= sprintf("Content-Type: %s;%s\ttype=\"text/html\";%s\tboundary=\"%s\"%s", 
                                    "multipart/related", $this->LE, $this->LE, 
                                    $this->boundary[1], $this->LE);
                }
                else
                {
                    $result .= $this->headerLine("Content-Type", "multipart/mixed;");
                    $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                }
                break;
            case "alt":
                $result .= $this->headerLine("Content-Type", "multipart/alternative;");
                $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
        }

        if($this->mailer != "mail")
            $result .= $this->LE.$this->LE;

        return $result;
    }

    /**
     * 创建内容体
     * @access private
     * @return string
     */
    function createBody() {
        $result = "";

        $this->setWordWrap();

        switch($this->messageType)
        {
            case "alt":
                $result .= $this->getBoundary($this->boundary[1], "", 
                                              "text/plain", "");
                $result .= $this->encodeString($this->altBody, $this->encoding);
                $result .= $this->LE.$this->LE;
                $result .= $this->getBoundary($this->boundary[1], "", 
                                              "text/html", "");
                
                $result .= $this->encodeString($this->body, $this->encoding);
                $result .= $this->LE.$this->LE;
    
                $result .= $this->endBoundary($this->boundary[1]);
                break;
            case "plain":
                $result .= $this->encodeString($this->body, $this->encoding);
                break;
            case "attachments":
                $result .= $this->getBoundary($this->boundary[1], "", "", "");
                $result .= $this->encodeString($this->body, $this->encoding);
                $result .= $this->LE;
     
                $result .= $this->attachAll();
                break;
            case "alt_attachments":
                $result .= sprintf("--%s%s", $this->boundary[1], $this->LE);
                $result .= sprintf("Content-Type: %s;%s" .
                                   "\tboundary=\"%s\"%s",
                                   "multipart/alternative", $this->LE, 
                                   $this->boundary[2], $this->LE.$this->LE);
    
                // Create text body
                $result .= $this->getBoundary($this->boundary[2], "", 
                                              "text/plain", "") . $this->LE;

                $result .= $this->encodeString($this->altBody, $this->encoding);
                $result .= $this->LE.$this->LE;
    
                // Create the HTML body
                $result .= $this->getBoundary($this->boundary[2], "", 
                                              "text/html", "") . $this->LE;
    
                $result .= $this->encodeString($this->body, $this->encoding);
                $result .= $this->LE.$this->LE;

                $result .= $this->endBoundary($this->boundary[2]);
                
                $result .= $this->attachAll();
                break;
        }
        if($this->isError())
            $result = "";

        return $result;
    }

    /**
     * 返回信息边界的开始
     * @access private
     */
    function getBoundary($boundary, $charSet, $contentType, $encoding) {
        $result = "";
        if($charSet == "") { $charSet = $this->charSet; }
        if($contentType == "") { $contentType = $this->contentType; }
        if($encoding == "") { $encoding = $this->encoding; }

        $result .= $this->textLine("--" . $boundary);
        $result .= sprintf("Content-Type: %s; charset = \"%s\"", 
                            $contentType, $charSet);
        $result .= $this->LE;
        $result .= $this->headerLine("Content-Transfer-Encoding", $encoding);
        $result .= $this->LE;
       
        return $result;
    }
    
    /**
     * 返回信息边界的结束
     * @access private
     */
    function endBoundary($boundary) {
        return $this->LE . "--" . $boundary . "--" . $this->LE; 
    }
    
    /**
     * 设置信息类型
     * @access private
     * @return void
     */
    function setMessageType() {
        if(count($this->attachment) < 1 && strlen($this->altBody) < 1)
            $this->messageType = "plain";
        else
        {
            if(count($this->attachment) > 0)
                $this->messageType = "attachments";
            if(strlen($this->altBody) > 0 && count($this->attachment) < 1)
                $this->messageType = "alt";
            if(strlen($this->altBody) > 0 && count($this->attachment) > 0)
                $this->messageType = "alt_attachments";
        }
    }

    /**
     * 返回一个格式头行.
     * @access private
     * @return string
     */
    function headerLine($name, $value) {
        return $name . ": " . $value . $this->LE;
    }

    /**
     * Returns a formatted mail line.
     * @access private
     * @return string
     */
    function textLine($value) {
        return $value . $this->LE;
    }

    /**
     * 添加附件
     * @param string $path Path to the attachment.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @return bool
     */
    function addAttachment($path, $name = "", $encoding = "base64", 
                           $type = "application/octet-stream") {
        if(!@is_file($path))
        {
            $this->setError($this->lang("file_access") . $path);
            return false;
        }

        $filename = basename($path);
        if($name == "")
            $name = $filename;

        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false; // isStringAttachment
        $this->attachment[$cur][6] = "attachment";
        $this->attachment[$cur][7] = 0;

        return true;
    }

    /**
     * Attaches all fs, string, and binary attachments to the message.
     * Returns an empty string on failure.
     * @access private
     * @return string
     */
    function attachAll() {
        // Return text of body
        $mime = array();

        // Add all attachments
        for($i = 0; $i < count($this->attachment); $i++)
        {
            // Check for string attachment
            $bString = $this->attachment[$i][5];
            if ($bString)
                $string = $this->attachment[$i][0];
            else
                $path = $this->attachment[$i][0];

            $filename    = $this->attachment[$i][1];
            $name        = $this->attachment[$i][2];
            $encoding    = $this->attachment[$i][3];
            $type        = $this->attachment[$i][4];
            $disposition = $this->attachment[$i][6];
            $cid         = $this->attachment[$i][7];
            
            $mime[] = sprintf("--%s%s", $this->boundary[1], $this->LE);
            $mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $name, $this->LE);
            $mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);

            if($disposition == "inline")
                $mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);

            $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", 
                              $disposition, $name, $this->LE.$this->LE);

            // Encode as string attachment
            if($bString)
            {
                $mime[] = $this->encodeString($string, $encoding);
                if($this->isError()) { return ""; }
                $mime[] = $this->LE.$this->LE;
            }
            else
            {
                $mime[] = $this->encodeFile($path, $encoding);                
                if($this->isError()) { return ""; }
                $mime[] = $this->LE.$this->LE;
            }
        }

        $mime[] = sprintf("--%s--%s", $this->boundary[1], $this->LE);

        return join("", $mime);
    }
    
    /**
     * Encodes attachment in requested format.  Returns an
     * empty string on failure.
     * @access private
     * @return string
     */
    function encodeFile ($path, $encoding = "base64") {
        if(!@$fd = fopen($path, "rb"))
        {
            $this->setError($this->lang("file_open") . $path);
            return "";
        }
        $file_buffer = fread($fd, filesize($path));
        $file_buffer = $this->encodeString($file_buffer, $encoding);
        fclose($fd);

        return $file_buffer;
    }

    /**
     * Encodes string to requested format. Returns an
     * empty string on failure.
     * @access private
     * @return string
     */
    function encodeString ($str, $encoding = "base64") {
        $encoded = "";
        switch(strtolower($encoding)) {
          case "base64":
              // chunk_split is found in PHP >= 3.0.6
              $encoded = chunk_split(base64_encode($str), 76, $this->LE);
              break;
          case "7bit":
          case "8bit":
              $encoded = $this->fixEOL($str);
              if (substr($encoded, -(strlen($this->LE))) != $this->LE)
                $encoded .= $this->LE;
              break;
          case "binary":
              $encoded = $str;
              break;
          case "quoted-printable":
              $encoded = $this->encodeQP($str);
              break;
          default:
              $this->setError($this->lang("encoding") . $encoding);
              break;
        }
        return $encoded;
    }

    /**
     * Encode a header string to best of Q, B, quoted or none.  
     * @access private
     * @return string
     */
    function encodeHeader ($str, $position = 'text') {
      $x = 0;
      
      switch (strtolower($position)) {
        case 'phrase':
          if (!preg_match('/[\200-\377]/', $str)) {
            // Can't use addslashes as we don't know what value has magic_quotes_sybase.
            $encoded = addcslashes($str, "\0..\37\177\\\"");

            if (($str == $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str))
              return ($encoded);
            else
              return ("\"$encoded\"");
          }
          $x = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
          break;
        case 'comment':
          $x = preg_match_all('/[()"]/', $str, $matches);
          // Fall-through
        case 'text':
        default:
          $x += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
          break;
      }

      if ($x == 0)
        return ($str);

      $maxlen = 75 - 7 - strlen($this->charSet);
      // Try to select the encoding which should produce the shortest output
      if (strlen($str)/3 < $x) {
        $encoding = 'B';
        $encoded = base64_encode($str);
        $maxlen -= $maxlen % 4;
        $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
      } else {
        $encoding = 'Q';
        $encoded = $this->encodeQ($str, $position);
        $encoded = $this->wrapText($encoded, $maxlen, true);
        $encoded = str_replace("=".$this->LE, "\n", trim($encoded));
      }

      $encoded = preg_replace('/^(.*)$/m', " =?".$this->charSet."?$encoding?\\1?=", $encoded);
      $encoded = trim(str_replace("\n", $this->LE, $encoded));
      
      return $encoded;
    }
    
    /**
     * Encode string to quoted-printable.  
     * @access private
     * @return string
     */
    function encodeQP ($str) {
        $encoded = $this->fixEOL($str);
        if (substr($encoded, -(strlen($this->LE))) != $this->LE)
            $encoded .= $this->LE;

        // Replace every high ascii, control and = characters
        $encoded = preg_replace('/([\000-\010\013\014\016-\037\075\177-\377])/e',
                  "'='.sprintf('%02X', ord('\\1'))", $encoded);
        // Replace every spaces and tabs when it's the last character on a line
        $encoded = preg_replace("/([\011\040])".$this->LE."/e",
                  "'='.sprintf('%02X', ord('\\1')).'".$this->LE."'", $encoded);

        // Maximum line length of 76 characters before CRLF (74 + space + '=')
        $encoded = $this->wrapText($encoded, 74, true);

        return $encoded;
    }

    /**
     * Encode string to q encoding.  
     * @access private
     * @return string
     */
    function encodeQ ($str, $position = "text") {
        // There should not be any EOL in the string
        $encoded = preg_replace("[\r\n]", "", $str);

        switch (strtolower($position)) {
          case "phrase":
            $encoded = preg_replace("/([^A-Za-z0-9!*+\/ -])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
            break;
          case "comment":
            $encoded = preg_replace("/([\(\)\"])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
          case "text":
          default:
            // Replace every high ascii, control =, ? and _ characters
            $encoded = preg_replace('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e',
                  "'='.sprintf('%02X', ord('\\1'))", $encoded);
            break;
        }
        
        // Replace every spaces to _ (more readable than =20)
        $encoded = str_replace(" ", "_", $encoded);

        return $encoded;
    }

    /**
     * Adds a string or binary attachment (non-filesystem) to the list.
     * This method can be used to attach ascii or binary data,
     * such as a BLOB record from a database.
     * @param string $string String attachment data.
     * @param string $filename Name of the attachment.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @return void
     */
    function addStringAttachment($string, $filename, $encoding = "base64", 
                                 $type = "application/octet-stream") {
        // Append to $attachment array
        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $string;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $filename;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = true; // isString
        $this->attachment[$cur][6] = "attachment";
        $this->attachment[$cur][7] = 0;
    }
    
    /**
     * Adds an embedded attachment.  This can include images, sounds, and 
     * just about any other document.  Make sure to set the $type to an 
     * image type.  For JPEG images use "image/jpeg" and for GIF images 
     * use "image/gif".
     * @param string $path Path to the attachment.
     * @param string $cid Content ID of the attachment.  Use this to identify 
     *        the Id for accessing the image in an HTML form.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.  
     * @return bool
     */
    function addEmbeddedImage($path, $cid, $name = "", $encoding = "base64", 
                              $type = "application/octet-stream") {
    
        if(!@is_file($path))
        {
            $this->setError($this->lang("file_access") . $path);
            return false;
        }

        $filename = basename($path);
        if($name == "")
            $name = $filename;

        // Append to $attachment array
        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false; // isStringAttachment
        $this->attachment[$cur][6] = "inline";
        $this->attachment[$cur][7] = $cid;
    
        return true;
    }
    
    /**
     * Returns true if an inline attachment is present.
     * @access private
     * @return bool
     */
    function inlineImageExists() {
        $result = false;
        for($i = 0; $i < count($this->attachment); $i++)
        {
            if($this->attachment[$i][6] == "inline")
            {
                $result = true;
                break;
            }
        }
        
        return $result;
    }

    /////////////////////////////////////////////////
    // MESSAGE RESET METHODS
    /////////////////////////////////////////////////

    /**
     * Clears all recipients assigned in the TO array.  Returns void.
     * @return void
     */
    function clearAddresses() {
        $this->to = array();
    }

    /**
     * Clears all recipients assigned in the CC array.  Returns void.
     * @return void
     */
    function clearCCs() {
        $this->cc = array();
    }

    /**
     * Clears all recipients assigned in the BCC array.  Returns void.
     * @return void
     */
    function clearBCCs() {
        $this->bcc = array();
    }

    /**
     * Clears all recipients assigned in the replyTo array.  Returns void.
     * @return void
     */
    function clearReplyTos() {
        $this->replyTo = array();
    }

    /**
     * Clears all recipients assigned in the TO, CC and BCC
     * array.  Returns void.
     * @return void
     */
    function clearAllRecipients() {
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
    }

    /**
     * Clears all previously set filesystem, string, and binary
     * attachments.  Returns void.
     * @return void
     */
    function clearAttachments() {
        $this->attachment = array();
    }

    /**
     * Clears all custom headers.  Returns void.
     * @return void
     */
    function clearCustomHeaders() {
        $this->customHeader = array();
    }


    /////////////////////////////////////////////////
    // MISCELLANEOUS METHODS
    /////////////////////////////////////////////////

    /**
     * Adds the error message to the error container.
     * Returns void.
     * @access private
     * @return void
     */
    function setError($msg) {
        $this->errorCount++;
        $this->errorInfo = $msg;
    }

    /**
     * Returns the proper RFC 822 formatted date. 
     * @access private
     * @return string
     */
    function RFCDate() {
        $tz = date("Z");
        $tzs = ($tz < 0) ? "-" : "+";
        $tz = abs($tz);
        $tz = ($tz/3600)*100 + ($tz%3600)/60;
        $result = sprintf("%s %s%04d", date("D, j M Y H:i:s"), $tzs, $tz);

        return $result;
    }
    
    /**
     * Returns the appropriate server variable.  Should work with both 
     * PHP 4.1.0+ as well as older versions.  Returns an empty string 
     * if nothing is found.
     * @access private
     * @return mixed
     */
    function serverVar($varName) {
        global $HTTP_SERVER_VARS;
        global $HTTP_ENV_VARS;

        if(!isset($_SERVER))
        {
            $_SERVER = $HTTP_SERVER_VARS;
            if(!isset($_SERVER["REMOTE_ADDR"]))
                $_SERVER = $HTTP_ENV_VARS; // must be Apache
        }
        
        if(isset($_SERVER[$varName]))
            return $_SERVER[$varName];
        else
            return "";
    }

    /**
     * Returns the server hostname or 'localhost.localdomain' if unknown.
     * @access private
     * @return string
     */
    function serverHostname() {
        if ($this->hostname != "")
            $result = $this->hostname;
        elseif ($this->serverVar('SERVER_NAME') != "")
            $result = $this->serverVar('SERVER_NAME');
        else
            $result = "localhost.localdomain";

        return $result;
    }

    /**
     * Returns a message in the appropriate language.
     * @access private
     * @return string
     */
    function lang($key) {
        if(count($this->language) < 1)
            $this->setLanguage("en"); // set the default language
    
        if(isset($this->language[$key]))
            return $this->language[$key];
        else
            return "Language string failed to load: " . $key;
    }
    
    /**
     * Returns true if an error occurred.
     * @return bool
     */
    function isError() {
        return ($this->errorCount > 0);
    }

    /**
     * Changes every end of line from CR or LF to CRLF.  
     * @access private
     * @return string
     */
    function fixEOL($str) {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\r", "\n", $str);
        $str = str_replace("\n", $this->LE, $str);
        return $str;
    }

    /**
     * 添加自定义头. 
     * @return void
     */
    function addCustomHeader($custom_header) {
        $this->customHeader[] = explode(":", $custom_header, 2);
    }
}

?>
