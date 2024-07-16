<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\EntityWithDeletableRelationType;
use App\Repository\EntityWithDeletableRelationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FormTestController extends AbstractController
{
    public function __construct(protected EntityWithDeletableRelationRepository $repository)
    {
    }

    #[Route('/form/test/{id}', name: 'app_form_test')]
    public function index(?int $id = null): Response
    {
        $entity = null;
        if (null !== $id) {
            $entity = $this->repository->find($id);
        }
        $form = $this->createForm(EntityWithDeletableRelationType::class, $entity);

        return $this->render('form_test/index.html.twig', [
            'controller_name' => 'FormTestController',
            'form' => $form,
        ]);
    }
}
