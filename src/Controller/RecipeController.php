<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as ConfigurationSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index', methods:['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );    

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
       
        ]);
    }



    #[Route('/recette/publique', 'recipe.index_public', methods:['GET'])]
    public function indexPublic(PaginatorInterface $paginator, 
                                RecipeRepository $repository, 
                                Request $request) : Response
    {
        $cache = new FilesystemAdapter();
        $data = $cache->get('recipes', function(ItemInterface $item) use($repository) {
            $item->expiresAfter(15);
            return $repository->findPublicRecipe(null);
        });

        $recipes = $paginator->paginate(
            $repository->findPublicRecipe(null),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index_public.html.twig', [
            'recipes' =>$recipes
        ]);
    }

    #[Security("is_granted('ROLE_USER') and recipe.getIsPublic() === true")]
    #[Route('/recette/{id}', 'recipe.show', methods:['GET', 'POST'])]
    public function show(Recipe $recipe, Request $request, MarkRepository $markRepository, EntityManagerInterface $manager): Response
    {
        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $mark->setUser($this->getUser())
                ->setRecipe($recipe);

            $existingMark = $markRepository->findOneBy([
                'user'=> $this->getUser(),
                'recipe' =>$recipe
            ]);
            if (!$existingMark) 
            {
                $manager->persist($mark);
            }
            else 
            {
                $existingMark->setMark(
                    $form->getData()->getMark()
                );

            }
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre note a bien été pris en compte.'
            );
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }
        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/recette/creation', name:'recipe.new', methods:['GET', 'POST'])]  
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $recipe = new Recipe;
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid())
            {
                $recipe = $form->getData();
                $recipe->setUser($this->getUser());
                $manager->persist($recipe);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre recette a été crée avec succès!'
                );

                return $this->redirectToRoute('recipe.index');
            }
        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/recette/edition/{id}', 'recipe.edit', methods:['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user=== recipe.getUser()")]
    public function edit(Recipe $recipe,  Request $request, EntityManagerInterface $manager) : Response
    {
       
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $recipe = $form->getData(); 
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifier avec succès!'
            );

            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()           
        ]);
    }

    #[Route('/recipe/suppression/{id}', 'recipe.delete', methods:['GET'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe) : Response
    {
        if(!$recipe)
        {
            $this->addFlash(
                'warning',
                'Votre recette n\'a pas été trouvé!'
            );
        }
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimé avec succès!'
        );

        return $this->redirectToRoute('recipe.index');

    }


}
