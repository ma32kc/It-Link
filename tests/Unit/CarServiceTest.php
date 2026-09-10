<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\Dto\CreateCarForm;
use App\Application\Exception\ValidationException;
use App\Application\Service\CarService;
use App\Domain\Entity\Car;
use App\Domain\Repository\CarRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class CarServiceTest extends TestCase
{
    public function testCreateWithoutOptionsPersistsCarAndReturnsIt(): void
    {
        $repository = $this->createMock(CarRepositoryInterface::class);
        $repository->expects(self::once())
            ->method('save')
            ->willReturnCallback(static function (Car $car): void {
                $car->assignId(42);
            });

        $service = new CarService($repository);
        $car = $service->create($this->makeForm());

        self::assertSame(42, $car->getId());
        self::assertSame('BMW X5 2020', $car->title);
        self::assertSame(2500000.0, $car->price);
        self::assertFalse($car->hasOption());
        self::assertNull($car->getOption());
    }

    public function testCreateWithOptionsBuildsFullEntity(): void
    {
        $repository = $this->createMock(CarRepositoryInterface::class);
        $repository->expects(self::once())->method('save');

        $service = new CarService($repository);
        $car = $service->create($this->makeForm([
            'brand' => 'BMW',
            'model' => 'X5',
            'year' => 2020,
            'body' => 'SUV',
            'mileage' => 65000,
        ]));

        self::assertTrue($car->hasOption());
        $option = $car->getOption();
        self::assertNotNull($option);
        self::assertSame('BMW', $option->brand);
        self::assertSame('X5', $option->model);
        self::assertSame(2020, $option->year);
        self::assertSame('SUV', $option->body);
        self::assertSame(65000, $option->mileage);
    }

    public function testCreateThrowsValidationExceptionOnInvalidDataAndSkipsSave(): void
    {
        $repository = $this->createMock(CarRepositoryInterface::class);
        $repository->expects(self::never())->method('save');

        $service = new CarService($repository);

        $form = new CreateCarForm();
        $form->setAttributes(['title' => 'Только заголовок'], false);

        $this->expectException(ValidationException::class);
        $service->create($form);
    }

    public function testCreateWithIncompleteOptionsFailsValidation(): void
    {
        $repository = $this->createMock(CarRepositoryInterface::class);
        $repository->expects(self::never())->method('save');

        $service = new CarService($repository);

        try {
            $service->create($this->makeForm(['brand' => 'BMW']));
            self::fail('Ожидалось исключение ValidationException.');
        } catch (ValidationException $e) {
            self::assertArrayHasKey('options.model', $e->getErrors());
        }
    }

    /**
     * @param array<string, mixed>|null $options
     */
    private function makeForm(?array $options = null): CreateCarForm
    {
        $form = new CreateCarForm();
        $form->setAttributes([
            'title' => 'BMW X5 2020',
            'description' => 'Отличное состояние, один владелец.',
            'price' => 2500000,
            'photo_url' => 'https://example.com/car.jpg',
            'contacts' => '+7 900 000-00-00',
            'options' => $options,
        ], false);

        return $form;
    }
}
