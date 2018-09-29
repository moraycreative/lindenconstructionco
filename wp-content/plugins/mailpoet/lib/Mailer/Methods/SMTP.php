<?php
namespace MailPoet\Mailer\Methods;

use MailPoet\Mailer\Mailer;
use MailPoet\Mailer\Methods\ErrorMappers\SMTPMapper;
use MailPoet\WP\Hooks;

if(!defined('ABSPATH')) exit;

class SMTP {
  public $host;
  public $port;
  public $authentication;
  public $login;
  public $password;
  public $encryption;
  public $sender;
  public $reply_to;
  public $return_path;
  public $mailer;
  const SMTP_CONNECTION_TIMEOUT = 15; // seconds

  /** @var SMTPMapper */
  private $error_mapper;

  function __construct(
    $host, $port, $authentication, $login = null, $password = null, $encryption,
    $sender, $reply_to, $return_path, SMTPMapper $error_mapper) {
    $this->host = $host;
    $this->port = $port;
    $this->authentication = $authentication;
    $this->login = $login;
    $this->password = $password;
    $this->encryption = $encryption;
    $this->sender = $sender;
    $this->reply_to = $reply_to;
    $this->return_path = ($return_path) ?
      $return_path :
      $this->sender['from_email'];
    $this->mailer = $this->buildMailer();
    $this->mailer_logger = new \Swift_Plugins_Loggers_ArrayLogger();
    $this->mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($this->mailer_logger));
    $this->error_mapper = $error_mapper;
  }

  function send($newsletter, $subscriber, $extra_params = array()) {
    try {
      $message = $this->createMessage($newsletter, $subscriber, $extra_params);
      $result = $this->mailer->send($message);
    } catch(\Exception $e) {
      return Mailer::formatMailerErrorResult(
        $this->error_mapper->getErrorFromException($e, $subscriber)
      );
    }
    if($result === 1) {
      return Mailer::formatMailerSendSuccessResult();
    } else {
      $error = $this->error_mapper->getErrorFromLog($this->mailer_logger->dump(), $subscriber);
      return Mailer::formatMailerErrorResult($error);
    }
  }

  function buildMailer() {
    $transport = \Swift_SmtpTransport::newInstance(
      $this->host, $this->port, $this->encryption);
    $connection_timeout = Hooks::applyFilters('mailpoet_mailer_smtp_connection_timeout', self::SMTP_CONNECTION_TIMEOUT);
    $transport->setTimeout($connection_timeout);
    if($this->authentication) {
      $transport
        ->setUsername($this->login)
        ->setPassword($this->password);
    }
    $transport = Hooks::applyFilters('mailpoet_mailer_smtp_transport_agent', $transport);
    return \Swift_Mailer::newInstance($transport);
  }

  function createMessage($newsletter, $subscriber, $extra_params = array()) {
    $message = \Swift_Message::newInstance()
      ->setTo($this->processSubscriber($subscriber))
      ->setFrom(
        array(
          $this->sender['from_email'] => $this->sender['from_name']
        )
      )
      ->setSender($this->sender['from_email'])
      ->setReplyTo(
        array(
          $this->reply_to['reply_to_email'] => $this->reply_to['reply_to_name']
        )
      )
      ->setReturnPath($this->return_path)
      ->setSubject($newsletter['subject']);
    if(!empty($extra_params['unsubscribe_url'])) {
      $headers = $message->getHeaders();
      $headers->addTextHeader('List-Unsubscribe', '<' . $extra_params['unsubscribe_url'] . '>');
    }
    if(!empty($newsletter['body']['html'])) {
      $message = $message->setBody($newsletter['body']['html'], 'text/html');
    }
    if(!empty($newsletter['body']['text'])) {
      $message = $message->addPart($newsletter['body']['text'], 'text/plain');
    }
    return $message;
  }

  function processSubscriber($subscriber) {
    preg_match('!(?P<name>.*?)\s<(?P<email>.*?)>!', $subscriber, $subscriber_data);
    if(!isset($subscriber_data['email'])) {
      $subscriber_data = array(
        'email' => $subscriber,
      );
    }
    return array(
      $subscriber_data['email'] =>
        (isset($subscriber_data['name'])) ? $subscriber_data['name'] : ''
    );
  }
}
