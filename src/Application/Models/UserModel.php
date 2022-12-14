<?php

namespace App\Application\Models;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserModel extends Model
{
    public function rules(): array
    {
        return [
            'name' => 'required|maxlength:30|minlength:2|',
            'password' => 'required|maxlength:30|minlength:8|',
            'email' => 'required|maxlength:250|emailFormat|unique|',
        ];
    }


    public function doLogin(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody()['request'];
        $validationResult = $this->validation(['email' => $data['email']], ['email' => 'required|maxlength:250|emailFormat|unique|']);
        if ($validationResult['result'] === true) {
            $user = $this->getData('password, id, name, blocked', $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE'], 'where email = ', $data['email']);

            if ($user['data'][0]['blocked'] === 'true') {
                return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(403);
            }

            if (password_verify($data['password'], $user['data'][0]['password'])) {
                $_SESSION['sessions'][substr(session_id(), 0, 6)]["user_id"] = $user['data'][0]['id'];
                $_SESSION['sessions'][substr(session_id(), 0, 6)]["user_name"] = $user['data'][0]['name'];

                return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(200);
            }
        }
        $response->getBody()->write(json_encode([
            'error' => [
                'email' => 'Wrong email or password'
            ]
        ]));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(206);
    }

    public function doRegister(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody()['request'];
        $data['name'] = htmlspecialchars(strip_tags(trim($data['name'])));
        $validationResult = $this->validation($data, $this->rules());

        if ($validationResult['result'] === true) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

            $user = $this->add($data, $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE']);
            if (in_array('data', $user)) {
                return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(200);
            }
            $validationResult[] = $user;
        }


        $response->getBody()->write(json_encode($validationResult));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(206);
    }

    public function sendMail(Request $request, Response $response): Response
    {
        $email = $request->getParsedBody()['request'];
        $email['reset_code'] = rand(100000, 999999);
        $result = $this->update(
            ['reset_code' => $email['reset_code']],
            'email',
            ['email' => "{$email['email']}"],
            $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE']
        );
        if (in_array('data', $result)) {
            $code = $email['reset_code'];
            $emailTo = $email['email'];
            $subject = "Reset code";
            $message = "Please, copy the code and paste it into the form at the reset page:\r\n$code";
            $message = wordwrap($message, 70, "\r\n");

            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host = 'smtp.gmail.com';                       //Set the SMTP server to send through
                $mail->SMTPAuth = true;                                   //Enable SMTP authentication
                $mail->Username = $_ENV['SMTP_MAIL'];      //SMTP username mailsender.parhomenko@gmail.com
                $mail->Password = $_ENV['SMTP_PASS'];                         //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port = 465;                                    //TCP port to connect to;
                // use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom($_ENV['SMTP_MAIL'], 'Mailer');
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

        $record = $this->getData('reset_code, fails_left', $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE'], 'where email = ', $user['email']);

        // Check if user has failed code-check 3 times
        if ($record['data'][0]['fails_left'] === 0) {
            $this->update(
                ['blocked' => 'true'],
                'email',
                ['email' => "{$user['email']}"],
                $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE']
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
                $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE']
            );

            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(403);
        }
    }

    public function resetPassword(Request $request, Response $response): Response
    {
        $user = $request->getParsedBody()['request'];

        $record = $this->getData('reset_code, fails_left', $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE'], 'where email = ', $user['email']);


        // Check if user has failed code-check 3 times
        if ($record['data'][0]['fails_left'] === 0) {
            $this->update(
                ['blocked' => 'true'],
                'email',
                ['email' => "{$user['email']}"],
                $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE']
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
                    'reset_code' => null
                ],
                'email',
                ['email' => "{$user['email']}"],
                $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE']
            );

            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $this->update(
                ['fails_left' => $record['data'][0]['fails_left'] - 1],
                'email',
                ['email' => "{$user['email']}"],
                $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE']
            );
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(403);
        }
    }
}
