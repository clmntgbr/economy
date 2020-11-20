<?php

namespace App\Util;

use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ResponseBody
{
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    const WRONG_PASSWORD = 'Incorrect Password.';
    const USER_NOT_FOUND = "User '%s' doesn't exist.";

    private function template(int $code, $data, $errors)
    {
        return [
            'status' => $code,
            'data_count' => count($data),
            'data' => $data,
            'errors_count' => count($errors),
            'errors' => $errors,
        ];
    }

    public function create(int $code, $data, $errors)
    {
        return View::create($this->template($code, $data, $errors), $code, []);
    }

    static function getValidatorErrors(ConstraintViolationListInterface $violationList): array
    {
        $errors = [];

        foreach ($violationList as $violation) {
            $errors[] = ['property' => $violation->getPropertyPath(), 'message' => $violation->getMessage()];
        }

        return $errors;
    }

    static function getValidatorErrorsToHTML(ConstraintViolationListInterface $violationList): string
    {
        $errors = self::getValidatorErrors($violationList);

        $message = "";
        foreach ($errors as $error) {
            $message .= sprintf("%s<br>", str_replace('value', $error['property'], $error['message']));
        }

        return $message;
    }

    static function getErrorsFormatted(string $property, string $message)
    {
        return [['property' => $property, 'message' => $message]];
    }
}