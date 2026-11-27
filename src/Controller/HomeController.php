<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Trip;
use App\Entity\User;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(#[CurrentUser] User $user, TripRepository $tripRepository): Response
    {
        $trips = $tripRepository->findForUser($user);

        $upcoming = array_values(array_filter($trips, static fn (Trip $t): bool => !$t->isPast()));
        $past = array_reverse(array_values(array_filter($trips, static fn (Trip $t): bool => $t->isPast())));

        return $this->render('home/index.html.twig', [
            'upcomingTrips' => $upcoming,
            'pastTrips' => $past,
            'ownedCount' => \count(array_filter($trips, static fn (Trip $t): bool => $t->isOwnedBy($user))),
        ]);
    }
}
