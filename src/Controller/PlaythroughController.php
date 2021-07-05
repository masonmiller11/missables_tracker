<?php

	namespace App\Controller;

	use App\Entity\Game;
	use App\Entity\Playthrough;
	use App\Entity\PlaythroughTemplate;
	use App\Entity\User;
	use Doctrine\Common\Collections\Collection;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;

	class PlaythroughController extends AbstractController {

		/**
		 * @Route(path="/playthroughs/{page<\d+>?1}", methods={"GET"}, name="playthroughs.read")
		 *
		 * @param string|int          $page
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function list(string|int $page, SerializerInterface $serializer): Response {

			$user = $this->getUser();

			assert($user instanceof User);

			$playthroughs = $user->getPlaythroughs();

			return new Response($serializer->serialize($this->normalizeMany($playthroughs), 'json',[
				'circular_reference_handler' => function ($object) {
					return $object->getId();
				}
			]), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]) ;

		}

		protected function normalizeMany(Collection $entities) :array {
			return [
				$entities->map(
					fn (Playthrough $playthrough) => [
						'id'=>$playthrough->getId(),
						'visibility'=>$playthrough->isVisible(),
						'owner'=>$playthrough->getOwner()->getUsername(),
						'game'=> [
							'id'=>$playthrough->getGame()->getId(),
							'title'=>$playthrough->getGame()->getTitle()
						],
						'template'=>$playthrough->getTemplate()->getId()
					]
				)->toArray()
			];

		}
	}