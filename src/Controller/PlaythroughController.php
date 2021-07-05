<?php

	namespace App\Controller;

	use App\DTO\Transformer\ResponseTransformer\PlaythroughResponseDTOTransformer;
	use App\Entity\Game;
	use App\Entity\Playthrough;
	use App\Entity\PlaythroughTemplate;
	use App\Entity\User;
	use Doctrine\Common\Collections\Collection;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class PlaythroughController extends AbstractController {

		private ValidatorInterface $validator;
		private PlaythroughResponseDTOTransformer $playthroughResponseDTOTransformer;

		public function __construct (ValidatorInterface $validator,
		                             PlaythroughResponseDTOTransformer $playthroughResponseDTOTransformer) {

			$this->validator = $validator;
			$this->playthroughResponseDTOTransformer = $playthroughResponseDTOTransformer;

		}

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
			$dto = $this->playthroughResponseDTOTransformer->transformFromObjects($playthroughs);

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				return new Response($errorString);
			}

			return new Response($serializer->serialize($dto, 'json',[
				'circular_reference_handler' => function ($object) {
					return $object->getId();
				}
			]), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]) ;

		}

	}