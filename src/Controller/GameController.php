<?php

	namespace App\Controller;

	use App\Entity\EntityInterface;
	use App\Entity\Game;
	use App\Entity\PlaythroughTemplate;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\SerializerInterface;

	class GameController extends AbstractController {

		/**
		 * @Route(path="/games/{game<\d+>}", methods={"GET"}, name="games.read")
		 *
		 * @param Game $game
		 * @param SerializerInterface $serializer
		 * @return Response
		 */
		public function read(Game $game, SerializerInterface $serializer) :Response {

			return new Response($serializer->serialize($this->normalizeOne($game), 'json'), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]) ;

		}

		protected function normalizeOne(EntityInterface $entity) :array {
			assert($entity instanceof Game);
			return [
				'id'=>$entity->getId(),
				'genre'=>$entity->getGenre(),
				'releaseDate'=>$entity->getReleaseDate(),
				'title'=>$entity->getTitle(),
				'templates'=>$entity->getTemplates()->map(
					fn(PlaythroughTemplate $playthroughTemplate) => [
						'id'=>$playthroughTemplate->getId(),
						'visibility'=>$playthroughTemplate->isVisible(),
						'owner'=>$playthroughTemplate->getOwner(),
						'votes'=>$playthroughTemplate->getVotes(),
					]
				)->toArray()
			];
		}

	}