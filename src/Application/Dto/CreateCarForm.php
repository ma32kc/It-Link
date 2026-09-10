<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\Entity\Car;
use DateTimeImmutable;
use yii\base\Model;

/**
 * Валидация запроса на создание объявления. options необязателен.
 */
final class CreateCarForm extends Model
{
    public ?string $title = null;
    public ?string $description = null;
    public int|float|string|null $price = null;
    public ?string $photo_url = null;
    public ?string $contacts = null;

    /** @var array<string, mixed>|null */
    public ?array $options = null;

    private ?CarOptionForm $optionForm = null;

    public function rules(): array
    {
        return [
            [['title', 'description', 'price', 'photo_url', 'contacts'], 'required'],
            [['title', 'contacts'], 'string', 'max' => 255],
            ['photo_url', 'string', 'max' => 1024],
            ['photo_url', 'url', 'defaultScheme' => 'https'],
            ['description', 'string'],
            ['price', 'number', 'min' => 0],
            ['options', 'validateOptions'],
        ];
    }

    public function validateOptions(string $attribute): void
    {
        if ($this->options === null) {
            return;
        }

        if (!is_array($this->options)) {
            $this->addError($attribute, 'Блок options должен быть объектом либо null.');

            return;
        }

        $form = new CarOptionForm();
        $form->setAttributes($this->options, false);

        if (!$form->validate()) {
            foreach ($form->getErrors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError("options.{$field}", $message);
                }
            }

            return;
        }

        $this->optionForm = $form;
    }

    // вызывать после успешной валидации
    public function toEntity(): Car
    {
        return new Car(
            title: (string) $this->title,
            description: (string) $this->description,
            price: (float) $this->price,
            photoUrl: (string) $this->photo_url,
            contacts: (string) $this->contacts,
            createdAt: new DateTimeImmutable(),
            option: $this->optionForm?->toEntity(),
        );
    }
}
