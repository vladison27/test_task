<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/api/user/create', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        $user = new User();
        $user->setEmail(@$data['email']);
        $user->setPassword(@$data['password']);
        $user->setUsername(@$data['username']);

        $errors = $validator->validate($user);

        if(empty((string) $errors)) {

            $user->setPassword(sha1($data['password']));
            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse([

                'success' => true,
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'username' => $user->getUsername(),
                ]

            ]);

        }
        else {

            $err = [];
            foreach ($errors as $error) {
                $err[] = $error->getMessage();
            }

            return new JsonResponse([

                'success' => false,
                'errors' => $err

            ]);

        }
    }

    #[Route('/api/user/edit', methods: ['POST'])]
    public function edit(): JsonResponse
    {


        return new JsonResponse([]);
    }
}
