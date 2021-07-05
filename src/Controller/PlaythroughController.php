<?php

	namespace App\Controller;

	use App\Entity\EntityInterface;
	use App\Entity\PlaythroughTemplate;
	use App\Entity\User;
	use Doctrine\Common\Collections\Collection;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\SerializerInterface;

	class PlaythroughController extends AbstractController {

		/**
		 * @Route(path="/games/{id<\d+>}", methods={"GET"}, name="games.read")
		 *
		 * @param string|int          $id
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function read(string|int $id, SerializerInterface $serializer): Response {

			$user = $this->getUser();

			assert($user instanceof User);

			$playthroughs = $user->getPlaythroughs()->toArray();

			return new Response($serializer->serialize($this->normalizeMany($playthroughs), 'json'), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]) ;

		}

		protected function normalizeMany(Collection $entities) :array {
			return [
				$entities->map(
					fn (PlaythroughTemplate $playthroughTemplate) => [
						'id'=>$playthroughTemplate->getId(),
						'visibility'=>$playthroughTemplate->isVisible(),
						'owner'=>$playthroughTemplate->getOwner(),
						'votes'=>$playthroughTemplate->getVotes(),
					]
				)->toArray()
			];

		}
	}