<?php
	namespace App\Repository;

	use App\Entity\Playthrough\PlaythroughTemplate;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\ORM\QueryBuilder;
	use Doctrine\ORM\Tools\Pagination\Paginator;
	use Doctrine\Persistence\ManagerRegistry;
	use JetBrains\PhpStorm\ArrayShape;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Routing\Exception\ResourceNotFoundException;

	abstract class AbstractBaseRepository extends ServiceEntityRepository {

		/**
		 * @param QueryBuilder $qb
		 * @param int $page
		 * @param int $pageSize
		 * @param string $itemsName
		 * @return array
		 */
		#[ArrayShape(['items' => "array", 'totalItems' => "int", 'pageCount' => "float"])]
		protected function doPagination (QueryBuilder $qb, int $page, int $pageSize, string $itemsName): array {

			$paginator = new Paginator($qb);

			$totalItems = count($paginator);

			$pagesCount = ceil($totalItems / $pageSize);

			$paginator
				->getQuery()
				->setFirstResult($pageSize * ($page-1))
				->setMaxResults($pageSize);

			$items = array();

			foreach($paginator as $template) {
				$items[] = $template;
			}

			$queryResults = [
				$itemsName => $items,
				'totalItems' => $totalItems,
				'pageCount' => $pagesCount
			];

			if ($queryResults[$itemsName] == []) {
				throw new NotFoundHttpException('no ' . $itemsName . ' were found');
			}

			return [
				$itemsName => $items,
				'totalItems' => $totalItems,
				'pageCount' => $pagesCount
			];

	}

	}