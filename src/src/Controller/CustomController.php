<?php

namespace App\Controller;

use App\ApiResource\CustomDto;
use App\Services\CustomSerializationHelper;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

#[AsController]
class CustomController extends AbstractController
{
    public function __construct(
        private SerializerInterface       $serializer,
        private ValidatorInterface        $validator,
        private CustomSerializationHelper $serializationHelper
    )
    {

    }

    /**
     * @throws \Exception
     */
    #[Route(
        path: '/api/custom',
        name: 'book_post_publication',
        defaults: [
            '_api_resource_class' => CustomDto::class,
        ],
        methods: ['post'],
    )]
    public function customAction(Request $request): JsonResponse
    {
        try {
            /** @var CustomDto $input */
            $input = $this->serializer->deserialize(
                $request->getContent(),
                CustomDto::class,
                'json',
                $this->serializationHelper->getDeserializeContext()
            );

            /** @var ConstraintViolationList $errors */
            $errors = $this->validator->validate($input);

            if ($errors->count() > 0) {
                $json = $this->serializationHelper->createValidationErrorMessage($errors, $input);
                $status = Response::HTTP_BAD_REQUEST;
            } else {
                $this->serializationHelper->doIncrement($input, 1);

                $json = $this->serializer->serialize(
                    $input,
                    'json',
                    $this->serializationHelper->getSerializeContext()
                );
                $status = Response::HTTP_OK;
            }

            return JsonResponse::fromJsonString($json, $status);
        } catch (Throwable $throwable) {
            return new JsonResponse(
                $this->serializationHelper->createRandomErrorMessage($throwable, $input),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}