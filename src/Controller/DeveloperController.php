<?php

namespace App\Controller;

use App\Entity\Developer;
use App\Entity\Project;
use App\Form\DeveloperType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperController extends AbstractController
{
    #[Route('/developer/new', name: 'developer_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $developer = new Developer();

        // Получаем все проекты из базы данных
        $projects = $entityManager->getRepository(Project::class)->findAll();

        // Создаем форму и передаем опцию 'projects' в форму
        $form = $this->createForm(DeveloperType::class, $developer, [
            'projects' => $projects,  // Исправлено: передаем 'projects' вместо 'project'
        ]);

        $form->handleRequest($request);

        // Проверяем, если форма отправлена и валидна
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($developer);
            $entityManager->flush();

            // Перенаправляем на страницу списка разработчиков
            return $this->redirectToRoute('developer_list');
        }

        // Отправляем форму в шаблон
        return $this->render('developer/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/developers', name: 'developer_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        // Получаем всех разработчиков
        $developers = $entityManager->getRepository(Developer::class)->findAll();

        return $this->render('developer/list.html.twig', [
            'developers' => $developers,
        ]);
    }

    #[Route('/developer/{id}/transfer', name: 'developer_transfer', methods: ['GET', 'POST'])]
    public function transfer(Developer $developer, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Получаем все проекты из базы данных
        $projects = $entityManager->getRepository(Project::class)->findAll();

        // Обрабатываем запрос на изменение проекта
        if ($request->isMethod('POST')) {
            // Получаем новый проект из запроса
            $newProject = $entityManager->getRepository(Project::class)->find($request->request->get('project'));

            // Если проект найден и он отличается от текущего проекта разработчика
            if ($newProject && $newProject !== $developer->getProject()) {
                // Обновляем проект разработчика
                $developer->setProject($newProject);
                $entityManager->flush();

                // Перенаправляем на страницу списка разработчиков
                return $this->redirectToRoute('developer_list');
            }
        }

        // Отправляем информацию о разработчике и списке проектов в шаблон
        return $this->render('developer/transfer.html.twig', [
            'developer' => $developer,
            'projects' => $projects,
        ]);
    }

    #[Route('/developer/{id}/delete', name: 'developer_delete', methods: ['POST'])]
    public function delete(Developer $developer, EntityManagerInterface $entityManager): Response
    {
        // Удаляем разработчика из базы данных
        $entityManager->remove($developer);
        $entityManager->flush();

        // Перенаправляем на страницу списка разработчиков
        return $this->redirectToRoute('developer_list');
    }
}
