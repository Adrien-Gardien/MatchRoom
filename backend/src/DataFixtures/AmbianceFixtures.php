<?php

namespace App\DataFixtures;

use App\Entity\Ambiance;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AmbianceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $ambiances = [
            'Unique' => 'Une ambiance rare et mémorable, idéale pour des séjours exceptionnels.',
            'Premium' => 'Un confort haut de gamme, pour une expérience luxueuse.',
            'Bien-être' => 'Repos, spa et relaxation sont au cœur de cette ambiance.',
            'Urbain' => 'En plein centre-ville, pour profiter de la vie citadine.',
            'Nature' => 'Entouré de verdure, parfait pour les amateurs de plein air.',
            'Romantique' => 'Un cadre intime pour des escapades à deux.',
            'Aventure' => 'Pour les esprits libres et les explorateurs.',
            'Historique' => 'Plongée dans l’histoire avec charme et authenticité.',
            'Familiale' => 'Pensée pour les enfants et les séjours en tribu.',
            'Zen' => 'Calme et sérénité pour se ressourcer.',
            'Montagne' => 'Air pur, randonnées et paysages spectaculaires.',
            'Plage' => 'Soleil, sable fin et brise marine.',
            'Design' => 'Une touche contemporaine pour les amoureux d’architecture.',
            'Culturelle' => 'Proche des musées, théâtres et événements locaux.',
            'Gastronomique' => 'Pour les gourmets en quête de découvertes culinaires.',
            'Festive' => 'Ambiance animée pour des vacances rythmées.',
            'Écologique' => 'Séjour responsable et respectueux de l’environnement.',
            'Sportive' => 'Activités et dynamisme au rendez-vous.',
            'Insolite' => 'Dormir dans un lieu inattendu et original.',
            'Rustique' => 'Authenticité et simplicité au cœur de la campagne.'
        ];

        foreach ($ambiances as $name => $description) {
            $ambiance = new Ambiance();
            $ambiance->setName($name);
            $ambiance->setDescription($description);

            $manager->persist($ambiance);
        }

        $manager->flush();
    }
}
