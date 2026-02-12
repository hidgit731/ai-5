<?php

declare(strict_types=1);

namespace App\Application\Mcp\Handlers;

use App\Application\Service\SeederService;
use App\Domain\Repository\CourseRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpMcp\Server\Attributes\McpTool;
use PhpMcp\Server\Attributes\Schema;
use Psr\Log\LoggerInterface;

readonly class CourseHandler
{
    public function __construct(
        private SeederService $seederService,
        private CourseRepositoryInterface $courseRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    #[McpTool(
        name: 'create_mock_courses_with_relations',
        description: 'Используй, когда просят создать в базе тестовые (mock) записи для сущности "курс". Также создаст цепочку связанных с этим курсом сущностей: урок, 2 задания, 2 навыка'
    )]
    public function createMockCourseWithRelations(
        #[Schema(description: 'Сколько просят создать записей (целое число >= 1, по-умолчанию = 1)', minimum: 1)]
        int $count = 1
    ): array
    {
        $this->logger->info('Processing debug tool', ['count' => $count]);

        $result = [];
        for ($i = 1; $i <= $count; $i++) {
            $result[] = ($this->seederService->createEntitiesFakeChain())->toArray();
        }

        return $result;
    }

    #[McpTool(
        name: 'cleanup_mock_courses',
        description: 'Удаляет тестовые курсы из базы данных. Полезен для очистки после тестирования. Можно указать максимальное количество или использовать dry-run режим для предварительного просмотра'
    )]
    public function cleanupMockCourses(
        #[Schema(description: 'Максимальное количество курсов для удаления (по умолчанию 10)', minimum: 1, maximum: 1000)]
        int $limit = 10,
        #[Schema(description: 'Режим предварительного просмотра: если true, покажет что будет удалено без фактического удаления')]
        bool $dryRun = false
    ): array
    {
        $this->logger->info('Processing cleanup tool', ['limit' => $limit, 'dryRun' => $dryRun]);

        // Получаем последние N курсов
        $courses = $this->courseRepository->findLatest($limit);

        if (empty($courses)) {
            return [
                'status' => 'no_courses_found',
                'message' => 'В базе данных не найдено курсов для удаления',
                'deleted_count' => 0,
                'courses' => []
            ];
        }

        $result = [
            'status' => $dryRun ? 'dry_run' : 'success',
            'message' => $dryRun
                ? 'Предварительный просмотр: следующие курсы будут удалены'
                : 'Курсы успешно удалены',
            'deleted_count' => count($courses),
            'courses' => []
        ];

        foreach ($courses as $course) {
            $courseData = $course->toArray();

            // Добавляем информацию о связанных сущностях
            $courseData['lessons_count'] = $course->getLessons()->count();
            $courseData['modules_count'] = $course->getModules()->count();

            $result['courses'][] = $courseData;

            // Удаляем только если не dry-run
            if (!$dryRun) {
                // Удаляем все связанные сущности вручную
                // 1. Удаляем задания с их связями с навыками
                foreach ($course->getLessons() as $lesson) {
                    foreach ($lesson->getTasks() as $task) {
                        $this->entityManager->remove($task);
                    }
                    $this->entityManager->remove($lesson);
                }

                // 2. Удаляем модули (если есть)
                foreach ($course->getModules() as $module) {
                    $this->entityManager->remove($module);
                }

                // 3. Удаляем сам курс
                $this->entityManager->remove($course);

                $this->logger->info('Marked for deletion: course', ['id' => $course->getId(), 'title' => $course->getTitle()]);
            }
        }

        // Выполняем все удаления одной транзакцией
        if (!$dryRun) {
            $this->entityManager->flush();
            $this->logger->info('Successfully deleted all courses from database');
        }

        return $result;
    }
}
