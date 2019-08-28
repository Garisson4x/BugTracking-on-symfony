<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use App\Entity\Projects;
use App\Form\ProjectsType;
use App\Repository\ProjectsRepository;
use App\Entity\Tickets;
use App\Form\TicketsType;
use App\Repository\TicketsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/projects/{project_id}/tickets")
 */
class TicketsController extends AbstractController
{
    /**
     * @Route("/", name="tickets_index", methods={"GET"})
     */
    public function index(TicketsRepository $ticketsRepository): Response
    {
        return $this->render('tickets/index.html.twig', [
            'tickets' => $ticketsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="tickets_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $projectId = $request->attributes->get('project_id');
        $ticket = new Tickets();
        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $project = $this->getDoctrine()->getRepository(Projects::class)->find($projectId);
            $ticket->setProject($project);
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('projects_show', ['id' => $projectId]);
        }

        return $this->render('tickets/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
            'project_id' => $projectId,
        ]);
    }

    /**
     * @Route("/{id}", name="tickets_show", methods={"GET","POST"})
     */
    public function show(Tickets $ticket, CommentsRepository $CommentsRepository, Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $projectId = $request->attributes->get('project_id');
        $ticketId = $request->attributes->get('id');
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $ticket = $this->getDoctrine()->getRepository(Tickets::class)->find($ticketId);
            $comment->setTicket($ticket);
            $author = $authenticationUtils->getLastUsername();
            $comment->setAuthor($author);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('tickets_show', ['project_id' => $projectId, 'id' => $ticketId]);
        }

        return $this->render('tickets/show.html.twig', [
            'form' => $form->createView(),
            'ticket' => $ticket,
            'ticket_id' => $ticketId,
            'comments' => $CommentsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tickets_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tickets $ticket): Response
    {
        $projectId = $request->attributes->get('project_id');
        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $this->getDoctrine()->getRepository(Projects::class)->find($projectId);
            $ticket->setProject($project);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('projects_show', ['id' => $projectId]);
        }

        return $this->render('tickets/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
            'project_id' => $projectId,
        ]);
    }

    /**
     * @Route("/{id}", name="tickets_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tickets $ticket): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tickets_index');
    }

}
