<?php

namespace App\DataFixtures;

use App\Entity\Bloc;
use App\Entity\Categorie;
use App\Entity\Langue;
use App\Entity\Menu;
use App\Entity\MenuPage;
use App\Entity\Page;
use App\Entity\SEO;
use App\Entity\TypeCategorie;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class BlocsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $repoUtilisateur = $manager->getRepository(Utilisateur::class);
        $utilisateur = $repoUtilisateur->find(1);

        $date = new \DateTime();

        $repoLangue = $manager->getRepository(Langue::class);
        $langue = $repoLangue->findOneBy(array('defaut' => true));

        $repoMenu = $manager->getRepository(Menu::class);
        $menu = $repoMenu->findOneBy(array('langue' => $langue));

        //Page
        $seo = new SEO();
        $seo->setUrl('tous-les-blocs')
            ->setMetaTitre('Blocs')
            ->setMetaDescription('Blocs');
        $manager->persist($seo);

        $page = new Page();
        $page->setTitre('Tous les blocs')
            ->setTitreMenu('Tous les blocs')
            ->setAuteur($utilisateur)
            ->setAuteurDerniereModification($utilisateur)
            ->setDateCreation($date)
            ->setDatePublication($date)
            ->setLangue($langue)
            ->setSEO($seo);
        $manager->persist($page);
        $manager->flush();

        $menuPage = new MenuPage();
        $menuPage->setPosition(0)
            ->setMenu($menu)
            ->setPage($page);
        $manager->persist($menuPage);

        //Blocs
        $image = [
            'image' => '/assets/img/logoNina.png',
            'description' => 'CMS Nina'
        ];

            //Accordéon
        $blocAccordeon = new Bloc();
        $blocAccordeon->setType('Accordeon')
            ->setPosition(0)
            ->setPage($page)
            ->setContenu([
                'sections' => [
                    [
                        'position' => 0,
                        'titre' => 'Section 1',
                        'texte' => 'Lorem Elsass ipsum morbi hoplageiss geht\'s ch\'ai gal commodo messti de Bischheim und et Richard Schirmeck hopla knack flammekueche s\'guelt suspendisse Wurschtsalad amet geïz sit sagittis id, Huguette baeckeoffe Pellentesque ac libero, Mauris tchao bissame condimentum jetz gehts los chambon eget yeuh.'
                    ],
                    [
                        'position' => 1,
                        'titre' => 'Lorem Elsass ipsum morbi hoplageiss',
                        'texte' => 'Lorem Elsass ipsum morbi hoplageiss geht\'s ch\'ai gal commodo messti de Bischheim und et Richard Schirmeck hopla knack flammekueche s\'guelt suspendisse Wurschtsalad amet geïz sit sagittis id, Huguette baeckeoffe Pellentesque ac libero, Mauris tchao bissame condimentum jetz gehts los chambon eget yeuh.'
                    ]
                ]
            ]);
        $manager->persist($blocAccordeon);

            //Annuaire
        $blocAnnuaire = new Bloc();
        $blocAnnuaire->setType('Annuaire')
            ->setPosition(1)
            ->setPage($page)
            ->setContenu([

            ]);
        $manager->persist($blocAnnuaire);

            //Bouton
        $blocBouton = new Bloc();
        $blocBouton->setType('Bouton')
            ->setPosition(2)
            ->setPage($page)
            ->setContenu([
                'lien' => '#',
                'texte' => 'Lire la suite',
                'titre' => 'Lire la suite'
            ]);
        $manager->persist($blocBouton);

            //Catégorie
        $typeCategorie = new TypeCategorie();
        $typeCategorie->setLangue($langue)
            ->setUrl('type-de-categorie')
            ->setNom('Type de catégorie');
        $manager->persist($typeCategorie);

        $categorie = new Categorie();
        $categorie->setNom('Catégorie de test')
            ->setUrl('categorie-de-test')
            ->setLangue($langue)
            ->setTypeCategorie($typeCategorie);
        $manager->persist($categorie);
        $manager->flush();

        $repoPage = $manager->getRepository(Page::class);
        $pages = $repoPage->findBy(array('langue' => $langue), array('titre' => 'ASC'), 6);

        foreach($pages as $item){
            $item->addCategory($categorie);
            $manager->persist($item);
        }

        $blocCategorie = new Bloc();
        $blocCategorie->setType('Categorie')
            ->setPosition(3)
            ->setPage($page)
            ->setContenu([
                'categorie' => $categorie->getId()
            ]);
        $manager->persist($blocCategorie);

            //Formulaire
        $blocFormulaire = new Bloc();
        $blocFormulaire->setType('Formulaire')
            ->setPosition(4)
            ->setPage($page)
            ->setContenu([
                'destinataires' => [
                    'blabla@blabla.com'
                ],
                'objet' => "Demande de contact",
                'messageConfirmation' => "Merci pour votre message, nous vous recontacterons dans les meilleurs délais.",
                'submit' => "Envoyer",
                'champs' => [
                    [
                        'type' => 'text',
                        'position' => 0,
                        'label' => 'Champ texte'
                    ],
                    [
                        'type' => 'select',
                        'position' => 3,
                        'label' => 'Select',
                        'choix' => [
                            '1', '2', '3', '5', '10', '+ de 15', 'Lorem schnapsum'
                        ]
                    ],
                    [
                        'type' => 'checkbox',
                        'position' => 3,
                        'label' => 'Checkbox',
                        'choix' => [
                            '1', '2', 'Lorem schnapsum', 'Lorem schnapsum schnapsum'
                        ]
                    ],
                    [
                        'type' => 'radio',
                        'position' => 3,
                        'label' => 'Radio',
                        'choix' => [
                            '1', '2', 'Lorem schnapsum', 'Lorem schnapsum schnapsum'
                        ]
                    ],
                    [
                        'type' => 'textarea',
                        'position' => 4,
                        'label' => 'Textarea',
                    ]
                ]
            ]);
        $manager->persist($blocFormulaire);

            //Galerie
        $blocGalerie = new Bloc();
        $blocGalerie->setType('Galerie')
            ->setPosition(5)
            ->setPage($page)
            ->setContenu([
                'affichage' => 'lightbox',
                'images' => [
                    [
                        'image' => $image,
                        'position' => 0
                    ],
                    [
                        'image' => $image,
                        'position' => 0
                    ]
                ]
            ]);
        $manager->persist($blocGalerie);

            //Grille
        $blocGrille = new Bloc();
        $blocGrille->setType('Grille')
            ->setPosition(6)
            ->setPage($page)
            ->setContenu([
                'nbColonnes' => 3,
                'cases' => [
                    [
                        'position' => 0,
                        'image' => $image,
                        'texte' => 'Lorem Elsass ipsum morbi hoplageiss geht\'s ch\'ai gal commodo messti de Bischheim und et Richard Schirmeck hopla knack'
                    ],
                    [
                        'position' => 1,
                        'titre' => 'Lorem Elsass ipsum morbi hoplageiss',
                        'image' => $image,
                        'texte' => 'Lorem Elsass ipsum morbi hoplageiss geht\'s ch\'ai gal commodo messti de Bischheim und et Richard Schirmeck hopla knack'
                    ],
                    [
                        'position' => 2,
                        'page' => $page->getId()
                    ]
                ]
            ]);
        $manager->persist($blocGrille);

            //Image
        $blocImage = new Bloc();
        $blocImage->setType('Image')
            ->setPosition(7)
            ->setPage($page)
            ->setContenu($image);
        $manager->persist($blocImage);

            //LEI
        $blocLEI = new Bloc();
        $blocLEI->setType('LEI')
            ->setPosition(8)
            ->setPage($page)
            ->setContenu([
                'flux' => 'http://apps.tourisme-alsace.info/xml/exploitation/listeproduits.asp?latable=&user=2001680&pwkey=5456160c226f4eb91259a727adf07ff3&lxml=sit%5Flistecomplete&SCHEMA=WEBACCESS&leschamps=Produit%2CNom%2CADRPROD%5FLIBELLE%5FCOMMUNE%2CAcompte%2CAdresse%2CAdresse+personne+en+charge%2CADRPEC%5FCOMPL%5FADRESSE%2CADRPEC%5FCP%2CADRPEC%5FDISTRI%5FSPE%2CADRPEC%5FEMAIL%2CADRPEC%5FFAX%2CADRPEC%5FLIBELLE%5FCOMMUNE%2CADRPEC%5FLIB%5FVOIE%2CADRPEC%5FNUM%5FVOIE%2CADRPEC%5FPAYS%2CADRPEC%5FTEL%2CADRPEC%5FTEL2%2CADRPEC%5FURL%2CADRPREST%5FCOMPL%5FADRESSE%2CADRPREST%5FCP%2CADRPREST%5FDISTRI%5FSPE%2CADRPREST%5FEMAIL%2CADRPREST%5FFAX%2CADRPREST%5FLIBELLE%5FCOMMUNE%2CADRPREST%5FLIB%5FVOIE%2CADRPREST%5FNUMERO%2CADRPREST%5FNUM%5FVOIE%2CADRPREST%5FPAYS%2CADRPREST%5FTEL%2CADRPREST%5FTEL2%2CADRPREST%5FURL%2CADRPROD%5FCOMPL%5FADRESSE%2CADRPROD%5FCP%2CADRPROD%5FDISTRI%5FSPE%2CADRPROD%5FEMAIL%2CADRPROD%5FFAX%2CADRPROD%5FLIB%5FVOIE%2CADRPROD%5FNUM%5FVOIE%2CADRPROD%5FPAYS%2CADRPROD%5FTEL%2CADRPROD%5FTEL2%2CADRPROD%5FURL%2CCivilit%E9+personne+en+charge%2CCivilit%E9+responsable%2CCommentaire%2CCommentaireinterne%2CCommentaireL1%2CCOMMENTAIREHTML%2CDocumentationF%2CDocumentationL1%2CDocumentationL2%2CNom+personne+en+charge%2CRaisonSoc+responsable%2CNom+responsable%2CPr%E9nom+personne+en+charge%2CPr%E9nom+responsable%2CPrestataire%2CPREST%5FCIVILITE%2CPREST%5FNADRESSE%2CPREST%5FNOM%2CPREST%5FNOM%5FRESP%2CPREST%5FPRENOM%5FRESP%2CR%E9f%E9rence%2CGEOREFTYPE%2CType+de+produit%2CTYPE%5FNOM%2CValable+depuis%2CValable+jusqu%27%27%E0%2CTVA+produit%2CMAXMAJ&lescritex=1900480%2C1900661%2C1900752%2C1900066%2C1900793%2C1901124%2C1900631%2C1900563%2C1900504%2C1900537%2C1900768%2C1900593%2C1900433%2C1900769%2C1900536%2C1900840%2C1900594%2C1900535%2C1900590%2C1900839%2C1900529%2C1900635%2C1900852%2C1900851%2C1900977%2C1900955%2C1900592%2C1900533%2C1900595%2C1900591%2C1900531%2C1900532%2C1900956%2C1900596%2C1901188%2C1900807%2C1900406%2C1900421%2C1900603%2C1900751%2C1900781%2C1900858%2C1901181%2C1901182%2C1901080%2C1900828%2C1900797%2C1901216%2C1901217%2C1901218%2C1901219%2C1900838%2C1900404%2C1900844%2C1900268%2C1900267%2C1900954%2C1900564%2C1901120%2C1900067&rfrom=1&rto=20&urlnames=tous&lestris=&champstri=&lentit=&lesvalid=%7C&leshoraires=%7C&lesheures=%7C&lesdispos=%7C&decompte=Y&contenu=&panprem=&nompanier=&panseul=&typsor=2&libtext=&delaiperemption=&clause=2001680000001',
                'pagination' => [
                    1
                ],
                'resultatsParPage' => 6
            ]);
        $manager->persist($blocLEI);

            //Menu
        $repoMenu = $manager->getRepository(Menu::class);
        $menu = $repoMenu->findOneBy(array('langue' => $langue));

        $blocMenu = new Bloc();
        $blocMenu->setType('Menu')
            ->setPosition(9)
            ->setPage($page)
            ->setContenu([
                'menu' => $menu->getId()
            ]);
        $manager->persist($blocMenu);

            //Menu des langues
        $blocMenuLangues = new Bloc();
        $blocMenuLangues->setType('MenuLangues')
            ->setPosition(10)
            ->setPage($page)
            ->setContenu([]);
        $manager->persist($blocMenuLangues);

            //Partage
        $blocPartage = new Bloc();
        $blocPartage->setType('Partage')
            ->setPosition(11)
            ->setPage($page)
            ->setContenu([
                'facebook' => [1],
                'twitter' => [1],
                'linkedIn' => [1]
            ]);
        $manager->persist($blocPartage);

            //Plan du site
        $blocPlanDuSite = new Bloc();
        $blocPlanDuSite->setType('PlanDuSite')
            ->setPosition(12)
            ->setPage($page)
            ->setContenu([]);
        $manager->persist($blocPlanDuSite);

            //Recherche
        $blocRecherche = new Bloc();
        $blocRecherche->setType('Recherche')
            ->setPosition(13)
            ->setPage($page)
            ->setContenu([]);
        $manager->persist($blocRecherche);

            //Réseaux sociaux
        $blocRS = new Bloc();
        $blocRS->setType('ReseauxSociaux')
            ->setPosition(14)
            ->setPage($page)
            ->setContenu([
                'facebook' => [1],
                'twitter' => [1],
                'linkedIn' => [1],
                'instagram' => [1],
                'youtube' => [1],
                'facebookUrl' => '#',
                'twitterUrl' => '#',
                'linkedInUrl' => '#',
                'instagramUrl' => '#',
                'youtubeUrl' => '#',
            ]);
        $manager->persist($blocRS);

            //Rubrique
        $blocRubrique = new Bloc();
        $blocRubrique->setType('Rubrique')
            ->setPosition(15)
            ->setPage($page)
            ->setContenu([]);
        $manager->persist($blocRubrique);

            //Slider
        $blocSlider = new Bloc();
        $blocSlider->setType('Slider')
            ->setPosition(16)
            ->setPage($page)
            ->setContenu([
                'nbSlides' => 1,
                'autoplay' => 0,
                'fleches' => 1,
                'points' => 1,
                'Slide' => [
                    [
                        'image' => $image,
                        'position' => 0,
                        'texte' => 'Lorem Elsass ipsum nullam Chulien  und morbi DNA, id, bissame salu so tellus flammekueche chambon amet',
                        'lien' => '#'
                    ],
                    [
                        'image' => $image,
                        'position' => 1
                    ]
                ]
            ]);
        $manager->persist($blocSlider);

        $blocCaroussel = new Bloc();
        $blocCaroussel->setType('Slider')
            ->setPosition(16)
            ->setPage($page)
            ->setContenu([
                'nbSlides' => 3,
                'autoplay' => 0,
                'fleches' => 1,
                'points' => 1,
                'Slide' => [
                    [
                        'image' => $image,
                        'position' => 0,
                        'texte' => 'Lorem Elsass ipsum nullam Chulien  und morbi DNA, id, bissame salu so tellus flammekueche chambon amet',
                        'lien' => '#'
                    ],
                    [
                        'image' => $image,
                        'position' => 1
                    ],
                    [
                        'image' => $image,
                        'position' => 3,
                        'texte' => 'Lorem Elsass ipsum nullam Chulien  und morbi DNA, id, bissame salu so tellus flammekueche chambon amet',
                        'lien' => '#'
                    ],
                    [
                        'image' => $image,
                        'position' => 4
                    ]
                ]
            ]);
        $manager->persist($blocCaroussel);

            //Titre
        $blocTitre = new Bloc();
        $blocTitre->setType('Titre')
            ->setPosition(17)
            ->setPage($page)
            ->setContenu([
                'texte' => 'Lorem Elsass ipsum nullam Chulien',
                'balise' => 'h2'
            ]);
        $manager->persist($blocTitre);

            //Type de catégorie
        $blocTypeCategorie = new Bloc();
        $blocTypeCategorie->setType('TypeCategorie')
            ->setPosition(18)
            ->setPage($page)
            ->setContenu([
                'typeCategorie' => $typeCategorie->getId(),
                'affichage' => 'categories'
            ]);
        $manager->persist($blocTypeCategorie);

        $blocTypeCategorie2 = new Bloc();
        $blocTypeCategorie2->setType('TypeCategorie')
            ->setPosition(19)
            ->setPage($page)
            ->setContenu([
                'typeCategorie' => $typeCategorie->getId(),
                'affichage' => 'pages'
            ]);
        $manager->persist($blocTypeCategorie2);

            //Vidéo
        $blocVideo = new Bloc();
        $blocVideo->setType('Video')
            ->setPosition(20)
            ->setPage($page)
            ->setContenu([
                'video' => 'https://www.youtube.com/watch?v=r8yqLJlzQlQ'
            ]);
        $manager->persist($blocVideo);

            //Vidéos
        $blocVideos = new Bloc();
        $blocVideos->setType('Videos')
            ->setPosition(21)
            ->setPage($page)
            ->setContenu([]);
        $manager->persist($blocVideos);

        $manager->flush();
    }
}
