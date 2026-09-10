<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Application\Dto\CarView;
use App\Application\Dto\CreateCarForm;
use App\Application\Exception\ValidationException;
use App\Application\Service\CarService;
use Yii;
use yii\base\Module;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * REST-контроллер объявлений.
 */
final class CarController extends Controller
{
    public $enableCsrfValidation = false;

    public function __construct(
        string $id,
        Module $module,
        private readonly CarService $carService,
        array $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * POST /car/create
     *
     * @return array<string, mixed>
     */
    public function actionCreate(): array
    {
        $form = new CreateCarForm();
        $form->setAttributes(Yii::$app->request->getBodyParams(), false);

        try {
            $car = $this->carService->create($form);
        } catch (ValidationException $e) {
            Yii::$app->response->statusCode = 422;

            return [
                'message' => 'Ошибка валидации.',
                'errors' => $e->getErrors(),
            ];
        }

        Yii::$app->response->statusCode = 201;

        return CarView::fromEntity($car);
    }

    /**
     * GET /car/{id}
     *
     * @return array<string, mixed>
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): array
    {
        $car = $this->carService->getById($id);

        if ($car === null) {
            throw new NotFoundHttpException('Объявление не найдено.');
        }

        return CarView::fromEntity($car);
    }

    /**
     * GET /car/list
     *
     * @return array<string, mixed>
     */
    public function actionList(int $page = 1): array
    {
        $pageSize = (int) (Yii::$app->params['car.list.pageSize'] ?? 20);

        return $this->carService->list($page, $pageSize)->toResponse();
    }
}
