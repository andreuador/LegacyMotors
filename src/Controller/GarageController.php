<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\InvoiceRepository;
use App\Repository\OrderRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GarageController extends AbstractController
{
    #[Route('/garages', name: 'app_garage')]
    public function index(InvoiceRepository $invoiceRepository, OrderRepository $orderRepository): Response
    {
        /*if ($this->isGranted('ROLE_ADMIN')) {
            $this->addFlash(
                'warning',
                "Sols els clients poden realitzar compres"
            );
            return $this->redirectToRoute('templates');
        }*/

        $login = $this->getUser();
        $customer = $login->getCustomer();

        $pendingOrder = $orderRepository->findOneBy(['state' => 'En proceso', 'customer' => $customer]);

        if (!$pendingOrder) {
            $vehicles = [];
        } else {
            $vehicles = $pendingOrder->getVehicles()->toArray();
        }

        $closedOrders = $orderRepository->findBy(['state' => 'Completado', 'customer' => $customer]);
        $userInvoices = $invoiceRepository->findBy(['customer' => $customer]);

        return $this->render('garage/index.html.twig', [
            'vehicles' => $vehicles,
            'invoices' => $userInvoices,
            'orders' => $closedOrders
        ]);
    }

    #[Route('/delete/{id}', name: 'app_garage_delete_vehicle')]
    public function remove($id, VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager): Response {
        $vehicleId = $id;
        $vehicle = $vehicleRepository->find($vehicleId);
        $vehicle->setVehicleOrder(null);

        $entityManager->persist($vehicle);
        $entityManager->flush();

        return $this->redirectToRoute('app_garage', [], Response::HTTP_SEE_OTHER);
    }
}
