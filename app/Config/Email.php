<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public $fromEmail  = '';
    public $fromName   = '';
    public $recipients = '';

    /**
     * The "user agent"
     */
    public $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     */
    public $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Address
     */
    public $SMTPHost = 'smtp.zoho.com';

    /**
     * SMTP Username
     */
    // public $SMTPUser = 'By.App@beingyouconsulting.com';
    public $SMTPUser = 'by.app@beingyouconsulting.com';

    /**
     * SMTP Password
     */
    // public string $SMTPPass = 'cjwtzotsdkrmjafq';
    public $SMTPPass = 'GMCKS@1#';

     /**
     * SMTP Encryption. Either tls or ssl
     */
    public $SMTPCrypto = 'tls';
    
    /**
     * SMTP Port
     */
    public $SMTPPort = 587;

    /**
     * SMTP Timeout (in seconds)
     */
    public  $SMTPTimeout = 60;

    /**
     * Enable persistent SMTP connections
     */
    public $SMTPKeepAlive = false;    

    /**
     * Enable word-wrap
     */
    public $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public $mailType = 'html';

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public $validate = false;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public $priority = 3;

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public $CRLF = "\r\n";

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public $newline = "\r\n";

    /**
     * Enable BCC Batch Mode.
     */
    public $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public $DSN = false;

}