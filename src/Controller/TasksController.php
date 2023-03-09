<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tasks;
use App\Repository\TasksRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TasksController extends AbstractController
{
    #[Route('/api/tasks', methods: ['GET'])]
    public function view(ManagerRegistry $doctrine): JsonResponse
    {
        $taskRep = new TasksRepository($doctrine);
        $tasks = $taskRep->findBy([], [ 'id' => 'DESC' ]);

        return $this->json([
            'success' => true,
            'data' => $tasks
        ], Response::HTTP_OK);
    }

    #[Route('/api/task', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, #[CurrentUser] ?User $user, ValidatorInterface $validator, Request $request): JsonResponse
    {
        $request = json_decode($request->getContent());

        $constraints = new Assert\Collection([
            'title' => [
                new Assert\NotBlank(),
            ],
            'description' => [
                new Assert\NotBlank(),
            ],
        ]);
    
        $validationResult = $validator->validate((array) $request, $constraints);

        if (count($validationResult) > 0) {
            $messages = [];

            foreach ($validationResult as $error) {
                $messages[] = $error->getPropertyPath() . " " . $error->getMessage();
            }
            
            return $this->json([
                'success' => false,
                'message' => $messages
            ]);
        }
        
        $task = new Tasks();
        $task->setTitle($request->title);
        $task->setDescription($request->description);
        $task->setCreatedOn(new DateTime());
        $task->setOwnedBy($user);

        $taskRep = new TasksRepository($doctrine);
        $taskRep->save($task, true);

        return $this->json([
            'success' => true,
            'message' => "Task created",
            'data' => $task
        ], Response::HTTP_OK);
    }

    #[Route('/api/task/{taskId}', methods: ['PUT'])]
    public function update(ManagerRegistry $doctrine, Request $request, int $taskId): JsonResponse
    {
        $request = json_decode($request->getContent());

        $taskRep = new TasksRepository($doctrine);
        $task = $taskRep->find($taskId);

        if (!$task) {
            return $this->json([
                'success' => false,
                'message' => "Task not found with given id"
            ], Response::HTTP_NOT_FOUND);
        }
   
        if (!empty($request->title)){
            $task->setTitle($request->title);
        }
   
        if (!empty($request->description)){
            $task->setDescription($request->description);
        }
        
        $taskRep->save($task, true);

        return $this->json([
            'success' => true,
            'message' => "Task updated",
            'data' => $task
        ], Response::HTTP_OK);
    }

    #[Route('/api/task/{taskId}', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, int $taskId): JsonResponse
    {
        $taskRep = new TasksRepository($doctrine);
        $task = $taskRep->find($taskId);

        if (!$task) {
            return $this->json([
                'success' => false,
                'message' => "Task not found with given id"
            ], Response::HTTP_NOT_FOUND);
        }

        $taskRep->remove($task, true);

        return $this->json([
            'success' => true,
            'message' => "Task deleted"
        ], Response::HTTP_OK);
    }

    #[Route('/api/task/{taskId}/close', methods: ['PUT'])]
    public function close(ManagerRegistry $doctrine, int $taskId): JsonResponse
    {
        $taskRep = new TasksRepository($doctrine);
        $task = $taskRep->find($taskId);

        if (!$task) {
            return $this->json([
                'success' => false,
                'message' => "Task not found with given id"
            ], Response::HTTP_NOT_FOUND);
        }
   
        $task->setClosedOn(new DateTime());
        $taskRep->save($task, true);

        return $this->json([
            'success' => true,
            'message' => "Task closed",
            'data' => $task
        ], Response::HTTP_OK);
    }
}
