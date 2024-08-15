<?php

    class MailService {
        public static function send($recipientEmail, $recipientUsername, $subject, $contentHTML){

            $apiKeyPublic = getenv('MAILJET_USER');
            $apiKeyPrivate = getenv('MAILJET_PASS');
            $senderEmail = 'albgarci@student.42madrid.com';

            $url = 'https://api.mailjet.com/v3.1/send';
            $data = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => $senderEmail,
                            'Name' => 'camagru-albgarci'
                        ],
                        'To' => [
                            [
                                'Email' => $recipientEmail,
                                'Name' => $recipientUsername
                            ]
                        ],
                        'Subject' =>  $subject,
                        'TextPart' => 'camagru',
                        'HTMLPart' => $contentHTML
                    ]
                ]
            ];

            $options = [
                'http' => [
                    'header'  => [
                        "Content-Type: application/json",
                        "Authorization: Basic " . base64_encode("$apiKeyPublic:$apiKeyPrivate")
                    ],
                    'method'  => 'POST',
                    'content' => json_encode($data),
                ],
            ];

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context); 
            $result = TRUE;

            if ($result === FALSE) {
                return false;
                //echo "Error sending email";
            } else {
                return $result;
            }
        }
    }

?>