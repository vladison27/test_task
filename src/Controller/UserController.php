<?php
namespace App\Controller;

use App\Entity\ClientSession;
use App\Entity\User;
use App\Repository\UserRepository;
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

            ], RESPONSE::HTTP_BAD_REQUEST);

        }
    }

    #[Route('/api/user/{id}', methods: ['PATCH'])]
    public function edit(int $id, Request $request, UserRepository $userRepository, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        if(!empty($id)) {

            $user = $userRepository->find($id);

            if(!empty($user)) {

                if(!empty($data['email'])) {
                    $user->setEmail($data['email']);
                }

                if(!empty($data['username'])) {
                    $user->setUsername($data['username']);
                }

                if(!empty($data['password'])) {
                    $user->setPassword($data['password']);
                }

                $errors = $validator->validate($user);

                if(empty((string) $errors)) {

                    if(!empty($data['password'])) {
                        $user->setPassword(sha1($data['password']));
                    }

                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $this->json([
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

                    ], RESPONSE::HTTP_BAD_REQUEST);

                }

            }
            else {

                return $this->json([
                    'success' => false,
                    'error' => 'User not found'
                ], RESPONSE::HTTP_NOT_FOUND);

            }

        }
        else {

            return $this->json([
                'success' => false,
                'error' => 'Id is required'
            ], RESPONSE::HTTP_BAD_REQUEST);

        }
    }

    #[Route('/api/user/{id}', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        if(!empty($id)) {

            $user = $userRepository->find($id);

            if(!empty($user)) {

                $entityManager->remove($user);
                $entityManager->flush();

                return $this->json([
                    'success' => true,
                    'error' => 'User has been deleted'
                ]);

            }
            else {

                return $this->json([
                    'success' => false,
                    'error' => 'user not found'
                ], RESPONSE::HTTP_NOT_FOUND);

            }

        }
        else {

            return $this->json([
                'success' => false,
                'error' => 'Id is required'
            ], RESPONSE::HTTP_BAD_REQUEST);

        }
    }

    #[Route('/api/user/auth', methods: ['POST'])]
    public function auth(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->getContent();
        $data = json_decode($data, true);
        $err = [];

        if(empty($data['username'])) {

            $err[] = 'Username is required';

        }

        if(empty($data['password'])) {

            $err[] = 'Password is required';

        }

        if(empty($err)) {

            $user = $userRepository->findOneBy(['username' => $data['username']]);

            if(empty($user)) {

                return $this->json([
                    'success' => false,
                    'error' => 'User not found'
                ], RESPONSE::HTTP_NOT_FOUND);

            }
            else {

                if(sha1($data['password']) == $user->getPassword()) {

                    $token = sha1($user->getUsername().microtime());
                    $session = new ClientSession();

                    $session->setClientId($user->getId());
                    $session->setToken($token);

                    $entityManager->persist($session);
                    $entityManager->flush();

                    return $this->json([
                        'success' => true,
                        'token' => $token,
                    ]);

                }
                else {

                    return $this->json([
                        'success' => false,
                        'error' => 'Wrong password'
                    ], RESPONSE::HTTP_BAD_REQUEST);

                }

            }

        }
        else {

            return $this->json([
                'success' => false,
                'errors' => $err
            ], RESPONSE::HTTP_BAD_REQUEST);

        }
    }
    #[Route('/api/user/{id}', methods: ['GET'])]
    public function getInfo(int $id, UserRepository $userRepository): JsonResponse
    {
        if(!empty($id)) {

            $user = $userRepository->find($id);
            if(!empty($user)) {

                return $this->json([
                    'success' => true,
                    'user' => [
                        'id' => $user->getId(),
                        'email' => $user->getEmail(),
                        'username' => $user->getUsername(),
                    ]
                ]);

            }
            else {

                return $this->json([
                    'success' => false,
                    'error' => 'User not found'
                ], RESPONSE::HTTP_BAD_REQUEST);

            }

        }
        else {

            return $this->json([
                'success' => false,
                'error' => 'Id is required'
            ], RESPONSE::HTTP_BAD_REQUEST);

        }
    }
}
