<?php

namespace App\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Entity\User;
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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        $user = $this->getUser();
        $projectId = $request->attributes->get('project_id');
        $ticket = new Tickets();
        $tag = new Tag();
        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);
        $tags = explode(",", $form['Tags']->getData());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            for ($i = 0; $i < count($tags); $i++) {
              $tag = $this->getDoctrine()->getRepository(Tag::class)->find(trim($tags[$i]));
              if ($tag == 0) {
                $tag = new Tag();
                $tag->setWord(trim($tags[$i]));
                $ticket->addTag($tag);
              }
            }

            $File = $form['file']->getData();

            if ($File) {
                $originalFilename = pathinfo($File->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$File->guessExtension();

                try {
                    $File->move(
                        $this->getParameter('file_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }
                $ticket->setFileName($originalFilename);
                $ticket->setFile($newFilename);
            }
            $project = $this->getDoctrine()->getRepository(Projects::class)->find($projectId);
            $ticket->setProject($project);
            $ticket->setCreator($user);

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
        $user = $this->getUser();
        $projectId = $request->attributes->get('project_id');
        $ticketId = $request->attributes->get('id');
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $ticket = $this->getDoctrine()->getRepository(Tickets::class)->find($ticketId);
            $comment->setTicket($ticket);
            $comment->setAuthor($user);
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
        $ticketId = $request->attributes->get('id');
        $projectId = $request->attributes->get('project_id');
        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);
        $tags = explode(",", $form['Tags']->getData());

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $this->getDoctrine()->getRepository(Projects::class)->find($projectId);
            $ticket->setProject($project);
            for ($i = 0; $i < count($tags); $i++) {
              $tag = $this->getDoctrine()->getRepository(Tag::class)->find(trim($tags[$i]));
              if ($tag == 0) {
                $tag = new Tag();
                $tag->setWord(trim($tags[$i]));
                $ticket->addTag($tag);
              }
            }
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

        return $this->redirectToRoute('projects_index');
    }

}
