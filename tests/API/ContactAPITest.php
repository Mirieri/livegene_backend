<?php

namespace App\Tests\API;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\DataFixtures\Test\UserFixtures;

class ContactAPITest extends WebTestCase
{
    private $fixtures = null;
    private $client;

    public function setUp()
    {
        $this->fixtures = $this->loadFixtures([
            'App\DataFixtures\Test\UserFixtures',
            'App\DataFixtures\Test\ContactFixtures',
        ])->getReferenceRepository();
        $username = $this->fixtures->getReference('user')->getUsername();
        $credentials = [
            'username' => $username,
            'password' => UserFixtures::PASSWORD
        ];
        $this->client = $this->makeClient($credentials);
    }

    public function testGetCollectionIsAvailable()
    {
        $this->client->request('GET', '/api/contacts', [], [], [
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
        $this->assertCount(1, $data);
    }

    public function testPostIsNotAllowed()
    {
        $this->client->request('POST', '/api/contacts', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertSame(
            Response::HTTP_METHOD_NOT_ALLOWED,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testGetItemIsAvailable()
    {
        $contact = $this->getContact();
        $this->client->request('GET', sprintf('/api/contacts/%s', $contact), [], [], [
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
        $this->assertArrayHasKey('firstName', $data);
        $this->assertArrayHasKey('lastName', $data);
        $this->assertSame(
            $data,
            [
                'id' => 1,
                'title' => 'Dr.',
                'firstName' => 'Max',
                'lastName' => 'Mustermann'
            ]
        );
    }

    public function testPutIsNotAllowed()
    {
        $contact = $this->getContact();
        $this->client->request('PUT', sprintf('/api/contacts/%s', $contact), [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertSame(
            Response::HTTP_METHOD_NOT_ALLOWED,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testDeleteIsNotAllowed()
    {
        $contact = $this->getContact();
        $this->client->request('DELETE', sprintf('/api/contacts/%s', $contact), [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertSame(
            Response::HTTP_METHOD_NOT_ALLOWED,
            $this->client->getResponse()->getStatusCode()
        );
    }

    private function getContact()
    {
        return $this->fixtures->getReference('contact')->getId();
    }
}
