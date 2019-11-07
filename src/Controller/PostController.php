<?php


namespace App\Controller;

use App\Entity\Post;
use App\Security\PostVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("posts", name="posts_")
 */
class PostController extends AbstractController {

    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index() {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render("post/index.html.twig", ["posts" => $posts]);
    }

    /**
     * @Route("/form", name="form", methods="GET")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function form($post = null) {
        return $this->render("post/form.html.twig", ["post" => $post]);
    }

    /**
     * @Route("/form", methods="POST")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function store(Request $req) {
        $post = new Post();
        $post->setTitle($req->get("title"));
        $post->setDescription($req->get("description"));
        $post->setUser($this->getUser());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirectToRoute("posts_index");
    }

    /**
     * @Route("/edit/{id}", name="edit", methods="GET")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit($id) {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if(!$this->isGranted(PostVoter::VIEW, $post)) {
            throw new LogicException("You cannot perform this action");
        }

        return $this->form($post);
    }

    /**
     * @Route("/edit/{id}", methods="POST")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function update($id, Request $req) {
        $entityManager = $this->getDoctrine()->getManager();

        $post = $entityManager->getRepository(Post::class)->find($id);

        if(!$this->isGranted(PostVoter::EDIT, $post)) {
            throw new LogicException("You cannot perform this action");
        }

        $post->setTitle($req->get("title"));
        $post->setDescription($req->get("description"));

        $entityManager->flush();

        return $this->redirectToRoute("posts_index");
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function delete($id) {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if(!$this->isGranted(PostVoter::DELETE, $post)) {
            throw new LogicException("You cannot perform this action");
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute("posts_index");
    }

}