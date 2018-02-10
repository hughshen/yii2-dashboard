<?php

namespace common\components;

use Yii;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use common\models\Config;

class Mailer
{
    public $from;
    public $to;

    public $smtp_host;
    public $smtp_user;
    public $smtp_pass;
    public $smtp_auth;
    public $smtp_port;
    public $smtp_secure;

    public $subject;
    public $body;
    public $isHtml;
    public $attachments;

    public $debugMode;

    public function __construct()
    {
        $this->from = Config::byName('smtp_sender');
        $this->to = Config::byName('smtp_receiver');

        $this->smtp_host = Config::byName('smtp_host');
        $this->smtp_user = Config::byName('smtp_user');
        $this->smtp_pass = Config::byName('smtp_pass');
        $this->smtp_auth = Config::byName('smtp_auth');
        $this->smtp_port = Config::byName('smtp_port');
        $this->smtp_secure = Config::byName('smtp_secure');

        $this->isHtml = true;

        $this->debugMode = false;
    }

    public function send()
    {
        if (!$this->from) {
            return 'Sender required.';
        }

        if (!$this->to) {
            return 'Receiver required.';
        }

        if (!$this->subject) {
            return 'Subject required.';
        }

        if (!$this->body) {
            return 'Body required.';
        }

        $mail = new PHPMailer(true);
        try {
            // Debug
            if ($this->debugMode === true) {
                $mail->SMTPDebug = 2;
            } elseif (is_numeric($this->debugMode)) {
                $mail->SMTPDebug = $this->debugMode;
                // $mail->SMTPDebug = 3;
            }

            //Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtp_host;
            $mail->SMTPAuth = (bool)$this->smtp_auth;
            $mail->Username = $this->smtp_user;
            $mail->Password = $this->smtp_pass;
            $mail->SMTPSecure = $this->smtp_secure;
            $mail->Port = $this->smtp_port;

            //Recipients
            $mail->setFrom($this->from);
            if (is_array($this->to)) {
                foreach ($this->to as $val) {
                    $mail->addAddress($val);
                }
            } else {
                $mail->addAddress($this->to);
            }

            //Attachments
            if ($this->attachments) {
                if (is_array($this->attachments)) {
                    foreach ($this->attachments as $val) {
                        $mail->addAttachment($val);    
                    }
                } else {
                    $mail->addAttachment($this->attachments);
                }
            }

            // https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting#php-56-certificate-verification-failure
            if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
            }

            //Content
            $mail->isHTML((bool)$this->isHtml);
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;

            if ($mail->send()) {
                return true;
            } else {
                return 'Message could not be sent.';
            }
        } catch (Exception $e) {
            return 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }

        return false;
    }
}
