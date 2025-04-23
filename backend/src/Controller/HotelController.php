<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Form\HotelType;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;




#[Route('/api/hotel')]
final class HotelController extends AbstractController
{
    #[Route(name: 'app_hotel_index', methods: ['GET'])]
    public function index(HotelRepository $hotelRepository): Response
    {
        return $this->render('hotel/index.html.twig', [
            'hotels' => $hotelRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hotel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hotel);
            $entityManager->flush();

            return $this->redirectToRoute('app_hotel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hotel/new.html.twig', [
            'hotel' => $hotel,
            'form' => $form,
        ]);
    }

    #[Route('/search-hotels', name: 'api_search_hotels', methods: ['GET'])]
    public function search(Request $request, HttpClientInterface $client, EntityManagerInterface $em): JsonResponse
    {
        $arrivalDate = $request->query->get('arrival_date');
        $departureDate = $request->query->get('departure_date');

        if (!$arrivalDate || !$departureDate) {
            return $this->json([
                'error' => 'Missing arrival_date or departure_date'
            ], 400);
        }

        $response = $client->request('GET', 'https://booking-com15.p.rapidapi.com/api/v1/hotels/searchHotels', [
            'headers' => [
                'x-rapidapi-key' => 'd707def6d3msh8f4a7503747f953p1bd476jsn3728dfc8691f',
                'x-rapidapi-host' => 'booking-com15.p.rapidapi.com',
            ],
            'query' => [
                'dest_id' => '-1448468',
                'search_type' => 'CITY',
                'adults' => $request->query->get('adults', '1'),
                'children_age' => $request->query->get('children_age', '0'),
                'room_qty' => $request->query->get('room_qty', '1'),
                'page_number' => '1',
                'units' => 'metric',
                'temperature_unit' => 'c',
                'languagecode' => 'en-us',
                'currency_code' => 'EUR',
                'location' => $request->query->get('location', 'FR'),
                'arrival_date' => $arrivalDate,
                'departure_date' => $departureDate,
            ],
        ]);

        $data = $response->toArray(false);

        if (!isset($data['data']['hotels'])) {
            return $this->json([
                'error' => 'No hotel data found'
            ], 500);
        }

        $hotels = [];

        foreach ($data['data']['hotels'] as $hotelData) {
            $hotel = new Hotel();
            $hotel->setName($hotelData['name'] ?? '');
            $hotel->setAddress($hotelData['address'] ?? '');
            $hotel->setCity($hotelData['cityName'] ?? 'Paris');
            $hotel->setCountry($hotelData['country_trans'] ?? 'France');
            $hotel->setDescription($hotelData['accessibilityLabel'] ?? $hotelData['district'] ?? '');
            $hotel->setImage($hotelData['property']['photoUrls'][0] ?? '');
            $hotel->setNumberId($hotelData['property']['id'] ?? '');

            $em->persist($hotel);
            $hotels[] = [
                'name' => $hotel->getName(),
                'address' => $hotel->getAddress(),
                'city' => $hotel->getCity(),
                'country' => $hotel->getCountry(),
                'description' => $hotel->getDescription(),
                'image' => $hotel->getImage(),
                'number_id' => $hotel->getNumberId(),
            ];
        }

        $em->flush();

        return $this->json($hotels);
    }



    #[Route('/{id}', name: 'app_hotel_show', methods: ['GET'])]
    public function show(Hotel $hotel): Response
    {
        return $this->render('hotel/show.html.twig', [
            'hotel' => $hotel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hotel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hotel $hotel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hotel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hotel/edit.html.twig', [
            'hotel' => $hotel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hotel_delete', methods: ['POST'])]
    public function delete(Request $request, Hotel $hotel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hotel->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($hotel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hotel_index', [], Response::HTTP_SEE_OTHER);
    }


}
