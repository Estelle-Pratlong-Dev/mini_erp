<?php

namespace App\Tests\Functional;

use App\Entity\Contact;
use App\Enum\TypeContact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Tests fonctionnels (base de test) : filtre soft-delete et validations métier.
 * Chaque test est isolé dans une transaction annulée à la fin.
 */
class SoftDeleteEtValidationTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $this->validator = $container->get(ValidatorInterface::class);
        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->rollback();
        parent::tearDown();
    }

    public function testElementSupprimeEstMasqueDesRequetes(): void
    {
        /** @var ContactRepository $repo */
        $repo = $this->em->getRepository(Contact::class);

        $contact = (new Contact())->setType(TypeContact::PARTICULIER)->setNom('Soft_'.uniqid());
        $this->em->persist($contact);
        $this->em->flush();
        $id = $contact->getId();

        self::assertNotNull($repo->find($id), 'Le contact actif doit être trouvé.');

        $contact->setSupprime(true);
        $this->em->flush();
        $this->em->clear();

        self::assertNull($repo->find($id), 'Un contact supprimé (soft delete) doit être masqué.');
    }

    public function testEntrepriseSansSiretEstInvalide(): void
    {
        $contact = (new Contact())->setType(TypeContact::ENTREPRISE)->setNom('SARL Test');

        $violations = $this->validator->validate($contact);

        self::assertGreaterThan(0, $violations->count());
        self::assertSame('siret', $violations->get(0)->getPropertyPath());
    }

    public function testEntrepriseAvecSiretEstValide(): void
    {
        $contact = (new Contact())
            ->setType(TypeContact::ENTREPRISE)
            ->setNom('SARL Test')
            ->setSiret('81234567800019');

        self::assertCount(0, $this->validator->validate($contact));
    }
}
