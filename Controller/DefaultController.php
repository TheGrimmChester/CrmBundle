<?php

/**
 * Copyright (c) 2010-2017, AWHSPanel by Nicolas Méloni
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *     * Neither the name of AWHSPanel nor the names of its contributors
 *       may be used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace AWHS\CrmBundle\Controller;

use AWHS\CrmBundle\Entity\Message;
use AWHS\CrmBundle\Entity\Ticket;
use AWHS\UserBundle\Entity\UserRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // On récupère l'entité correspondant à  l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('AWHSCrmBundle:Default:index.html.twig');
    }

    //Post
    public function usersJsonAction()
    {
        // On récupère l'entité correspondant à  l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        if (isset($_GET["search"])) {
            $users = $em->createQuery("
				SELECT u
				FROM AWHSUserBundle:User u 
				WHERE u.firstname LIKE :text OR u.lastname LIKE :text")
                ->setParameter('text', "%" . $_GET["search"] . "%")
                ->getResult();
        } else {
            $users = $em->getRepository('AWHSUserBundle:User')
                ->findAll();
        }

        $users_array = array();

        foreach ($users as $key => $user) {
            $users_array[$key]["id"] = $user->getId();
            $users_array[$key]["full_name"] = "{$user->getLastname()} {$user->getFirstname()}";
        }

        $response = new  Response(json_encode($users_array));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function createAction(Request $request)
    {
        // On récupère l'entité correspondant à  l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        $defaultData = array('title' => '',
            'message' => '');

        $builder = $this->createFormBuilder($defaultData);
        $builder->add('priority', EntityType::class, array(
            'required' => true,
            'label' => 'Priorité:',
            'class' => 'AWHSCrmBundle:TicketPriority',
            'choice_label' => 'name',
        ))
            ->add('title', TextType::class, array('label' => 'Titre:'));

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')) {
            $builder->
            add('assignedTo', EntityType::class, array(
                'required' => false,
                'label' => 'Assigné à:',
                'class' => 'AWHSUserBundle:User',
                'choice_label' => 'fullname',
                'data' => $user
            ));
        }
        $builder->add('message', TextareaType::class, array('label' => 'Message:'));
        $form = $builder->getForm();

        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') {
//
//            var_dump($_POST);
//            exit;
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $article contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);
            $data = $form->getData();

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                $ticket = new Ticket();
                $ticket->setTitle($data['title']);
                $ticket->setPriority($data['priority']);
                if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')) {
                    $ticket->setAssignedTo($data['assignedTo']);
                } else {
                    $ticket->setClient($user);
                }
                $ticket->setAuthor($user);

                // Création d'un premier message
                $message = new Message();
                $message->setMessage($data['message']);
                $message->setSender($user);
                $message->setDate(new \Datetime);
                $message->setTicket($ticket);

                // On récupère l'EntityManager
                $em = $this->getDoctrine()->getManager();
                // Étape 1 : On persiste les entités
                $em->persist($ticket);
                // Pour cette relation pas de cascade, car elle est définie dans l'entité message et non ticket
                // On doit donc tout persister à la main ici
                $em->persist($message);

                // Étape 2 : On déclenche l'enregistrement
                $em->flush();

                // On redirige vers la page de visualisation de l'article nouvellement créé
                return $this->redirect($this->generateUrl('awhs_crm_show', array('id' => $ticket->getId())));
            }
        }

        // On passe la méthode createView() du formulaire à la vue afin qu'elle puisse afficher le formulaire toute seule
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')) {
            return $this->render('AWHSCrmBundle:' . $this->container->getParameter('awhs')['template'] . '/Default:create.html.twig', array(
                'user' => $user,
                'form' => $form->createView(),
            ));
        } else {
            return $this->render('AWHSCrmBundle:' . $this->container->getParameter('awhs')['template'] . '/Client:create.html.twig', array(
                'user' => $user,
                'form' => $form->createView(),
            ));
        }
    }

    public function listAction()
    {
        // On récupère l'entité correspondant à  l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $author = $user->getId();

        // On récupère la liste des tickets du client
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')) {
            $liste_tickets = $em->getRepository('AWHSCrmBundle:Ticket')
                ->findAll();
        } else {
            $liste_tickets = $em->getRepository('AWHSCrmBundle:Ticket')
                ->findByAuthor($author);
        }

        // Puis modifiez la ligne du render comme ceci, pour prendre en compte l'article :
        return $this->render('AWHSCrmBundle:' . $this->container->getParameter('awhs')['template'] . '/Default:list.html.twig', array(
            'user' => $user,
            'tickets' => $liste_tickets
        ));
    }

    public function afficherAction(Request $request, $id)
    {
        // On récupère l'entité correspondant à  l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $author = $user->getId();

        $ticket = $em->getRepository('AWHSCrmBundle:Ticket')->find(array('id' => $id));

        // Ou null si aucun article n'a été trouvé avec l'id $id
        if ($ticket === null || (($ticket->getAuthor()->getId() != $author && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT'))
                && ($ticket->getClient()->getId() != $author && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')))
        ) {
            throw $this->createNotFoundException('Ticket[id=' . $id . '] inexistant.');
        }

        $message = new Message();
        $message->setTicket($ticket);

        $formMessage = $this->createFormBuilder($message)
            ->add('message', TextareaType::class, array('label' => false))
            ->add('save', SubmitType::class, array(
                'label' => 'Envoyer le message',
                'attr' => array('class' => 'save'),
            ))
            ->getForm();

        $ticketBuilder = $this->createFormBuilder($ticket);
        $ticketBuilder->add('priority', EntityType::class, array(
            'required' => true,
            'label' => 'Priorité:',
            'class' => 'AWHSCrmBundle:TicketPriority',
            'choice_label' => 'name',
        ))
            ->add('title', TextType::class, array('label' => 'Titre:'));
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')) {
            $ticketBuilder->add('client', EntityType::class, array(
                'required' => false,
                'label' => 'Pour client:',
                'class' => 'AWHSUserBundle:User',
                'choice_label' => 'fullname',
            ))
                ->add('assignedTo', EntityType::class, array(
                    'required' => false,
                    'label' => 'Personne en charge:',
                    'class' => 'AWHSUserBundle:User',
                    'choice_label' => 'fullname',
                    'query_builder' => function (UserRepository $er) {
                        return $er->findByRole('ROLE_SUPPORT');
                    },
                ))
                ->add('settings', TextareaType::class, array(
                        'label' => 'Informations additionnelles:')
                );
        }
        $ticketBuilder->add('save', SubmitType::class, array(
            'label' => 'Enregistrer les modifications',
            'attr' => array('class' => 'save'),
        ));
        $formTicket = $ticketBuilder->getForm();

        $formMessageAction = function ($form) use (&$aVar, &$em, &$ticket, &$user) {
            $message = $form->getData();
            $message->setSender($user);
            $em->persist($message);
            // Étape 2 : On déclenche l'enregistrement
            $em->flush();
        };

        $formTicketAction = function ($form) use (&$aVar, &$em) {
            $ticket = $form->getData();
            $em->persist($ticket);
            // Étape 2 : On déclenche l'enregistrement
            $em->flush();
        };

        $forms[] = array("form" => $formMessage, "action" => $formMessageAction);
        $forms[] = array("form" => $formTicket, "action" => $formTicketAction);


        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') {
            foreach ($forms as $key => $form) {
                $form["form"]->handleRequest($request);
                if ($form["form"]->get('save')->isClicked() and $form["form"]->isValid()) {
                    $form["action"]($form["form"]);
                }
            }
            // On redirige vers la page de visualisation de l'article nouvellement créé
            return $this->redirect($this->generateUrl('awhs_crm_show', array('id' => $ticket->getId())));
        }


        // On récupère la liste des messages//->findBy(array('ticket' => $id));
        //$liste_messages = $em->getRepository('AWHSTicketBundle:Message')->findBy(array('ticket' => $id));
        $liste_messages = $em->createQuery("SELECT m FROM AWHSCrmBundle:Message m WHERE m.ticket='$id' order by m.id DESC")->getResult();

        /*foreach ($liste_messages as $message) {
            $message->setClientReaded(1);
            $em->persist($message);
        }
        $em->flush();*/

        // Puis modifiez la ligne du render comme ceci, pour prendre en compte l'article :
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')) {
            return $this->render('AWHSCrmBundle:' . $this->container->getParameter('awhs')['template'] . '/Default:index.html.twig', array(
                'user' => $user,
                'ticket' => $ticket,
                'liste_messages' => $liste_messages,
                'formMessage' => $formMessage->createView(),
                'formTicket' => $formTicket->createView(),
            ));
        } else {
            return $this->render('AWHSCrmBundle:' . $this->container->getParameter('awhs')['template'] . '/Client:index.html.twig', array(
                'user' => $user,
                'ticket' => $ticket,
                'liste_messages' => $liste_messages,
                'formMessage' => $formMessage->createView(),
                'formTicket' => $formTicket->createView(),
            ));
        }

    }

    public function resolveAction($id)
    {
        // On récupère l'entité correspondant à  l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondant à l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        $author = $user->getId();

        $ticket = $em->getRepository('AWHSCrmBundle:Ticket')->find(array('id' => $id));

        // Ou null si aucun article n'a été trouvé avec l'id $id
        if ($ticket === null || (($ticket->getAuthor()->getId() != $author && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT'))
                && ($ticket->getClient()->getId() != $author && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')))
        ) {
            throw $this->createNotFoundException('Ticket[id=' . $id . '] inexistant.');
        }

        $ticket->setResolved(1);
        $em->persist($ticket);
        $em->flush();

        return $this->redirect($this->generateUrl('awhs_crm_show', array('id' => $ticket->getId())));
        return $this->redirect($this->generateUrl('awhs_crm_homepage', array()));
    }

    public function progressAction($id)
    {
        // On récupère l'entité correspondant à  l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondant à l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        $author = $user->getId();

        $ticket = $em->getRepository('AWHSCrmBundle:Ticket')->find(array('id' => $id));

        // Ou null si aucun article n'a été trouvé avec l'id $id
        if ($ticket === null || (($ticket->getAuthor()->getId() != $author && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT'))
                && ($ticket->getClient()->getId() != $author && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')))
        ) {
            throw $this->createNotFoundException('Ticket[id=' . $id . '] inexistant.');
        }

        $ticket->setResolved(0);
        $em->persist($ticket);
        $em->flush();

        return $this->redirect($this->generateUrl('awhs_crm_show', array('id' => $ticket->getId())));
        return $this->redirect($this->generateUrl('awhs_crm_homepage', array()));
    }

    public function openAction($id)
    {
        // On récupère l'entité correspondant à  l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondant à l'id $id
        // On récupère l'entité correspondant à l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        $author = $user->getId();

        $ticket = $em->getRepository('AWHSCrmBundle:Ticket')->find(array('id' => $id));

        // Ou null si aucun article n'a été trouvé avec l'id $id
        if ($ticket === null || (($ticket->getAuthor()->getId() != $author && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT'))
                && ($ticket->getClient()->getId() != $author && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')))
        ) {
            throw $this->createNotFoundException('Ticket[id=' . $id . '] inexistant.');
        }

        $ticket->setClosed(0);
        $em->persist($ticket);
        $em->flush();

        return $this->redirect($this->generateUrl('awhs_crm_show', array('id' => $ticket->getId())));
        return $this->redirect($this->generateUrl('awhs_crm_homepage', array()));
    }

    public function closeAction($id)
    {
        // On récupère l'entité correspondant à  l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondant à l'id $id
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        $author = $user->getId();

        $ticket = $em->getRepository('AWHSCrmBundle:Ticket')->find(array('id' => $id));

        // Ou null si aucun article n'a été trouvé avec l'id $id
        if ($ticket === null || (($ticket->getAuthor()->getId() != $author && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT'))
                && ($ticket->getClient()->getId() != $author && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPPORT')))
        ) {
            throw $this->createNotFoundException('Ticket[id=' . $id . '] inexistant.');
        }

        $ticket->setClosed(1);
        $em->persist($ticket);
        $em->flush();

        return $this->redirect($this->generateUrl('awhs_crm_show', array('id' => $ticket->getId())));
        return $this->redirect($this->generateUrl('awhs_crm_homepage', array()));
    }
}
