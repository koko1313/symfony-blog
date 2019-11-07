<?php


namespace App\Security;



use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter {

    const VIEW = "view";
    const EDIT = "edit";
    const DELETE = "delete";

    protected function supports($attribute, $subject) {
        if(!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if(!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        // Check if the user is the actual owner of the post
        if($subject->getUser() !== $user) {
            return false;
        }

        return true;
    }
}