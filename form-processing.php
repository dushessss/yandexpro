<?php
header('Content-Type: application/json');

if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
  exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  exit();
}

define('LOG_FILE', 'logs/' . date('Y-m-d') . '.log');

define('HAS_WRITE_LOG', true);

define('HAS_SEND_EMAIL', true);

define('EMAIL_SETTINGS', [
  'addresses' => ['rayspark.andrey@yandex.ru'], // кому необходимо отправить письмо
  'from' => ['rayspark.andrey@yandex.ru', 'yandextaxi.pro'], // от какого email и имени необходимо отправить письмо
  'subject' => 'Сообщение с формы обратной связи', // тема письма
  'host' => 'ssl://smtp.yandex.ru', // SMTP-хост
  'username' => 'rayspark.andrey@yandex.ru', // // SMTP-пользователь
  'password' => 'bzbvvipradpmaxej', // SMTP-пароль
  'port' => '465' // SMTP-порт
]);
define('BASE_URL', 'https://yandextaxi.pro');
define('SUBJECT_FOR_CLIENT', 'Ваше сообщение доставлено');

function itc_log($message)
{
  if (HAS_WRITE_LOG) {
    error_log('Date:  ' . date('d.m.Y h:i:s') . '  |  ' . $message . PHP_EOL, 3, LOG_FILE);
  }
}

$data = [
  'errors' => [],
  'form' => [],
  'logs' => [],
  'result' => 'success'
];

if (!empty($_POST['name'])) {
  $data['form']['name'] = htmlspecialchars($_POST['name']);
} else {
  $data['result'] = 'error';
  $data['errors']['name'] = 'Заполните это поле.';
  itc_log('Не заполнено поле name.');
}

if (!empty($_POST['phone'])) {
  $data['form']['phone'] = preg_replace('/\D/', '', $_POST['phone']);
  if (!preg_match('/^(\d{11})$/', $data['form']['phone'])) {
    $data['result'] = 'error';
    $data['errors']['phone'] = 'Поле содержит не корректный номер.';
    itc_log('Phone не корректный.');
  }
}

if ($_POST['que1'] == 'true') {
  $data['form']['que1'] = 'Да';
} else{ 
  $data['form']['que1'] = 'Нет';

}
if ($_POST['que2'] == 'true') {
  $data['form']['que2'] = 'Да';
} else {
  $data['form']['que2'] = 'Нет';
}
if ($_POST['que3'] == 'true') {
  $data['form']['que3'] = 'Да';
} else {
  $data['form']['que3'] = 'Нет';
}

if ($_POST['agree'] == 'true') {
  $data['form']['agree'] = true;
} else {
  $data['result'] = 'error';
  $data['errors']['agree'] = 'Необходимо установить этот флажок.';
  itc_log('Не установлен флажок для поля agree.');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

if ($data['result'] == 'success' && HAS_SEND_EMAIL == true) {
  // получаем содержимое email шаблона и заменяем в нём
  $template = file_get_contents(dirname(__FILE__) . '/template/email.tpl');
  $search = ['%subject%', '%name%', '%phone%', '%date%', '%que1%', '%que2%', '%que3%'];
  $replace = [EMAIL_SETTINGS['subject'], $data['form']['name'], $data['form']['phone'], date('d.m.Y H:i'), $data['form']['que1'], $data['form']['que2'], $data['form']['que3']];
  $body = str_replace($search, $replace, $template);
  // добавление файлов в виде ссылок
  
  $mail = new PHPMailer();
  try {
    //Server settings
    $mail->isSMTP();
    $mail->Host = EMAIL_SETTINGS['host'];
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_SETTINGS['username'];
    $mail->Password = EMAIL_SETTINGS['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = EMAIL_SETTINGS['port'];
    //Recipients
    $mail->setFrom(EMAIL_SETTINGS['from'][0], EMAIL_SETTINGS['from'][1]);
    foreach (EMAIL_SETTINGS['addresses'] as $address) {
      $mail->addAddress(trim($address));
    }
    //Attachments
    
    //Content
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->isHTML(true);
    $mail->Subject = EMAIL_SETTINGS['subject'];
    $mail->Body = $body;
    $mail->send();
    itc_log('Форма успешно отправлена.');
  } catch (Exception $e) {
    $data['result'] = 'error';
    itc_log('Ошибка при отправке письма: ' . $mail->ErrorInfo);
  }
}

echo json_encode($data);
exit();
