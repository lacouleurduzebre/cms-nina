<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-08-27
 * Time: 14:03
 */

namespace App\DataFixtures;


use App\Controller\SEOController;
use App\Entity\Bloc;
use App\Entity\Categorie;
use App\Entity\Page;
use App\Entity\SEOCategorie;
use App\Entity\SEOPage;
use App\Entity\SEOTypeCategorie;
use App\Entity\TypeCategorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RazSEOFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Pages
        $repoPage = $manager->getRepository(Page::class);
        $repoBloc = $manager->getRepository(Bloc::class);
        $pages = $repoPage->findAll();

        foreach($pages as $page){
            $SEO = new SEOPage();

            $description = $titre = $page->getTitre();

            $blocTexte = $repoBloc->premierBlocTexte($page);
            if($blocTexte) {
                $description = strip_tags($blocTexte[0]->getContenu()['texte']);
            }

            $repoSEOPage = $manager->getRepository(SEOPage::class);
            $url = SEOController::slugify($titre);
            while($repoSEOPage->findOneBy(array('url' => $url))){
                $url .= '-copie';
            }

            $this->setSEO($SEO, $titre, $url, $description);
            $manager->persist($SEO);

            $page->setSEO($SEO);
            $manager->persist($page);

            $manager->flush();
        }

        //Catégories
        $repoCategorie = $manager->getRepository(Categorie::class);
        $categories = $repoCategorie->findAll();

        foreach($categories as $categorie){
            $SEO = new SEOCategorie();

            $description = $titre = $categorie->getNom();

            $repoSEOCategorie = $manager->getRepository(SEOCategorie::class);
            $url = SEOController::slugify($titre);
            while($repoSEOCategorie->findOneBy(array('url' => $url))){
                $url .= '-copie';
            }

            $this->setSEO($SEO, $titre, $url, $description);
            $manager->persist($SEO);

            $categorie->setSEO($SEO);
            $manager->persist($categorie);

            $manager->flush();
        }

        //Types de catégories
        $repoTypeCategorie = $manager->getRepository(TypeCategorie::class);
        $typesCategories = $repoTypeCategorie->findAll();

        foreach($typesCategories as $typeCategorie){
            $SEO = new SEOTypeCategorie();

            $description = $titre = $typeCategorie->getNom();

            $repoSEOTypeCategorie = $manager->getRepository(SEOTypeCategorie::class);
            $url = SEOController::slugify($titre);
            while($repoSEOTypeCategorie->findOneBy(array('url' => $url))){
                $url .= '-copie';
            }

            $this->setSEO($SEO, $titre, $url, $description);
            $manager->persist($SEO);

            $typeCategorie->setSEO($SEO);
            $manager->persist($typeCategorie);

            $manager->flush();
        }
    }

    private function setSEO($SEO, $titre, $url, $description){
        $SEO->setMetaTitre(substr($titre, 0, 65));
        $SEO->setMetaDescription(substr($description, 0, 150));
        $SEO->setUrl(substr($url, 0, 75));
    }
}