<?php

namespace Rottenwood\KingdomBundle\Service;

use Monolog\Logger;
use Rottenwood\KingdomBundle\Entity\Infrastructure\InventoryItemRepository;
use Rottenwood\KingdomBundle\Entity\Infrastructure\ItemRepository;
use Rottenwood\KingdomBundle\Entity\Infrastructure\RoomRepository;
use Rottenwood\KingdomBundle\Entity\Infrastructure\HumanRepository;
use Rottenwood\KingdomBundle\Entity\Room;
use Predis\Client as RedisClient;
use Symfony\Component\HttpKernel\KernelInterface;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{

    private $kernel;
    private $redis;
    private $logger;
    private $humanRepository;
    private $inventoryItemRepository;
    private $roomRepository;
    public $itemRepository;

    protected function setUp()
    {
        $this->kernel = \Phake::mock(KernelInterface::class);
        $this->redis = \Phake::mock(RedisClient::class);
        $this->logger = \Phake::mock(Logger::class);
        $this->humanRepository = \Phake::mock(HumanRepository::class);
        $this->inventoryItemRepository = \Phake::mock(InventoryItemRepository::class);
        $this->roomRepository = \Phake::mock(RoomRepository::class);
        $this->itemRepository = \Phake::mock(ItemRepository::class);
    }

    /** @test */
    public function transliterate_GivenEnglishName_ReturnsRussianTranslation()
    {
        $userService = $this->createUserService();

        foreach ($this->getNames() as $englishName => $russianName) {
            $this->assertEquals($userService->transliterate($englishName), $russianName);
        }
    }

    /** @test */
    public function getStartRoom_GivenRoomRepositoryWithDefaultRoom_ReturnsDefaultRoom()
    {
        $this->givenRoomRepositoryWithDefaultRoom();

        $userService = $this->createUserService();

        $this->assertInstanceOf(Room::class, $userService->getStartRoom());
    }

    /**
     * @return UserService
     */
    private function createUserService()
    {
        return new UserService($this->kernel,
            $this->redis,
            $this->logger,
            $this->humanRepository,
            $this->inventoryItemRepository,
            $this->roomRepository,
            $this->itemRepository
        );
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return [
            'Tester'                                 => 'Тестер',
            'Paul'                                   => 'Паул',
            'Ringo'                                  => 'Райнго',
            'John'                                   => 'Джохн',
            'George'                                 => 'Георге',
            'NameWithNumbers123'                     => 'Намевайтхнумберс',
            'NameWithSpecialChars!@#$%^&*()"№;:?*[]' => 'Намевайтхспецайалцхарс',
            'КириллическоеИмяСРазнымиРегистрами'     => 'Кириллическоеимясразнымирегистрами',
            'КириллическоеAndИногдаLatinskoeImya'    => 'Кириллическоеандиногдалатайнскоеаймйа',
        ];
    }

    private function givenRoomRepositoryWithDefaultRoom()
    {
        \Phake::when($this->roomRepository)
            ->findOneByXandY(\Phake::anyParameters())
            ->thenReturn(\Phake::mock(Room::class));
    }
}
