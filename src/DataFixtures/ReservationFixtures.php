<?php

namespace App\DataFixtures;

use App\Entity\PaymentDetails;
use App\Entity\Reservation;
use App\Entity\Review;
use App\Entity\Order;
use App\Entity\Invoice;
use DateTime;
use App\Repository\CustomerRepository;
use App\Repository\VehicleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;
    private CustomerRepository $customerRepository;
    private VehicleRepository $vehicleRepository;

    public function __construct(CustomerRepository $customerRepository, VehicleRepository $vehicleRepository) {
        $this->faker = Factory::create('es_ES');
        $this->customerRepository = $customerRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $customers = $this->customerRepository->findAll();
        $vehicles = $this->vehicleRepository->findAll();

        // Solo crear reservas si hay clientes y vehículos disponibles
        if (!empty($customers) && !empty($vehicles)) {
            // Seleccionar un subconjunto de vehículos para reservar
            $vehiclesToReserve = (count($vehicles) > 5) ? array_rand($vehicles, 5) : array_keys($vehicles);

            for ($i = 0; $i < 5 && $i < count($vehiclesToReserve); $i++) {
                $reservation = new Reservation();
                $startDate = $this->faker->dateTimeBetween('now', '+1 month');

                // Calcular la fecha de fin
                $endDate = clone $startDate;
                $endDate->modify('+1 day');

                $reservation->setStartDate($startDate);
                $reservation->setEndDate($endDate);
                $reservation->setTotalPrice($this->faker->numberBetween(100, 1000));
                $reservation->setDeleted(false);

                // Asignar un cliente y un vehículo a la reserva
                $randomCustomer = array_rand($customers);
                $customer = $customers[$randomCustomer];
                $reservation->setCustomer($customer);

                $vehicle = $vehicles[$vehiclesToReserve[$i]];
                $reservation->setVehicle($vehicle);

                // Crear la review aleatoria
                $review = new Review();
                $review->setComment($this->faker->sentence());
                $review->setRating($this->faker->numberBetween(1, 5));

                $endDateReservation = $reservation->getEndDate()->format('Y-m-d');
                $review->setDate($this->faker->dateTimeBetween($startDate, $endDateReservation));
                $reservation->addReview($review);

                // Crear PaymentDetails aleatorios
                $paymentDetails = new PaymentDetails();
                $paymentDetails->setPaymentMethod($this->faker->randomElement(['visa', 'mastercard']));
                $paymentDetails->setCardNumber($this->faker->creditCardNumber());
                $paymentDetails->setExpiryDate($this->faker->dateTimeBetween('now', '+5 years'));
                $paymentDetails->setCvv($this->faker->randomNumber(3));

                $reservation->setPaymentDetails($paymentDetails);

                // Crear Order
                $order = new Order();
                $order->setDeleted(false);
                $order->setState($this->faker->randomElement(['En proceso', 'Completado']));

                $manager->persist($order); // Persistir la orden antes de asociarla

                $reservation->setOrders($order);

                // Crear Invoice
                $invoice = new Invoice();
                $invoice->setDeleted(false);
                $invoice->setNumber($this->faker->numberBetween('200€', '1000€'));
                $invoice->setPrice($this->faker->numberBetween(100000, 1000000));
                $dateString = $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
                $date = DateTime::createFromFormat('Y-m-d', $dateString);
                $invoice->setDate($date);

                $manager->persist($invoice);

                $reservation->setInvoices($invoice);

                $manager->persist($reservation);
                $manager->persist($review);
                $manager->persist($paymentDetails);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [CustomerFixtures::class, VehicleFixtures::class];
    }
}
