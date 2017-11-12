<?php

namespace AppBundle\Manager\Project;

use AppBundle\Gateway\FeedbackGateway;

use FOS\UserBundle\Doctrine\UserManager;

use AppBundle\Manager\NotificationManager;

use AppBundle\Model\Project\{Commentary, Feedback};


class CommentaryManager
{
    /** @var FeedbackGateway **/
    protected $feedbackGateway;
    /** @var NotificationManager **/
    protected $notificationManager;
    /** @var UserManager **/
    protected $userManager;
    
    /**
     * @param FeedbackGateway $feedbackGateway
     * @param NotificationManager $notificationManager
     * @param UserManager $userManager
     */
    public function __construct(FeedbackGateway $feedbackGateway, NotificationManager $notificationManager, UserManager $userManager)
    {
        $this->feedbackGateway = $feedbackGateway;
        $this->notificationManager = $notificationManager;
        $this->userManager = $userManager;
    }
    
    /**
     * @param string $feedbackId
     * @param string $feedbackType
     * @param string $content
     * @param Player $author
     * @return Response
     */
    public function create(Feedback $feedback, $content, Player $author)
    {
        $commentary = $this->format(json_decode($this
            ->feedbackGateway
            ->createCommentary($feedback->getId(), $feedback->getType(), $content, $author->getName(), $author->getBind())
            ->getBody()
        , true));
        
        $title = 'Nouveau commentaire';
        $content =
            "{$author->getName()} a posté un commentaire sur " .
            (($feedback->getType() === Feedback::TYPE_BUG) ? 'le bug ': 'l\'évolution ') .
            " \"{$feedback->getTitle()}\"."
        ;
        // We avoid sending notification to the comment author, whether he is the feedback author or not
        $players = [$author->getId()];
        if ($feedback->getAuthor()->getId() !== $author->getId() && $feedback->getAuthor()->getId() !== 0) {
            $players[] = $feedback->getAuthor()->getId();
            $this->notificationManager->create($feedback->getAuthor(), $title, $content);
        }
        foreach ($feedback->getCommentaries() as $comment) {
            $commentAuthor = $comment->getAuthor();
            
            if (in_array($commentAuthor->getId(), $players) || $commentAuthor->getId() === 0) {
                continue;
            }
            $players[] = $commentAuthor->getId();
            $this->notificationManager->create($commentAuthor, $title, $content);
        }
        return $commentary;
    }
    
    /**
     * @param type $data
     * @param type $getAuthor
     * @return type
     */
    public function format($data, $getAuthor = false)
    {
        return
            (new Commentary())
            ->setId($data['id'])
            ->setContent($data['content'])
            ->setAuthor($this->getAuthor($data['author']['username'], $getAuthor))
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setUpdatedAt(new \DateTime($data['updated_at']))
        ;
    }
    
    protected function getAuthor($name, $getAuthorData = false)
    {
        if ($getAuthorData === false) {
            return $name;
        }
        if (($author = $this->userManager->findUserByUsername($name)) === null) {
            return
                (new User())
                ->setUsername($name)
            ;
        }
        return $author;
    }
}