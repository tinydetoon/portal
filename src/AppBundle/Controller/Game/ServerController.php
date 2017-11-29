<?php

namespace AppBundle\Controller\Game;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use AppBundle\Manager\Game\ServerManager;
use AppBundle\Entity\Game\Server;

class ServerController extends Controller
{
    /**
     * @Route("/admin/servers/new", name="new_game_server")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newServerAction()
    {
        return $this->render('admin/game/server/new.html.twig');
    }
    
    /**
     * @Route("/admin/servers", name="create_game_server")
     * @Method({"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function createServerAction(Request $request)
    {
        if (empty($name = $request->request->get('name'))) {
            throw new BadRequestHttpException('game.server.missing_name');
        }
        if (empty($description = $request->request->get('description'))) {
            throw new BadRequestHttpException('game.server.missing_description');
        }
        if (empty($startedAt = $request->request->get('started_at'))) {
            throw new BadRequestHttpException('game.server.missing_started_at');
        }
        if (empty($publicKey = $request->request->get('public_key'))) {
            throw new BadRequestHttpException('game.server.missing_public_key');
        }
        $this->get(ServerManager::class)->create(
            $name,
            $description,
            $request->request->get('banner', 'default.png'),
            $startedAt,
            $publicKey,
            Server::TYPE_MULTIPLAYER
        );
        return $this->redirectToRoute('admin_dashboard');
    }
    
    /**
     * @Route("/play/{server_id}", name="join_game")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     */
    public function joinServerAction(Request $request)
    {
        return new Response('ok');
    }
}