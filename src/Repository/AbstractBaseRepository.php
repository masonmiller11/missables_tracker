<?php
	namespace App\Repository;

	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\ORM\QueryBuilder;
	use Doctrine\ORM\Tools\Pagination\Paginator;
	use JetBrains\PhpStorm\ArrayShape;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

			foreach($paginator as $item) {
				$items[] = $item;
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