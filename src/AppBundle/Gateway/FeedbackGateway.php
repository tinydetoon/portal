<?php

namespace AppBundle\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

use AppBundle\Model\Project\{
    Feedback,
    Evolution,
    Bug
};

class FeedbackGateway
{
    /** @var Client **/
    protected $client;
    
    /**
     * @param string $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->client = new Client(['base_uri' => $apiUrl]);
    }
    
    /**
     * @param string $title
     * @param string $description
     * @param string $status
     * @param string $authorName
     * @param string $authorEmail
     * @return Response
     */
    public function createEvolution($title, $description, $status, $authorName, $authorEmail)
    {
        return $this->client->post('/evolutions', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'author' => [
                    'name' => $authorName,
                    'email' => $authorEmail
                ],
            ])
        ]);
    }
    
    /**
     * @param Evolution $evolution
     * @return Response
     */
    public function updateEvolution(Evolution $evolution)
    {
        return $this->client->put('/evolutions/' . $evolution->getId(), [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'title' => $evolution->getTitle(),
                'description' => $evolution->getDescription(),
                'status' => $evolution->getStatus(),
                'author' => [
                    'name' => $evolution->getAuthor()->getUsername(),
                    'email' => $evolution->getAuthor()->getEmail()
                ],
            ])
        ]);
    }
    
    /**
     * @param string $id
     * @return Response
     */
    public function getEvolution($id)
    {
        return $this->client->get("/evolutions/$id");
    }
    
    /**
     * @return Response
     */
    public function getEvolutions()
    {
        return $this->client->get('/evolutions');
    }
    
    /**
     * @param string $title
     * @param string $description
     * @param string $status
     * @param string $authorName
     * @param string $authorEmail
     * @return Response
     */
    public function createBug($title, $description, $status, $authorName, $authorEmail)
    {
        return $this->client->post('/bugs', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'author' => [
                    'name' => $authorName,
                    'email' => $authorEmail
                ],
            ])
        ]);
    }
    
    /**
     * @param Bug $bug
     * @return Response
     */
    public function updateBug(Bug $bug)
    {
        return $this->client->put('/bugs/' . $bug->getId(), [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'title' => $bug->getTitle(),
                'description' => $bug->getDescription(),
                'status' => $bug->getStatus(),
                'author' => [
                    'name' => $bug->getAuthor()->getUsername(),
                    'email' => $bug->getAuthor()->getEmail()
                ],
            ])
        ]);
    }
    
    /**
     * @param string $id
     * @return Response
     */
    public function getBug($id)
    {
        return $this->client->get("/bugs/$id");
    }
    
    /**
     * @return Response
     */
    public function getBugs()
    {
        return $this->client->get('/bugs');
    }
    
    public function createComment($feedbackId, $feedbackType, $content, $authorName, $authorEmail)
    {
        $endpoint = ($feedbackType === Feedback::TYPE_BUG) ? 'bugs' : 'evolutions';
        return $this->client->post("/$endpoint/$feedbackId/comments", [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'content' => $content,
                'author' => [
                    'name' => $authorName,
                    'email' => $authorEmail
                ],
            ])
        ]);
    }
    
    /**
     * @return Response
     */
    public function getLabels()
    {
        return $this->client->get('/labels');
    }
    
    /**
     * @param string $name
     * @param string $color
     * @return Response
     */
    public function createLabel($name, $color)
    {
        return $this->client->post("/labels", [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'name' => $name,
                'color' => $color
            ])
        ]);
    }
    
    /**
     * @param Bug $bug
     * @param string $labelId
     * @return Response
     */
    public function addLabelToBug(Bug $bug, $labelId)
    {
        return $this->client->post("/bugs/{$bug->getId()}/labels/$labelId");
    }
    
    /**
     * @param Bug $bug
     * @param string $labelId
     * @return Response
     */
    public function removeLabelFromBug(Bug $bug, $labelId)
    {
        return $this->client->delete("/bugs/{$bug->getId()}/labels/$labelId");
    }
    
    /**
     * @param Evolution $evolution
     * @param string $labelId
     * @return Response
     */
    public function addLabelToEvolution(Evolution $evolution, $labelId)
    {
        return $this->client->post("/evolutions/{$evolution->getId()}/labels/$labelId");
    }
    
    /**
     * @param Evolution $evolution
     * @param string $labelId
     * @return Response
     */
    public function removeLabelFromEvolution(Evolution $evolution, $labelId)
    {
        return $this->client->delete("/evolutions/{$evolution->getId()}/labels/$labelId");
    }
}