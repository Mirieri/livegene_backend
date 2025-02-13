<?php

namespace App\Tests\API;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\DataFixtures\Test\UserFixtures;
use App\Entity\PartnershipType;

class PartnershipTypeAPITest extends WebTestCase
{
    private $fixtures = null;
    private $client;

    public function setUp()
    {
        $this->fixtures = $this->loadFixtures([
            'App\DataFixtures\Test\UserFixtures',
            'App\DataFixtures\PartnershipTypeFixtures',
        ])->getReferenceRepository();
        $username = $this->fixtures->getReference('user')->getUsername();
        $credentials = [
            'username' => $username,
            'password' => UserFixtures::PASSWORD
        ];
        $this->client = $this->makeClient($credentials);
        $this->em = self::$container->get('doctrine.orm.entity_manager');
    }

    public function testGetCollectionIsAvailable()
    {
        $this->client->request('GET', '/api/partnership_types', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertSame(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json; charset=utf-8'
            )
        );
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(5, $data);
    }

    public function testPostIsNotAllowed()
    {
        $this->client->request('POST', '/api/partnership_types', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertSame(
            Response::HTTP_METHOD_NOT_ALLOWED,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testGetItemIsAvailable()
    {
        $partnership_type = $this->getPartnershipType();
        $this->client->request('GET', sprintf('/api/partnership_types/%s', $partnership_type), [], [], [
            'HTTP_ACCEPT' => 'application/json'
        ]);
        $this->assertSame(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json; charset=utf-8'
            )
        );
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('description', $data);
        $this->assertSame(
            $data,
            [
                'id' => 1,
                'description' => 'Unspecified',
            ]
        );
    }

    public function testPutIsNotAllowed()
    {
        $partnership_type = $this->getPartnershipType();
        $this->client->request('PUT', sprintf('/api/partnership_types/%s', $partnership_type), [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertSame(
            Response::HTTP_METHOD_NOT_ALLOWED,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testDeleteIsNotAllowed()
    {
        $partnership_type = $this->getPartnershipType();
        $this->client->request('DELETE', sprintf('/api/partnership_types/%s', $partnership_type), [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertSame(
            Response::HTTP_METHOD_NOT_ALLOWED,
            $this->client->getResponse()->getStatusCode()
        );
    }

    private function getPartnershipType()
    {
        return $this->em->getRepository(PartnershipType::class)->findOneByDescription('Unspecified')->getId();
    }
}
