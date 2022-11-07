<?php

namespace App\Application\Models;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserModel extends Model
{
    public function doLogin(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody()['request'];

        $user = $this->getData('password, id, name, blocked', 'Users.users', 'where email = ', $data['email']);

        if ($user['data'][0]['blocked'] === 'true') {
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(403);
        }

        if (password_verify($data['password'], $user['data'][0]['password'])) {
            session_start();
            $_SESSION["user_id"] = $user['data'][0]['id'];
            $_SESSION["user_name"] = $user['data'][0]['name'];
//            setcookie('name', $admin['data'][0]['name'], time() + 1800);
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        }
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(403);
    }

    public function doRegister(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody()['request'];
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = $this->add($data, 'Users.users');
        if (in_array('data', $user)) {
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        }
        $response->getBody()->write(json_encode($user));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(403);
    }

    public function sendMail(Request $request, Response $response): Response
    {
        $email = $request->getParsedBody()['request'];
        $email['reset_code'] = rand(100000, 999999);
        $result = $this->update(
            ['reset_code' => $email['reset_code']],
            'email',
            ['email' => "{$email['email']}"],
            'Users.users'
        );
        if (in_array('data', $result)) {
            $code = $email['reset_code'];
            $emailTo = $email['email'];
            $subject = "Reset code";
            $message = "Please, copy the code below and paste it into the form at the reset page:\r\n$code";
            $message = wordwrap($message, 70, "\r\n");

            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host = 'smtp.gmail.com';                       //Set the SMTP server to send through
                $mail->SMTPAuth = true;                                   //Enable SMTP authentication
                $mail->Username = 'mailsender.parhomenko@gmail.com';      //SMTP username mailsender.parhomenko@gmail.com
                $mail->Password = '';                         //SMTP password 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port = 465;                                    //TCP port to connect to;
                // use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom('mailsender.parhomenko@gmail.com', 'Mailer');
                $mail->addAddress($emailTo);     //Add a recipient

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body = "Please, copy the code below and paste it into the form at the reset page: <b>$code</b>";
                $mail->AltBody = $message;

                $mail->send();
            } catch (Exception $e) {
                $response->getBody()->write(json_encode("Message could not be sent. Mailer Error: {$mail->ErrorInfo}"));
            }

            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        }
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(403);
    }

    public function acceptCode(Request $request, Response $response): Response
    {
        $user = $request->getParsedBody()['request'];

        $record = $this->getData('reset_code, fails_left', 'Users.users', 'where email = ', $user['email']);

        // Check if user has failed code-check 3 times
        if ($record['data'][0]['fails_left'] === 0) {
            $this->update(
                ['blocked' => 'true'],
                'email',
                ['email' => "{$user['email']}"],
                'Users.users'
            );
            $response->getBody()->write(json_encode("fail on fails"));

            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(403);
        }

        if (intval($user['code']) === intval($record['data'][0]['reset_code'])) {
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $this->update(
                ['fails_left' => $record['data'][0]['fails_left'] - 1],
                'email',
                ['email' => "{$user['email']}"],
                'Users.users'
            );

            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(403);
        }
    }

    public function resetPassword(Request $request, Response $response): Response
    {
        $user = $request->getParsedBody()['request'];

        $record = $this->getData('reset_code, fails_left', 'Users.users', 'where email = ', $user['email']);


        // Check if user has failed code-check 3 times
        if ($record['data'][0]['fails_left'] === 0) {
            $this->update(
                ['blocked' => 'true'],
                'email',
                ['email' => "{$user['email']}"],
                'Users.users'
            );
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(403);
        }

        if (intval($user['code']) === intval($record['data'][0]['reset_code'])) {
            $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
            $this->update(
                [
                    'password' => $user['password'],
                    'fails_left' => 3,
                    'reset_code' => NULL
                ],
                'email',
                ['email' => "{$user['email']}"],
                'Users.users'
            );

            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $this->update(
                ['fails_left' => $record['data'][0]['fails_left'] - 1],
                'email',
                ['email' => "{$user['email']}"],
                'Users.users'
            );
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(403);
        }
    }
}
